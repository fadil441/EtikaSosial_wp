<?php
/**
 * Template halaman tidak ditemukan.
 *
 * @package EtikaSosial
 */

get_header();
?>

<main id="mainContent" class="site-main">
	<div class="container narrow">
		<header class="page-header">
			<h1>Halaman tidak ditemukan</h1>
			<p>Tautannya mungkin sudah berubah atau salah ketik. Silakan kembali ke beranda atau hubungi kami.</p>
		</header>
		<p>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke Beranda</a>
			<a class="btn" href="<?php echo esc_url( es_page_url( 'kontak' ) ); ?>">Hubungi Kami</a>
		</p>
	</div>
</main>

<?php
get_footer();
