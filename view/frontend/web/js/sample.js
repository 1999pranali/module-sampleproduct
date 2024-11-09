/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
require(['jquery', 'mage/url', 'Magento_Customer/js/customer-data'], function ($, urlBuilder, customerData) {
    $(document).ready(function () {
        $('.get-sample').click(function () {
            var productId = $(this).data('product-id');
            var addToCartUrl = urlBuilder.build('sampleproduct/cart/add'); 
            var inputQty = parseInt($('#qty').val());
            var totalQty = inputQty;
            var cartData = customerData.get('cart')().items;

            // Check if the product is already in the cart
            $.each(cartData, function (index, item) {
                if (item.product_id == productId) {
                    totalQty += item.qty;
                }
            });

            if (typeof window.sampleQtyConfig !== 'undefined') {
                var sampleQty = window.sampleQtyConfig.maxSampleQty;
                // Prevent adding to the cart if the total quantity exceeds the allowed limit
                if (totalQty > sampleQty) {
                    displayErrorMessage('You cannot add more than ' + sampleQty + ' of this sample product.');
                    return false;
                }
            }
            
            $.ajax({
                url: addToCartUrl,
                type: 'POST',
                dataType: 'json',
                data: { 
                    product_id: productId,
                    qty: inputQty
                 },
                success: function (response) {
                    if (response.success) {
                        customerData.reload(['cart'], true);
                        var cartUrl = urlBuilder.build('checkout/cart');
                        var successMessage = 'You added sample product to your <a href="' + cartUrl + '">shopping cart</a>.';
                        displaySuccessMessage(successMessage);
                    } else {
                        displayErrorMessage('Unable to add product to cart.');
                    }
                },
                error: function () {
                    displayErrorMessage('Unable to add product to cart.');
                }
            });
        });

        function displaySuccessMessage(message) {
            var successMessageHtml = `
                <div class="messages">
                    <div class="message-success success message" data-ui-id="message-success">
                        <div>${message}</div>
                    </div>
                </div>
            `;
            $('.page.messages').html(successMessageHtml);
        }

        function displayErrorMessage(message) {
            var errorMessageHtml = `
                <div class="messages">
                    <div class="message-error error message" data-ui-id="message-error">
                        <div>${message}</div>
                    </div>
                </div>
            `;
            $('.page.messages').html(errorMessageHtml);
        }

    });
});
