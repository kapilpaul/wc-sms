<?php
/**
 * Class ElitBuzz.
 *
 * @since 1.0.0
 *
 * @package ElitBuzz\Sms\Provider
 *
 * @author Kapil Paul
 */
namespace ElitBuzz\Sms\Provider;

/**
 * Class ElitBuzz.
 */
class ElitBuzz {
	public $base_url = 'https://msg.elitbuzz-bd.com/smsapi';

	/**
	 * Send SMS.
	 *
	 * @param string $content The SMS content.
	 * @param string $to      The recipient phone number.
	 *
	 * @since 1.0.0
	 *
	 * @return string|array|\WP_Error The decoded response body if successful, WP_Error on failure.
	 */
	public function send_sms( $content, $to ) {
		$api_key   = ebs_get_settings_option( 'api_key' );
		$sender_id = ebs_get_settings_option( 'sender_id' );

		if ( ! $api_key || ! $sender_id || empty( $to ) || empty( $content ) ) {
			return new \WP_Error( 'elitbuzz_send_sms_error', __( 'Credentials or data missing!', 'elitbuzz-sms' ) );
		}

		$data = [
			"api_key"  => $api_key,
			"type"     => "text",
			"contacts" => $to,
			"senderid" => $sender_id,
			"msg"      => $content,
		];

		$response = $this->make_request( $this->base_url, $data );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'elitbuzz_send_sms_error', $response->get_error_message(), $response );
		}

		return $response;
	}

	/**
	 * Make an HTTP request to a specified URL.
	 *
	 * Sends a POST request to the specified URL with the provided data and headers.
	 * If headers are not provided, it uses the authorization header.
	 *
	 * @param string $url     The URL to make the request to.
	 * @param array  $data    The data to include in the request.
	 * @param array  $headers Optional. Additional headers for the request. Defaults to an empty array.
	 *
	 * @since 1.0.0
	 *
	 * @return string|array|\WP_Error The decoded response body if successful, WP_Error on failure.
	 */
	public function make_request( $url, $data, $headers = [] ) {
		$args = [
			'timeout'     => '30',
			'redirection' => '30',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'cookies'     => [],
		];

		if ( $data ) {
			$args['body'] = $data;
		}

		$response = wp_remote_post( esc_url( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return wp_remote_retrieve_body( $response );
	}
}