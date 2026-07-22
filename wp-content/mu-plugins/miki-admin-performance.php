<?php
/**
 * Plugin Name: Miki Admin Performance Compatibility
 * Description: Prevents the WordPress command palette from blocking administration screens with the current plugin set.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'admin_init',
	static function () {
		remove_action( 'admin_enqueue_scripts', 'wp_enqueue_command_palette_assets' );
	},
	PHP_INT_MIN
);
