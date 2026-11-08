<?php
/**
 * Setup tema, aset, dan penyesuaian WordPress.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Daftarkan dukungan tema.
 */
function es_setup() {
	load_theme_textdomain( 'etikasosial', ES_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'es-card', 900, 600, true );
	add_image_size( 'es-wide', 1400, 900, true );

	register_nav_menus(
		array(
			'primary' => 'Navigasi Utama',
			'footer'  => 'Navigasi Footer',
		)
	);
}
add_action( 'after_setup_theme', 'es_setup' );

/**
 * Muat aset front-end.
 */
function es_enqueue_assets() {
	wp_enqueue_style(
		'es-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'es-site', ES_URI . '/assets/css/site.css', array(), ES_VERSION );
	wp_enqueue_style( 'es-wp', ES_URI . '/assets/css/wp.css', array( 'es-site' ), ES_VERSION );

	wp_enqueue_script( 'es-site', ES_URI . '/assets/js/site.js', array(), ES_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'es_enqueue_assets' );

/**
 * Hapus CSS blok bawaan yang tidak dipakai agar halaman tetap ringan.
 */
function es_dequeue_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'es_dequeue_block_styles', 20 );

/**
 * Hapus gaya dan skrip inline bawaan WordPress.
 *
 * Tema memakai CSS sendiri, sehingga CSS global, emoji, dan speculative
 * loading tidak diperlukan. Menghapusnya juga menjaga markup tetap bersih
 * tanpa blok <style> atau <script> inline.
 */
function es_disable_inline_assets() {
	// CSS ukuran gambar otomatis.
	remove_action( 'wp_head', 'wp_enqueue_img_auto_sizes_contain_css_fix', 0 );
	remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );

	// CSS dan skrip emoji.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

	// CSS global hasil theme.json.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

	// Speculative loading.
	remove_action( 'wp_footer', 'wp_print_speculation_rules' );
}
add_action( 'init', 'es_disable_inline_assets' );

/**
 * Gunakan atribut bahasa Indonesia pada elemen html.
 *
 * @param string $output Atribut bawaan WordPress.
 * @return string
 */
function es_language_attributes( $output ) {
	return str_replace( 'en-US', 'id', $output );
}
add_filter( 'language_attributes', 'es_language_attributes' );

/**
 * Gaya halaman login WordPress.
 */
function es_enqueue_login_assets() {
	wp_enqueue_style(
		'es-login-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'es-login', ES_URI . '/assets/css/login.css', array(), ES_VERSION );
}
add_action( 'login_enqueue_scripts', 'es_enqueue_login_assets' );

/**
 * Judul dan tautan logo di halaman login.
 */
function es_login_header_text() {
	return 'EtikaSosial';
}
add_filter( 'login_headertext', 'es_login_header_text' );

function es_login_header_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'es_login_header_url' );

/**
 * Kelas body untuk peran pengguna.
 *
 * @param array $classes Kelas bawaan WordPress.
 * @return array
 */
function es_body_classes( $classes ) {
	$classes[] = 'es-site';

	if ( ! is_user_logged_in() ) {
		$classes[] = 'es-guest';
	} elseif ( current_user_can( 'manage_options' ) ) {
		$classes[] = 'es-role-admin';
	} else {
		$classes[] = 'es-role-user';
	}

	return $classes;
}
add_filter( 'body_class', 'es_body_classes' );

/**
 * Tampilkan admin bar WordPress untuk semua pengguna yang sudah masuk.
 *
 * @param bool $show Nilai bawaan.
 * @return bool
 */
function es_show_admin_bar( $show ) {
	return is_user_logged_in() ? true : $show;
}
add_filter( 'show_admin_bar', 'es_show_admin_bar' );

/**
 * Panjang kutipan.
 *
 * @return int
 */
function es_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'es_excerpt_length', 999 );

/**
 * Akhiran kutipan.
 *
 * @return string
 */
function es_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'es_excerpt_more' );

/**
 * Setelah tema diaktifkan: buat halaman, menu, dan artikel contoh.
 */
function es_after_switch_theme() {
	if ( function_exists( 'es_install_content' ) ) {
		es_install_content();
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'es_after_switch_theme' );
