<?php
/**
 * Template halaman umum.
 *
 * @package EtikaSosial
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="mainContent" class="site-main">
		<div class="container narrow">
			<article class="single-page">
				<header class="page-header">
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="prose">
					<?php the_content(); ?>
				</div>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
