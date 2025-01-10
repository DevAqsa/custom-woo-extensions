<?php
/**
 * Custom Cart Template
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<form class="woocommerce-cart-form custom-cart-wrapper" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    <div class="cart-content">
        <?php if (WC()->cart->is_empty()): ?>
            <div class="empty-cart-message">
                <p><?php echo esc_html__('Your cart is currently empty.', 'woocommerce'); ?></p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button continue-shopping">
                    <?php echo esc_html__('Continue Shopping', 'woocommerce'); ?>
                </a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <h2><?php echo esc_html__('Shopping Cart', 'woocommerce'); ?></h2>
                
                <?php do_action('woocommerce_before_cart_contents'); ?>

                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters(
                        'woocommerce_cart_item_product',
                        $cart_item['data'],
                        $cart_item,
                        $cart_item_key
                    );

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                        $product_permalink = apply_filters(
                            'woocommerce_cart_item_permalink',
                            $_product->is_visible() ? $_product->get_permalink($cart_item) : '',
                            $cart_item,
                            $cart_item_key
                        );
                        ?>
                        <div class="cart-item">
                            <!-- Product Thumbnail -->
                            <div class="product-thumbnail">
                                <?php
                                $thumbnail = apply_filters(
                                    'woocommerce_cart_item_thumbnail',
                                    $_product->get_image(),
                                    $cart_item,
                                    $cart_item_key
                                );

                                if ($product_permalink) {
                                    echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                                } else {
                                    echo $thumbnail;
                                }
                                ?>
                            </div>

                            <!-- Product Details -->
                            <div class="product-details">
                                <h3 class="product-name">
                                    <?php
                                    if ($product_permalink) {
                                        echo '<a href="' . esc_url($product_permalink) . '">';
                                    }
                                    
                                    echo wp_kses_post(apply_filters(
                                        'woocommerce_cart_item_name',
                                        $_product->get_name(),
                                        $cart_item,
                                        $cart_item_key
                                    ));

                                    if ($product_permalink) {
                                        echo '</a>';
                                    }

                                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
                                    ?>
                                </h3>

                                <!-- Product Price -->
                                <div class="product-price">
                                    <?php
                                    echo apply_filters(
                                        'woocommerce_cart_item_price',
                                        WC()->cart->get_product_price($_product),
                                        $cart_item,
                                        $cart_item_key
                                    );
                                    ?>
                                </div>

                                <!-- Quantity Input -->
                                <div class="product-quantity">
                                    <?php
                                    if ($_product->is_sold_individually()) {
                                        $min_quantity = 1;
                                        $max_quantity = 1;
                                    } else {
                                        $min_quantity = 0;
                                        $max_quantity = $_product->get_max_purchase_quantity();
                                    }

                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $max_quantity,
                                            'min_value'    => $min_quantity,
                                            'product_name' => $_product->get_name(),
                                        ),
                                        $_product,
                                        false
                                    );

                                    echo apply_filters(
                                        'woocommerce_cart_item_quantity',
                                        $product_quantity,
                                        $cart_item_key,
                                        $cart_item
                                    );
                                    ?>
                                </div>

                                <!-- Subtotal -->
                                <div class="product-subtotal">
                                    <?php
                                    echo apply_filters(
                                        'woocommerce_cart_item_subtotal',
                                        WC()->cart->get_product_subtotal($_product, $cart_item['quantity']),
                                        $cart_item,
                                        $cart_item_key
                                    );
                                    ?>
                                </div>

                                <!-- Remove Item -->
                                <div class="product-remove">
                                    <?php
                                    echo apply_filters(
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                                            esc_html__('Remove this item', 'woocommerce'),
                                            esc_attr($_product->get_id()),
                                            esc_attr($_product->get_sku())
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                <?php do_action('woocommerce_cart_contents'); ?>

                <!-- Cart Actions -->
                <div class="cart-actions">
                    <button type="submit" class="button update-cart" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                        <?php esc_html_e('Update cart', 'woocommerce'); ?>
                    </button>

                    <?php do_action('woocommerce_cart_actions'); ?>

                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </div>

                <?php do_action('woocommerce_after_cart_contents'); ?>
            </div>

            <!-- Cart Totals -->
            <div class="cart-collaterals">
                <?php
                    /**
                     * Cart collaterals hook.
                     * @hooked woocommerce_cross_sell_display
                     * @hooked woocommerce_cart_totals
                     */
                    do_action('woocommerce_cart_collaterals');
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<?php do_action('woocommerce_after_cart'); ?>