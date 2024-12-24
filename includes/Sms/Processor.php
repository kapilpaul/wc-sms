<?php
/**
 * Class Processor.
 *
 * @since 1.0.0
 *
 * @package ElitBuzz\Sms
 *
 * @author Kapil Paul
 */
namespace ElitBuzz\Sms;

use ElitBuzz\Sms\Provider\ElitBuzz;

/**
 * Class Processor.
 */
class Processor {

	/**
	 * Send SMS.
	 *
	 * @param string $to      Recipient number.
	 * @param string $content SMS content.
	 *
	 * @since 1.0.0
	 *
	 * @return void|\WP_Error
	 */
	public function send_sms( $to, $content, \WC_Order $order ) {
		$elitbuzz = new ElitBuzz();
		$response = $elitbuzz->send_sms( $content, $to );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'elitbuzz_send_sms_error', $response->get_error_message(), $response );
		}

		// ElitBuzz SMS ID.
		$ebs_sms_id = ebs_extract_sms_id_from_string( $response );

		// Keep note in order.
		$order->add_order_note(
			sprintf(
				/* translators: %s: SMS ID */
				__( 'SMS - Order creation sms have been sent. SMS ID: %s', 'elitbuzz-sms' ),
				$ebs_sms_id
			)
		);

		$order->save();

		// Insert in database.
		elitbuzz_sms_insert( [
			'customer_number' => $to,
			'customer_id'     => $order->get_customer_id(),
			'order_id'        => $order->get_order_number(),
			'message_content' => $content,
			'message_id'      => $ebs_sms_id,
			'delivery_status' => 0,
		] );
	}
}
