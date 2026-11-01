<?php
/**
 * Home page: tutte le sezioni del centro in un'unica pagina.
 */

get_header();

$playtomic = vd( 'playtomic' );
$whatsapp  = 'https://wa.me/' . preg_replace( '/\D/', '', vd( 'whatsapp' ) );
$freccia   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

// Galleria: le foto vuote (rimosse da Personalizza) vengono saltate.
$galleria = array();
for ( $i = 1; $i <= 9; $i++ ) {
	if ( vd( 'galleria_' . $i ) ) {
		$galleria[] = vd( 'galleria_' . $i );
	}
}
$formati = array( 'gallery__item--wide', 'gallery__item--tall', '', '', '', '', '', '', 'gallery__item--wide' );

$sponsor = array();
for ( $i = 1; $i <= 12; $i++ ) {
	if ( vd( 'sponsor_' . $i ) ) {
		$sponsor[] = vd( 'sponsor_' . $i );
	}
}
?>

<main>

	<!-- ============ APERTURA ============ -->
	<section class="hero">
		<div class="hero__bg" data-parallax="0.25">
			<img src="<?php echo esc_url( vd( 'hero_img' ) ); ?>" alt="Vista aerea dei campi Valley d'Or con il mare sullo sfondo">
		</div>
		<div class="hero__shade"></div>

		<div class="hero__content wrap">
			<p class="eyebrow hero__eyebrow"><span class="dot"></span> <?php echo esc_html( vd( 'hero_luogo' ) ); ?></p>
			<h1 class="hero__title">
				<span class="hero__name">
					<span class="sr-only"><?php echo esc_html( vd( 'hero_nome' ) ); ?></span>
					<?php
					// Il nome entra lettera per lettera; l'ultima parola ("d'Or") è evidenziata.
					$parole = preg_split( '/\s+/', trim( vd( 'hero_nome' ) ) );
					$n      = 0;
					foreach ( $parole as $k => $parola ) {
						$classe = ( $k === count( $parole ) - 1 && count( $parole ) > 1 ) ? 'w w--or' : 'w';
						echo '<span class="' . $classe . '" aria-hidden="true">';
						foreach ( preg_split( '//u', $parola, -1, PREG_SPLIT_NO_EMPTY ) as $c ) {
							echo '<span class="ch" style="--i:' . (int) $n++ . '">' . esc_html( $c ) . '</span>';
						}
						echo '</span> ';
					}
					?>
				</span>
				<span class="hero__tag"><span class="line"><span>Padel a <em>100 metri</em> dal mare.</span></span></span>
			</h1>
			<p class="hero__lead"><?php echo esc_html( vd( 'hero_testo' ) ); ?></p>
			<div class="hero__actions">
				<a href="<?php echo esc_url( $playtomic ); ?>" class="btn btn--lime btn--big" target="_blank" rel="noopener">Prenota il campo <?php echo $freccia; // phpcs:ignore -- SVG statico ?></a>
				<a href="#prezzi" class="btn btn--ghost btn--big">Vedi i prezzi</a>
			</div>
		</div>

		<div class="ball" aria-hidden="true"></div>
		<a href="#centro" class="scroll-hint" aria-label="Scorri verso il basso"><span></span></a>
	</section>

	<!-- ============ FASCIA SCORREVOLE ============ -->
	<div class="marquee" aria-hidden="true">
		<div class="marquee__track">
			<?php for ( $i = 0; $i < 2; $i++ ) : ?>
				<span>Valley d'Or</span><i></i><span>Padel</span><i></i><span>Sport</span><i></i><span>Mare</span><i></i><span>Amici</span><i></i>
			<?php endfor; ?>
		</div>
	</div>

	<!-- ============ IL CENTRO ============ -->
	<section class="section" id="centro">
		<div class="wrap">
			<div class="section__head reveal">
				<p class="eyebrow">Il centro</p>
				<h2>Uno sport che unisce <em>competizione</em> e piacere di stare insieme.</h2>
			</div>

			<div class="stats">
				<div class="stat reveal"><strong data-count="2">2</strong><span>campi panoramici</span></div>
				<div class="stat reveal"><strong data-count="100" data-prefix="&lt;">&lt;100</strong><span>metri dal mare</span></div>
				<div class="stat reveal"><strong data-count="15">15</strong><span>ore al giorno di gioco</span></div>
				<div class="stat reveal"><strong data-count="7">7</strong><span>giorni su 7</span></div>
			</div>

			<div class="features">
				<article class="feature reveal">
					<div class="feature__icon">
						<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="6" y="10" width="36" height="28" rx="2"/><path d="M24 10v28M6 24h36"/></svg>
					</div>
					<h3>2 campi panoramici</h3>
					<p>Struttura in vetro e acciaio, erba sintetica di ultima generazione e illuminazione per giocare anche la sera.</p>
				</article>
				<article class="feature reveal">
					<div class="feature__icon">
						<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M15 8h18v10a9 9 0 0 1-18 0zM15 12H8a6 6 0 0 0 7 8M33 12h7a6 6 0 0 1-7 8M24 27v7M17 40h14l-2-6H19z"/></svg>
					</div>
					<h3>Tornei</h3>
					<p>Doppio maschile, doppio femminile e misto. Mettiti alla prova e conosci nuovi compagni di gioco.</p>
					<ul class="tags"><li>Maschile</li><li>Femminile</li><li>Misto</li></ul>
				</article>
				<article class="feature reveal">
					<div class="feature__icon">
						<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="14" r="7"/><path d="M10 42c0-8 6-14 14-14s14 6 14 14M30 34l3 3 6-6"/></svg>
					</div>
					<h3>Istruttori federali</h3>
					<p>Lezioni e allenamenti personalizzati con istruttori FITP, per chi inizia e per chi vuole crescere.</p>
				</article>
			</div>
		</div>
	</section>

	<!-- ============ PREZZI ============ -->
	<section class="section section--dark" id="prezzi">
		<div class="wrap">
			<div class="section__head reveal">
				<p class="eyebrow">Prezzi</p>
				<h2>Semplici e <em>trasparenti</em>.</h2>
				<p class="section__sub">Prezzo per campo, 4 giocatori. Disdetta gratuita fino a 24 ore prima.</p>
			</div>

			<div class="prices">
				<article class="price reveal" data-tilt>
					<p class="price__time">1 ora</p>
					<p class="price__amount"><span><?php echo esc_html( vd( 'prezzo_60' ) ); ?></span>€</p>
					<p class="price__per"><strong><?php echo esc_html( vd_a_persona( vd( 'prezzo_60' ) ) ); ?> €</strong> a persona</p>
					<ul class="price__list">
						<li>Disdetta entro 24h</li>
						<li>Campo panoramico</li>
					</ul>
					<a href="<?php echo esc_url( $playtomic ); ?>" class="btn btn--ghost" target="_blank" rel="noopener">Prenota 1 ora</a>
				</article>

				<article class="price price--hot reveal" data-tilt>
					<span class="price__badge">Più scelto</span>
					<p class="price__time">1 ora e 30</p>
					<p class="price__amount"><span><?php echo esc_html( vd( 'prezzo_90' ) ); ?></span>€</p>
					<p class="price__per"><strong><?php echo esc_html( vd_a_persona( vd( 'prezzo_90' ) ) ); ?> €</strong> a persona</p>
					<ul class="price__list">
						<li>Disdetta entro 24h</li>
						<li>Campo panoramico</li>
					</ul>
					<a href="<?php echo esc_url( $playtomic ); ?>" class="btn btn--lime" target="_blank" rel="noopener">Prenota 1h30</a>
				</article>

				<div class="extras reveal">
					<h3>Extra</h3>
					<ul>
						<li><span>Tubo di palline</span><b><?php echo esc_html( vd( 'palline' ) ); ?> €</b></li>
						<li><span>Noleggio racchetta</span><b><?php echo esc_html( vd( 'racchetta' ) ); ?> €</b></li>
						<li><span>Overgrip</span><b><?php echo esc_html( vd( 'overgrip' ) ); ?> €</b></li>
					</ul>
					<p class="extras__note">Non hai la racchetta? Nessun problema, la trovi da noi.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ CORSI ============ -->
	<section class="section" id="corsi">
		<div class="wrap corsi">
			<div class="corsi__media reveal">
				<img src="<?php echo esc_url( vd( 'corsi_img' ) ); ?>" alt="Istruttore federale Valley d'Or" loading="lazy">
				<div class="corsi__badge"><strong>FITP</strong><span>Istruttori federali</span></div>
			</div>

			<div class="corsi__body">
				<div class="section__head reveal">
					<p class="eyebrow">Corsi</p>
					<h2>Impara, migliora, <em>vinci</em>.</h2>
				</div>

				<div class="accordion">
					<details class="acc reveal" open>
						<summary><span>Lezioni con istruttore FITP</span><i></i></summary>
						<div class="acc__body">
							<p>Lezioni individuali o di gruppo, per imparare i colpi base o perfezionare la tecnica.</p>
							<ul class="tags"><li>1 persona</li><li>2 persone</li><li>3 persone</li></ul>
						</div>
					</details>
					<details class="acc reveal">
						<summary><span>Allenamenti personalizzati</span><i></i></summary>
						<div class="acc__body">
							<p>Programmi su misura per atleti singoli o coppie che preparano tornei e competizioni.</p>
							<ul class="tags"><li>1 persona</li><li>2 persone</li></ul>
						</div>
					</details>
					<details class="acc reveal">
						<summary><span>Scuola padel junior</span><i></i></summary>
						<div class="acc__body">
							<p>Il padel per i più giovani: otto lezioni al mese con istruttore federale.</p>
							<p class="acc__price"><b><?php echo esc_html( vd( 'junior' ) ); ?> €</b> / mese · 8 lezioni</p>
						</div>
					</details>
				</div>

				<div class="corsi__cta reveal">
					<p>Vuoi sapere orari e prezzi delle lezioni?</p>
					<div class="hero__actions">
						<a href="<?php echo esc_url( $whatsapp . '?text=' . rawurlencode( 'Ciao, vorrei informazioni sui corsi di padel' ) ); ?>" class="btn btn--lime" target="_blank" rel="noopener">
							<svg viewBox="0 0 24 24" aria-hidden="true" class="ico-fill"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.3 0 .5l-.3.5-.4.4c-.1.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.1 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1.1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.2z"/></svg>
							Scrivici su WhatsApp
						</a>
						<a href="tel:<?php echo esc_attr( vd_tel() ); ?>" class="btn btn--ghost-dark"><?php echo esc_html( vd( 'telefono' ) ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ GALLERIA ============ -->
	<?php if ( $galleria ) : ?>
	<section class="section section--dark" id="galleria">
		<div class="wrap">
			<div class="section__head reveal">
				<p class="eyebrow">Galleria</p>
				<h2>Vieni a <em>vedere</em>.</h2>
			</div>

			<div class="gallery">
				<?php foreach ( $galleria as $i => $src ) : ?>
					<a href="<?php echo esc_url( $src ); ?>" class="gallery__item <?php echo esc_attr( $formati[ $i ] ); ?> reveal">
						<img src="<?php echo esc_url( $src ); ?>" alt="Il centro Valley d'Or – foto <?php echo (int) ( $i + 1 ); ?>" loading="lazy">
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ============ SPONSOR ============ -->
	<?php if ( $sponsor ) : ?>
	<section class="sponsors" aria-labelledby="sponsor-title">
		<div class="wrap">
			<p class="eyebrow eyebrow--center" id="sponsor-title">I nostri sponsor</p>
		</div>
		<div class="sponsors__rail">
			<div class="sponsors__track" id="sponsorTrack">
				<?php foreach ( $sponsor as $src ) : ?>
					<img src="<?php echo esc_url( $src ); ?>" alt="Sponsor" loading="lazy">
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ============ DOVE SIAMO ============ -->
	<section class="section" id="dove">
		<div class="wrap dove">
			<div class="dove__info">
				<div class="section__head reveal">
					<p class="eyebrow">Dove siamo</p>
					<h2>Ti aspettiamo a <em>La Ciaccia</em>.</h2>
				</div>

				<ul class="contacts reveal">
					<li>
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
						<div><small>Indirizzo</small><?php echo esc_html( vd( 'indirizzo' ) ); ?></div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
						<div><small>Orari</small><?php echo esc_html( vd( 'orari' ) ); ?></div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>
						<div><small>Telefono</small><a href="tel:<?php echo esc_attr( vd_tel() ); ?>"><?php echo esc_html( vd( 'telefono' ) ); ?></a></div>
					</li>
					<li>
						<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
						<div><small>Email</small><a href="mailto:<?php echo esc_attr( antispambot( vd( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( vd( 'email' ) ) ); ?></a></div>
					</li>
				</ul>

				<div class="hero__actions reveal">
					<a href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( vd( 'lat' ) . ',' . vd( 'lng' ) ) ); ?>" class="btn btn--lime" target="_blank" rel="noopener">Indicazioni stradali</a>
					<a href="<?php echo esc_url( $whatsapp ); ?>" class="btn btn--ghost-dark" target="_blank" rel="noopener">WhatsApp</a>
				</div>

				<?php if ( vd( 'modulo' ) ) : ?>
					<div class="dove__form reveal">
						<h3>Inviaci un messaggio</h3>
						<?php echo do_shortcode( vd( 'modulo' ) ); // phpcs:ignore -- output dello shortcode ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="dove__map reveal">
				<iframe title="Mappa di Valley d'Or Padel"
					src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( vd( 'lat' ) . ',' . vd( 'lng' ) ) . '&z=16&output=embed' ); ?>"
					loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
	</section>

	<!-- ============ CHIAMATA FINALE ============ -->
	<section class="cta">
		<div class="cta__bg" data-parallax="0.15"><img src="<?php echo esc_url( vd( 'cta_img' ) ); ?>" alt="" loading="lazy"></div>
		<div class="wrap cta__inner reveal">
			<h2>Pronto a scendere <em>in campo</em>?</h2>
			<p>Prenota in pochi secondi con l'app Playtomic.</p>
			<a href="<?php echo esc_url( $playtomic ); ?>" class="btn btn--lime btn--big" target="_blank" rel="noopener">Prenota ora <?php echo $freccia; // phpcs:ignore -- SVG statico ?></a>
		</div>
	</section>

</main>

<?php
get_footer();
