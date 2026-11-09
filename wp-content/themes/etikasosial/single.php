<?php
/**
 * Template artikel tunggal.
 *
 * @package EtikaSosial
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id  = get_the_ID();
	$image    = es_get_post_image( $post_id );
	$alt      = es_get_post_image_alt( $post_id );
	$category = es_primary_category( $post_id );
	$tags     = get_the_tags( $post_id );
	$source   = es_get_post_source( $post_id );
	$date     = get_post_timestamp( $post_id );
	?>

	<main id="mainContent" class="site-main">
		<div class="container narrow">
			<article class="single-article">
				<header class="article-header">
					<div class="post-meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( es_format_date( $date ) ); ?></time>
						<?php if ( $category ) : ?>
							<span aria-hidden="true">·</span>
							<span><?php echo esc_html( $category->name ); ?></span>
						<?php endif; ?>
					</div>
					<h1><?php the_title(); ?></h1>
					<p class="article-byline">Oleh <?php echo esc_html( get_the_author() ); ?></p>
				</header>

				<?php if ( $image ) : ?>
					<figure class="article-featured">
						<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="1400" height="900">
					</figure>
				<?php endif; ?>

				<div class="prose">
					<?php the_content(); ?>
				</div>

				<?php if ( $tags ) : ?>
					<div class="article-tags" aria-label="Tag artikel">
						<?php foreach ( $tags as $tag ) : ?>
							<a class="tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $source['label'] ) && ! empty( $source['url'] ) ) : ?>
					<p class="article-source">
						Sumber gambar: <a href="<?php echo esc_url( $source['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source['label'] ); ?></a>.
					</p>
				<?php endif; ?>

				<footer class="article-footer">
					<?php if ( current_user_can( 'edit_post', $post_id ) ) : ?>
						<a class="btn" href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>">Edit artikel</a>
					<?php endif; ?>
					<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke beranda</a>
				</footer>
			</article>
		</div>
	</main>

	<?php
endwhile;

get_footer();
