<?php
/**
 * Template for displaying subscription product details
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product->is_type('subscription')) {
    return;
}

$subscription_period = get_post_meta($product->get_id(), '_subscription_period', true);
?>

<div class="subscription-details-wrapper">
    <h2><?php echo esc_html__('Subscription Information', 'custom-woo-extensions'); ?></h2>
    
    <div class="subscription-info-box">
        <!-- Subscription Period -->
        <div class="subscription-period">
            <span class="label"><?php echo esc_html__('Billing Period:', 'custom-woo-extensions'); ?></span>
            <span class="value">
                <?php echo esc_html(sprintf(
                    __('Every %s days', 'custom-woo-extensions'),
                    $subscription_period
                )); ?>
            </span>
        </div>

        <!-- Next Payment Date -->
        <div class="next-payment">
            <span class="label"><?php echo esc_html__('Next Payment:', 'custom-woo-extensions'); ?></span>
            <span class="value">
                <?php echo esc_html(date('F j, Y', strtotime('+' . $subscription_period . ' days'))); ?>
            </span>
        </div>

        <!-- Subscription Terms -->
        <div class="subscription-terms">
            <h3><?php echo esc_html__('Terms & Conditions', 'custom-woo-extensions'); ?></h3>
            <ul>
                <li><?php echo esc_html__('Automatic renewal every billing period', 'custom-woo-extensions'); ?></li>
                <li><?php echo esc_html__('Cancel anytime from your account', 'custom-woo-extensions'); ?></li>
                <li><?php echo esc_html__('Prorated refunds available', 'custom-woo-extensions'); ?></li>
            </ul>
        </div>
    </div>
</div>