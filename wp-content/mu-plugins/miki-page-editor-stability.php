<?php
/**
 * Plugin Name: Miki Page Editor Stability
 * Description: Keeps the classic page editor layout stable and extends the CFS editing session.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the current request is a classic fixed-page editor.
 */
function miki_is_classic_page_editor_request() {
	if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return false;
	}

	global $pagenow;
	if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
		return false;
	}

	$post_type = isset( $_REQUEST['post_type'] ) ? sanitize_key( wp_unslash( $_REQUEST['post_type'] ) ) : '';
	$post_id   = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0;
	if ( ! $post_id && isset( $_REQUEST['post_ID'] ) ) {
		$post_id = absint( $_REQUEST['post_ID'] );
	}
	if ( $post_id ) {
		$post_type = get_post_type( $post_id );
	}

	return 'page' === $post_type;
}

/**
 * Remove one box ID from every stored metabox context.
 */
function miki_remove_box_from_metabox_order( $order, $box_id ) {
	$order = is_array( $order ) ? $order : array();
	foreach ( $order as $context => $box_ids ) {
		$ids = array_filter( array_map( 'trim', explode( ',', (string) $box_ids ) ) );
		$ids = array_values( array_diff( $ids, array( $box_id ) ) );
		$order[ $context ] = implode( ',', $ids );
	}

	return $order;
}

/**
 * Keep Publish in the top-right column and open on fixed-page edit screens.
 *
 * Dragging a box and immediately pressing Update can cancel WordPress' separate
 * metabox-order AJAX request. Normalizing the saved preference on every page
 * editor load prevents the Publish box from returning to a collapsed left
 * column without disturbing the order of the other boxes.
 */
function miki_stabilize_classic_page_editor_layout() {
	if ( ! miki_is_classic_page_editor_request() || ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( 2 !== (int) get_user_option( 'screen_layout_page', $user_id ) ) {
		update_user_option( $user_id, 'screen_layout_page', 2 );
	}

	$current_order = get_user_option( 'meta-box-order_page', $user_id );
	$updated_order = miki_remove_box_from_metabox_order( $current_order, 'submitdiv' );
	$side_ids      = array_filter( array_map( 'trim', explode( ',', (string) ( $updated_order['side'] ?? '' ) ) ) );
	$updated_order['side'] = implode( ',', array_merge( array( 'submitdiv' ), $side_ids ) );

	if ( $updated_order !== $current_order ) {
		update_user_option( $user_id, 'meta-box-order_page', $updated_order );
	}

	foreach ( array( 'closedpostboxes_page', 'metaboxhidden_page' ) as $option_name ) {
		$current = (array) get_user_option( $option_name, $user_id );
		$updated = array_values( array_diff( $current, array( 'submitdiv' ) ) );
		if ( $updated !== $current ) {
			update_user_option( $user_id, $option_name, $updated );
		}
	}
}
add_action( 'admin_init', 'miki_stabilize_classic_page_editor_layout', 20 );

/**
 * CFS defaults to a four-hour form session. Fixed pages with long interview
 * content are commonly left open while copy is reviewed, so keep forms valid
 * for one day to prevent an otherwise valid update from being discarded.
 */
function miki_extend_cfs_page_editor_session() {
	if ( ! miki_is_classic_page_editor_request() || ! function_exists( 'CFS' ) ) {
		return;
	}

	$cfs = CFS();
	if (
		isset( $cfs->form ) &&
		is_object( $cfs->form ) &&
		isset( $cfs->form->session ) &&
		is_object( $cfs->form->session ) &&
		property_exists( $cfs->form->session, 'expires' )
	) {
		$cfs->form->session->expires = DAY_IN_SECONDS;
	}
}
add_action( 'init', 'miki_extend_cfs_page_editor_session', 101 );
