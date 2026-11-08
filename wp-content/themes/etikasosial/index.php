<?php
/**
 * Template daftar artikel dan arsip.
 *
 * @package EtikaSosial
 */

get_header();

$posts_page_id = (int) get_option( 'page_for_posts' );
$heading       = 'Artikel';
$lead          = 'Kumpulan bacaan tentang etika digital, privasi, dan literasi media.';

if ( is_home() && $posts_page_id ) {
	$heading = get_the_title( $posts_page_id );
} elseif ( is_search() ) {
	$heading = 'Hasil pencarian: ' . get_search_query();
	$lead    = 'Berikut artikel yang cocok dengan kata kunci pencarianmu.';
} elseif ( is_archive() ) {
	$heading = wp_strip_all_tags( get_the_archive_title() );
	$lead    = wp_strip_all_tags( get_the_archive_description() );
}
?>

<main id="mainContent" class="site-main">
	<div class="container">
		<header class="page-header">
			<h1><?php echo esc_html( $heading ); ?></h1>
			<?php if ( $lead ) : ?>
				<p><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => 'Sebelumnya',
					'next_text' => 'Berikutnya',
				)
			);
			?>
		<?php else : ?>
			<p>Belum ada artikel yang tersedia.</p>
			<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke beranda</a></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
