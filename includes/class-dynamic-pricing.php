<?php
class Dynamic_Pricing {
    public function __construct() {
        add_filter('woocommerce_get_price', array($this, 'apply_dynamic_pricing'), 10, 2);
    }

    public function apply_dynamic_pricing($price, $product) {
        // Time-based pricing
        $current_hour = (int)current_time('G');
        $current_day = date('N');

        // Happy hour discount (20% off between 2 PM and 4 PM)
        if ($current_hour >= 14 && $current_hour < 16) {
            $price = $price * 0.8;
        }

        // Weekend pricing (10% increase)
        if ($current_day >= 6) {
            $price = $price * 1.1;
        }

        // Bulk pricing
        if (WC()->cart) {
            $items_in_cart = $this->count_items_in_cart($product->get_id());
            
            if ($items_in_cart >= 5) {
                $price = $price * 0.9; // 10% off for 5 or more
            } elseif ($items_in_cart >= 3) {
                $price = $price * 0.95; // 5% off for 3 or more
            }
        }

        return $price;
    }

    private function count_items_in_cart($product_id) {
        $count = 0;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $count += $cart_item['quantity'];
            }
        }
        return $count;
    }
}