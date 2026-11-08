<?php
/**
 * Footer tema.
 *
 * @package EtikaSosial
 */

?>
<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-brand">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<span class="brand-mark" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-3.8-.8L3 21l1.9-4.6A8.4 8.4 0 0 1 12 3.1a8.4 8.4 0 0 1 9 8.4Z" />
						<path d="M8.5 10.5h7M8.5 14h4.5" />
					</svg>
				</span>
				<span class="brand-text">Etika<em>Sosial</em></span>
			</a>
			<p>Kumpulan artikel sederhana tentang etika bermedia sosial, privasi, dan literasi digital.</p>
		</div>

		<nav class="footer-nav" aria-label="Navigasi footer">
			<h2>Halaman</h2>
			<?php es_nav_menu( 'footer', '' ); ?>
		</nav>

		<div class="footer-credit">
			<h2>Tentang situs</h2>
			<p>Foto unggulan berasal dari <a href="https://www.pexels.com/license/" target="_blank" rel="noopener noreferrer">Pexels</a> dengan lisensi bebas royalti.</p>
			<p class="footer-copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> EtikaSosial.</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
