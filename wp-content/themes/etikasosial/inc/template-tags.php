<?php
/**
 * Helper tampilan, navigasi, tiket, dan format tanggal.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Navigasi & URL
 * ---------------------------------------------------------------------- */

/**
 * URL halaman berdasarkan kunci.
 *
 * @param string $key Kunci halaman (beranda, tentang, kontak, artikel).
 * @return string
 */
function es_page_url( $key ) {
	$slugs = array(
		'beranda' => 'beranda',
		'tentang' => 'tentang-saya',
		'kontak'  => 'kontak',
		'artikel' => 'artikel',
		'masuk'   => 'masuk',
	);

	$page_id = (int) get_option( 'es_page_' . $key, 0 );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}

	$slug = isset( $slugs[ $key ] ) ? $slugs[ $key ] : $key;
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return get_permalink( $page );
	}

	return home_url( '/' . $slug . '/' );
}

/**
 * Apakah halaman saat ini adalah halaman dengan kunci tertentu.
 *
 * @param string $key Kunci halaman.
 * @return bool
 */
function es_is_page_key( $key ) {
	if ( ! is_page() ) {
		return false;
	}

	$page_id = (int) get_option( 'es_page_' . $key, 0 );
	if ( $page_id ) {
		return get_queried_object_id() === $page_id;
	}

	$slugs = array(
		'beranda' => 'beranda',
		'tentang' => 'tentang-saya',
		'kontak'  => 'kontak',
		'artikel' => 'artikel',
		'masuk'   => 'masuk',
	);

	return isset( $slugs[ $key ] ) && is_page( $slugs[ $key ] );
}

/**
 * URL halaman masuk, dengan tujuan setelah berhasil masuk.
 *
 * @param string $redirect Tujuan redirect.
 * @return string
 */
function es_login_url( $redirect = '' ) {
	$url = es_page_url( 'masuk' );

	if ( $redirect ) {
		$url = add_query_arg( 'redirect_to', $redirect, $url );
	}

	return $url;
}

/**
 * URL saat ini tanpa query string.
 *
 * @return string
 */
function es_current_url() {
	global $wp;

	$request = isset( $wp->request ) ? $wp->request : '';

	return home_url( '/' . ltrim( $request, '/' ) );
}

/**
 * Periksa apakah item menu sedang aktif.
 *
 * @param WP_Post $item Item menu.
 * @return bool
 */
function es_menu_item_is_active( $item ) {
	if ( is_front_page() && untrailingslashit( $item->url ) === untrailingslashit( home_url( '/' ) ) ) {
		return true;
	}

	if ( 'post_type' === $item->type && 'page' === $item->object && is_page( (int) $item->object_id ) ) {
		return true;
	}

	if ( is_home() && 'page' === $item->object && (int) $item->object_id === (int) get_option( 'page_for_posts' ) ) {
		return true;
	}

	if ( 'custom' === $item->type ) {
		return untrailingslashit( es_current_url() ) === untrailingslashit( $item->url );
	}

	return false;
}

/**
 * Tampilkan menu berdasarkan lokasi, dengan filter peran.
 *
 * @param string $location   Lokasi menu.
 * @param string $link_class Kelas untuk tautan.
 */
function es_nav_menu( $location = 'primary', $link_class = 'nav-link' ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		es_nav_fallback( $location, $link_class );
		return;
	}

	$menu = wp_get_nav_menu_object( $locations[ $location ] );
	if ( ! $menu ) {
		es_nav_fallback( $location, $link_class );
		return;
	}

	$items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
	if ( ! $items ) {
		es_nav_fallback( $location, $link_class );
		return;
	}

	$is_admin    = current_user_can( 'manage_options' );
	$is_logged_in = is_user_logged_in();

	foreach ( $items as $item ) {
		$classes = (array) $item->classes;

		if ( in_array( 'es-nav-admin', $classes, true ) && ! $is_admin ) {
			continue;
		}
		if ( in_array( 'es-nav-user', $classes, true ) && ( ! $is_logged_in || $is_admin ) ) {
			continue;
		}
		if ( in_array( 'es-nav-nonadmin', $classes, true ) && $is_admin ) {
			continue;
		}
		if ( in_array( 'es-nav-guest', $classes, true ) && $is_logged_in ) {
			continue;
		}

		$active     = es_menu_item_is_active( $item );
		$class_attr = trim( $link_class . ( $active ? ' is-active' : '' ) );

		printf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			esc_attr( $class_attr ),
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}

}

/**
 * Menu cadangan bila menu WordPress belum dibuat.
 *
 * @param string $location   Lokasi menu.
 * @param string $link_class Kelas tautan.
 */
function es_nav_fallback( $location, $link_class ) {
	$links = array(
		array( 'Beranda', home_url( '/' ), '' ),
		array( 'Artikel', es_page_url( 'artikel' ), '' ),
		array( 'Tentang Saya', es_page_url( 'tentang' ), '' ),
		array( 'Kontak', es_page_url( 'kontak' ), '' ),
	);

	foreach ( $links as $link ) {
		printf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			esc_attr( $link_class ),
			esc_url( $link[1] ),
			esc_html( $link[0] )
		);
	}

}

/* -------------------------------------------------------------------------
 * Format & pengguna
 * ---------------------------------------------------------------------- */

/**
 * Inisial nama untuk avatar teks.
 *
 * @param string $name Nama.
 * @return string
 */
function es_initials( $name = '' ) {
	$name = preg_replace( '/[^\p{L}\p{N}\s]/u', ' ', (string) $name );
	$name = preg_split( '/\s+/', trim( $name ) );

	$initials = '';
	foreach ( array_slice( array_filter( $name ), 0, 2 ) as $part ) {
		$initials .= mb_substr( $part, 0, 1 );
	}

	return $initials ? mb_strtoupper( $initials ) : 'U';
}

/**
 * Nama depan pengguna.
 *
 * @param WP_User|null $user Pengguna.
 * @return string
 */
function es_first_name( $user = null ) {
	if ( ! $user instanceof WP_User ) {
		$user = wp_get_current_user();
	}

	if ( ! $user || ! $user->exists() ) {
		return 'teman';
	}

	$name  = $user->first_name ? $user->first_name : $user->display_name;
	$parts = preg_split( '/\s+/', trim( $name ) );

	return ! empty( $parts[0] ) ? $parts[0] : 'teman';
}

/**
 * Format tanggal Indonesia sederhana.
 *
 * @param int|null $timestamp Unix timestamp.
 * @return string
 */
function es_format_date( $timestamp = null ) {
	$timestamp = null === $timestamp ? time() : (int) $timestamp;
	$months    = array(
		1  => 'Jan',
		2  => 'Feb',
		3  => 'Mar',
		4  => 'Apr',
		5  => 'Mei',
		6  => 'Jun',
		7  => 'Jul',
		8  => 'Agu',
		9  => 'Sep',
		10 => 'Okt',
		11 => 'Nov',
		12 => 'Des',
	);

	$month = (int) wp_date( 'n', $timestamp );

	return wp_date( 'j', $timestamp ) . ' ' . $months[ $month ] . ' ' . wp_date( 'Y', $timestamp );
}

/**
 * Format tanggal dan waktu Indonesia.
 *
 * @param int|null $timestamp Unix timestamp.
 * @return string
 */
function es_format_datetime( $timestamp = null ) {
	$timestamp = null === $timestamp ? time() : (int) $timestamp;

	return es_format_date( $timestamp ) . ' · ' . wp_date( 'H:i', $timestamp );
}

/* -------------------------------------------------------------------------
 * Flash message (tanpa menyimpan teks bebas di URL)
 * ---------------------------------------------------------------------- */

/**
 * Simpan data sementara untuk ditampilkan setelah redirect.
 *
 * @param string $token Token formulir.
 * @param array  $data  Data.
 * @return string
 */
function es_flash_put( $token, $data ) {
	$token = sanitize_key( $token );

	if ( ! $token ) {
		return '';
	}

	set_transient( 'es_flash_' . $token, $data, 10 * MINUTE_IN_SECONDS );

	return $token;
}

/**
 * Ambil dan hapus data sementara.
 *
 * @param string $token Token formulir.
 * @return array
 */
function es_flash_get( $token ) {
	$token = sanitize_key( $token );

	if ( ! $token ) {
		return array();
	}

	$data = get_transient( 'es_flash_' . $token );
	delete_transient( 'es_flash_' . $token );

	return is_array( $data ) ? $data : array();
}

/**
 * Buat token formulir.
 *
 * @return string
 */
function es_form_token() {
	return strtolower( wp_generate_password( 20, false, false ) );
}

/* -------------------------------------------------------------------------
 * Tiket
 * ---------------------------------------------------------------------- */

/**
 * Daftar kategori tiket.
 *
 * @return array
 */
function es_ticket_categories() {
	return array(
		'Ujaran kebencian',
		'Perundungan siber',
		'Hoaks & fitnah',
		'Privasi & data pribadi',
		'Hak cipta konten',
		'Spam & penipuan',
		'Keamanan akun',
		'Lainnya',
	);
}

/**
 * Label status tiket.
 *
 * @param string $status Status.
 * @return string
 */
function es_ticket_status_label( $status ) {
	$labels = array(
		'open'     => 'Terbuka',
		'answered' => 'Dijawab',
		'closed'   => 'Selesai',
	);

	return isset( $labels[ $status ] ) ? $labels[ $status ] : 'Terbuka';
}

/**
 * Kelas CSS status tiket.
 *
 * @param string $status Status.
 * @return string
 */
function es_ticket_status_class( $status ) {
	$classes = array(
		'open'     => 'status-pill--open',
		'answered' => 'status-pill--answered',
		'closed'   => 'status-pill--closed',
	);

	return isset( $classes[ $status ] ) ? $classes[ $status ] : 'status-pill--open';
}

/**
 * Nomor tiket yang mudah dibaca.
 *
 * @param int $ticket_id ID tiket.
 * @return string
 */
function es_ticket_number( $ticket_id ) {
	return 'TKT-' . str_pad( (string) absint( $ticket_id ), 4, '0', STR_PAD_LEFT );
}

/**
 * Status tiket.
 *
 * @param int $ticket_id ID tiket.
 * @return string
 */
function es_ticket_status( $ticket_id ) {
	$status = get_post_meta( $ticket_id, '_es_ticket_status', true );

	return in_array( $status, array( 'open', 'answered', 'closed' ), true ) ? $status : 'open';
}

/**
 * Balasan tiket.
 *
 * @param int $ticket_id ID tiket.
 * @return array
 */
function es_ticket_replies( $ticket_id ) {
	$replies = get_post_meta( $ticket_id, '_es_ticket_replies', true );

	return is_array( $replies ) ? $replies : array();
}

/**
 * Ambil tiket sesuai filter.
 *
 * @param array $args Argumen.
 * @return WP_Query
 */
function es_get_tickets( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'user_id' => 0,
			'status'  => '',
			'limit'   => -1,
		)
	);

	$query_args = array(
		'post_type'      => 'ticket',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $args['limit'],
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => array(),
	);

	if ( $args['user_id'] ) {
		$query_args['meta_query'][] = array(
			'key'     => '_es_ticket_user',
			'value'   => (int) $args['user_id'],
			'compare' => '=',
			'type'    => 'NUMERIC',
		);
	}

	if ( $args['status'] && in_array( $args['status'], array( 'open', 'answered', 'closed' ), true ) ) {
		$query_args['meta_query'][] = array(
			'key'     => '_es_ticket_status',
			'value'   => $args['status'],
			'compare' => '=',
		);
	}

	if ( empty( $query_args['meta_query'] ) ) {
		unset( $query_args['meta_query'] );
	}

	return new WP_Query( $query_args );
}

/**
 * Hitung statistik tiket.
 *
 * @param int $user_id Batasi ke pengguna tertentu, 0 untuk semua.
 * @return array
 */
function es_ticket_counts( $user_id = 0 ) {
	$query  = es_get_tickets( array( 'user_id' => $user_id ) );
	$counts = array(
		'total'    => 0,
		'open'     => 0,
		'answered' => 0,
		'closed'   => 0,
	);

	foreach ( $query->posts as $ticket ) {
		$status = es_ticket_status( $ticket->ID );
		$counts['total']++;
		if ( isset( $counts[ $status ] ) ) {
			$counts[ $status ]++;
		}
	}

	return $counts;
}

/**
 * Tampilkan kotak statistik tiket.
 *
 * @param array $counts Statistik.
 */
function es_stat_grid( $counts ) {
	?>
	<div class="stat-grid" aria-label="Ringkasan tiket">
		<div class="stat-card">
			<span class="stat-label">Total tiket</span>
			<span class="stat-value"><?php echo esc_html( (string) $counts['total'] ); ?></span>
		</div>
		<div class="stat-card stat-card--gold">
			<span class="stat-label">Terbuka</span>
			<span class="stat-value"><?php echo esc_html( (string) $counts['open'] ); ?></span>
		</div>
		<div class="stat-card stat-card--mint">
			<span class="stat-label">Dijawab</span>
			<span class="stat-value"><?php echo esc_html( (string) $counts['answered'] ); ?></span>
		</div>
		<div class="stat-card">
			<span class="stat-label">Selesai</span>
			<span class="stat-value"><?php echo esc_html( (string) $counts['closed'] ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * URL tombol buat tiket sesuai peran.
 *
 * @return string
 */
function es_ticket_button_url() {
	if ( current_user_can( 'manage_options' ) ) {
		return es_page_url( 'panel_cs' );
	}

	if ( is_user_logged_in() ) {
		return es_page_url( 'dasbor' );
	}

	return es_login_url( es_page_url( 'dasbor' ) );
}

/* -------------------------------------------------------------------------
 * Artikel
 * ---------------------------------------------------------------------- */

/**
 * URL gambar unggulan artikel.
 *
 * @param int $post_id ID pos.
 * @return string
 */
function es_get_post_image( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$image   = get_post_meta( $post_id, '_es_image', true );

	if ( $image ) {
		return $image;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail_url( $post_id, 'es-card' );
	}

	return '';
}

/**
 * Alt gambar unggulan artikel.
 *
 * @param int $post_id ID pos.
 * @return string
 */
function es_get_post_image_alt( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$alt     = get_post_meta( $post_id, '_es_image_alt', true );

	if ( $alt ) {
		return $alt;
	}

	$thumbnail_id = get_post_thumbnail_id( $post_id );
	if ( $thumbnail_id ) {
		return (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
	}

	return get_the_title( $post_id );
}

/**
 * Estimasi waktu baca.
 *
 * @param int $post_id ID pos.
 * @return string
 */
function es_get_post_read_time( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$read    = get_post_meta( $post_id, '_es_read_time', true );

	return $read ? $read : '4 menit baca';
}

/**
 * Sumber gambar artikel.
 *
 * @param int $post_id ID pos.
 * @return array
 */
function es_get_post_source( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return array(
		'label' => get_post_meta( $post_id, '_es_source_label', true ),
		'url'   => get_post_meta( $post_id, '_es_source_url', true ),
	);
}

/**
 * Kategori utama artikel.
 *
 * @param int $post_id ID pos.
 * @return WP_Term|null
 */
function es_primary_category( $post_id = 0 ) {
	$post_id    = $post_id ? $post_id : get_the_ID();
	$categories = get_the_category( $post_id );

	return ! empty( $categories ) ? $categories[0] : null;
}

/* -------------------------------------------------------------------------
 * Notifikasi formulir
 * ---------------------------------------------------------------------- */

/**
 * Tampilkan kotak notifikasi.
 *
 * @param string $type    Jenis: success, error, info.
 * @param string $title   Judul singkat.
 * @param string $message Isi pesan.
 */
function es_notice( $type, $title, $message = '' ) {
	if ( ! $message && ! $title ) {
		return;
	}

	$type = in_array( $type, array( 'success', 'error', 'info' ), true ) ? $type : 'info';
	?>
	<div class="form-notice form-notice--<?php echo esc_attr( $type ); ?>" role="<?php echo 'error' === $type ? 'alert' : 'status'; ?>">
		<?php if ( $title ) : ?>
			<strong><?php echo esc_html( $title ); ?></strong>
		<?php endif; ?>
		<?php if ( $message ) : ?>
			<p><?php echo esc_html( $message ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Ambil data flash dari token formulir di URL.
 *
 * @param string $key Nama parameter URL, misalnya ft.
 * @return array
 */
function es_get_flash_from_request( $key = 'ft' ) {
	if ( empty( $_GET[ $key ] ) ) {
		return array();
	}

	$token = sanitize_key( wp_unslash( $_GET[ $key ] ) );

	return $token ? es_flash_get( $token ) : array();
}
