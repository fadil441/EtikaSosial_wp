<?php
/**
 * Autentikasi dan pembatasan akses pengguna.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blokir panel admin bagi pengguna yang hanya boleh membaca.
 */
function es_redirect_readers_from_admin() {
	if (
		! is_admin()
		|| wp_doing_ajax()
		|| ( defined( 'DOING_CRON' ) && DOING_CRON )
		|| ! is_user_logged_in()
		|| current_user_can( 'edit_posts' )
	) {
		return;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}
add_action( 'init', 'es_redirect_readers_from_admin', 1 );

/**
 * Sembunyikan admin bar dari pembaca.
 *
 * @param bool $show Apakah admin bar ditampilkan.
 * @return bool
 */
function es_hide_admin_bar_for_readers( $show ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return $show;
}
add_filter( 'show_admin_bar', 'es_hide_admin_bar_for_readers' );

/**
 * Jangan indeks halaman masuk.
 *
 * @param array $robots Direktif robots.
 * @return array
 */
function es_noindex_login_page( $robots ) {
	if ( is_page( 'masuk' ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'es_noindex_login_page' );
