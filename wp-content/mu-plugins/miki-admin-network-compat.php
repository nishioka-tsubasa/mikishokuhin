<?php
/**
 * Plugin Name: Miki Admin Network Compatibility
 * Description: Keeps migration admin status screens usable while the server cannot complete outbound HTTP requests.
 * Version: 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the current request is a Site Health REST or AJAX test.
 */
function miki_is_site_health_api_request() {
	$uri        = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path       = (string) parse_url( $uri, PHP_URL_PATH );
	$rest_route = isset( $_GET['rest_route'] ) ? (string) $_GET['rest_route'] : '';
	$action     = isset( $_REQUEST['action'] ) ? (string) $_REQUEST['action'] : '';

	if ( false !== strpos( $path, '/wp-json/wp-site-health/' ) ) {
		return true;
	}

	if ( 0 === strpos( $rest_route, '/wp-site-health/' ) ) {
		return true;
	}

	return false !== strpos( $path, '/wp-admin/admin-ajax.php' ) && 0 === strpos( $action, 'health-check-' );
}

/**
 * Limit the workaround to the main dashboard and Site Health requests.
 */
function miki_is_admin_network_recovery_request() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '';
	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path   = (string) parse_url( $uri, PHP_URL_PATH );

	if ( miki_is_site_health_api_request() ) {
		return true;
	}

	return 'GET' === $method && in_array(
		$path,
		array( '/cms/wp-admin/', '/cms/wp-admin/index.php', '/cms/wp-admin/site-health.php' ),
		true
	);
}

/**
 * These plugins load remote admin data during bootstrap or status output.
 * Their public-side behavior and dedicated admin pages remain unchanged.
 */
add_filter(
	'option_active_plugins',
	static function ( $plugins ) {
		if ( ! miki_is_admin_network_recovery_request() || ! is_array( $plugins ) ) {
			return $plugins;
		}

		$remote_admin_plugins = array(
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'google-analytics-for-wordpress/googleanalytics.php',
			'optinmonster/optin-monster-wp-api.php',
			'userfeedback-lite/userfeedback.php',
			'wpforms-lite/wpforms.php',
		);

		return array_values( array_diff( $plugins, $remote_admin_plugins ) );
	},
	PHP_INT_MIN
);

/**
 * Do not let status-screen remote checks wait on the broken network path.
 */
add_filter(
	'pre_http_request',
	static function ( $preempt ) {
		if ( false !== $preempt || ! miki_is_admin_network_recovery_request() ) {
			return $preempt;
		}

		return new WP_Error(
			'miki_admin_http_unavailable',
			'Outbound HTTP is temporarily unavailable in the migration environment.'
		);
	},
	PHP_INT_MIN,
	3
);

/**
 * Leave update transients untouched on the dashboard. Update screens remain
 * available once outbound DNS/HTTP is restored at the server level.
 */
add_action(
	'admin_init',
	static function () {
		if ( ! miki_is_admin_network_recovery_request() ) {
			return;
		}

		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );
	},
	0
);
