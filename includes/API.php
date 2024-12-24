<?php
/**
 * API Class
 *
 * @package ElitBuzz\API
 */

namespace ElitBuzz;

use DC\Pathao\API\MerchantData;
use ElitBuzz\API\Settings;

/**
 * API Class
 */
class API {

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
	    $this->classes = [
		    Settings::class,
	    ];

        add_action( 'rest_api_init', [ $this, 'register_api' ] );
    }

    /**
     * Register the API.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_api() {
	    foreach ( $this->classes as $class ) {
		    $object = new $class();
		    $object->register_routes();
	    }
    }
}