<?php
/**
 * WooCommerce Class
 *
 * @package ElitBuzz
 */
namespace ElitBuzz;

/**
 * WooCommerce Class
 */
class WooCommerce {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_new_order', [ $this, 'send_sms_on_create_order' ], 99, 2 );
	}

	/**
	 * Send SMS on create order
	 *
	 * @param int     $order_id Order ID.
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool
	 */
	public function send_sms_on_create_order( $order_id, \WC_Order $order ) {
		if ( ! $order_id ) {
			return;
		}

		$message = ebs_get_settings_option( 'create_order_message' );

		if ( empty( $message ) ) {
			return;
		}

		$message = str_replace( '%order_number%', $order->get_order_number(), $message );
		$message = str_replace( '%name%', $order->get_billing_first_name(), $message );

		elitbuzz_sms()->sms_processor->send_sms( $order->get_billing_phone() ,$message, $order );

		return true;
	}
}
