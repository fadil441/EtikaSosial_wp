<?php
/**
 * Header tema.
 *
 * @package EtikaSosial
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#0a0e13">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#mainContent">Lompat ke konten utama</a>

<header class="site-header">
	<div class="container header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>, kembali ke beranda">
			<span class="brand-mark" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-3.8-.8L3 21l1.9-4.6A8.4 8.4 0 0 1 12 3.1a8.4 8.4 0 0 1 9 8.4Z" />
					<path d="M8.5 10.5h7M8.5 14h4.5" />
				</svg>
			</span>
			<span class="brand-text">Etika<em>Sosial</em></span>
		</a>

		<nav class="main-nav" id="mainNav" aria-label="Navigasi utama">
			<?php es_nav_menu( 'primary', 'nav-link' ); ?>
		</nav>

		<div class="header-account">
			<?php if ( is_user_logged_in() ) : ?>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<a class="nav-link" href="<?php echo esc_url( admin_url() ); ?>">Kelola</a>
				<?php endif; ?>
				<a class="nav-link" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">Keluar</a>
			<?php else : ?>
				<a class="btn btn-small" href="<?php echo esc_url( es_login_url() ); ?>">Masuk</a>
			<?php endif; ?>
		</div>

		<button class="nav-toggle" id="navToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mainNav">
			<span class="nav-toggle-bar" aria-hidden="true"></span>
			<span class="nav-toggle-bar" aria-hidden="true"></span>
			<span class="nav-toggle-bar" aria-hidden="true"></span>
		</button>
	</div>
</header>
