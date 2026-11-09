<?php
/**
 * Template halaman Kontak.
 *
 * @package EtikaSosial
 */

get_header();

$form   = array(
	'name'    => '',
	'email'   => '',
	'subject' => '',
	'message' => '',
);
$reason = '';

if ( isset( $_GET['reason'] ) ) {
	$reason = sanitize_key( wp_unslash( $_GET['reason'] ) );
}

$flash = es_get_flash_from_request( 'ft' );

if ( ! empty( $flash['reason'] ) ) {
	$reason = sanitize_key( $flash['reason'] );
}

if ( ! empty( $flash['data'] ) && is_array( $flash['data'] ) ) {
	$form = array_merge( $form, $flash['data'] );
}

$token = es_form_token();
?>

<main id="mainContent" class="site-main">
	<div class="container narrow">
		<header class="page-header">
			<h1>Kontak</h1>
			<p>Kirim pertanyaan, masukan, atau kerja sama melalui formulir di bawah ini.</p>
		</header>

		<div class="contact-layout">
			<aside class="contact-info">
				<h2>Informasi kontak</h2>
				<p><strong>Surel:</strong><br><a href="mailto:EtikaSosial@gmail.com">EtikaSosial@gmail.com</a></p>
				<p><strong>Lokasi:</strong><br>Surabaya, Indonesia</p>
				<p><strong>Jam layanan:</strong><br>Senin–Jumat, 08.00–20.00 WIB</p>
			</aside>

			<form class="contact-form" id="contactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<h2>Kirim pesan</h2>

				<?php if ( isset( $_GET['sent'] ) ) : ?>
					<?php es_notice( 'success', 'Pesan terkirim', 'Terima kasih. Pesanmu sudah kami terima.' ); ?>
				<?php elseif ( 'error' === ( isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '' ) ) : ?>
					<?php es_notice( 'error', 'Pesan belum terkirim', es_contact_error_message( $reason ) ); ?>
				<?php endif; ?>

				<input type="hidden" name="action" value="es_contact">
				<input type="hidden" name="es_token" value="<?php echo esc_attr( $token ); ?>">
				<?php wp_nonce_field( 'es_contact' ); ?>

				<div class="form-row">
					<div class="field">
						<label for="contactName">Nama lengkap</label>
						<input id="contactName" name="name" type="text" placeholder="Nama kamu" value="<?php echo esc_attr( $form['name'] ); ?>" required minlength="2">
					</div>
					<div class="field">
						<label for="contactEmail">Alamat surel</label>
						<input id="contactEmail" name="email" type="email" placeholder="nama@email.com" value="<?php echo esc_attr( $form['email'] ); ?>" required>
					</div>
				</div>
				<div class="field">
					<label for="contactSubject">Subjek</label>
					<input id="contactSubject" name="subject" type="text" placeholder="Misalnya: pertanyaan tentang artikel" value="<?php echo esc_attr( $form['subject'] ); ?>" required minlength="3">
				</div>
				<div class="field">
					<label for="contactMessage">Pesan</label>
					<textarea id="contactMessage" name="message" rows="6" placeholder="Tuliskan pesanmu di sini..." required minlength="20" maxlength="1000"><?php echo esc_textarea( $form['message'] ); ?></textarea>
					<span class="field-hint">Minimal 20 karakter, maksimal 1000 karakter.</span>
				</div>
				<label class="checkbox">
					<input id="contactConsent" name="consent" type="checkbox" required>
					<span>Saya setuju pesan ini disimpan untuk keperluan tindak lanjut.</span>
				</label>
				<button class="btn btn-primary btn-block" type="submit">Kirim Pesan</button>
			</form>
		</div>
	</div>
</main>

<?php
get_footer();
