<?php
/**
 * Finestra "Padel Clinic": si apre toccando il cartello in evidenza dell'apertura.
 * Tutti i testi arrivano dal pannello (vd( 'clinic_*' )).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tel      = vd_tel();
$whatsapp = 'https://wa.me/' . preg_replace( '/\D/', '', vd( 'whatsapp' ) ) . '?text=' . rawurlencode(
	'Ciao! Vorrei un preventivo per una ' . vd( 'clinic_titolo' ) . '. Siamo in … persone, periodo: …, giorni: …'
);
$mesi     = array_filter( array_map( 'trim', explode( ',', vd( 'clinic_mesi' ) ) ) );
$inclusi  = array_filter( array_map( 'trim', explode( "\n", vd( 'clinic_inclusi' ) ) ) );
$prezzo   = vd_num( vd( 'clinic_prezzo' ) );
?>
<dialog class="clinic" id="clinic" aria-labelledby="clinic-titolo">
	<div class="clinic__box">
		<button type="button" class="clinic__close" data-chiudi aria-label="Chiudi">×</button>

		<header class="clinic__head" style="--foto: url('<?php echo esc_url( vd( 'clinic_img' ) ); ?>')">
			<?php if ( vd( 'promo_etichetta' ) ) : ?>
				<span class="clinic__bollino"><?php echo esc_html( vd( 'promo_etichetta' ) ); ?></span>
			<?php endif; ?>
			<p class="eyebrow">Valley d'Or · <?php echo esc_html( vd( 'hero_sotto' ) ? vd( 'hero_sotto' ) : 'Padel' ); ?></p>
			<h2 id="clinic-titolo"><?php echo esc_html( vd( 'clinic_titolo' ) ); ?></h2>
			<p class="clinic__slogan"><?php echo esc_html( vd( 'clinic_slogan' ) ); ?></p>
		</header>

		<div class="clinic__body">
			<p class="clinic__intro"><?php echo esc_html( vd( 'clinic_intro' ) ); ?></p>

			<ul class="clinic__numeri">
				<li><strong><?php echo esc_html( str_replace( '-', '–', vd( 'clinic_giorni' ) ) ); ?></strong><span>giorni di clinic</span></li>
				<li><strong><?php echo esc_html( vd( 'clinic_ore' ) ); ?>h</strong><span>di padel al giorno</span></li>
				<li><strong>FITP</strong><span>istruttore federale</span></li>
			</ul>

			<section class="clinic__sez">
				<h3>La giornata tipo</h3>
				<div class="clinic__giornata">
					<div class="clinic__fase">
						<span class="clinic__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></span>
						<div><b>Mattina</b><span><?php echo esc_html( vd( 'clinic_mattina' ) ); ?></span></div>
					</div>
					<div class="clinic__fase clinic__fase--sera">
						<span class="clinic__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z"/></svg></span>
						<div><b>Sera</b><span><?php echo esc_html( vd( 'clinic_sera' ) ); ?></span></div>
					</div>
				</div>
			</section>

			<?php if ( $mesi ) : ?>
				<section class="clinic__sez">
					<h3>Quando</h3>
					<ul class="clinic__mesi">
						<?php foreach ( $mesi as $m ) : ?>
							<li><?php echo esc_html( $m ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<section class="clinic__sez clinic__offerta">
				<div class="clinic__prezzo">
					<small>Quota a persona</small>
					<p><span>da</span> <strong><?php echo esc_html( null !== $prezzo ? vd_fmt( $prezzo ) : vd( 'clinic_prezzo' ) ); ?></strong><?php echo null !== $prezzo ? '€' : ''; ?></p>
					<small><?php echo esc_html( vd( 'clinic_prezzo_nota' ) ); ?></small>
				</div>
				<?php if ( $inclusi ) : ?>
					<ul class="clinic__inclusi">
						<?php foreach ( $inclusi as $voce ) : ?>
							<?php $no = 0 === strpos( $voce, '-' ); ?>
							<li class="<?php echo $no ? 'is-no' : ''; ?>"><?php echo esc_html( $no ? ltrim( $voce, '- ' ) : $voce ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>

			<?php if ( vd( 'clinic_locali' ) || vd( 'clinic_alloggi' ) ) : ?>
				<div class="clinic__servizi">
					<?php if ( vd( 'clinic_locali' ) ) : ?>
						<section class="clinic__servizio">
							<span class="clinic__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="8" cy="8" r="3"/><circle cx="16.5" cy="9" r="2.5"/><path d="M2.5 19c0-3 2.5-5.5 5.5-5.5s5.5 2.5 5.5 5.5M13.5 14.2A4.5 4.5 0 0 1 21.5 17"/></svg></span>
							<div><b>Partite con i locali</b><p><?php echo esc_html( vd( 'clinic_locali' ) ); ?></p></div>
						</section>
					<?php endif; ?>
					<?php if ( vd( 'clinic_alloggi' ) ) : ?>
						<section class="clinic__servizio">
							<span class="clinic__ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 11l9-7 9 7M5 10v10h14V10M10 20v-6h4v6"/></svg></span>
							<div><b>L'alloggio lo troviamo noi</b><p><?php echo esc_html( vd( 'clinic_alloggi' ) ); ?></p></div>
						</section>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<footer class="clinic__foot">
			<div>
				<b>Pronto a scendere in campo?</b>
				<p><?php echo esc_html( vd( 'clinic_finale' ) ); ?></p>
			</div>
			<div class="clinic__cta">
				<a href="<?php echo esc_url( $whatsapp ); ?>" class="btn btn--lime" target="_blank" rel="noopener">Richiedi un preventivo</a>
				<a href="tel:<?php echo esc_attr( $tel ); ?>" class="btn btn--ghost">Chiama</a>
			</div>
		</footer>
	</div>
</dialog>
