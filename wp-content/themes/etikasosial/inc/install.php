<?php
/**
 * Instalasi konten awal saat tema diaktifkan.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Buat halaman bila belum ada.
 *
 * @param string $slug     Slug halaman.
 * @param string $title    Judul.
 * @param string $template Template halaman.
 * @param string $content  Isi.
 * @return int ID halaman.
 */
function es_install_page( $slug, $title, $template = '', $content = '' ) {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		$page_id = (int) $page->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
			),
			true
		);
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	if ( $template ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}

	return (int) $page_id;
}

/**
 * Tambahkan item menu.
 *
 * @param int    $menu_id Menu ID.
 * @param array  $args    Argumen item.
 * @return int
 */
function es_install_menu_item( $menu_id, $args ) {
	$defaults = array(
		'menu-item-status' => 'publish',
	);

	return (int) wp_update_nav_menu_item( $menu_id, 0, wp_parse_args( $args, $defaults ) );
}

/**
 * Buat halaman, menu, kategori, tag, dan artikel awal.
 */
function es_install_content() {
	if ( get_option( 'es_content_installed' ) ) {
		return;
	}

	$author_id = get_current_user_id() ? get_current_user_id() : 1;

	/* ---------------------------------------------------------------------
	 * Halaman
	 * ------------------------------------------------------------------ */
	$pages = array(
		'beranda' => array( 'Beranda', '' ),
		'tentang' => array( 'Tentang Saya', 'page-tentang-saya.php' ),
		'kontak'  => array( 'Kontak', 'page-kontak.php' ),
		'artikel' => array( 'Artikel', '' ),
		'masuk'   => array( 'Masuk', 'page-masuk.php' ),
	);

	$slugs = array(
		'beranda' => 'beranda',
		'tentang' => 'tentang-saya',
		'kontak'  => 'kontak',
		'artikel' => 'artikel',
		'masuk'   => 'masuk',
	);

	$page_ids = array();

	foreach ( $pages as $key => $page_data ) {
		$page_id = es_install_page( $slugs[ $key ], $page_data[0], $page_data[1] );
		if ( $page_id ) {
			$page_ids[ $key ] = $page_id;
			update_option( 'es_page_' . $key, $page_id );
		}
	}

	if ( ! empty( $page_ids['beranda'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['beranda'] );
	}

	if ( ! empty( $page_ids['artikel'] ) ) {
		update_option( 'page_for_posts', $page_ids['artikel'] );
	}

	/* ---------------------------------------------------------------------
	 * Menu utama
	 * ------------------------------------------------------------------ */
	$primary_menu = wp_get_nav_menu_object( 'Menu Utama' );
	$primary_id   = $primary_menu ? (int) $primary_menu->term_id : (int) wp_create_nav_menu( 'Menu Utama' );

	if ( $primary_id && ! is_wp_error( $primary_id ) ) {
		$existing = wp_get_nav_menu_items( $primary_id );

		if ( ! $existing ) {
			es_install_menu_item(
				$primary_id,
				array(
					'menu-item-title' => 'Beranda',
					'menu-item-url'   => home_url( '/' ),
					'menu-item-type'  => 'custom',
				)
			);

			if ( ! empty( $page_ids['artikel'] ) ) {
				es_install_menu_item(
					$primary_id,
					array(
						'menu-item-title'     => 'Artikel',
						'menu-item-object-id' => $page_ids['artikel'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}

			if ( ! empty( $page_ids['tentang'] ) ) {
				es_install_menu_item(
					$primary_id,
					array(
						'menu-item-title'     => 'Tentang Saya',
						'menu-item-object-id' => $page_ids['tentang'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}

			if ( ! empty( $page_ids['kontak'] ) ) {
				es_install_menu_item(
					$primary_id,
					array(
						'menu-item-title'     => 'Kontak',
						'menu-item-object-id' => $page_ids['kontak'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}
		}
	}

	/* ---------------------------------------------------------------------
	 * Menu footer
	 * ------------------------------------------------------------------ */
	$footer_menu = wp_get_nav_menu_object( 'Menu Footer' );
	$footer_id   = $footer_menu ? (int) $footer_menu->term_id : (int) wp_create_nav_menu( 'Menu Footer' );

	if ( $footer_id && ! is_wp_error( $footer_id ) ) {
		$existing = wp_get_nav_menu_items( $footer_id );

		if ( ! $existing ) {
			es_install_menu_item(
				$footer_id,
				array(
					'menu-item-title' => 'Beranda',
					'menu-item-url'   => home_url( '/' ),
					'menu-item-type'  => 'custom',
				)
			);

			if ( ! empty( $page_ids['artikel'] ) ) {
				es_install_menu_item(
					$footer_id,
					array(
						'menu-item-title'     => 'Artikel',
						'menu-item-object-id' => $page_ids['artikel'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}

			if ( ! empty( $page_ids['tentang'] ) ) {
				es_install_menu_item(
					$footer_id,
					array(
						'menu-item-title'     => 'Tentang Saya',
						'menu-item-object-id' => $page_ids['tentang'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}

			if ( ! empty( $page_ids['kontak'] ) ) {
				es_install_menu_item(
					$footer_id,
					array(
						'menu-item-title'     => 'Kontak',
						'menu-item-object-id' => $page_ids['kontak'],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
					)
				);
			}
		}
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( $primary_id && ! is_wp_error( $primary_id ) ) {
		$locations['primary'] = $primary_id;
	}

	if ( $footer_id && ! is_wp_error( $footer_id ) ) {
		$locations['footer'] = $footer_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );

	/* ---------------------------------------------------------------------
	 * Kategori dan tag
	 * ------------------------------------------------------------------ */
	$categories = array(
		'Etika Digital'       => 'etika-digital',
		'Privasi & Keamanan' => 'privasi-keamanan',
		'Literasi Digital'    => 'literasi-digital',
	);

	$category_ids = array();

	foreach ( $categories as $name => $slug ) {
		$term = term_exists( $slug, 'category' );

		if ( ! $term ) {
			$term = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		}

		if ( ! is_wp_error( $term ) ) {
			$category_ids[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}

	$tags = array(
		'KomentarSehat',
		'EmpatiDigital',
		'KritikKonstruktif',
		'JejakDigital',
		'Privasi',
		'ReputasiOnline',
		'AntiHoaks',
		'FaktaCek',
		'LiterasiDigital',
		'EtikaGrupChat',
		'KomunikasiDigital',
		'KeamananAkun',
		'Phishing',
		'KesehatanMental',
		'DetoksDigital',
		'HakCipta',
		'KreditKarya',
	);

	foreach ( $tags as $tag ) {
		if ( ! term_exists( $tag, 'post_tag' ) ) {
			wp_insert_term( $tag, 'post_tag' );
		}
	}

	/* ---------------------------------------------------------------------
	 * Artikel
	 * ------------------------------------------------------------------ */
	$articles = array(
		array(
			'slug'        => 'komentar-empati',
			'title'       => 'Berkomentar dengan Empati: Seni Menyampaikan Kritik di Ruang Digital',
			'category'    => 'etika-digital',
			'date'        => '2026-10-14 09:00:00',
			'read'        => '6 menit baca',
			'tags'        => array( 'KomentarSehat', 'EmpatiDigital', 'KritikKonstruktif' ),
			'image'       => 'artikel-komentar.jpg',
			'image_alt'   => 'Seorang perempuan mengetik di laptop di atas meja putih, ditemani buku catatan, ponsel, dan secangkir kopi',
			'source_url'  => 'https://www.pexels.com/photo/4050315/',
			'excerpt'     => 'Kritik pedas tidak selalu berarti tegas, dan sopan tidak selalu berarti lemah. Inilah cara memisahkan kritik dari serangan pribadi.',
			'content'     => <<<'HTML'
<p>Kritik dan penghinaan sering kali hanya dipisahkan oleh satu kata. Keduanya sama-sama menyampaikan ketidaksetujuan, tetapi yang satu membuka ruang perbaikan, sedangkan yang lain menutup pintu komunikasi. Di media sosial, perbedaan ini menjadi semakin rumit karena tulisan tidak membawa intonasi. Kalimat yang di kepala kita terdengar tegas bisa terbaca sebagai serangan di layar orang lain.</p>
<p>Etika berkomentar bertumpu pada satu pertanyaan sederhana: apakah yang saya tulis membantu orang lain memahami sesuatu, atau hanya melampiaskan emosi saya? Komentar yang berfokus pada gagasan, bukan pada fisik, keluarga, agama, atau latar belakang penulis, memiliki peluang jauh lebih besar untuk didengar. Sebaliknya, komentar yang menyerang pribadi hampir selalu memicu pembelaan, bukan perubahan.</p>
<h3>Tiga kebiasaan yang bisa langsung diterapkan</h3>
<ul>
<li><strong>Gunakan kalimat &ldquo;saya&rdquo;.</strong> Daripada menulis &ldquo;kamu salah dan tidak paham apa-apa&rdquo;, cobalah &ldquo;saya membaca sumber yang berbeda, bolehkah kamu sertakan tautannya?&rdquo;. Kalimat seperti ini menyampaikan ketidaksetujuan tanpa merendahkan lawan bicara.</li>
<li><strong>Tanyakan niat sebelum menekan kirim.</strong> Jika tujuanmu hanya membuat orang lain malu di depan publik, tahan komentar itu. Kritik yang baik disampaikan untuk memperbaiki keadaan, bukan untuk mendapatkan sorakan dari penonton.</li>
<li><strong>Beri jeda sepuluh menit.</strong> Emosi di media sosial bergerak jauh lebih cepat daripada pikiran. Menunda sebentar sering cukup untuk mengubah kalimat yang menyakitkan menjadi pertanyaan yang membangun.</li>
</ul>
<p>Hal yang sering dilupakan adalah posisi saksi. Ketika seseorang dirundung di kolom komentar, kehadiran orang lain yang ikut menertawakan membuat dampaknya berkali-kali lipat. Sebaliknya, satu komentar yang menenangkan dapat mengubah suasana seluruh percakapan. Menjadi saksi yang baik berarti berani mengatakan &ldquo;ini sudah keterlaluan&rdquo; tanpa harus ikut berdebat panjang.</p>
<blockquote>Sopan bukan berarti setuju. Sopan berarti tetap membuka jalan bagi orang lain untuk berubah.</blockquote>
<p>Jika kamu berada di posisi korban, kamu berhak memblokir, melaporkan, dan menyimpan bukti sebelum memutuskan langkah berikutnya. Jangan ragu menceritakan kepada orang yang kamu percaya. Etika digital bukan hanya soal menghukum pelaku, tetapi juga soal memastikan korban tidak menghadapi tekanannya sendirian.</p>
HTML
		),
		array(
			'slug'        => 'jejak-digital',
			'title'       => 'Jejak Digital: Mengapa Unggahan Masa Lalu Bisa Menentukan Masa Depanmu',
			'category'    => 'privasi-keamanan',
			'date'        => '2026-10-21 09:00:00',
			'read'        => '7 menit baca',
			'tags'        => array( 'JejakDigital', 'Privasi', 'ReputasiOnline' ),
			'image'       => 'artikel-jejak.jpg',
			'image_alt'   => 'Tampilan dekat deretan ikon media sosial seperti Instagram, Facebook, Twitter, dan YouTube pada layar ponsel',
			'source_url'  => 'https://www.pexels.com/photo/267350/',
			'excerpt'     => 'Internet tidak pernah benar-benar lupa. Sebelum mengunggah, ada baiknya kamu tahu siapa yang mungkin membaca lima tahun dari sekarang.',
			'content'     => <<<'HTML'
<p>Internet tidak benar-benar lupa. Sebuah unggahan yang dihapus hari ini bisa saja sudah tersimpan sebagai tangkapan layar, masuk ke grup percakapan, atau terindeks mesin pencari. Banyak orang baru menyadari hal ini ketika melamar pekerjaan, mendaftar beasiswa, atau menjalin hubungan baru, lalu mendapati bahwa masa lalunya mudah ditemukan hanya dengan mengetik namanya.</p>
<p>Jejak digital bukan hanya tentang foto. Ia mencakup komentar di forum lama, ulasan yang ditulis saat marah, alamat surel yang pernah dipakai, nomor telepon di unggahan jual beli, hingga lokasi yang tercatat pada metadata foto. Semua potongan kecil ini, bila digabungkan, dapat membentuk gambaran yang jauh lebih lengkap daripada yang kita bayangkan.</p>
<h3>Mengapa jejak digital penting</h3>
<p>Perekrut kerja, panitia beasiswa, dan calon mitra bisnis kini terbiasa memeriksa jejak publik seseorang. Mereka mencari konsistensi antara apa yang kita tulis dan bagaimana kita bersikap. Satu komentar kasar yang ditulis lima tahun lalu bisa menjadi pertanyaan besar dalam wawancara, meskipun kita sudah berubah. Di sisi lain, jejak digital yang positif, seperti tulisan, karya, atau dokumentasi kegiatan sosial, dapat menjadi bukti kompetensi yang sulit dibantah.</p>
<h3>Langkah sederhana mengelola jejak digital</h3>
<ul>
<li><strong>Audit nama sendiri.</strong> Cari namamu di mesin pencari setiap beberapa bulan, lalu periksa hasil yang muncul di halaman pertama.</li>
<li><strong>Pisahkan ruang pribadi dan profesional.</strong> Gunakan pengaturan privasi yang berbeda untuk akun keluarga dan akun yang kamu pakai untuk bekerja atau berkarya.</li>
<li><strong>Hindari membagikan data sensitif.</strong> Nomor identitas, alamat rumah, tiket perjalanan, dan foto dokumen sebaiknya tidak pernah diunggah ke ruang publik.</li>
<li><strong>Matikan geotag pada unggahan real-time.</strong> Menandai lokasi saat sedang bepergian memberi tahu orang lain bahwa rumahmu sedang kosong.</li>
</ul>
<p>Menghapus unggahan lama memang tidak menjamin semuanya hilang, tetapi tetap bermanfaat. Mulailah dari akun yang paling sering dilihat orang, lalu lanjutkan ke platform yang jarang kamu buka. Jika menemukan konten yang sudah tidak mencerminkan dirimu, hapus atau ubah pengaturannya menjadi pribadi.</p>
<blockquote>Reputasi online dibangun bertahun-tahun, tetapi bisa runtuh dalam satu unggahan yang salah tempat.</blockquote>
<p>Pada akhirnya, mengelola jejak digital bukan tentang berpura-pura sempurna. Ini tentang kesadaran bahwa apa yang kita bagikan akan dibaca oleh orang yang belum mengenal kita. Sedikit kehati-hatian hari ini akan menghemat banyak penjelasan di kemudian hari.</p>
HTML
		),
		array(
			'slug'        => 'verifikasi-hoaks',
			'title'       => 'Verifikasi Sebelum Membagikan: Melawan Hoaks Tanpa Ikut Menyebarkannya',
			'category'    => 'literasi-digital',
			'date'        => '2026-10-28 09:00:00',
			'read'        => '6 menit baca',
			'tags'        => array( 'AntiHoaks', 'FaktaCek', 'LiterasiDigital' ),
			'image'       => 'artikel-hoaks.jpg',
			'image_alt'   => 'Enam tangan saling menumpuk di atas meja kerja kayu sebagai simbol kerja sama, dengan laptop dan dokumen di sekitarnya',
			'source_url'  => 'https://www.pexels.com/photo/3184418/',
			'excerpt'     => 'Satu klik "bagikan" bisa menghancurkan reputasi seseorang. Kenali ciri hoaks dan lima langkah sederhana untuk memeriksanya.',
			'content'     => <<<'HTML'
<p>Hoaks jarang datang dalam bentuk yang jelas-jelas palsu. Ia biasanya dibungkus dengan judul yang emosional, potongan video yang dipotong sedemikian rupa, atau tangkapan layar tanpa konteks. Karena terasa mendesak dan membuat marah, banyak orang membagikannya sebelum sempat berpikir. Padahal, setiap kali hoaks dibagikan, jangkauannya bertambah dan biaya untuk meluruskannya semakin besar.</p>
<p>Melawan hoaks tidak selalu berarti menjadi ahli pemeriksa fakta. Sebagian besar hoaks dapat dikenali dari pola sederhana: sumbernya tidak jelas, tanggalnya tidak cocok, gambar terasa terlalu dramatis, atau isinya meminta kita segera menyebarkan ke banyak grup. Semakin sebuah pesan mendesak kita untuk bertindak cepat, semakin besar alasan untuk berhenti dan memeriksanya.</p>
<h3>Ciri hoaks yang perlu diwaspadai</h3>
<ul>
<li>Judul memancing emosi dan menggunakan huruf kapital berlebihan.</li>
<li>Tidak mencantumkan nama media, penulis, atau tanggal peristiwa.</li>
<li>Meminta pembaca menyebarkan pesan &ldquo;sebelum dihapus&rdquo;.</li>
<li>Menggunakan foto lama untuk peristiwa baru.</li>
<li>Menyertakan satu kutipan tanpa tautan ke pidato atau dokumen aslinya.</li>
</ul>
<h3>Lima langkah verifikasi</h3>
<ol>
<li><strong>Baca seluruh isi, bukan hanya judul.</strong> Banyak hoaks runtuh ketika dibaca sampai akhir.</li>
<li><strong>Cari sumber aslinya.</strong> Periksa apakah peristiwa itu benar-benar diberitakan media kredibel dan apakah tanggalnya sesuai.</li>
<li><strong>Periksa gambar dengan pencarian terbalik.</strong> Foto yang sama sering muncul dalam konteks yang berbeda beberapa tahun sebelumnya.</li>
<li><strong>Bandingkan dengan lebih dari satu sumber.</strong> Satu sumber tidak cukup, terutama jika isinya sangat emosional.</li>
<li><strong>Tunggu sebelum membagikan.</strong> Jika masih ragu, jangan sebarkan. Diam bukan berarti tidak peduli.</li>
</ol>
<p>Ketika menemukan hoaks, langkah paling membantu adalah melaporkannya ke platform, memberi tahu pengirim dengan sopan disertai tautan klarifikasi, dan tidak menyebarkan ulang kontennya meski untuk tujuan mengoreksi. Tangkapan layar yang disensor biasanya lebih aman daripada membagikan tautan aslinya.</p>
<blockquote>Kecepatan bukan ukuran kepedulian. Kadang cara paling bertanggung jawab untuk peduli adalah memeriksa dahulu.</blockquote>
<p>Literasi digital bukan kemampuan menghafal aplikasi terbaru, melainkan kebiasaan bertanya: siapa yang diuntungkan jika saya membagikan ini? Jika jawabannya tidak jelas, menahan jempol adalah pilihan yang bijak.</p>
HTML
		),
		array(
			'slug'        => 'privasi-media-sosial',
			'title'       => 'Privasi di Media Sosial: Data Kecil yang Sering Kita Serahkan Tanpa Sadar',
			'category'    => 'privasi-keamanan',
			'date'        => '2026-11-01 09:00:00',
			'read'        => '5 menit baca',
			'tags'        => array( 'Privasi', 'KeamananAkun', 'JejakDigital' ),
			'image'       => 'artikel-privasi.jpg',
			'image_alt'   => 'Tampilan dekat layar ponsel yang menampilkan deretan ikon aplikasi media sosial',
			'source_url'  => 'https://www.pexels.com/photo/15406294/',
			'excerpt'     => 'Nama sekolah, lokasi real-time, sampai foto tiket pesawat. Tanpa sadar, unggahan kecil bisa membuka pintu bagi orang yang tidak kita kenal.',
			'content'     => <<<'HTML'
<p>Media sosial dirancang agar kita merasa sedang berbicara dengan teman sendiri. Karena suasana terasa akrab, kita sering membagikan hal-hal yang sebenarnya tidak layak diketahui publik: nama sekolah anak, nomor rumah, lokasi saat sedang bepergian, atau foto tiket pesawat yang masih memperlihatkan kode pemesanan. Satu per satu terlihat sepele, tetapi bila digabungkan, potongan informasi itu dapat menggambarkan rutinitas dan titik lemah seseorang.</p>
<p>Masalah utama privasi digital bukan hanya pencurian akun. Informasi yang kita bagikan dapat dipakai untuk menebak kata sandi, menyusun pesan penipuan yang meyakinkan, atau mengetahui kapan rumah sedang kosong. Pelaku tidak perlu meretas apa pun jika pemilik akun sendiri yang membuka pintunya.</p>
<h3>Informasi yang sebaiknya tidak diunggah</h3>
<ul>
<li><strong>Dokumen identitas.</strong> KTP, SIM, paspor, dan kartu keluarga sebaiknya tidak pernah difoto untuk ruang publik, termasuk grup yang anggotanya belum benar-benar kita kenal.</li>
<li><strong>Tiket dan boarding pass.</strong> Kode pemesanan pada tiket dapat dipakai untuk mengubah atau membatalkan perjalanan.</li>
<li><strong>Lokasi secara real-time.</strong> Menandai lokasi saat sedang di luar rumah memberi tahu orang lain bahwa rumah sedang kosong.</li>
<li><strong>Data anak dan keluarga.</strong> Nama lengkap, sekolah, dan jadwal kegiatan anak adalah informasi yang paling sering disalahgunakan.</li>
</ul>
<h3>Langkah sederhana melindungi akun</h3>
<ol>
<li>Aktifkan verifikasi dua langkah pada surel dan akun media sosial utama.</li>
<li>Gunakan kata sandi berbeda untuk setiap layanan, idealnya dengan bantuan pengelola kata sandi.</li>
<li>Periksa pengaturan privasi setiap beberapa bulan, terutama setelah pembaruan aplikasi.</li>
<li>Batasi siapa yang dapat melihat daftar teman, nomor telepon, dan unggahan lama.</li>
</ol>
<p>Privasi bukan berarti menyembunyikan diri. Privasi berarti kita yang memutuskan siapa yang boleh mengetahui sesuatu, bukan menyerahkan keputusan itu kepada algoritma atau orang asing yang kebetulan melihat unggahan kita.</p>
<blockquote>Berbagi itu baik. Berbagi tanpa berpikir tentang siapa yang membaca adalah masalah lain.</blockquote>
HTML
		),
		array(
			'slug'        => 'etika-grup-chat',
			'title'       => 'Etika di Grup Chat: Batas Antara Akrab, Ramai, dan Mengganggu',
			'category'    => 'etika-digital',
			'date'        => '2026-11-02 09:00:00',
			'read'        => '5 menit baca',
			'tags'        => array( 'EtikaGrupChat', 'EmpatiDigital', 'KomunikasiDigital' ),
			'image'       => 'artikel-grup-chat.jpg',
			'image_alt'   => 'Seorang perempuan muda mengetik pesan di ponsel pintar di dalam ruangan',
			'source_url'  => 'https://www.pexels.com/photo/girl-using-mobile-phone-6411/',
			'excerpt'     => 'Grup keluarga, kelas, dan kerja punya aturan tak tertulis. Mengetahui kapan harus membalas dan kapan diam membuat percakapan lebih nyaman.',
			'content'     => <<<'HTML'
<p>Grup chat terasa seperti ruang santai, padahal isinya bisa sangat beragam: keluarga besar, teman sekolah, rekan kerja, hingga komunitas hobi. Setiap grup memiliki norma yang berbeda, dan norma itu jarang dituliskan. Akibatnya, candaan yang lucu di satu grup bisa terasa menyinggung di grup lain.</p>
<p>Etika grup chat bertumpu pada kesadaran bahwa setiap pesan masuk ke ponsel orang lain, sering kali di waktu yang tidak tepat. Kita tidak tahu apakah penerima sedang bekerja, mengemudi, atau menemani anak belajar. Sedikit kehati-hatian dalam mengirim akan menghemat banyak salah paham.</p>
<h3>Kebiasaan yang membuat grup lebih nyaman</h3>
<ul>
<li><strong>Kirim pesan dalam satu kesatuan.</strong> Menghindari puluhan pesan pendek berturut-turut membantu orang lain membaca tanpa terganggu.</li>
<li><strong>Hindari meneruskan pesan berantai.</strong> Terutama pesan yang meminta segera disebarkan, berisi ancaman, atau tidak jelas sumbernya.</li>
<li><strong>Jangan membahas masalah pribadi orang lain.</strong> Tangkapan layar percakapan pribadi sebaiknya tidak dipindahkan ke grup tanpa izin.</li>
<li><strong>Perhatikan waktu.</strong> Pesan tengah malam sebaiknya dihindari kecuali memang mendesak.</li>
<li><strong>Gunakan fitur balas dengan bijak.</strong> Membalas pesan tertentu membuat konteks percakapan tetap jelas.</li>
</ul>
<h3>Ketika terjadi konflik</h3>
<p>Perbedaan pendapat di grup chat cepat membesar karena tidak ada intonasi dan ekspresi wajah. Jika percakapan mulai memanas, lebih baik memindahkannya ke pesan pribadi atau menelepon. Menyelesaikan konflik di depan banyak orang cenderung membuat semua pihak bertahan pada posisi masing-masing.</p>
<p>Admin grup juga perlu mengambil peran, bukan sekadar diam. Menetapkan aturan singkat di awal, menonaktifkan penerusan pesan berantai, dan menegur dengan sopan dapat menjaga suasana tetap sehat.</p>
<blockquote>Grup yang nyaman bukan grup yang paling ramai, melainkan grup yang tahu kapan harus berbicara dan kapan harus mendengarkan.</blockquote>
HTML
		),
		array(
			'slug'        => 'waspada-phishing',
			'title'       => 'Waspada Phishing: Tautan yang Terlihat Resmi Belum Tentu Aman',
			'category'    => 'privasi-keamanan',
			'date'        => '2026-11-03 09:00:00',
			'read'        => '6 menit baca',
			'tags'        => array( 'Phishing', 'KeamananAkun', 'Privasi' ),
			'image'       => 'artikel-phishing.jpg',
			'image_alt'   => 'Laptop di atas meja menampilkan ikon gembok keamanan, ditemani tanaman kecil dan jam',
			'source_url'  => 'https://www.pexels.com/photo/4973899/',
			'excerpt'     => 'Surel palsu dan situs tiruan makin rapi. Kenali tanda-tandanya sebelum mengetik kata sandi atau kode OTP.',
			'content'     => <<<'HTML'
<p>Phishing adalah upaya menipu orang agar menyerahkan informasi rahasia, seperti kata sandi, kode OTP, atau data kartu pembayaran. Pelakunya tidak selalu menggunakan teknologi rumit. Sering kali mereka hanya meniru tampilan situs resmi dan mengirim pesan yang terasa mendesak.</p>
<p>Pesan phishing biasanya memancing rasa takut atau keinginan. Ada yang mengaku akun kita akan diblokir, ada yang menawarkan hadiah, dan ada yang berpura-pura menjadi rekan kerja. Semua variasi itu bertujuan sama: membuat kita bertindak cepat tanpa memeriksa.</p>
<h3>Tanda-tanda pesan phishing</h3>
<ul>
<li>Alamat pengirim terlihat mirip, tetapi ada huruf atau tanda tambahan yang janggal.</li>
<li>Tautan mengarah ke alamat yang tidak sesuai dengan nama layanan.</li>
<li>Pesan meminta data pribadi, kata sandi, atau kode OTP dengan alasan mendesak.</li>
<li>Ada lampiran tidak terduga, misalnya faktur atau surat tagihan.</li>
<li>Ejaan dan tata bahasanya tidak rapi meskipun mengaku dari perusahaan besar.</li>
</ul>
<h3>Langkah aman saat menerima pesan mencurigakan</h3>
<ol>
<li>Jangan langsung mengeklik tautan. Buka situs resmi lewat alamat yang kamu ketik sendiri.</li>
<li>Periksa alamat situs dengan teliti sebelum mengetik apa pun.</li>
<li>Jangan pernah membagikan kode OTP kepada siapa pun, termasuk yang mengaku petugas layanan.</li>
<li>Hubungi layanan resmi melalui saluran yang kamu cari sendiri, bukan nomor yang tertera di pesan.</li>
<li>Laporkan dan hapus pesan tersebut agar tidak tersebar ke orang lain.</li>
</ol>
<p>Jika sudah terlanjur mengetik kata sandi di situs palsu, segera ubah kata sandi dari perangkat lain, aktifkan verifikasi dua langkah, dan periksa riwayat aktivitas akun. Bertindak cepat dapat membatasi kerugian.</p>
<blockquote>Rasa mendesak adalah alat utama penipu. Semakin mereka meminta cepat, semakin kita perlu berhenti sebentar.</blockquote>
HTML
		),
		array(
			'slug'        => 'kesehatan-mental-digital',
			'title'       => 'Kesehatan Mental di Ruang Digital: Kapan Harus Berhenti Menggulir',
			'category'    => 'literasi-digital',
			'date'        => '2026-11-04 09:00:00',
			'read'        => '5 menit baca',
			'tags'        => array( 'KesehatanMental', 'DetoksDigital', 'LiterasiDigital' ),
			'image'       => 'artikel-kesehatan-mental.jpg',
			'image_alt'   => 'Mesin tik klasik dengan kertas bertuliskan "Digital Detox" di luar ruangan',
			'source_url'  => 'https://www.pexels.com/photo/18500701/',
			'excerpt'     => 'Media sosial bisa menghubungkan, tetapi juga melelahkan. Mengenali tanda jenuh digital adalah bagian dari literasi.',
			'content'     => <<<'HTML'
<p>Membuka media sosial untuk mencari satu informasi sering berakhir dengan satu jam menggulir tanpa tujuan. Awalnya menyenangkan, tetapi setelahnya muncul rasa lelah, cemas, atau merasa hidup sendiri kurang menarik dibandingkan unggahan orang lain.</p>
<p>Perasaan itu wajar. Media sosial menampilkan potongan kehidupan yang sudah dipilih, diedit, dan sering kali hanya bagian terbaiknya. Membandingkan hari biasa kita dengan sorotan orang lain adalah perbandingan yang tidak seimbang sejak awal.</p>
<h3>Tanda jenuh digital</h3>
<ul>
<li>Sulit berhenti menggulir meskipun sudah bosan.</li>
<li>Merasa cemas bila tidak memeriksa ponsel beberapa menit.</li>
<li>Mudah tersinggung atau membandingkan diri setelah melihat unggahan tertentu.</li>
<li>Waktu tidur berkurang karena menonton atau membaca komentar.</li>
<li>Kehilangan minat pada kegiatan yang sebelumnya disukai.</li>
</ul>
<h3>Cara menjaga kesehatan mental di ruang digital</h3>
<ol>
<li><strong>Tetapkan batas waktu.</strong> Gunakan pengatur waktu atau fitur kesejahteraan digital untuk membatasi durasi membuka aplikasi tertentu.</li>
<li><strong>Bersihkan beranda.</strong> Berhenti mengikuti akun yang membuatmu merasa buruk, dan ikuti akun yang benar-benar bermanfaat.</li>
<li><strong>Buat ruang tanpa layar.</strong> Misalnya saat makan bersama keluarga atau satu jam sebelum tidur.</li>
<li><strong>Matikan notifikasi yang tidak perlu.</strong> Kamu tidak wajib mengetahui setiap pembaruan secara langsung.</li>
<li><strong>Ceritakan bila terasa berat.</strong> Bicara dengan orang yang dipercaya atau cari bantuan profesional bila perasaan tidak nyaman berlangsung lama.</li>
</ol>
<p>Berhenti sejenak dari media sosial bukan tanda kalah atau ketinggalan. Justru dengan memberi jarak, kita dapat menilai kembali apa yang benar-benar ingin kita lihat dan bagikan.</p>
<blockquote>Ruang digital yang sehat dimulai dari keputusan kecil untuk tidak selalu hadir di sana.</blockquote>
HTML
		),
		array(
			'slug'        => 'kredit-karya-digital',
			'title'       => 'Kredit Karya Digital: Menghargai Karya Orang Lain di Internet',
			'category'    => 'etika-digital',
			'date'        => '2026-11-05 09:00:00',
			'read'        => '5 menit baca',
			'tags'        => array( 'HakCipta', 'KreditKarya', 'LiterasiDigital' ),
			'image'       => 'artikel-kredit-karya.jpg',
			'image_alt'   => 'Seorang perempuan bekerja dengan laptop dan tablet digital untuk membuat karya desain',
			'source_url'  => 'https://www.pexels.com/photo/4348404/',
			'excerpt'     => 'Membagikan ulang bukan sekadar menekan tombol. Tanpa kredit dan izin, apresiasi bisa berubah menjadi pelanggaran.',
			'content'     => <<<'HTML'
<p>Internet memudahkan siapa saja menyimpan dan membagikan ulang karya orang lain. Foto, ilustrasi, musik, dan tulisan berpindah tangan dalam hitungan detik. Sayangnya, kemudahan itu sering membuat kita lupa bahwa di balik setiap karya ada waktu, keterampilan, dan hak yang perlu dihormati.</p>
<p>Memberi kredit bukan sekadar mencantumkan nama. Kredit yang baik juga menyertakan tautan ke karya asli dan tidak menghapus tanda air atau informasi pembuatnya. Namun perlu dipahami bahwa mencantumkan nama tidak otomatis memberi izin untuk menggunakan karya tersebut secara komersial.</p>
<h3>Hal yang perlu diperiksa sebelum membagikan karya</h3>
<ul>
<li><strong>Siapa pembuatnya?</strong> Cari nama atau akun asli, bukan akun yang mengunggah ulang tanpa sumber.</li>
<li><strong>Apa lisensinya?</strong> Beberapa karya mengizinkan penggunaan dengan syarat tertentu, sebagian lain melarang penggunaan komersial.</li>
<li><strong>Apakah sudah ada izin?</strong> Untuk penggunaan di luar batas wajar, sebaiknya minta izin langsung kepada pembuatnya.</li>
<li><strong>Apakah konteksnya berubah?</strong> Menggunakan karya untuk mendukung pesan yang bertentangan dengan maksud pembuatnya bisa merugikan mereka.</li>
</ul>
<h3>Cara sederhana menghargai karya</h3>
<p>Saat membagikan ulang, cantumkan nama pembuat, tautan sumber, dan jangan mengakuinya sebagai karya sendiri. Jika karya itu digunakan untuk keperluan usaha, pastikan lisensinya memang mengizinkan atau beli lisensi yang sesuai.</p>
<p>Menghargai karya orang lain juga berarti menghargai diri sendiri. Ketika kita terbiasa meminta izin dan memberi kredit, kita ikut menjaga ekosistem digital yang lebih adil bagi semua pembuat konten, termasuk kita di kemudian hari.</p>
<blockquote>Kredit bukan formalitas. Ia adalah cara paling sederhana untuk mengatakan bahwa karya seseorang berarti.</blockquote>
HTML
		),
	);

	foreach ( $articles as $article ) {
		$existing = get_page_by_path( $article['slug'], OBJECT, 'post' );

		if ( $existing ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'post_title'     => $article['title'],
				'post_name'      => $article['slug'],
				'post_content'   => wp_slash( $article['content'] ),
				'post_excerpt'   => wp_slash( $article['excerpt'] ),
				'post_date'      => $article['date'],
				'post_date_gmt'  => get_gmt_from_date( $article['date'] ),
				'post_author'    => $author_id,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		if ( isset( $category_ids[ $article['category'] ] ) ) {
			wp_set_post_terms( $post_id, array( $category_ids[ $article['category'] ] ), 'category', false );
		}

		wp_set_post_terms( $post_id, $article['tags'], 'post_tag', false );

		update_post_meta( $post_id, '_es_read_time', $article['read'] );

		if ( ! empty( $article['image'] ) ) {
			update_post_meta( $post_id, '_es_image', ES_URI . '/assets/img/' . $article['image'] );
			update_post_meta( $post_id, '_es_image_alt', $article['image_alt'] );
		}

		if ( ! empty( $article['source_url'] ) ) {
			update_post_meta( $post_id, '_es_source_label', 'Pexels' );
			update_post_meta( $post_id, '_es_source_url', $article['source_url'] );
		}
	}

	/* Hapus pos contoh bawaan agar tidak muncul di beranda. */
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );

	if ( $hello && false !== strpos( $hello->post_content, 'Welcome to WordPress' ) ) {
		wp_trash_post( $hello->ID );
	}

	if ( ! get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', 'Etika Bermedia Sosial yang Baik dan Benar' );
	}

	update_option( 'es_content_installed', 1 );
}
