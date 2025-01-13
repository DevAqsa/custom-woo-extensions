<?php
/**
 * Plugin Name: Custom WooCommerce Extensions
 * Description: Custom product types, hooks, and dynamic pricing
 * Version: 1.0
 * Author: Aqsa Mumtaz
 * Text Domain: custom-woo-extensions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CWE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CWE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CWE_VERSION', '1.0.0');

// Security nonce name
define('CWE_NONCE_ACTION', 'custom_woo_extensions_nonce');

require_once CWE_PLUGIN_PATH . 'admin/class-admin-menu.php';

// Autoloader for classes
spl_autoload_register(function ($class_name) {
    // Sanitize class name to prevent directory traversal
    $class_name = preg_replace('/[^a-zA-Z0-9_]/', '', $class_name);
    
    $classes_dir = CWE_PLUGIN_PATH . 'includes/';
    $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
    $class_path = $classes_dir . $class_file;

    if (file_exists($class_path)) {
        require_once $class_path;
    }
});

// Check WooCommerce dependency with nonce verification
function cwe_check_woocommerce() {
    if (isset($_GET['activate'])) {
       
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'activate-plugin_' . plugin_basename(__FILE__))) {
            wp_die('Security check failed');
        }
        
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', 'cwe_woocommerce_missing_notice');
            deactivate_plugins(plugin_basename(__FILE__));
            unset($_GET['activate']);
        }
    }
}

// Admin notice for missing WooCommerce
function cwe_woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php 
            echo wp_kses(
                sprintf(
                    'Custom Product Creator requires %sWooCommerce%s to be installed and activated.',
                    '<a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">',
                    '</a>'
                ),
                array(
                    'a' => array(
                        'href' => array()
                    )
                )
            );
        ?></p>
    </div>
    <?php
}

// Initialize plugin
function cwe_init() {
    if (class_exists('WooCommerce')) {
        // Load classes
        new Custom_Product_Type();
        new Dynamic_Pricing();
        new Template_Loader();

        // Enqueue assets
        add_action('wp_enqueue_scripts', 'cwe_enqueue_assets');
    }
}

// Enqueue CSS and JavaScript with version control
function cwe_enqueue_assets() {
    $version = defined('WP_DEBUG') && WP_DEBUG ? time() : CWE_VERSION;
    
    wp_enqueue_style(
        'custom-woo-styles',
        esc_url(CWE_PLUGIN_URL . 'assets/css/custom-styles.css'),
        array(),
        $version
    );

    wp_enqueue_script(
        'custom-woo-scripts',
        esc_url(CWE_PLUGIN_URL . 'assets/js/custom-scripts.js'),
        array('jquery'),
        $version,
        true
    );

    // Add nonce to JavaScript
    wp_localize_script('custom-woo-scripts', 'cweAjax', array(
        'nonce' => wp_create_nonce(CWE_NONCE_ACTION),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}

register_activation_hook(__FILE__, 'cwe_check_woocommerce');
add_action('plugins_loaded', 'cwe_init');