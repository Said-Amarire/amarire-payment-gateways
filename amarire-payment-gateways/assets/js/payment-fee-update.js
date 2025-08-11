jQuery(function($){
    // Detect payment method change
    $('form.checkout').on('change', 'input[name="payment_method"]', function(){
        var payment_method = $(this).val();

        $.ajax({
            url: amarire_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'amarire_force_recalculate_fee',
                payment_method: payment_method,
                _ajax_nonce: amarire_ajax.nonce
            },
            success: function(response){
                if (response.success) {
                    // Build fees HTML rows
                    var fees_html = '';
                    $.each(response.data.fees, function(i, fee){
                        fees_html += '<tr class="fee"><th>' + fee.name + '</th><td>' + fee.amount + '</td></tr>';
                    });

                    // Remove prior fee lines and insert new ones before order-total row
                    $('.woocommerce-checkout-review-order-table tfoot .fee').remove();
                    $('.woocommerce-checkout-review-order-table tfoot tr.order-total').before(fees_html);

                    // Update total price text; the selector for amount may vary by theme
                    var total_html = response.data.total;
                    // Update the amount inside order-total cell
                    var $orderTotalAmount = $('.woocommerce-checkout-review-order-table tfoot tr.order-total .woocommerce-Price-amount').first();
                    if ( $orderTotalAmount.length ) {
                        $orderTotalAmount.html( total_html );
                    } else {
                        // fallback: replace whole order-total cell text
                        $('.woocommerce-checkout-review-order-table tfoot tr.order-total').find('td').html(total_html);
                    }
                }
            }
        });
    });
});
