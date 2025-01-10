<?php
/**
 * Menu System for Custom WooCommerce Extensions
 */

class CWE_Admin_Menu {
    private $settings = array();

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Add menu and submenu pages
     */
    public function add_menu_pages() {
        // Main Menu
        add_menu_page(
            __('Custom WooCommerce Extensions', 'custom-woo-extensions'),
            __('CW Extensions', 'custom-woo-extensions'),
            'manage_options',
            'custom-woo-extensions',
            array($this, 'render_dashboard_page'),
            'dashicons-cart',
            56
        );

        // Submenus
        add_submenu_page(
            'custom-woo-extensions',
            __('Dashboard', 'custom-woo-extensions'),
            __('Dashboard', 'custom-woo-extensions'),
            'manage_options',
            'custom-woo-extensions',
            array($this, 'render_dashboard_page')
        );

        add_submenu_page(
            'custom-woo-extensions',
            __('Subscription Settings', 'custom-woo-extensions'),
            __('Subscriptions', 'custom-woo-extensions'),
            'manage_options',
            'cwe-subscriptions',
            array($this, 'render_subscription_page')
        );

        add_submenu_page(
            'custom-woo-extensions',
            __('Dynamic Pricing', 'custom-woo-extensions'),
            __('Pricing Rules', 'custom-woo-extensions'),
            'manage_options',
            'cwe-pricing',
            array($this, 'render_pricing_page')
        );

        add_submenu_page(
            'custom-woo-extensions',
            __('Template Settings', 'custom-woo-extensions'),
            __('Templates', 'custom-woo-extensions'),
            'manage_options',
            'cwe-templates',
            array($this, 'render_template_page')
        );

        add_submenu_page(
            'custom-woo-extensions',
            __('Reports & Analytics', 'custom-woo-extensions'),
            __('Reports', 'custom-woo-extensions'),
            'manage_options',
            'cwe-reports',
            array($this, 'render_reports_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Template Settings
        register_setting('cwe_template_settings', 'cwe_product_template');
        register_setting('cwe_template_settings', 'cwe_cart_template');
        register_setting('cwe_template_settings', 'cwe_subscription_template');

        // Subscription Settings
        register_setting('cwe_subscription_settings', 'cwe_default_period');
        register_setting('cwe_subscription_settings', 'cwe_trial_period');
        register_setting('cwe_subscription_settings', 'cwe_cancellation_terms');

        // Dynamic Pricing Settings
        register_setting('cwe_pricing_settings', 'cwe_happy_hour_discount');
        register_setting('cwe_pricing_settings', 'cwe_weekend_markup');
        register_setting('cwe_pricing_settings', 'cwe_bulk_discount_rules');
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'custom-woo-extensions') !== false) {
            wp_enqueue_style('cwe-admin-styles', CWE_PLUGIN_URL . 'assets/css/admin-styles.css', array(), CWE_VERSION);
            wp_enqueue_script('cwe-admin-scripts', CWE_PLUGIN_URL . 'assets/js/admin-scripts.js', array('jquery'), CWE_VERSION, true);
        }
    }

    /**
     * Render Dashboard Page
     */
    public function render_dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Custom Woo Extensions Dashboard', 'custom-woo-extensions'); ?></h1>
            
            <div class="cwe-dashboard-widgets">
                <!-- Statistics Widget -->
                <div class="cwe-widget">
                    <h2><?php echo esc_html__('Quick Statistics', 'custom-woo-extensions'); ?></h2>
                    <div class="cwe-stats">
                        <div class="stat-item">
                            <span class="stat-label"><?php echo esc_html__('Active Subscriptions', 'custom-woo-extensions'); ?></span>
                            <span class="stat-value"><?php echo $this->get_active_subscriptions_count(); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label"><?php echo esc_html__('Dynamic Prices Active', 'custom-woo-extensions'); ?></span>
                            <span class="stat-value"><?php echo $this->get_dynamic_prices_count(); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Widget -->
                <div class="cwe-widget">
                    <h2><?php echo esc_html__('Quick Actions', 'custom-woo-extensions'); ?></h2>
                    <div class="cwe-actions">
                        <a href="<?php echo admin_url('post-new.php?post_type=product&product_type=subscription'); ?>" class="button button-primary">
                            <?php echo esc_html__('Add New Subscription', 'custom-woo-extensions'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=cwe-pricing'); ?>" class="button button-secondary">
                            <?php echo esc_html__('Manage Pricing Rules', 'custom-woo-extensions'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    //-------------------------------------

/**
     * Render Dynamic Pricing Page
     */
    public function render_pricing_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Dynamic Pricing Rules', 'custom-woo-extensions'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('cwe_pricing_settings');
                do_settings_sections('cwe_pricing_settings');
                ?>
                
                <div class="cwe-settings-section">
                    <h2><?php echo esc_html__('Time-Based Pricing', 'custom-woo-extensions'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html__('Happy Hour Discount (%)', 'custom-woo-extensions'); ?></th>
                            <td>
                                <input type="number" 
                                    name="cwe_happy_hour_discount" 
                                    value="<?php echo esc_attr(get_option('cwe_happy_hour_discount', '20')); ?>" 
                                    min="0" 
                                    max="100" 
                                    step="1" 
                                />
                                <p class="description">
                                    <?php echo esc_html__('Discount applied between 2 PM and 4 PM', 'custom-woo-extensions'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php echo esc_html__('Happy Hour Time Range', 'custom-woo-extensions'); ?></th>
                            <td>
                                <select name="cwe_happy_hour_start">
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        $time = sprintf('%02d:00', $i);
                                        $selected = selected(get_option('cwe_happy_hour_start', '14'), $i, false);
                                        echo "<option value='{$i}' {$selected}>{$time}</option>";
                                    }
                                    ?>
                                </select>
                                to
                                <select name="cwe_happy_hour_end">
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        $time = sprintf('%02d:00', $i);
                                        $selected = selected(get_option('cwe_happy_hour_end', '16'), $i, false);
                                        echo "<option value='{$i}' {$selected}>{$time}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="cwe-settings-section">
                    <h2><?php echo esc_html__('Weekly Pricing', 'custom-woo-extensions'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html__('Weekend Markup (%)', 'custom-woo-extensions'); ?></th>
                            <td>
                                <input type="number" 
                                    name="cwe_weekend_markup" 
                                    value="<?php echo esc_attr(get_option('cwe_weekend_markup', '10')); ?>" 
                                    min="0" 
                                    max="100" 
                                    step="1" 
                                />
                                <p class="description">
                                    <?php echo esc_html__('Additional markup applied on weekends', 'custom-woo-extensions'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php echo esc_html__('Enable Weekend Pricing', 'custom-woo-extensions'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                        name="cwe_weekend_pricing_enabled" 
                                        value="1" 
                                        <?php checked(get_option('cwe_weekend_pricing_enabled', '1')); ?> 
                                    />
                                    <?php echo esc_html__('Apply weekend markup', 'custom-woo-extensions'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="cwe-settings-section">
                    <h2><?php echo esc_html__('Bulk Pricing Rules', 'custom-woo-extensions'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html__('Enable Bulk Pricing', 'custom-woo-extensions'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                        name="cwe_bulk_pricing_enabled" 
                                        value="1" 
                                        <?php checked(get_option('cwe_bulk_pricing_enabled', '1')); ?> 
                                    />
                                    <?php echo esc_html__('Apply bulk discounts', 'custom-woo-extensions'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php echo esc_html__('Bulk Pricing Tiers', 'custom-woo-extensions'); ?></th>
                            <td>
                                <div class="bulk-pricing-tiers">
                                    <!-- Tier 1 -->
                                    <div class="pricing-tier">
                                        <label><?php echo esc_html__('Tier 1 (3-4 items):', 'custom-woo-extensions'); ?></label>
                                        <input type="number" 
                                            name="cwe_bulk_discount_tier1" 
                                            value="<?php echo esc_attr(get_option('cwe_bulk_discount_tier1', '5')); ?>" 
                                            min="0" 
                                            max="100" 
                                            step="1" 
                                        />
                                        <span>%</span>
                                    </div>

                                    <!-- Tier 2 -->
                                    <div class="pricing-tier">
                                        <label><?php echo esc_html__('Tier 2 (5+ items):', 'custom-woo-extensions'); ?></label>
                                        <input type="number" 
                                            name="cwe_bulk_discount_tier2" 
                                            value="<?php echo esc_attr(get_option('cwe_bulk_discount_tier2', '10')); ?>" 
                                            min="0" 
                                            max="100" 
                                            step="1" 
                                        />
                                        <span>%</span>
                                    </div>
                                </div>
                                <p class="description">
                                    <?php echo esc_html__('Set discount percentages for different quantity tiers', 'custom-woo-extensions'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>

        <style>
            .bulk-pricing-tiers {
                margin: 10px 0;
            }
            .pricing-tier {
                margin-bottom: 10px;
            }
            .pricing-tier label {
                display: inline-block;
                width: 120px;
            }
            .pricing-tier input {
                width: 80px;
            }
            .cwe-settings-section {
                background: #fff;
                padding: 20px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
        </style>

     <?php
}

    //---------------------------------------


    /**
 * Render Subscription Settings Page
 */
public function render_subscription_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Subscription Settings', 'custom-woo-extensions'); ?></h1>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('cwe_subscription_settings');
            do_settings_sections('cwe_subscription_settings');
            ?>
            
            <div class="cwe-settings-section">
                <h2><?php echo esc_html__('Default Subscription Settings', 'custom-woo-extensions'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Default Billing Period', 'custom-woo-extensions'); ?></th>
                        <td>
                            <select name="cwe_default_period">
                                <option value="month" <?php selected(get_option('cwe_default_period'), 'month'); ?>>
                                    <?php echo esc_html__('Monthly', 'custom-woo-extensions'); ?>
                                </option>
                                <option value="year" <?php selected(get_option('cwe_default_period'), 'year'); ?>>
                                    <?php echo esc_html__('Yearly', 'custom-woo-extensions'); ?>
                                </option>
                                <option value="week" <?php selected(get_option('cwe_default_period'), 'week'); ?>>
                                    <?php echo esc_html__('Weekly', 'custom-woo-extensions'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php echo esc_html__('Trial Period (days)', 'custom-woo-extensions'); ?></th>
                        <td>
                            <input type="number" 
                                name="cwe_trial_period" 
                                value="<?php echo esc_attr(get_option('cwe_trial_period', '14')); ?>" 
                                min="0" 
                                step="1" 
                            />
                            <p class="description">
                                <?php echo esc_html__('Default trial period for new subscriptions (0 for no trial)', 'custom-woo-extensions'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="cwe-settings-section">
                <h2><?php echo esc_html__('Cancellation Settings', 'custom-woo-extensions'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Cancellation Terms', 'custom-woo-extensions'); ?></th>
                        <td>
                            <?php 
                            $terms = get_option('cwe_cancellation_terms', '');
                            wp_editor($terms, 'cwe_cancellation_terms', array(
                                'textarea_name' => 'cwe_cancellation_terms',
                                'textarea_rows' => 5,
                                'media_buttons' => false
                            ));
                            ?>
                            <p class="description">
                                <?php echo esc_html__('Terms displayed to customers when canceling a subscription', 'custom-woo-extensions'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php echo esc_html__('Allow Immediate Cancellation', 'custom-woo-extensions'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                    name="cwe_immediate_cancellation" 
                                    value="1" 
                                    <?php checked(get_option('cwe_immediate_cancellation', '1')); ?> 
                                />
                                <?php echo esc_html__('Allow customers to cancel immediately (if unchecked, subscription will end at billing period)', 'custom-woo-extensions'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>

    <style>
        .cwe-settings-section {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .wp-editor-container {
            border: 1px solid #ddd;
        }
    </style>
    <?php
}

    /**
     * Render Template Settings Page
     */


    public function render_template_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Template Settings', 'custom-woo-extensions'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('cwe_template_settings');
                do_settings_sections('cwe_template_settings');
                ?>
                
                <div class="cwe-settings-section">
                    <h2><?php echo esc_html__('Product Template Settings', 'custom-woo-extensions'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html__('Custom Product Template', 'custom-woo-extensions'); ?></th>
                            <td>
                                <select name="cwe_product_template">
                                    <option value="default" <?php selected(get_option('cwe_product_template'), 'default'); ?>>
                                        <?php echo esc_html__('Default Template', 'custom-woo-extensions'); ?>
                                    </option>
                                    <option value="custom" <?php selected(get_option('cwe_product_template'), 'custom'); ?>>
                                        <?php echo esc_html__('Custom Template', 'custom-woo-extensions'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo esc_html__('Custom Cart Template', 'custom-woo-extensions'); ?></th>
                            <td>
                                <select name="cwe_cart_template">
                                    <option value="default" <?php selected(get_option('cwe_cart_template'), 'default'); ?>>
                                        <?php echo esc_html__('Default Template', 'custom-woo-extensions'); ?>
                                    </option>
                                    <option value="custom" <?php selected(get_option('cwe_cart_template'), 'custom'); ?>>
                                        <?php echo esc_html__('Custom Template', 'custom-woo-extensions'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render Reports Page
     */
    public function render_reports_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Reports & Analytics', 'custom-woo-extensions'); ?></h1>
            
            <div class="cwe-reports-wrapper">
                <!-- Subscription Reports -->
                <div class="cwe-widget">
                    <h2><?php echo esc_html__('Subscription Overview', 'custom-woo-extensions'); ?></h2>
                    <div class="cwe-report-content">
                        <?php $this->render_subscription_reports(); ?>
                    </div>
                </div>

                <!-- Pricing Reports -->
                <div class="cwe-widget">
                    <h2><?php echo esc_html__('Dynamic Pricing Impact', 'custom-woo-extensions'); ?></h2>
                    <div class="cwe-report-content">
                        <?php $this->render_pricing_reports(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Subscription Reports
     */
    private function render_subscription_reports() {
        // Add your subscription reporting logic here
        echo '<p>' . esc_html__('Subscription reports will be displayed here.', 'custom-woo-extensions') . '</p>';
    }

    /**
     * Render Pricing Reports
     */

    private function render_pricing_reports() {
        // Add your pricing reporting logic here
        echo '<p>' . esc_html__('Pricing impact reports will be displayed here.', 'custom-woo-extensions') . '</p>';
    }

    /**
     * Helper methods for statistics
     */
    private function get_active_subscriptions_count() {
        $args = array(
            'post_type' => 'product',
            'meta_key' => '_subscription_period',
            'posts_per_page' => -1
        );
        $subscriptions = new WP_Query($args);
        return $subscriptions->found_posts;
    }

    private function get_dynamic_prices_count() {
        $args = array(
            'post_type' => 'product',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_happy_hour_enabled',
                    'value' => 'yes'
                ),
                array(
                    'key' => '_weekend_pricing_enabled',
                    'value' => 'yes'
                )
            ),
            'posts_per_page' => -1
        );
        $dynamic_prices = new WP_Query($args);
        return $dynamic_prices->found_posts;
    }


    
}




// Initialize the menu system
new CWE_Admin_Menu();