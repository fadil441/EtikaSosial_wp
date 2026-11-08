<?php
/**
 * Pesan kontak: CPT, formulir, dan tampilan admin.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Daftarkan CPT pesan kontak.
 */
function es_register_contact_post_type() {
	register_post_type(
		'contact_message',
		array(
			'labels'              => array(
				'name'               => 'Pesan Kontak',
				'singular_name'      => 'Pesan Kontak',
				'menu_name'          => 'Pesan Kontak',
				'edit_item'          => 'Lihat Pesan',
				'view_item'          => 'Lihat Pesan',
				'search_items'       => 'Cari Pesan',
				'not_found'          => 'Belum ada pesan.',
				'not_found_in_trash' => 'Tidak ada pesan di sampah.',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'supports'            => array( 'title', 'editor' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'es_register_contact_post_type' );

/**
 * Pesan kesalahan formulir kontak.
 *
 * @param string $reason Kode.
 * @return string
 */
function es_contact_error_message( $reason ) {
	$messages = array(
		'name'    => 'Nama lengkap minimal 2 karakter.',
		'email'   => 'Alamat surel tidak valid.',
		'subject' => 'Subjek minimal 3 karakter.',
		'message' => 'Pesan minimal 20 karakter dan maksimal 1000 karakter.',
		'consent' => 'Centang persetujuan sebelum mengirim pesan.',
		'failed'  => 'Pesan gagal disimpan. Silakan coba lagi.',
	);

	return isset( $messages[ $reason ] ) ? $messages[ $reason ] : 'Pesan gagal dikirim. Silakan coba lagi.';
}

/**
 * Proses formulir kontak.
 */
function es_handle_contact() {
	check_admin_referer( 'es_contact' );

	$token   = isset( $_POST['es_token'] ) ? sanitize_key( wp_unslash( $_POST['es_token'] ) ) : '';
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$consent = isset( $_POST['consent'] );
	$data    = array(
		'name'    => $name,
		'email'   => $email,
		'subject' => $subject,
		'message' => $message,
	);

	$reason = '';

	if ( mb_strlen( $name ) < 2 ) {
		$reason = 'name';
	} elseif ( ! is_email( $email ) ) {
		$reason = 'email';
	} elseif ( mb_strlen( $subject ) < 3 ) {
		$reason = 'subject';
	} elseif ( mb_strlen( $message ) < 20 || mb_strlen( $message ) > 1000 ) {
		$reason = 'message';
	} elseif ( ! $consent ) {
		$reason = 'consent';
	}

	if ( $reason ) {
		$args = array(
			'contact' => 'error',
			'reason'  => $reason,
		);

		if ( $token ) {
			es_flash_put( $token, array( 'reason' => $reason, 'data' => $data ) );
			$args['ft'] = $token;
		}

		wp_safe_redirect( add_query_arg( $args, es_page_url( 'kontak' ) ) );
		exit;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'contact_message',
			'post_status'  => 'publish',
			'post_title'   => $subject,
			'post_content' => $message,
			'post_author'  => get_current_user_id(),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		$args = array(
			'contact' => 'error',
			'reason'  => 'failed',
		);

		if ( $token ) {
			es_flash_put( $token, array( 'reason' => 'failed', 'data' => $data ) );
			$args['ft'] = $token;
		}

		wp_safe_redirect( add_query_arg( $args, es_page_url( 'kontak' ) ) );
		exit;
	}

	update_post_meta( $post_id, '_es_contact_name', $name );
	update_post_meta( $post_id, '_es_contact_email', $email );
	update_post_meta( $post_id, '_es_contact_consent', $consent ? '1' : '0' );

	wp_safe_redirect( add_query_arg( 'sent', '1', es_page_url( 'kontak' ) ) );
	exit;
}
add_action( 'admin_post_nopriv_es_contact', 'es_handle_contact' );
add_action( 'admin_post_es_contact', 'es_handle_contact' );

/**
 * Kolom daftar pesan kontak.
 *
 * @param array $columns Kolom.
 * @return array
 */
function es_contact_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => 'Subjek',
		'es_name'    => 'Nama',
		'es_email'   => 'Surel',
		'date'       => 'Tanggal',
	);
}
add_filter( 'manage_contact_message_posts_columns', 'es_contact_columns' );

/**
 * Isi kolom kustom pesan kontak.
 *
 * @param string $column  Nama kolom.
 * @param int    $post_id ID pos.
 */
function es_contact_custom_column( $column, $post_id ) {
	if ( 'es_name' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_es_contact_name', true ) );
	}

	if ( 'es_email' === $column ) {
		$email = get_post_meta( $post_id, '_es_contact_email', true );
		if ( $email ) {
			echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
		} else {
			echo '—';
		}
	}
}
add_action( 'manage_contact_message_posts_custom_column', 'es_contact_custom_column', 10, 2 );

/**
 * Meta box detail pesan kontak.
 */
function es_contact_meta_box() {
	add_meta_box(
		'es_contact_details',
		'Detail Pengirim',
		'es_render_contact_meta_box',
		'contact_message',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'es_contact_meta_box' );

/**
 * Tampilan meta box pesan kontak.
 *
 * @param WP_Post $post Pos.
 */
function es_render_contact_meta_box( $post ) {
	$name    = get_post_meta( $post->ID, '_es_contact_name', true );
	$email   = get_post_meta( $post->ID, '_es_contact_email', true );
	$consent = get_post_meta( $post->ID, '_es_contact_consent', true );
	?>
	<p><strong>Nama:</strong><br><?php echo esc_html( $name ? $name : '—' ); ?></p>
	<p><strong>Surel:</strong><br>
		<?php if ( $email ) : ?>
			<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
		<?php else : ?>
			—
		<?php endif; ?>
	</p>
	<p><strong>Persetujuan:</strong><br><?php echo '1' === $consent ? 'Disetujui' : 'Tidak dicatat'; ?></p>
	<?php
}
