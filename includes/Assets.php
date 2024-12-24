<?php
/**
 * Scripts and Styles Class.
 *
 * @package ElitBuzz\Assets
 */

namespace ElitBuzz;

/**
 * Scripts and Styles Class
 */
class Assets {

    /**
     * Assets constructor.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
	    if ( is_admin() ) {
		    add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
	    } else {
		    add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
	    }
    }

    /**
     * Register our app scripts and styles
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param array $scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : ELITBUZZ_SMS_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param array $styles
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, ELITBUZZ_SMS_VERSION );
        }
    }

    /**
     * Get all registered scripts
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_scripts() {
	    $page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL );

	    if ( $page !== 'elitbuzz-sms' ) {
		    return [];
	    }

	    $plugin_js_assets_path = ELITBUZZ_SMS_ASSETS . '/js/';

	    $dependencies = [
		    'wp-api-fetch',
	    ];

	    // for local development
	    // when webpack "hot module replacement" is enabled, this
	    // constant needs to be turned "true" on "wp-config.php".
	    if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
		    $plugin_js_assets_path = 'http://localhost:8080/';
	    }

	    $scripts = [
		    'elitbuzz-sms' => [
			    'src'       => $plugin_js_assets_path . 'main.js',
			    'version'   => filemtime( ELITBUZZ_SMS_PATH . '/assets/js/main.js' ),
			    'deps'      => [],
			    'in_footer' => true,
		    ],
		    'sweetalert'        => [
			    'src'       => '//cdn.jsdelivr.net/npm/sweetalert2@10',
			    'deps'      => [ 'jquery' ],
			    'in_footer' => true,
		    ],
		    'elitbuzz-app-runtime'    => [
			    'src'       => $plugin_js_assets_path . 'runtime.js',
			    'version'   => filemtime( ELITBUZZ_SMS_PATH . '/assets/js/runtime.js' ),
			    'deps'      => $dependencies,
			    'in_footer' => true,
		    ],
		    'elitbuzz-app-vendor'     => [
			    'src'       => $plugin_js_assets_path . 'vendors.js',
			    'version'   => filemtime( ELITBUZZ_SMS_PATH . '/assets/js/vendors.js' ),
			    'deps'      => [ 'elitbuzz-app-runtime' ],
			    'in_footer' => true,
		    ],
		    'elitbuzz-app-script'     => [
			    'src'       => $plugin_js_assets_path . 'admin-app.js',
			    'version'   => filemtime( ELITBUZZ_SMS_PATH . '/assets/js/admin-app.js' ),
			    'deps'      => [ 'elitbuzz-app-vendor' ],
			    'in_footer' => true,
		    ],
	    ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_styles() {
	    $page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL );

	    if ( $page !== 'elitbuzz-sms' ) {
		    return [];
	    }

        $plugin_css_assets_path = ELITBUZZ_SMS_ASSETS . '/css/';

        $styles = [
            'elitbuzz-sms' => [
                'src'       => $plugin_css_assets_path . 'main.css',
                'deps'      => [],
                'version'   => filemtime( ELITBUZZ_SMS_PATH . '/assets/css/main.css' ),
            ],
        ];

	    // for local development
	    // when webpack "hot module replacement" is enabled, this
	    // constant needs to be turned "true" on "wp-config.php".
	    if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
		    $plugin_css_assets_path = 'http://localhost:8080/';
	    }

	    $styles['elitbuzz-sms-admin-app'] = [
		    'src'       => $plugin_css_assets_path . 'admin-app.css',
		    'deps'      => [],
		    'version'   => filemtime( SHIP_WITH_PATHAO_PATH . '/assets/css/admin-app.css' ),
	    ];

        return $styles;
    }

	/**
	 * Admin localized scripts
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_admin_localized_scripts() {
		$localize_data = [
			'ajaxurl'                => admin_url( 'admin-ajax.php' ),
			'nonce'                  => wp_create_nonce( 'elitbuzz_sms_admin' ),
			'rest'                   => [
				'root'    => esc_url_raw( get_rest_url() ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'version' => 'elitbuzz-sms/v1',
			],
			'api'                    => null,
			'libs'                   => [],
			'current_time'           => current_time( 'mysql' ),
			'text_domain'            => 'elitbuzz-sms',
			'asset_url'              => ELITBUZZ_SMS_ASSETS,
		];

		return apply_filters( 'elitbuzz_sms_admin_localize_script', $localize_data );
	}
}
