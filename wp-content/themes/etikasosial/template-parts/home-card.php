<?php
/**
 * Kartu artikel di beranda.
 *
 * @package EtikaSosial
 */

$post_id   = get_the_ID();
$image     = es_get_post_image( $post_id );
$image_alt = es_get_post_image_alt( $post_id );
$category  = es_primary_category( $post_id );
$permalink = get_permalink( $post_id );
$post_date = get_post_timestamp( $post_id );
?>
<article class="home-card">
	<?php if ( $image ) : ?>
		<a class="home-card-media" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( 'Baca artikel: ' . get_the_title( $post_id ) ); ?>">
			<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" width="900" height="563" loading="lazy">
		</a>
	<?php endif; ?>

	<div class="home-card-body">
		<div class="post-meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( es_format_date( $post_date ) ); ?></time>
			<?php if ( $category ) : ?>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( $category->name ); ?></span>
			<?php endif; ?>
		</div>

		<h3>
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
		</h3>

		<p><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
		<a class="read-more" href="<?php echo esc_url( $permalink ); ?>">Baca artikel</a>
	</div>
</article>
