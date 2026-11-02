<?php
/**
 * Piè di pagina, pulsante fisso per mobile e lightbox della galleria.
 */
?>
<footer class="footer">
	<div class="wrap footer__inner">
		<img src="<?php echo esc_url( vd_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="footer__logo" loading="lazy">
		<nav class="footer__nav" aria-label="Collegamenti a piè di pagina">
			<a href="<?php echo esc_url( vd_anchor( 'centro' ) ); ?>">Il centro</a>
			<a href="<?php echo esc_url( vd_anchor( 'prezzi' ) ); ?>">Prezzi</a>
			<a href="<?php echo esc_url( vd_anchor( 'corsi' ) ); ?>">Corsi</a>
			<?php vd_promo_menu_link(); ?>
			<a href="<?php echo esc_url( vd_anchor( 'tornei' ) ); ?>">Tornei</a>
			<a href="<?php echo esc_url( vd_anchor( 'galleria' ) ); ?>">Galleria</a>
			<a href="<?php echo esc_url( vd_anchor( 'dove' ) ); ?>">Dove siamo</a>
		</nav>
		<div class="footer__social">
			<?php if ( vd( 'instagram' ) ) : ?>
				<a href="<?php echo esc_url( vd( 'instagram' ) ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
					<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="ico-fill"/></svg>
				</a>
			<?php endif; ?>
			<?php if ( vd( 'facebook' ) ) : ?>
				<a href="<?php echo esc_url( vd( 'facebook' ) ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z"/></svg>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="wrap footer__bottom">
		<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php echo esc_html( vd( 'indirizzo' ) ); ?></p>
		<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
			<a class="footer__login" href="<?php echo esc_url( vd_pannello_url() ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Gestisci il sito</a>
		<?php else : ?>
			<a class="footer__login" href="<?php echo esc_url( wp_login_url() ); ?>" rel="nofollow"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg> Area riservata</a>
		<?php endif; ?>
	</div>
</footer>

<a href="<?php echo esc_url( vd( 'playtomic' ) ); ?>" class="fab" target="_blank" rel="noopener">Prenota il campo</a>

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Foto ingrandita" hidden>
	<button class="lightbox__close" aria-label="Chiudi">×</button>
	<button class="lightbox__nav lightbox__prev" aria-label="Foto precedente">‹</button>
	<img alt="">
	<button class="lightbox__nav lightbox__next" aria-label="Foto successiva">›</button>
</div>

<?php wp_footer(); ?>
</body>
</html>
