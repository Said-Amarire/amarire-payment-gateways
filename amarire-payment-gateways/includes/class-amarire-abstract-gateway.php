<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', function() {
    if ( ! class_exists('WC_Payment_Gateway') ) return;

    abstract class Amarire_Abstract_Gateway extends WC_Payment_Gateway {
        protected $fee_percentage;
        protected $fixed_fee;
        protected $logo_url;
        protected $logo_width;
        protected $detailed_description;

        public function __construct() {
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();

            $this->title                = $this->get_option('title');
            $this->description          = $this->get_option('description');
            $this->enabled              = $this->get_option('enabled');
            $this->instructions         = $this->get_option('instructions', $this->description);
            $this->fee_percentage       = floatval($this->get_option('fee_percentage', 0));
            $this->fixed_fee            = floatval($this->get_option('fixed_fee', 0));
            $this->logo_url             = esc_url($this->get_option('logo_url', ''));
            $this->logo_width           = intval($this->get_option('logo_width', 150));
            $this->detailed_description = $this->get_option('detailed_description', '');

            add_action("woocommerce_update_options_payment_gateways_{$this->id}", [$this, 'process_admin_options']);
        }

        public function init_form_fields() {
            $this->form_fields = [
                'enabled' => [
                    'title'   => __('Enable/Disable', 'amarire'),
                    'type'    => 'checkbox',
                    'label'   => __('Enable this payment method', 'amarire'),
                    'default' => 'no',
                ],
                'title' => [
                    'title'       => __('Title', 'amarire'),
                    'type'        => 'text',
                    'description' => __('Title displayed during checkout.', 'amarire'),
                    'default'     => $this->method_title,
                ],
                'description' => [
                    'title'       => __('Description', 'amarire'),
                    'type'        => 'textarea',
                    'description' => __('Short description shown on checkout page.', 'amarire'),
                    'default'     => '',
                ],
                'detailed_description' => [
                    'title'       => __('Detailed Description', 'amarire'),
                    'type'        => 'textarea',
                    'description' => __('Detailed info shown on checkout (below logo).', 'amarire'),
                    'default'     => '',
                ],
                'logo_url' => [
                    'title'       => __('Logo Image URL', 'amarire'),
                    'type'        => 'text',
                    'description' => __('Enter the full URL of the logo/image to display on checkout.', 'amarire'),
                    'default'     => '',
                ],
                'logo_width' => [
                    'title'             => __('Logo Width (px)', 'amarire'),
                    'type'              => 'number',
                    'description'       => __('Set the width of the logo image in pixels.', 'amarire'),
                    'default'           => 150,
                    'custom_attributes' => ['min' => '50', 'max' => '500', 'step' => '1'],
                ],
                'fee_percentage' => [
                    'title'             => __('Fee (%)', 'amarire'),
                    'type'              => 'number',
                    'default'           => 0,
                    'custom_attributes' => ['step' => '0.01', 'min' => '0'],
                ],
                'fixed_fee' => [
                    'title'             => __('Fixed Fee', 'amarire'),
                    'type'              => 'number',
                    'default'           => 0,
                    'custom_attributes' => ['step' => '0.01', 'min' => '0'],
                ],
                'instructions' => [
                    'title'       => __('Instructions', 'amarire'),
                    'type'        => 'textarea',
                    'description' => __('Shown after order and in emails.', 'amarire'),
                ],
            ];
        }

        public function payment_fields() {
            if ( $this->logo_url ) {
                echo '<div class="amarire-payment-logo" style="margin-bottom:10px;">';
                echo '<img src="' . esc_url($this->logo_url) . '" alt="' . esc_attr($this->title) . '" style="max-width:' . $this->logo_width . 'px; height:auto;">';
                echo '</div>';
            }
            if ( $this->description ) {
                echo wpautop( wp_kses_post( $this->description ) );
            }
            if ( $this->detailed_description ) {
                echo wpautop( wp_kses_post( $this->detailed_description ) );
            }
        }

        public function add_payment_fee( $cart ) {
            if ( is_admin() && ! defined('DOING_AJAX') ) return;

            $chosen = WC()->session->get('chosen_payment_method');
            if ( $chosen !== $this->id ) return;

            // remove existing amarire payment fees to prevent duplicates
            foreach ( $cart->get_fees() as $key => $fee ) {
                if ( strpos( $fee->name, __('Payment Fee', 'amarire') ) === 0 ) {
                    unset( $cart->fees_api()->fees[ $key ] );
                }
            }

            $base = $cart->get_cart_contents_total() + $cart->get_cart_contents_tax();

            $fee = 0;
            if ( $this->fee_percentage > 0 ) {
                $fee += ( $base * $this->fee_percentage ) / 100;
            }
            if ( $this->fixed_fee > 0 ) {
                $fee += $this->fixed_fee;
            }

            if ( $fee > 0 ) {
                $cart->add_fee( sprintf( __('Payment Fee (%s)', 'amarire'), $this->title ), $fee, true );
                WC()->session->set('amarire_payment_fee', $fee);
            } else {
                WC()->session->set('amarire_payment_fee', 0);
            }
        }

        public function thankyou_page() {
            if ( $this->instructions ) {
                echo wpautop( wptexturize( $this->instructions ) );
            }
        }

        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            $order->update_status('on-hold', sprintf(__('Awaiting %s payment', 'amarire'), $this->method_title));
            wc_reduce_stock_levels( $order_id );
            WC()->cart->empty_cart();

            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            ];
        }
    }
});
