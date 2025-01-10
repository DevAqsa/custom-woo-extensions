<?php
/**
 * Plugin Name: Custom WooCommerce Extensions
 * Description: Custom product types, hooks, and dynamic pricing
 * Version: 1.0
 * Author: Your Name
 * Text Domain: custom-woo-extensions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CWE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CWE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CWE_VERSION', '1.0.0');

require_once CWE_PLUGIN_PATH . 'admin/class-admin-menu.php';

// Autoloader for classes
spl_autoload_register(function ($class_name) {
    $classes_dir = CWE_PLUGIN_PATH . 'includes/';
    $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
    $class_path = $classes_dir . $class_file;

    if (file_exists($class_path)) {
        require_once $class_path;
    }
});

// Check WooCommerce dependency
function cwe_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'cwe_woocommerce_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

// Admin notice for missing WooCommerce
function cwe_woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php 
            echo sprintf(
                'Custom Product Creator requires %sWooCommerce%s to be installed and activated.',
                '<a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">',
                '</a>'
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

// Enqueue CSS and JavaScript
function cwe_enqueue_assets() {
    wp_enqueue_style(
        'custom-woo-styles',
        CWE_PLUGIN_URL . 'assets/css/custom-styles.css',
        array(),
        CWE_VERSION
    );

    wp_enqueue_script(
        'custom-woo-scripts',
        CWE_PLUGIN_URL . 'assets/js/custom-scripts.js',
        array('jquery'),
        CWE_VERSION,
        true
    );
}

// Activation hook
register_activation_hook(__FILE__, 'cwe_check_woocommerce');

// Initialize plugin after WooCommerce is loaded
add_action('plugins_loaded', 'cwe_init');