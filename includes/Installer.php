<?php
/**
 * The Installer class.
 * Install all dependency from here while activating the plugin.
 *
 * @package ElitBuzz\Installer
 */

namespace ElitBuzz;

/**
 * Class Installer
 * @package ElitBuzz
 */
class Installer {

    /**
     * Run the installer.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_version() {
        $installed = get_option( 'elitbuzz_sms_installed' );

        if ( ! $installed ) {
            update_option( 'elitbuzz_sms_installed', time() );
        }

        update_option( 'elitbuzz_sms_version', ELITBUZZ_SMS_VERSION );
    }

    /**
     * Create necessary database tables.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create_tables() {
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $this->create_elitbuzz_sms_messages_table();
    }

    /**
     * Create elitbuzz_sms_messages table
     *
     * @return void
     */
    public function create_elitbuzz_sms_messages_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . 'elitbuzz_sms_messages';

        $schema = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `sender_id` VARCHAR(15) DEFAULT NULL,
                      `customer_number` VARCHAR(15) DEFAULT NULL,
                      `customer_id` VARCHAR(11) DEFAULT NULL,
                      `order_id` VARCHAR(11) DEFAULT NULL,
                      `message_content` VARCHAR(256) DEFAULT NULL,
                      `message_id` VARCHAR(256) DEFAULT NULL,
                      `delivery_status` VARCHAR(11) DEFAULT NULL,
                      `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`)
                    ) $charset_collate";

        dbDelta( $schema );
    }
}
