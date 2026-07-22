<?php
/**
 * Plugin Name: Miki Admin Network Compatibility
 * Description: Routes migration loopbacks locally and sends external WordPress HTTP requests through the migration proxy.
 * Version: 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_PROXY_HOST' ) ) {
	define( 'WP_PROXY_HOST', '157.205.35.238' );
}
if ( ! defined( 'WP_PROXY_PORT' ) ) {
	define( 'WP_PROXY_PORT', 80 );
}
if ( ! defined( 'WP_PROXY_BYPASS_HOSTS' ) ) {
	define( 'WP_PROXY_BYPASS_HOSTS', 'localhost,127.0.0.1,mikishokuhin.co.jp,*.mikishokuhin.co.jp' );
}

/**
 * Menu markup from the current plugin set makes command-palette construction
 * exceed the gateway timeout, including on WordPress 7. The palette is not
 * required for the affected administration screens.
 */
add_action(
	'admin_init',
	static function () {
		remove_action( 'admin_enqueue_scripts', 'wp_enqueue_command_palette_assets' );
	},
	PHP_INT_MIN
);

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
	return ( defined( 'WP_ADMIN' ) && WP_ADMIN ) || miki_is_site_health_api_request();
}

/**
 * Keep the plugin-bootstrap workaround restricted to screens where it is needed.
 */
function miki_is_remote_admin_plugin_recovery_request() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '';
	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path   = (string) parse_url( $uri, PHP_URL_PATH );

	if ( miki_is_site_health_api_request() ) {
		return true;
	}

	return 'GET' === $method && in_array(
		$path,
		array( '/cms/wp-admin/', '/cms/wp-admin/index.php', '/cms/wp-admin/plugins.php', '/cms/wp-admin/site-health.php' ),
		true
	);
}

/**
 * Determine whether an authenticated outbound proxy has been configured.
 */
function miki_has_outbound_http_proxy() {
	return defined( 'WP_PROXY_HOST' ) && WP_PROXY_HOST && defined( 'WP_PROXY_PORT' ) && WP_PROXY_PORT;
}

/**
 * Return the hosts WordPress uses for this migration site.
 */
function miki_local_http_hosts() {
	$hosts = array();

	foreach ( array( home_url( '/' ), site_url( '/' ) ) as $url ) {
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( is_string( $host ) && '' !== $host ) {
			$hosts[] = strtolower( rtrim( $host, '.' ) );
		}
	}

	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		$host = wp_parse_url( '//' . (string) $_SERVER['HTTP_HOST'], PHP_URL_HOST );
		if ( is_string( $host ) && '' !== $host ) {
			$hosts[] = strtolower( rtrim( $host, '.' ) );
		}
	}

	return array_values( array_unique( $hosts ) );
}

/**
 * Check whether an HTTP request points back to this WordPress installation.
 */
function miki_is_local_http_url( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		return false;
	}

	return in_array( strtolower( rtrim( $host, '.' ) ), miki_local_http_hosts(), true );
}

/**
 * The public DNS entry points at the live server. Pin self-requests to this
 * migration host so REST, loopbacks, and WP-Cron cannot cross environments.
 */
add_action(
	'http_api_curl',
	static function ( $handle, $parsed_args, $url ) {
		if ( ! miki_is_local_http_url( $url ) || ! defined( 'CURLOPT_RESOLVE' ) ) {
			return;
		}

		$host   = wp_parse_url( $url, PHP_URL_HOST );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		$port   = wp_parse_url( $url, PHP_URL_PORT );
		if ( ! $port ) {
			$port = 'https' === strtolower( (string) $scheme ) ? 443 : 80;
		}

		curl_setopt( $handle, CURLOPT_RESOLVE, array( $host . ':' . $port . ':127.0.0.1' ) );
	},
	PHP_INT_MIN,
	3
);

/**
 * These plugins load remote admin data during bootstrap or status output.
 * Their public-side behavior and dedicated admin pages remain unchanged.
 */
function miki_filter_remote_admin_plugins( $plugins ) {
	if ( miki_has_outbound_http_proxy() || ! miki_is_remote_admin_plugin_recovery_request() || ! is_array( $plugins ) ) {
		return $plugins;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path = (string) parse_url( $uri, PHP_URL_PATH );
	if ( '/cms/wp-admin/plugins.php' === $path ) {
		return array();
	}

	$remote_admin_plugins = array(
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
		'google-analytics-for-wordpress/googleanalytics.php',
		'optinmonster/optin-monster-wp-api.php',
		'userfeedback-lite/userfeedback.php',
		'wpforms-lite/wpforms.php',
	);

	return array_values( array_diff( $plugins, $remote_admin_plugins ) );
}
add_filter( 'option_active_plugins', 'miki_filter_remote_admin_plugins', PHP_INT_MIN );

/**
 * Restore the unfiltered option before admin tables inspect active state.
 */
add_action(
	'plugins_loaded',
	static function () {
		remove_filter( 'option_active_plugins', 'miki_filter_remote_admin_plugins', PHP_INT_MIN );
	},
	PHP_INT_MAX
);

/**
 * Do not let any migration request wait on the broken external network path.
 * Self-requests remain available for REST, loopbacks, and WP-Cron.
 */
add_filter(
	'pre_http_request',
	static function ( $preempt, $parsed_args, $url ) {
		if (
			false !== $preempt ||
			miki_has_outbound_http_proxy() ||
			miki_is_local_http_url( $url )
		) {
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
		if ( miki_has_outbound_http_proxy() || ! miki_is_admin_network_recovery_request() ) {
			return;
		}

		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );
	},
	0
);
