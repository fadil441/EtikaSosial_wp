<?php
/**
 * Template halaman Tentang Saya.
 *
 * @package EtikaSosial
 */

get_header();
?>

<main id="mainContent" class="site-main">
	<div class="container narrow">
		<header class="page-header">
			<h1>Tentang Saya</h1>
			<p>Ruang belajar sederhana tentang cara bermedia sosial dengan lebih manusiawi.</p>
		</header>

		<div class="about-layout">
			<figure class="about-photo">
				<img
					src="<?php echo esc_url( ES_URI . '/assets/img/fadil.jpg' ); ?>"
					alt="Fadil, pengelola EtikaSosial, memakai kemeja putih, dasi navy, dan blazer hijau dengan latar merah marun"
					width="716" height="895" loading="lazy">
				<figcaption>Fadil — pengelola EtikaSosial.</figcaption>
			</figure>

			<div class="prose">
				<h2>Halo, saya Fadil.</h2>
				<p>
					EtikaSosial adalah ruang belajar dan berbagi pengalaman tentang etika bermedia sosial.
					Situs ini berisi artikel sederhana yang membahas cara berkomentar, menghargai privasi,
					memverifikasi informasi, dan menjaga kesehatan mental di ruang digital.
				</p>
				<p>
					Saya bukan penegak hukum dan tidak mewakili platform mana pun. Tulisan di sini dibuat
					sebagai panduan awal agar pembaca lebih sadar dampak dari apa yang dibagikan di internet.
				</p>

				<h2>Fokus tulisan</h2>
				<ul>
					<li>Etika berkomentar dan berbagi informasi.</li>
					<li>Privasi serta keamanan data pribadi.</li>
					<li>Verifikasi informasi dan literasi digital.</li>
					<li>Kesehatan mental di tengah arus media sosial.</li>
				</ul>

				<p>
					Jika ada pertanyaan atau masukan, silakan gunakan halaman <a href="<?php echo esc_url( es_page_url( 'kontak' ) ); ?>">Kontak</a>.
				</p>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
