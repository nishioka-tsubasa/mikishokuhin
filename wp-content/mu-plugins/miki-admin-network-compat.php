<?php
/**
 * Plugin Name: Miki Admin Network Compatibility
 * Description: Keeps the migration dashboard usable while the server cannot complete outbound HTTP requests.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Limit the workaround to the main dashboard GET request.
 */
function miki_is_admin_dashboard_request() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '';
	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path   = parse_url( $uri, PHP_URL_PATH );

	return 'GET' === $method && in_array( $path, array( '/cms/wp-admin/', '/cms/wp-admin/index.php' ), true );
}

/**
 * These plugins load remote dashboard data during bootstrap or widget output.
 * Their public-side behavior and dedicated admin pages remain unchanged.
 */
add_filter(
	'option_active_plugins',
	static function ( $plugins ) {
		if ( ! miki_is_admin_dashboard_request() || ! is_array( $plugins ) ) {
			return $plugins;
		}

		$dashboard_remote_plugins = array(
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'google-analytics-for-wordpress/googleanalytics.php',
			'optinmonster/optin-monster-wp-api.php',
			'userfeedback-lite/userfeedback.php',
			'wpforms-lite/wpforms.php',
		);

		return array_values( array_diff( $plugins, $dashboard_remote_plugins ) );
	},
	PHP_INT_MIN
);

/**
 * Do not let dashboard-only remote status checks wait on the broken resolver.
 */
add_filter(
	'pre_http_request',
	static function ( $preempt ) {
		if ( false !== $preempt || ! miki_is_admin_dashboard_request() ) {
			return $preempt;
		}

		return new WP_Error(
			'miki_admin_dashboard_http_unavailable',
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
		if ( ! miki_is_admin_dashboard_request() ) {
			return;
		}

		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );
	},
	0
);
