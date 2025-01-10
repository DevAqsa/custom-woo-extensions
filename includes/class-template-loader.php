<?php
class Template_Loader {
    public function __construct() {
        add_filter('woocommerce_locate_template', array($this, 'locate_custom_template'), 10, 3);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_template_styles'));
    }

    public function locate_custom_template($template, $template_name, $template_path) {
        $plugin_template = CWE_PLUGIN_PATH . 'templates/woocommerce/' . $template_name;
        
        return file_exists($plugin_template) ? $plugin_template : $template;
    }




    public function enqueue_template_styles() {
        if (is_product() || is_cart()) {
            wp_enqueue_style('custom-woo-templates', 
                CWE_PLUGIN_URL . 'assets/css/custom-styles.css',
                array(),
                CWE_VERSION
            );
        }
    }


    
}