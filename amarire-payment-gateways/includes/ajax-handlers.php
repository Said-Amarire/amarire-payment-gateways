<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// AJAX handler to set chosen payment method and recalc fees
add_action('wp_ajax_amarire_force_recalculate_fee', 'amarire_force_recalculate_fee_callback');
add_action('wp_ajax_nopriv_amarire_force_recalculate_fee', 'amarire_force_recalculate_fee_callback');

function amarire_force_recalculate_fee_callback() {
    // optional nonce check (frontend included localize created a nonce)
    if ( isset($_POST['_ajax_nonce']) && ! wp_verify_nonce( sanitize_text_field($_POST['_ajax_nonce']), 'amarire_payment_fee_nonce' ) ) {
        wp_send_json_error('Invalid nonce');
    }

    if ( isset($_POST['payment_method']) ) {
        $payment_method = sanitize_text_field($_POST['payment_method']);
        WC()->session->set('chosen_payment_method', $payment_method);

        // Recalculate cart totals and fees
        if ( WC()->cart ) {
            // calculate fees/triggers
            WC()->cart->calculate_fees();
            WC()->cart->calculate_totals();
        }

        // Collect current fees after recalculation
        $fees = [];
        foreach(WC()->cart->get_fees() as $fee){
            $fees[] = [
                'name' => $fee->name,
                'amount' => wc_price($fee->amount),
            ];
        }

        // Return fees and total
        wp_send_json_success([
            'fees' => $fees,
            'total' => WC()->cart->get_total(''),
        ]);
    }
    wp_send_json_error('Payment method not set');
}
