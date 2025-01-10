<?php
/**
 * Plugin Name: Custom WooCommerce Extensions
 * Description: Custom product types, hooks, and dynamic pricing
 * Version: 1.0
 * Author: Aqsa Mumtaz
 */

if (!defined('ABSPATH')) {
    exit; 
}

// Check WooCommerce dependency
function check_woocommerce_dependency() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'woocommerce_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

// Admin notice for missing WooCommerce
function woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php 
            echo sprintf(
                'Custom Product Creator requires %sWooCommerce%s to be installed and activated. Please install and activate WooCommerce first.',
                '<a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">',
                '</a>'
            );
        ?></p>
    </div>
    <?php
}

// Hook to check dependency on plugin activation
register_activation_hook(__FILE__, 'check_woocommerce_dependency');

class Custom_WooCommerce_Extensions {
    public function __construct() {
        // Check WooCommerce is active before initializing
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Register custom product type
        add_filter('product_type_selector', array($this, 'add_custom_product_type'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_custom_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_custom_product_data_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_fields'));

        // Dynamic pricing
        add_filter('woocommerce_get_price', array($this, 'apply_dynamic_pricing'), 10, 2);
    }

    // Add custom product type to dropdown
    public function add_custom_product_type($types) {
        $types['subscription'] = 'Subscription Product';
        return $types;
    }

    // Add custom product data tab
    public function add_custom_product_data_tab($tabs) {
        $tabs['subscription'] = array(
            'label' => __('Subscription', 'woocommerce'),
            'target' => 'subscription_product_data',
            'class' => array('show_if_subscription'),
        );
        return $tabs;
    }

    // Add custom fields in product data tab
    public function add_custom_product_data_fields() {
        echo '<div id="subscription_product_data" class="panel woocommerce_options_panel">';
        
        woocommerce_wp_text_input(array(
            'id' => '_subscription_period',
            'label' => __('Subscription Period (days)', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1'
            )
        ));

        echo '</div>';
    }

    // Save custom fields
    public function save_custom_product_fields($post_id) {
        if (isset($_POST['_subscription_period'])) {
            update_post_meta($post_id, '_subscription_period', sanitize_text_field($_POST['_subscription_period']));
        }
    }

    // Dynamic pricing implementation
    public function apply_dynamic_pricing($price, $product) {
        // Example dynamic pricing rules
        $current_hour = (int)current_time('G');
        $current_day = date('N'); // 1 (Monday) to 7 (Sunday)

        // Happy hour discount (20% off between 2 PM and 4 PM)
        if ($current_hour >= 14 && $current_hour < 16) {
            $price = $price * 0.8;
        }

        // Weekend pricing (10% increase)
        if ($current_day >= 6) { // Saturday or Sunday
            $price = $price * 1.1;
        }

        // Bulk pricing
        $items_in_cart = 0;
        if (WC()->cart) {
            foreach (WC()->cart->get_cart() as $cart_item) {
                if ($cart_item['product_id'] == $product->get_id()) {
                    $items_in_cart += $cart_item['quantity'];
                }
            }
            
            // Apply bulk discounts
            if ($items_in_cart >= 5) {
                $price = $price * 0.9; // 10% off for 5 or more items
            } elseif ($items_in_cart >= 3) {
                $price = $price * 0.95; // 5% off for 3 or more items
            }
        }

        return $price;
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    // Additional check before initialization
    if (class_exists('WooCommerce')) {
        new Custom_WooCommerce_Extensions();
    }
});