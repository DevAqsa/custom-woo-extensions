<?php
class Custom_Product_Type {
    public function __construct() {
        add_filter('product_type_selector', array($this, 'add_custom_product_type'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_custom_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_custom_product_data_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_fields'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_custom_product_type($types) {
        $types['subscription'] = __('Subscription Product', 'custom-woo-extensions');
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
        global $post;
        
        // Add nonce field
        wp_nonce_field('save_subscription_data', 'subscription_nonce');
        
        echo '<div id="subscription_product_data" class="panel woocommerce_options_panel">';
        
        woocommerce_wp_text_input(array(
            'id' => '_subscription_period',
            'label' => __('Subscription Period (days)', 'custom-woo-extensions'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1',
                'max' => '365'
            ),
            'desc_tip' => true,
            'description' => __('Enter the subscription period in days.', 'custom-woo-extensions')
        ));

        echo '</div>';
    }

    public function save_custom_product_fields($post_id) {
        // Verify nonce
        if (!isset($_POST['subscription_nonce']) || 
            !wp_verify_nonce($_POST['subscription_nonce'], 'save_subscription_data')) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_product', $post_id)) {
            return;
        }

        // Save subscription period
        if (isset($_POST['_subscription_period'])) {
            $period = absint($_POST['_subscription_period']);
            // Validate period range
            if ($period >= 1 && $period <= 365) {
                update_post_meta($post_id, '_subscription_period', $period);
            }
        }
    }

    public function enqueue_admin_scripts($hook) {
        global $post;
        
        if ($hook == 'post.php' || $hook == 'post-new.php') {
            if (isset($post) && 'product' === $post->post_type) {
                wp_enqueue_script(
                    'custom-product-admin',
                    CWE_PLUGIN_URL . 'assets/js/admin-product.js',
                    array('jquery'),
                    CWE_VERSION,
                    true
                );

                wp_localize_script('custom-product-admin', 'customProductAdmin', array(
                    'nonce' => wp_create_nonce('custom_product_nonce'),
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));
            }
        }
    }
}