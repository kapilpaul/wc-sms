<?php
/**
 * Class Settings API.
 *
 * @since 1.0.0
 *
 * @package DC\Pathao\API
 *
 * @author Kapil Paul
 */
namespace ElitBuzz\API;

use WP_REST_Server;

/**
 * Settings option key.
 */
const ELITBUZZ_SMS_SETTINGS_OPTION_KEY = 'dc_elitbuzz_sms_settings';

class Settings extends BaseRestController {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->rest_base = 'settings';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->get_namespace(),
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_settings' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_settings' ],
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}

	/**
	 * Get settings data.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_settings( $request ) {
		$settings       = $this->setting_fields();
		$settings_value = get_option( ELITBUZZ_SMS_SETTINGS_OPTION_KEY );

		foreach ( $settings as $key => $field ) {
			$settings[ $key ]['value'] = isset( $settings_value[ $key ] ) ? $settings_value[ $key ] : '';
		}

		return rest_ensure_response( $settings );
	}

	/**
	 * Update items.
	 *
	 * @param object $request Request Object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update_settings( $request ) {
		$fields = $request->get_param( 'data' );

		$settings_value = [];

		foreach ( $fields as $key => $field ) {
			$settings_value[ $key ] = sanitize_text_field( $field['value'] );
		}

		update_option( ELITBUZZ_SMS_SETTINGS_OPTION_KEY, $settings_value );

		return rest_ensure_response( $fields );
	}

	/**
	 * Get settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function setting_fields() {
		$fields = [
			'api_key'              => [
				'title' => __( 'API KEY', 'elitbuzz-sms' ),
				'type'  => 'text',
				'value' => '',
			],
			'sender_id'            => [
				'title' => __( 'SENDER ID', 'elitbuzz-sms' ),
				'type'  => 'text',
				'value' => '',
			],
			'create_order_message' => [
				'title'       => __( 'Message for create order', 'elitbuzz-sms' ),
				'type'        => 'text',
				'value'       => '',
				'description' => 'Use %name%, %order_number% where you want to put the name and order number.',
			],
		];

		return apply_filters( 'elitbuzz_sms_settings_fields', $fields );
	}
}