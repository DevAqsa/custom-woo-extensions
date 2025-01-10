<?php
class Custom_Product_Type {
    public function __construct() {
        add_filter('product_type_selector', array($this, 'add_custom_product_type'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_custom_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_custom_product_data_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_fields'));
    }

    public function add_custom_product_type($types) {
        $types['subscription'] = 'Subscription Product';
        return $types;
    }

    public function add_custom_product_data_tab($tabs) {
        $tabs['subscription'] = array(
            'label' => __('Subscription', 'custom-woo-extensions'),
            'target' => 'subscription_product_data',
            'class' => array('show_if_subscription'),
        );
        return $tabs;
    }

    public function add_custom_product_data_fields() {
        echo '<div id="subscription_product_data" class="panel woocommerce_options_panel">';
        
        woocommerce_wp_text_input(array(
            'id' => '_subscription_period',
            'label' => __('Subscription Period (days)', 'custom-woo-extensions'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1'
            )
        ));

        echo '</div>';
    }

    public function save_custom_product_fields($post_id) {
        if (isset($_POST['_subscription_period'])) {
            update_post_meta($post_id, '_subscription_period', 
                sanitize_text_field($_POST['_subscription_period'])
            );
        }
    }
}