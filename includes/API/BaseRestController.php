<?php
/**
 * Class BaseRestController
 *
 * @since 1.0.0
 *
 * @package ElitBuzz\API
 *
 * @author Kapil Paul
 */
namespace ElitBuzz\API;

use WP_Http;
use WP_REST_Controller;

/**
 * Class BaseRestController
 */
class BaseRestController extends WP_REST_Controller {

	/**
	 * Namespace.
	 *
	 * @var string Namespace.
	 */
	public $namespace = 'elitbuzz-sms';

	/**
	 * Version.
	 *
	 * @var string version.
	 */
	public $version = 'v1';

	/**
	 * Permission check
	 *
	 * @param \WP_REST_Request $request WP Rest Request.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Error|bool
	 */
	public function admin_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'elitbuzz_sms_permission_error',
				__( 'You have no permission to do that', 'sw-pathao' ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		return true;
	}

	/**
	 * Get full namespace.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace . '/' . $this->version;
	}
}
