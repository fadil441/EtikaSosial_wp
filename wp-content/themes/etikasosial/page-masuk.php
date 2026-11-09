<?php
/**
 * Template halaman masuk dan pendaftaran akun.
 *
 * @package EtikaSosial
 */

if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();

	nocache_headers();
	get_header();
	?>
	<main id="mainContent" class="site-main">
		<div class="container narrow">
			<div class="login-card">
				<p class="eyebrow">Akun</p>
				<h1>Anda sudah masuk</h1>
				<p>Masuk sebagai <strong><?php echo esc_html( $current_user->display_name ); ?></strong>.</p>

				<div class="login-actions">
					<?php if ( current_user_can( 'edit_posts' ) ) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Kelola artikel</a>
					<?php endif; ?>
					<?php if ( current_user_can( 'edit_pages' ) ) : ?>
						<a class="btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Kelola halaman</a>
					<?php endif; ?>
					<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke beranda</a>
					<a class="btn" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">Keluar</a>
				</div>
			</div>
		</div>
	</main>
	<?php
	get_footer();
	return;
}

$login_error       = '';
$register_error    = '';
$login_value       = '';
$register_username = '';
$register_email    = '';
$action            = 'login';

if ( isset( $_POST['es_action'] ) ) {
	$action = sanitize_key( wp_unslash( $_POST['es_action'] ) );
}

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	if ( 'register' === $action ) {
		$nonce = isset( $_POST['es_register_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['es_register_nonce'] ) ) : '';

		$register_username = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ), true ) : '';
		$register_email    = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
		$password          = isset( $_POST['user_pass'] ) ? wp_unslash( $_POST['user_pass'] ) : '';
		$password_confirm  = isset( $_POST['user_pass_confirm'] ) ? wp_unslash( $_POST['user_pass_confirm'] ) : '';
		$honeypot          = isset( $_POST['website'] ) ? trim( wp_unslash( $_POST['website'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'es_register' ) ) {
			$register_error = 'Sesi formulir kedaluwarsa. Silakan coba lagi.';
		} elseif ( '' !== $honeypot ) {
			$register_error = 'Pendaftaran tidak dapat diproses.';
		} elseif ( '' === $register_username || '' === $register_email || '' === $password ) {
			$register_error = 'Isi nama pengguna, email, dan kata sandi terlebih dahulu.';
		} elseif ( strlen( $register_username ) < 3 || strlen( $register_username ) > 60 ) {
			$register_error = 'Nama pengguna harus terdiri dari 3 sampai 60 karakter.';
		} elseif ( ! validate_username( $register_username ) ) {
			$register_error = 'Nama pengguna hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.';
		} elseif ( username_exists( $register_username ) ) {
			$register_error = 'Nama pengguna sudah digunakan. Pilih nama lain.';
		} elseif ( ! is_email( $register_email ) ) {
			$register_error = 'Alamat email tidak valid.';
		} elseif ( email_exists( $register_email ) ) {
			$register_error = 'Email sudah terdaftar. Silakan masuk atau gunakan email lain.';
		} elseif ( strlen( $password ) < 8 ) {
			$register_error = 'Kata sandi minimal 8 karakter.';
		} elseif ( $password !== $password_confirm ) {
			$register_error = 'Ulangi kata sandi dengan benar.';
		} else {
			$user_id = wp_create_user( $register_username, $password, $register_email );

			if ( is_wp_error( $user_id ) ) {
				$register_error = 'Akun gagal dibuat. Silakan coba lagi.';
			} else {
				$user = new WP_User( $user_id );
				$user->set_role( 'subscriber' );

				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id, false, is_ssl() );
				wp_safe_redirect( home_url( '/' ) );
				exit;
			}
		}
	} else {
		$nonce = isset( $_POST['es_login_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['es_login_nonce'] ) ) : '';

		$login_value = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
		$password    = isset( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'es_login' ) ) {
			$login_error = 'Sesi formulir kedaluwarsa. Silakan coba lagi.';
		} elseif ( '' === $login_value || '' === $password ) {
			$login_error = 'Isi nama pengguna dan kata sandi terlebih dahulu.';
		} else {
			$user = wp_signon(
				array(
					'user_login'    => $login_value,
					'user_password' => $password,
					'remember'      => ! empty( $_POST['rememberme'] ),
				),
				is_ssl()
			);

			if ( is_wp_error( $user ) ) {
				$login_error = 'Nama pengguna atau kata sandi tidak cocok. Silakan coba lagi.';
			} else {
				$destination = user_can( $user, 'edit_posts' ) ? admin_url() : home_url( '/' );

				wp_safe_redirect( $destination );
				exit;
			}
		}
	}
}

nocache_headers();
get_header();
?>

<main id="mainContent" class="site-main">
	<div class="container">
		<div class="account-grid">
			<div class="login-card" id="masuk">
				<p class="eyebrow">Akun</p>
				<h1>Masuk ke EtikaSosial</h1>

				<?php if ( $login_error ) : ?>
					<?php es_notice( 'error', 'Gagal masuk', $login_error ); ?>
				<?php endif; ?>

				<form class="login-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>">
					<input type="hidden" name="es_action" value="login">
					<?php wp_nonce_field( 'es_login', 'es_login_nonce' ); ?>

					<div class="field">
						<label for="user_login">Nama pengguna atau email</label>
						<input type="text" name="log" id="user_login" autocomplete="username" value="<?php echo esc_attr( $login_value ); ?>" required>
					</div>

					<div class="field">
						<label for="user_pass">Kata sandi</label>
						<input type="password" name="pwd" id="user_pass" autocomplete="current-password" required>
					</div>

					<label class="checkbox">
						<input type="checkbox" name="rememberme" value="forever">
						Ingat saya di perangkat ini
					</label>

					<button class="btn btn-primary btn-block" type="submit">Masuk</button>
				</form>

				<p class="login-help">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Lupa kata sandi?</a>
					· <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke beranda</a>
					· <a href="#buat-akun">Buat akun</a>
				</p>
			</div>

			<div class="login-card" id="buat-akun">
				<p class="eyebrow">Akun baru</p>
				<h2>Buat akun</h2>
				<p>Daftar untuk mulai membaca artikel.</p>

				<?php if ( $register_error ) : ?>
					<?php es_notice( 'error', 'Gagal membuat akun', $register_error ); ?>
				<?php endif; ?>

				<form class="login-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>">
					<input type="hidden" name="es_action" value="register">
					<?php wp_nonce_field( 'es_register', 'es_register_nonce' ); ?>

					<div class="hp-field" aria-hidden="true">
						<label for="website">Website</label>
						<input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
					</div>

					<div class="field">
						<label for="register_login">Nama pengguna</label>
						<input type="text" name="user_login" id="register_login" autocomplete="username" value="<?php echo esc_attr( $register_username ); ?>" required>
					</div>

					<div class="field">
						<label for="register_email">Email</label>
						<input type="email" name="user_email" id="register_email" autocomplete="email" value="<?php echo esc_attr( $register_email ); ?>" required>
					</div>

					<div class="field">
						<label for="register_pass">Kata sandi</label>
						<input type="password" name="user_pass" id="register_pass" autocomplete="new-password" required>
						<span class="field-hint">Minimal 8 karakter.</span>
					</div>

					<div class="field">
						<label for="register_pass_confirm">Ulangi kata sandi</label>
						<input type="password" name="user_pass_confirm" id="register_pass_confirm" autocomplete="new-password" required>
					</div>

					<button class="btn btn-primary btn-block" type="submit">Buat akun</button>
				</form>

				<p class="login-help">
					Sudah punya akun? <a href="#masuk">Masuk di sini</a>.
				</p>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
