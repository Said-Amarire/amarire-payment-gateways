<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Recalculate fee when cart calculates fees
add_action('woocommerce_cart_calculate_fees', 'amarire_recalculate_payment_fee', 20, 1);
function amarire_recalculate_payment_fee( $cart ) {
    if ( is_admin() && ! defined('DOING_AJAX') ) return;

    $chosen = WC()->session->get('chosen_payment_method');
    if ( !$chosen ) return;

    $payment_gateways = WC()->payment_gateways()->payment_gateways();
    if ( isset($payment_gateways[$chosen]) && method_exists($payment_gateways[$chosen], 'add_payment_fee') ) {
        $payment_gateways[$chosen]->add_payment_fee($cart);
    }
}

// Save payment fee to order meta at order creation
add_action('woocommerce_checkout_create_order', function($order) {
    $fee = floatval(WC()->session->get('amarire_payment_fee', 0));
    if ( $fee !== null ) {
        $order->update_meta_data('_amarire_payment_fee', $fee);
    }
}, 20);

// Output payment fee in emails, thank you, and admin
function amarire_output_fee( $order ) {
    $fee = $order->get_meta('_amarire_payment_fee');
    if ( $fee && $fee > 0 ) {
        echo '<tr class="amr-payment-fee-order"><th>' . __('Payment Fee', 'amarire') . ':</th><td>' . wc_price( $fee ) . '</td></tr>';
    }
}
add_action('woocommerce_email_after_order_table', 'amarire_output_fee', 20);
add_action('woocommerce_order_details_after_order_table', 'amarire_output_fee');
add_action('woocommerce_admin_order_data_after_order_details', function( $order ) {
    amarire_output_fee( $order );
});

// Make payment fee always visible in checkout fee list (formatting)
add_filter('woocommerce_cart_fee_html', function($fee_html, $fee) {
    if (strpos($fee->name, 'Payment Fee') !== false) {
        return wc_price($fee->amount);
    }
    return $fee_html;
}, 10, 2);

// Ensure there's always a Payment Fee line (even zero)
add_action('woocommerce_cart_calculate_fees', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    $fee = floatval(WC()->session->get('amarire_payment_fee', 0));

    $found = false;
    foreach ($cart->get_fees() as $cart_fee) {
        if (strpos($cart_fee->name, 'Payment Fee') !== false) {
            $found = true;
            break;
        }
    }

    if ( ! $found ) {
        $cart->add_fee("Payment Fee", $fee, true);
    }
}, 25);
