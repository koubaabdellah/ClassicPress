<?php
/**
 * Action handler for Multisite administration panels.
 *
 * @package ClassicPress
 * @subpackage Multisite
 * @since WP-3.0.0
 */

/** Load ClassicPress Administration Bootstrap */
require_once dirname( __FILE__ ) . '/admin.php';

if ( empty( $_GET['action'] ) ) {
	wp_redirect( network_admin_url() );
	exit;
}

/**
 * Fires just before the action handler in several Network Admin screens.
 *
 * This hook fires on multiple screens in the Multisite Network Admin,
 * including Users, Network Settings, and Site Settings.
 *
 * @since WP-3.0.0
 */
do_action( 'wpmuadminedit' );

/**
 * Fires the requested handler action.
 *
 * The dynamic portion of the hook name, `$_GET['action']`, refers to the name
 * of the requested action.
 *
 * @since WP-3.1.0
 */
do_action( 'network_admin_edit_' . $_GET['action'] );

wp_redirect( network_admin_url() );
exit();
