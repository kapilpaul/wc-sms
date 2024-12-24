<?php
/**
 * Admin Pages Handler
 * Class Menu
 *
 * @package ElitBuzz\Admin
 */
namespace ElitBuzz\Admin;

/**
 * Class Menu
 */
class Menu {
    /**
     * Menu constructor.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register our menu page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_menu() {
	    global $submenu;
	    $parent_slug = 'elitbuzz-sms';
	    $capability = 'manage_options';

	    $hook = add_menu_page( __( 'ElitBuzz SMS', 'elitbuzz-sms' ), __( 'ElitBuzz SMS', 'elitbuzz-sms' ), $capability, $parent_slug, [ $this, 'plugin_page' ], 'dashicons-admin-tools' );

	    if ( current_user_can( $capability ) ) {
//		    $submenu[ $parent_slug ][] = [ __( 'Transactions', 'dc-bkash' ), $capability, $this->get_submenu_url() ]; // phpcs:ignore
//
//		    $submenu[ $parent_slug ][] = [ __( 'Search Transaction', 'dc-bkash' ), $capability, $this->get_submenu_url( 'search-transaction' ) ]; // phpcs:ignore
//
//		    $submenu[ $parent_slug ][] = [ __( 'Refund', 'dc-bkash' ), $capability, $this->get_submenu_url( 'refund' ) ]; // phpcs:ignore

		    $submenu[ $parent_slug ][] = [ __( 'Settings', 'elitbuzz-sms' ), $capability, $this->get_submenu_url( 'settings' ) ]; // phpcs:ignore
	    }

	    add_action( 'load-' . $hook, [ $this, 'init_hooks' ] );
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Load scripts and styles for the app
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
	    wp_enqueue_script( 'elitbuzz-app-script' );
	    wp_localize_script( 'elitbuzz-app-script', 'elitbuzzSMS', elitbuzz_sms()->assets->get_admin_localized_scripts() );
	    wp_enqueue_style( 'elitbuzz-sms-admin-app' );
    }

    /**
     * Handles the main page
     *
     * @since 1.0.0
     *
     * @return void
     */
	public function plugin_page() {
		echo '<div id="dc-elitbuzz-sms-app"></div>';
	}

	/**
	 * Make submenu admin url from slug
	 *
	 * @param string $slug Slug for menu.
	 * @param string $parent_slug Parent slug.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_submenu_url( $slug = '', $parent_slug = 'elitbuzz-sms' ) {
		return 'admin.php?page=' . $parent_slug . '#/' . $slug;
	}
}
