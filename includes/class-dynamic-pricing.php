<?php
class Dynamic_Pricing {
    public function __construct() {
        add_filter('woocommerce_get_price', array($this, 'apply_dynamic_pricing'), 10, 2);
        add_action('wp_ajax_update_dynamic_price', array($this, 'handle_price_update'));
        add_action('wp_ajax_nopriv_update_dynamic_price', array($this, 'handle_price_update'));
    }

    public function apply_dynamic_pricing($price, $product) {
        if (!is_numeric($price) || !is_object($product)) {
            return $price;
        }

        // Sanitize and validate the price
        $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        // Time-based pricing
        $current_hour = absint(current_time('G'));
        $current_day = absint(date('N'));

        // Get settings with defaults
        $happy_hour_discount = absint(get_option('cwe_happy_hour_discount', 20));
        $weekend_markup = absint(get_option('cwe_weekend_markup', 10));

        // Happy hour discount
        if ($current_hour >= 14 && $current_hour < 16) {
            $price = $price * ((100 - $happy_hour_discount) / 100);
        }

        // Weekend pricing
        if ($current_day >= 6) {
            $price = $price * (1 + ($weekend_markup / 100));
        }

        // Bulk pricing
        if (WC()->cart) {
            $items_in_cart = $this->count_items_in_cart($product->get_id());
            
            if ($items_in_cart >= 5) {
                $price = $price * 0.9;
            } elseif ($items_in_cart >= 3) {
                $price = $price * 0.95;
            }
        }

        return round($price, wc_get_price_decimals());
    }

    private function count_items_in_cart($product_id) {
        $count = 0;
        $product_id = absint($product_id);

        if (!$product_id) {
            return $count;
        }

        foreach (WC()->cart->get_cart() as $cart_item) {
            if (absint($cart_item['product_id']) === $product_id) {
                $count += absint($cart_item['quantity']);
            }
        }
        
        return $count;
    }

    public function handle_price_update() {
        // Verify nonce
        if (!check_ajax_referer(CWE_NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error('Invalid security token');
        }

        // Validate and sanitize input
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error('Invalid product ID');
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error('Product not found');
        }

        $price = $this->apply_dynamic_pricing($product->get_price(), $product);
        
        wp_send_json_success(array(
            'price' => wc_price($price),
            'raw_price' => $price
        ));
    }
}