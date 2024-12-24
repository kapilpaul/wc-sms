<?php
/**
 * All our plugins custom functions.
 *
 * @since 1.0.0
 */

/**
 * Get template part implementation.
 *
 * Looks at the theme directory first.
 *
 * @param string $slug Slug of template.
 * @param string $name Name of template.
 * @param array  $args Arguments to passed.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elitbuzz_sms_get_template_part( $slug, $name = '', $args = [] ) {
  $defaults = [ 'pro' => false ];

  $args = wp_parse_args( $args, $defaults );

  if ( $args && is_array( $args ) ) {
    extract( $args ); //phpcs:ignore
  }

  $template = '';

  // Look in yourtheme/elitbuzz-sms/slug-name.php and yourtheme/elitbuzz-sms/slug.php.
  $template = locate_template(
    [
      ELITBUZZ_SMS_TEMPLATE_PATH . "{$slug}-{$name}.php",
      ELITBUZZ_SMS_TEMPLATE_PATH . "{$slug}.php",
    ]
  );

  /**
  * Change template directory path filter.
  *
  * @since 1.0.0
  */
  $template_path = apply_filters( 'elitbuzz_sms_set_template_path', ELITBUZZ_SMS_TEMPLATE_PATH, $template, $args );

  // Get default slug-name.php.
  if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
    $template = $template_path . "/{$slug}-{$name}.php";
  }

  if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
    $template = $template_path . "/{$slug}.php";
  }

  // Allow 3rd party plugin filter template file from their plugin.
  $template = apply_filters( 'elitbuzz_sms_get_template_part', $template, $slug, $name );

  if ( $template ) {
    include $template;
  }
}

/**
* Get other templates (e.g. product attributes) passing attributes and including the file.
*
* @param mixed  $template_name Template Name.
* @param array  $args          (default: array()) arguments.
* @param string $template_path (default: '').
* @param string $default_path  (default: '').
*
* @since 1.0.0
*
* @return void
*/
function elitbuzz_sms_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
  if ( $args && is_array( $args ) ) {
    extract( $args ); //phpcs:ignore
  }

  $extension = elitbuzz_sms_get_extension( $template_name ) ? '' : '.php';

  $located = elitbuzz_sms_locate_template( $template_name . $extension, $template_path, $default_path );

  if ( ! file_exists( $located ) ) {
    _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '2.1' );

    return;
  }

  do_action( 'elitbuzz_sms_before_template_part', $template_name, $template_path, $located, $args );

  include $located;

  do_action( 'elitbuzz_sms_after_template_part', $template_name, $template_path, $located, $args );
}

/**
* Locate a template and return the path for inclusion.
*
* This is the load order:
*
*      yourtheme       /   $template_path  /   $template_name
*      yourtheme       /   $template_name
*      $default_path   /   $template_name
*
* @param mixed  $template_name Template name.
* @param string $template_path (default: '').
* @param string $default_path  (default: '').
*
* @since 1.0.0
*
* @return string
*/
function elitbuzz_sms_locate_template( $template_name, $template_path = '', $default_path = '' ) {
  if ( ! $template_path ) {
    $template_path = ELITBUZZ_SMS_TEMPLATE_PATH;
  }

  if ( ! $default_path ) {
    $default_path = ELITBUZZ_SMS_TEMPLATE_PATH;
  }

  // Look within passed path within the theme - this is priority.
  $template = locate_template(
    [
      trailingslashit( $template_path ) . $template_name,
    ]
  );

  // Get default template.
  if ( ! $template ) {
    $template = $default_path . $template_name;
  }

  // Return what we found.
  return apply_filters( 'elitbuzz_sms_locate_template', $template, $template_name, $template_path );
}

/**
* Get filename extension.
*
* @param string $file_name File name.
*
* @since 1.0.0
*
* @return false|string
*/
function elitbuzz_sms_get_extension( $file_name ) {
  $n = strrpos( $file_name, '.' );

  return ( false === $n ) ? '' : substr( $file_name, $n + 1 );
}


/**
 * Insert a new
 *
 * @param  array  $args
 *
 * @since 1.0.0
 *
 * @return int|WP_Error
 */
function elitbuzz_sms_insert( $args = [] ) {
    global $wpdb;

    $defaults = [
	    'sender_id'       => ebs_get_settings_option( 'sender_id' ),
	    'customer_number' => '',
	    'customer_id'     => '',
	    'order_id'        => '',
	    'message_content' => '',
	    'message_id'      => '',
	    'delivery_status' => '',
    ];

	$table_name = $wpdb->prefix . 'elitbuzz_sms_messages';

    $data = wp_parse_args( $args, $defaults );

    if ( isset( $data['id'] ) ) {
        $id = $data['id'];
        unset( $data['id'] );

        $updated = $wpdb->update(
            $table_name,
            $data,
            [ 'id' => $id ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ],
            [ '%d' ]
        );

        elitbuzz_sms__purge_cache( $id );

        return $updated;

    } else {
	    $inserted = $wpdb->insert( $table_name, $data, [
		    '%s',
		    '%s',
		    '%s',
		    '%s',
		    '%s',
		    '%s',
		    '%s',
	    ] );

        if ( ! $inserted ) {
            return new \WP_Error( 'failed-to-insert', __( 'Failed to insert data', 'elitbuzz-sms' ) );
        }

        elitbuzz_sms__purge_cache();

        return $wpdb->insert_id;
    }
}

/**
 * Fetch all messages.
 *
 * @param  array  $args
 *
 * @since 1.0.0
 *
 * @return array
 */
function elitbuzz_sms_get_messages( $args = [] ) {
    global $wpdb;

    $defaults = [
        'number'  => 20,
        'offset'  => 0,
        'orderby' => 'id',
        'order'   => 'ASC'
    ];

    $args = wp_parse_args( $args, $defaults );

    $last_changed = wp_cache_get_last_changed( '' );
    $key          = md5( serialize( array_diff_assoc( $args, $defaults ) ) );
    $cache_key    = "all:$key:$last_changed";

    $sql = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}elitbuzz_sms_messages
            ORDER BY {$args['orderby']} {$args['order']}
            LIMIT %d, %d",
            $args['offset'], $args['number']
    );

    $items = wp_cache_get( $cache_key, 'elitbuzz_sms_messages' );

    if ( false === $items ) {
        $items = $wpdb->get_results( $sql );

        wp_cache_set( $cache_key, $items, 'elitbuzz_sms_messages' );
    }

    return $items;
}

/**
 * Get the count of total
 *
 * @since 1.0.0
 *
 * @return int
 */
function elitbuzz_sms__count() {
    global $wpdb;

    $count = wp_cache_get( 'count', 'elitbuzz_sms_messages' );

    if ( false === $count ) {
        $count = (int) $wpdb->get_var( "SELECT count(id) FROM {$wpdb->prefix}elitbuzz_sms_messages" );

        wp_cache_set( 'count', $count, 'elitbuzz_sms_messages' );
    }

    return $count;
}

/**
 * Fetch a single  from the DB
 *
 * @param  int $id
 *
 * @since 1.0.0
 *
 * @return object
 */
function elitbuzz_sms_get_message( $id ) {
    global $wpdb;

    $item = wp_cache_get( '-item-' . $id, 'elitbuzz_sms_messages' );

    if ( false === $item ) {
        $item = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}elitbuzz_sms_messages WHERE id = %d", $id )
        );

        wp_cache_set( '-item-' . $id, $item, 'elitbuzz_sms_messages' );
    }

    return $item;
}

/**
 * Delete an
 *
 * @param  int $id
 *
 * @since 1.0.0
 *
 * @return int|boolean
 */
function elitbuzz_sms_delete_( $id ) {
    global $wpdb;

    elitbuzz_sms__purge_cache( $id );

    return $wpdb->delete(
        $wpdb->prefix . 'elitbuzz_sms_messages',
        [ 'id' => $id ],
        [ '%d' ]
    );
}

/**
 * Purge the cache for elitbuzz_sms_messages items
 *
 * @param  int $item_id
 *
 * @since 1.0.0
 *
 * @return void
 */
function elitbuzz_sms__purge_cache( $item_id = null ) {
    $group = 'elitbuzz_sms_messages';

    if ( $item_id ) {
        wp_cache_delete( '-item-' . $item_id, $group );
    }

    wp_cache_delete( 'count', $group );
    wp_cache_set( 'last_changed', microtime(), $group );
}

/**
 * Get settings option.
 *
 * @param  string $key
 *
 * @since 1.0.0
 */
function ebs_get_settings_option( $key = null ) {
	$settings = get_option( 'dc_elitbuzz_sms_settings' );

	if ( ! $key ) {
		return $settings;
	}

	return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
}

/**
 * Extract ID from a string.
 *
 * @param  string $input_string
 *
 * @since 1.0.0
 *
 * @return string|null
 */
function ebs_extract_sms_id_from_string( $input_string ) {
	// Use regex to find the ID after "ID - "
	if ( preg_match( '/ID - ([a-zA-Z0-9-]+)/', $input_string, $matches ) ) {
		return $matches[1]; // Return the captured ID
	}

	// Return null if no match is found
	return null;
}
