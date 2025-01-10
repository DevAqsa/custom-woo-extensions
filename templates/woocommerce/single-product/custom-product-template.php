<?php
/**
 * Custom Single Product Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get global product object
global $product;

// Ensure $product is valid
if (!is_object($product)) {
    return;
}
?>

<div class="custom-product-wrapper" id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>
    <div class="custom-product-header">
        <?php do_action('woocommerce_before_single_product'); ?>
        
        <h1 class="product-title"><?php the_title(); ?></h1>
        
        <?php if ($product->is_on_sale()): ?>
            <span class="sale-badge">
                <?php echo esc_html__('Sale!', 'woocommerce'); ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="product-content-wrapper">
        <div class="product-gallery-col">
            <?php
            /**
             * Hook: woocommerce_before_single_product_summary
             * @hooked woocommerce_show_product_sale_flash - 10
             * @hooked woocommerce_show_product_images - 20
             */
            do_action('woocommerce_before_single_product_summary');
            ?>
        </div>

        <div class="product-summary-col">
            <div class="summary entry-summary">
                <?php
                /**
                 * Hook: woocommerce_single_product_summary
                 * @hooked woocommerce_template_single_title - 5
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 */
                do_action('woocommerce_single_product_summary');
                ?>

                <?php if ($product->get_type() === 'subscription'): ?>
                    <div class="subscription-details">
                        <?php
                        $period = get_post_meta($product->get_id(), '_subscription_period', true);
                        if ($period) {
                            echo '<div class="subscription-info">';
                            echo '<h3>' . esc_html__('Subscription Details', 'woocommerce') . '</h3>';
                            echo '<p class="subscription-period">' . 
                                sprintf(
                                    esc_html__('Subscription Period: %s days', 'woocommerce'),
                                    esc_html($period)
                                ) . 
                                '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Custom Product Features -->
                <div class="product-features">
                    <h3><?php echo esc_html__('Product Features', 'woocommerce'); ?></h3>
                    <ul>
                        <?php
                        $features = $product->get_attribute('features');
                        if ($features) {
                            foreach (explode(', ', $features) as $feature) {
                                echo '<li>' . esc_html($feature) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <!-- Stock Status with Custom Styling -->
                <div class="stock-status">
                    <?php if ($product->is_in_stock()): ?>
                        <p class="in-stock">
                            <?php 
                            echo sprintf(
                                esc_html__('In Stock (%s items remaining)', 'woocommerce'),
                                $product->get_stock_quantity()
                            ); 
                            ?>
                        </p>
                    <?php else: ?>
                        <p class="out-of-stock">
                            <?php echo esc_html__('Out of Stock', 'woocommerce'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="custom-product-tabs">
        <?php
        /**
         * Hook: woocommerce_after_single_product_summary
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action('woocommerce_after_single_product_summary');
        ?>
    </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?>