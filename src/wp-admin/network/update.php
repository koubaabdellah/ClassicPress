<?php
/**
 * Update/Install Plugin/Theme network administration panel.
 *
 * @package ClassicPress
 * @subpackage Multisite
 * @since WP-3.1.0
 */

if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ) ) ) {
	define( 'IFRAME_REQUEST', true );
}

/** Load ClassicPress Administration Bootstrap */
require_once dirname( __FILE__ ) . '/admin.php';

require ABSPATH . 'wp-admin/update.php';
