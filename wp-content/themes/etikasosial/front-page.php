<?php
/**
 * Beranda: pengantar situs, ringkasan, dan artikel terbaru.
 *
 * @package EtikaSosial
 */

get_header();

$article_count = (int) wp_count_posts( 'post' )->publish;
$topics        = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
$topic_count   = count( $topics );
$tag_count     = count( get_tags( array( 'hide_empty' => true ) ) );

$latest_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

$latest_update = '';
if ( $latest_query->have_posts() ) {
	$latest_update = es_format_date( get_post_timestamp( $latest_query->posts[0], 'modified' ) );
}

$feature_slugs = array( 'etika-digital', 'privasi-keamanan', 'literasi-digital' );
$feature_copy  = array(
	'etika-digital'    => 'Batas sopan santun saat berkomentar, berbagi, dan berinteraksi di ruang digital.',
	'privasi-keamanan' => 'Cara menjaga data pribadi, akun, dan tautan yang mencurigakan.',
	'literasi-digital' => 'Membaca informasi, memeriksa hoaks, dan memahami jejak digital.',
);
?>

<main id="mainContent" class="site-main site-main--home">
	<section class="home-hero">
		<div class="container home-hero-grid">
			<div class="home-hero-copy">
				<p class="eyebrow">Etika Digital · Literasi Media</p>
				<h1>Ruang digital yang lebih sehat dimulai dari kebiasaan kecil.</h1>
				<p class="home-lead">EtikaSosial adalah ruang baca sederhana tentang cara berinteraksi, menjaga privasi, dan memahami informasi di internet. Tidak perlu menjadi ahli untuk mulai bersikap lebih bijak di dunia digital.</p>
				<div class="home-actions">
					<a class="btn btn-primary" href="<?php echo esc_url( es_page_url( 'artikel' ) ); ?>">Jelajahi artikel</a>
					<a class="btn" href="<?php echo esc_url( es_page_url( 'tentang' ) ); ?>">Tentang situs</a>
				</div>
				<ul class="home-points">
					<li>Privasi &amp; keamanan akun</li>
					<li>Etika berkomunikasi</li>
					<li>Literasi digital</li>
				</ul>
			</div>

			<figure class="home-hero-media">
				<img src="<?php echo esc_url( ES_URI . '/assets/img/artikel-privasi.jpg' ); ?>" alt="Tampilan dekat layar ponsel yang menampilkan deretan ikon aplikasi media sosial" width="900" height="675">
				<figcaption>Ilustrasi: <a href="https://www.pexels.com/license/" target="_blank" rel="noopener noreferrer">Pexels</a></figcaption>
			</figure>
		</div>
	</section>

	<section class="home-section">
		<div class="container">
			<header class="home-section-head">
				<div>
					<p class="eyebrow">Yang bisa kamu temukan</p>
					<h2>Bacaan singkat untuk kebiasaan digital yang lebih baik</h2>
				</div>
			</header>

			<div class="home-feature-grid">
				<?php $feature_index = 0; ?>
				<?php foreach ( $feature_slugs as $feature_slug ) : ?>
					<?php
					$feature_category = get_category_by_slug( $feature_slug );
					if ( ! $feature_category ) {
						continue;
					}
					$feature_index++;
					?>
					<article class="home-feature">
						<span class="home-feature-index" aria-hidden="true"><?php echo esc_html( str_pad( (string) $feature_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><a href="<?php echo esc_url( get_category_link( $feature_category ) ); ?>"><?php echo esc_html( $feature_category->name ); ?></a></h3>
						<p><?php echo esc_html( isset( $feature_copy[ $feature_slug ] ) ? $feature_copy[ $feature_slug ] : '' ); ?></p>
						<span class="home-feature-count"><?php echo esc_html( (string) $feature_category->count ); ?> artikel</span>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="home-section">
		<div class="container">
			<header class="home-section-head">
				<div>
					<p class="eyebrow">Ringkasan situs</p>
					<h2>EtikaSosial dalam angka</h2>
				</div>
				<p class="home-section-note">Panel kecil untuk melihat isi situs sekilas.</p>
			</header>

			<div class="home-dashboard">
				<div class="home-stat-grid">
					<div class="home-stat">
						<span class="home-stat-label">Artikel</span>
						<strong><?php echo esc_html( (string) $article_count ); ?></strong>
						<span class="home-stat-note">Bacaan yang tersedia</span>
					</div>
					<div class="home-stat">
						<span class="home-stat-label">Kategori</span>
						<strong><?php echo esc_html( (string) $topic_count ); ?></strong>
						<span class="home-stat-note">Topik utama</span>
					</div>
					<div class="home-stat">
						<span class="home-stat-label">Tag</span>
						<strong><?php echo esc_html( (string) $tag_count ); ?></strong>
						<span class="home-stat-note">Kata kunci bahasan</span>
					</div>
					<div class="home-stat">
						<span class="home-stat-label">Pembaruan</span>
						<strong class="home-stat-date"><?php echo esc_html( $latest_update ? $latest_update : '—' ); ?></strong>
						<span class="home-stat-note">Artikel terakhir diperbarui</span>
					</div>
				</div>

				<div class="home-dashboard-panel">
					<h3>Sebaran topik</h3>
					<ul class="home-topic-list">
						<?php foreach ( $topics as $topic ) : ?>
							<li>
								<span class="home-topic-name"><?php echo esc_html( $topic->name ); ?></span>
								<span class="home-topic-count"><?php echo esc_html( (string) $topic->count ); ?></span>
								<progress max="<?php echo esc_attr( (string) max( 1, $article_count ) ); ?>" value="<?php echo esc_attr( (string) $topic->count ); ?>"><?php echo esc_html( (string) $topic->count ); ?></progress>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="home-dashboard-note">Setiap artikel ditulis singkat dan mudah dibaca.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="home-section">
		<div class="container">
			<header class="home-section-head home-section-head--row">
				<div>
					<p class="eyebrow">Artikel terbaru</p>
					<h2>Bacaan yang baru ditambahkan</h2>
				</div>
				<a class="btn" href="<?php echo esc_url( es_page_url( 'artikel' ) ); ?>">Lihat semua artikel</a>
			</header>

			<?php if ( $latest_query->have_posts() ) : ?>
				<div class="home-post-grid">
					<?php
					while ( $latest_query->have_posts() ) :
						$latest_query->the_post();
						get_template_part( 'template-parts/home-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<p>Belum ada artikel yang dipublikasikan.</p>
			<?php endif; ?>
		</div>
	</section>

	<section class="home-section home-section--cta">
		<div class="container">
			<div class="home-cta">
				<div>
					<p class="eyebrow">Diskusi</p>
					<h2>Punya pertanyaan tentang etika digital?</h2>
					<p>Kirim pesan lewat halaman kontak. Mari bicarakan topik yang ingin kamu pahami lebih jauh.</p>
				</div>
				<a class="btn btn-primary" href="<?php echo esc_url( es_page_url( 'kontak' ) ); ?>">Hubungi saya</a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
