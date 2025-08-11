<?php
/**
 * Plugin Name: Amarire Payment Gateways with Dynamic Total-Based Fees (Dynamic Checkout)
 * Description: Adds multiple dynamic payment gateways with fees calculated on final total, updates dynamically in checkout.
 * Version: 1.5
 * Author: Amarire Dev
 * Text Domain: amarire-payment-gateways
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// include files
require_once plugin_dir_path(__FILE__) . 'includes/class-amarire-abstract-gateway.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-amarire-gateway-bank.php';
require_once plugin_dir_path(__FILE__) . 'includes/fees-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';

// disable default payment method selection
add_filter('woocommerce_default_payment_method', '__return_empty_string');

// unset chosen method on load checkout
add_action('woocommerce_before_checkout_form', function() {
    if ( WC()->session ) {
        WC()->session->__unset('chosen_payment_method');
    }
});

// enqueue script on checkout
add_action('wp_enqueue_scripts', function() {
    if ( is_checkout() ) {
        wp_enqueue_script(
            'amarire_payment_fee_update',
            plugin_dir_url(__FILE__) . 'assets/js/payment-fee-update.js',
            ['jquery','wc-checkout'],
            '1.1',
            true
        );
        wp_localize_script('amarire_payment_fee_update', 'amarire_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('amarire_payment_fee_nonce'),
        ]);
    }
});
