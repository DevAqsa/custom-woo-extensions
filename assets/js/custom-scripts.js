jQuery(document).ready(function($) {
    // Handle quantity updates in cart
    $('.product-quantity input').on('change', function() {
        $('[name="update_cart"]').trigger('click');
    });

    // Dynamic pricing display
    function updateDynamicPricing() {
        const currentHour = new Date().getHours();
        const currentDay = new Date().getDay();

        // Happy hour notification (2 PM - 4 PM)
        if (currentHour >= 14 && currentHour < 16) {
            $('.custom-product-notice').remove();
            $('.custom-product-wrapper').prepend(
                '<div class="custom-product-notice">🎉 Happy Hour! 20% off right now!</div>'
            );
        }

        // Weekend pricing notification
        if (currentDay === 0 || currentDay === 6) {
            $('.custom-product-notice').remove();
            $('.custom-product-wrapper').prepend(
                '<div class="custom-product-notice">Weekend pricing in effect</div>'
            );
        }
    }

    // Initialize dynamic pricing notices
    if ($('.custom-product-wrapper').length) {
        updateDynamicPricing();
        setInterval(updateDynamicPricing, 60000); // Update every minute
    }

    // Handle subscription form submission
    $('.subscription-details form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate subscription period
        const period = $('#_subscription_period').val();
        if (period < 1) {
            alert('Please enter a valid subscription period');
            return false;
        }
        
        // Continue with form submission
        this.submit();
    });

    // Bulk pricing notification
    function updateBulkPricingNotice(quantity) {
        $('.cart-notice').remove();
        
        if (quantity >= 5) {
            $('.cart-items').prepend(
                '<div class="custom-cart-notice">10% bulk discount applied!</div>'
            );
        } else if (quantity >= 3) {
            $('.cart-items').prepend(
                '<div class="custom-cart-notice">5% bulk discount applied!</div>'
            );
        }
    }

    // Update notices when quantity changes
    $('.product-quantity input').on('change', function() {
        const quantity = $(this).val();
        updateBulkPricingNotice(quantity);
    });
});