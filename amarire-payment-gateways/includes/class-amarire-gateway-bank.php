<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// this file registers 30 gateway classes dynamically (keeps same behavior)
add_action('plugins_loaded', function() {
    if ( ! class_exists('WC_Payment_Gateway') ) return;

    $gateways = [];
    for ( $i = 1; $i <= 30; $i++ ) {
        $class_name = "Amarire_Gateway_Bank{$i}";
        // create class dynamically using eval to preserve same approach
        if ( ! class_exists($class_name) ) {
            eval("class {$class_name} extends Amarire_Abstract_Gateway {
                public function __construct() {
                    \$this->id = 'amarire_gateway_bank{$i}';
                    \$this->method_title = __('Bank {$i}', 'amarire');
                    parent::__construct();
                }
            }");
        }
        $gateways[] = $class_name;
    }

    add_filter('woocommerce_payment_gateways', function($methods) use ($gateways) {
        return array_merge($methods, $gateways);
    });
});
