<?php
/**
 * Home page: tutte le sezioni del centro in un'unica pagina.
 */

get_header();

$playtomic = vd( 'playtomic' );
$whatsapp  = 'https://wa.me/' . preg_replace( '/\D/', '', vd( 'whatsapp' ) );
$freccia   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

// Galleria e sponsor: foto scelte nel pannello, o quelle predefinite del tema.
$galleria = vd_foto( 'galleria' );
$sponsor  = vd_sponsor_visibili();
// Forma di ogni riquadro: la prima voce larga (2 colonne), le verticali alte (2 righe).
// In home si riempie una griglia 4×4 (16 caselle); le altre voci si aprono con "Vedi tutte".
$forme   = array();
$in_home = 0;
$caselle = 0;
foreach ( $galleria as $i => $f ) {
	$forma = ! empty( $f['verticale'] ) ? 'gallery__item--tall' : ( 0 === $i ? 'gallery__item--wide' : '' );
	$conta = $forma ? 2 : 1;
	if ( $caselle + $conta > 16 ) {
		break;
	}
	$forme[]  = $forma;
	$caselle += $conta;
	$in_home++;
	if ( 16 === $caselle ) {
		break;
	}
}

$tornei = vd_tornei_prossimi( 3 );

// Tariffe, extra e corsi dal pannello: solo le voci visibili.
$tariffe = vd_visibili( vd_tariffe() );
$extra   = vd_visibili( vd_extra() );
$corsi   = vd_visibili( vd_corsi() );

// Si apre il primo corso in evidenza, altrimenti il primo.
$corso_aperto = 0;
foreach ( $corsi as $i => $c ) {
	if ( ! empty( $c['evidenza'] ) ) {
		$corso_aperto = $i;
		break;
	}
}
$corsi_con_prezzi = false;
foreach ( $corsi as $c ) {
	foreach ( $c['righe'] as $r ) {
		$corsi_con_prezzi = $corsi_con_prezzi || '' !== trim( (string) $r['prezzo'] );
	}
}

// Prezzo a persona più basso, per il riquadro dei numeri.
$persona_min = null;
foreach ( $tariffe as $t ) {
	$n = vd_num( vd_tariffa_persona( $t ) );
	if ( null !== $n && ( null === $persona_min || $n < $persona_min ) ) {
		$persona_min = $n;
	}
}
?>

<main>

	<!-- ============ APERTURA ============ -->
	<section class="hero">
		<div class="hero__bg" data-parallax="0.25">
			<img src="<?php echo esc_url( vd( 'hero_img' ) ); ?>" alt="Vista aerea del Valley d'Or Padel di Valledoria con il mare sullo sfondo">
		</div>
		<div class="hero__shade"></div>

		<div class="hero__content wrap">
			<p class="eyebrow hero__eyebrow"><span class="dot"></span> <?php echo esc_html( vd( 'hero_luogo' ) ); ?></p>
			<h1 class="hero__title">
				<span class="hero__brand">
				<span class="hero__name">
					<span class="sr-only"><?php echo esc_html( vd( 'hero_nome' ) ); ?></span>
					<?php
					// Il nome entra lettera per lettera; l'ultima parola ("d'Or") è evidenziata.
					$parole = preg_split( '/\s+/', trim( vd( 'hero_nome' ) ) );
					$n      = 0;
					foreach ( $parole as $k => $parola ) {
						$classe = ( $k === count( $parole ) - 1 && count( $parole ) > 1 ) ? 'w w--or' : 'w';
						echo '<span class="' . $classe . '" aria-hidden="true">';
						$lettere = preg_split( '//u', $parola, -1, PREG_SPLIT_NO_EMPTY );
						foreach ( $lettere as $j => $c ) {
							// Lettere separate perdono la crenatura del carattere: la si rimette a mano.
							$kern = isset( $lettere[ $j + 1 ] ) ? vd_crenatura( $c, $lettere[ $j + 1 ] ) : 0;
							$stile = '--i:' . (int) $n++ . ( $kern ? ';--k:' . $kern . 'em' : '' );
							echo '<span class="ch" style="' . esc_attr( $stile ) . '">' . esc_html( $c ) . '</span>';
						}
						echo '</span> ';
					}
					?>
				</span>
				<?php if ( vd( 'hero_sotto' ) ) : ?>
					<span class="hero__padel"><?php echo esc_html( vd( 'hero_sotto' ) ); ?></span>
				<?php endif; ?>
				</span>
				<span class="hero__tag"><span class="line"><span>Padel a <em>100 metri</em> dal mare.</span></span></span>
			</h1>
			<p class="hero__lead"><?php echo esc_html( vd( 'hero_testo' ) ); ?></p>
			<div class="hero__actions">
				<a href="<?php echo esc_url( $playtomic ); ?>" class="btn btn--lime btn--big" target="_blank" rel="noopener">Prenota il campo <?php echo $freccia; // phpcs:ignore -- SVG statico ?></a>
				<a href="#prezzi" class="btn btn--ghost btn--big">Vedi i prezzi</a>
			</div>

			<?php if ( vd( 'promo_attiva' ) && vd( 'promo_testo' ) ) : ?>
				<?php
				// Pulsante in evidenza (es. "Clinic di padel"): senza link apre WhatsApp con un messaggio pronto.
				$promo_link  = vd( 'promo_link' ) ? vd( 'promo_link' ) : $whatsapp . '?text=' . rawurlencode( 'Ciao, vorrei informazioni su: ' . vd( 'promo_testo' ) );
				$promo_fuori = 0 !== strpos( $promo_link, '#' );
				?>
				<a href="<?php echo esc_url( $promo_link ); ?>" class="promo" <?php echo $promo_fuori ? 'target="_blank" rel="noopener"' : ''; ?><?php echo vd( 'promo_finestra' ) ? ' data-finestra="clinic" aria-haspopup="dialog"' : ''; ?>>
					<?php if ( vd( 'promo_etichetta' ) ) : ?>
						<span class="promo__tag"><span><?php echo esc_html( vd( 'promo_etichetta' ) ); ?></span></span>
					<?php endif; ?>
					<span class="promo__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M5.6 5.7c3.4 3.2 3.4 9.4 0 12.6M18.4 5.7c-3.4 3.2-3.4 9.4 0 12.6" /></svg>
					</span>
					<span class="promo__txt">
						<strong><?php echo esc_html( vd( 'promo_testo' ) ); ?></strong>
						<?php if ( vd( 'promo_sotto' ) ) : ?>
							<small><?php echo esc_html( vd( 'promo_sotto' ) ); ?> →</small>
						<?php endif; ?>
					</span>
				</a>
			<?php endif; ?>
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
				<?php if ( null !== $persona_min ) : ?>
					<div class="stat reveal"><strong <?php echo floor( $persona_min ) == $persona_min ? 'data-count="' . (int) $persona_min . '" data-suffix="€"' : ''; ?>><?php echo esc_html( vd_fmt( $persona_min ) ); ?>€</strong><span>a persona per giocare</span></div>
				<?php else : ?>
					<div class="stat reveal"><strong data-count="4">4</strong><span>giocatori a campo</span></div>
				<?php endif; ?>
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
				<p class="section__sub">Prezzo del campo intero, con il costo a persona già calcolato.</p>
			</div>

			<?php if ( $tariffe || $extra ) : ?>
			<div class="prices<?php echo $extra ? '' : ' prices--solo'; ?>">
				<?php foreach ( $tariffe as $t ) : ?>
					<?php
					$classi = 'price reveal';
					if ( ! empty( $t['evidenza'] ) ) {
						$classi .= ' price--hot';
					}
					if ( vd_in_offerta( $t ) ) {
						$classi .= ' price--sale';
					}
					$attuale = vd_num( vd_prezzo_attuale( $t ) );
					$persona = vd_tariffa_persona( $t );
					$note    = array_filter( array_map( 'trim', explode( "\n", $t['note'] ) ) );
					?>
					<article class="<?php echo esc_attr( $classi ); ?>" data-tilt>
						<div class="price__badges">
							<?php if ( ! empty( $t['evidenza'] ) ) : ?>
								<span class="price__badge"><?php echo esc_html( $t['etichetta'] ? $t['etichetta'] : 'In evidenza' ); ?></span>
							<?php endif; ?>
							<?php if ( vd_in_offerta( $t ) ) : ?>
								<span class="price__badge price__badge--sale">Offerta</span>
							<?php endif; ?>
						</div>
						<p class="price__time"><?php echo esc_html( $t['nome'] ); ?></p>
						<?php if ( vd_in_offerta( $t ) && null !== vd_num( $t['prezzo'] ) ) : ?>
							<p class="price__old"><del><?php echo esc_html( vd_fmt( vd_num( $t['prezzo'] ) ) ); ?> €</del></p>
						<?php endif; ?>
						<p class="price__amount"><span><?php echo esc_html( null !== $attuale ? vd_fmt( $attuale ) : vd_prezzo_attuale( $t ) ); ?></span><?php echo null !== $attuale ? '€' : ''; ?></p>
						<?php if ( $persona ) : ?>
							<p class="price__per"><strong><?php echo esc_html( $persona ); ?> €</strong> a persona · <?php echo (int) $t['persone']; ?> giocatori</p>
						<?php endif; ?>
						<ul class="price__list">
							<?php foreach ( $note as $n ) : ?>
								<li><?php echo esc_html( $n ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a href="<?php echo esc_url( $playtomic ); ?>" class="btn <?php echo ! empty( $t['evidenza'] ) ? 'btn--lime' : 'btn--ghost'; ?>" target="_blank" rel="noopener">Prenota <?php echo esc_html( $t['nome'] ); ?></a>
					</article>
				<?php endforeach; ?>

				<?php if ( $extra ) : ?>
					<div class="extras reveal">
						<h3>Extra</h3>
						<ul>
							<?php foreach ( $extra as $e ) : ?>
								<li class="<?php echo ! empty( $e['evidenza'] ) ? 'is-star' : ''; ?><?php echo vd_in_offerta( $e ) ? ' is-sale' : ''; ?>">
									<span>
										<?php echo esc_html( $e['nome'] ); ?>
										<?php if ( ! empty( $e['evidenza'] ) ) : ?>
											<em class="extras__tag"><?php echo esc_html( $e['etichetta'] ? $e['etichetta'] : 'Novità' ); ?></em>
										<?php endif; ?>
										<?php if ( vd_in_offerta( $e ) ) : ?>
											<em class="extras__tag extras__tag--sale">Offerta</em>
										<?php endif; ?>
									</span>
									<span class="extras__price"><?php echo vd_prezzo_html( $e ); // phpcs:ignore -- già escapato in vd_prezzo_html() ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
						<p class="extras__note">Non hai la racchetta? Nessun problema, la trovi da noi.</p>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
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
					<?php foreach ( $corsi as $i => $c ) : ?>
						<?php
						$righe = array_values( array_filter( $c['righe'], function ( $r ) {
							return '' !== trim( (string) $r['prezzo'] );
						} ) );
						?>
						<details class="acc reveal<?php echo ! empty( $c['evidenza'] ) ? ' acc--star' : ''; ?>" <?php echo $i === $corso_aperto ? 'open' : ''; ?>>
							<summary>
								<span>
									<?php echo esc_html( $c['nome'] ); ?>
									<?php if ( ! empty( $c['evidenza'] ) ) : ?>
										<em class="acc__tag"><?php echo esc_html( $c['etichetta'] ? $c['etichetta'] : 'Novità' ); ?></em>
									<?php endif; ?>
								</span>
								<i></i>
							</summary>
							<div class="acc__body">
								<?php if ( $c['descrizione'] ) : ?>
									<p><?php echo nl2br( esc_html( $c['descrizione'] ) ); ?></p>
								<?php endif; ?>
								<?php if ( $righe ) : ?>
									<ul class="acc__prices">
										<?php foreach ( $righe as $r ) : ?>
											<li class="<?php echo vd_in_offerta( $r ) ? 'is-sale' : ''; ?>">
												<span><?php echo esc_html( $r['chi'] ); ?><?php if ( vd_in_offerta( $r ) ) : ?> <em class="extras__tag extras__tag--sale">Offerta</em><?php endif; ?></span>
												<span class="acc__amount"><?php echo vd_prezzo_html( $r, $r['unita'] ); // phpcs:ignore -- già escapato ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php else : ?>
									<?php $chi = array_filter( wp_list_pluck( $c['righe'], 'chi' ) ); ?>
									<?php if ( $chi ) : ?>
										<ul class="tags">
											<?php foreach ( $chi as $x ) : ?>
												<li><?php echo esc_html( $x ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<div class="corsi__cta reveal">
					<p><?php echo $corsi_con_prezzi ? 'Vuoi prenotare una lezione?' : 'Vuoi sapere orari e prezzi delle lezioni?'; ?></p>
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

	<!-- ============ TORNEI ============ -->
	<section class="section section--dark" id="tornei">
		<div class="wrap">
			<div class="section__head section__head--row reveal">
				<div>
					<p class="eyebrow">Tornei</p>
					<h2>Mettiti in <em>gioco</em>.</h2>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'torneo' ) ); ?>" class="btn btn--ghost">Tutti i tornei</a>
			</div>

			<?php if ( $tornei ) : ?>
				<div class="tornei">
					<?php foreach ( $tornei as $t ) : ?>
						<?php
						$categoria = get_post_meta( $t->ID, '_vd_categoria', true );
						$quota     = get_post_meta( $t->ID, '_vd_quota', true );
						$orario    = get_post_meta( $t->ID, '_vd_orario', true );
						?>
						<article class="torneo-card reveal">
							<a class="torneo-card__link" href="<?php echo esc_url( get_permalink( $t ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $t ) ); ?>"></a>
							<div class="torneo-card__date">
								<strong><?php echo esc_html( vd_torneo_data( $t->ID, 'j' ) ); ?></strong>
								<span><?php echo esc_html( vd_torneo_data( $t->ID, 'M' ) ); ?></span>
							</div>
							<div class="torneo-card__body">
								<?php if ( $categoria ) : ?><p class="torneo-card__cat"><?php echo esc_html( $categoria ); ?></p><?php endif; ?>
								<h3><?php echo esc_html( get_the_title( $t ) ); ?></h3>
								<p class="torneo-card__meta">
									<?php echo esc_html( ucfirst( vd_torneo_data( $t->ID, 'l j F' ) ) ); ?>
									<?php if ( $orario ) : ?> · <?php echo esc_html( $orario ); ?><?php endif; ?>
									<?php if ( $quota ) : ?><br><?php echo esc_html( $quota ); ?><?php endif; ?>
								</p>
								<a href="<?php echo esc_url( vd_torneo_iscrizione( $t->ID ) ); ?>" class="btn btn--lime torneo-card__cta" target="_blank" rel="noopener">Iscriviti</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="tornei-vuoto reveal">
					<p><strong>Nessun torneo in calendario in questo momento.</strong> Doppio maschile, femminile e misto: segui Instagram per sapere quando apriamo le iscrizioni.</p>
					<?php if ( vd( 'instagram' ) ) : ?>
						<a href="<?php echo esc_url( vd( 'instagram' ) ); ?>" class="btn btn--lime" target="_blank" rel="noopener">Seguici su Instagram</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ============ GALLERIA ============ -->
	<?php if ( $galleria ) : ?>
	<section class="section" id="galleria">
		<div class="wrap">
			<div class="section__head reveal">
				<p class="eyebrow">Galleria</p>
				<h2>Vieni a <em>vedere</em>.</h2>
			</div>

			<div class="gallery">
				<?php foreach ( $galleria as $i => $f ) : ?>
					<?php
					$video = 'video' === $f['tipo'];
					$dati  = 'data-tipo="' . esc_attr( $f['tipo'] ) . '" data-alt="' . esc_attr( $f['alt'] ) . '"' . ( $f['poster'] ? ' data-poster="' . esc_url( $f['poster'] ) . '"' : '' );
					?>
					<?php if ( $i < $in_home ) : ?>
						<a href="<?php echo esc_url( $f['full'] ); ?>" class="gallery__item <?php echo esc_attr( $forme[ $i ] . ( $video ? ' gallery__item--video' : '' ) ); ?> reveal" <?php echo $dati; // phpcs:ignore -- escapato sopra ?>>
							<?php if ( $video ) : ?>
								<video src="<?php echo esc_url( $f['full'] ); ?>"<?php echo $f['poster'] ? ' poster="' . esc_url( $f['poster'] ) . '"' : ''; ?> muted loop playsinline preload="<?php echo $f['poster'] ? 'none' : 'metadata'; ?>" aria-hidden="true"></video>
								<span class="gallery__play" aria-hidden="true"></span>
								<span class="sr-only">Guarda il video: <?php echo esc_html( $f['alt'] ); ?></span>
							<?php else : ?>
								<img src="<?php echo esc_url( $f['thumb'] ); ?>" alt="<?php echo esc_attr( $f['alt'] ); ?>" loading="lazy">
							<?php endif; ?>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( $f['full'] ); ?>" class="gallery__item gallery__item--extra" <?php echo $dati; // phpcs:ignore -- escapato sopra ?> hidden></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $galleria ) > $in_home ) : ?>
				<?php $con_video = in_array( 'video', wp_list_pluck( $galleria, 'tipo' ), true ); ?>
				<p class="gallery__more reveal">
					<button type="button" class="btn btn--ghost-dark" data-open-gallery="<?php echo (int) $in_home; ?>">Vedi tutte: <?php echo (int) count( $galleria ); ?> <?php echo $con_video ? 'foto e video' : 'foto'; ?></button>
				</p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<!-- ============ SPONSOR ============ -->
	<?php if ( $sponsor ) : ?>
	<section class="sponsors" id="sponsor" aria-labelledby="sponsor-title">
		<div class="wrap">
			<div class="section__head section__head--center reveal">
				<p class="eyebrow eyebrow--center">I nostri sponsor</p>
				<h2 id="sponsor-title">Grazie a chi <em>gioca</em> con noi.</h2>
			</div>
			<ul class="sponsors__list">
				<?php foreach ( $sponsor as $s ) : ?>
					<?php
					$img = vd_sponsor_img( $s );
					$alt = $s['nome'] ? $s['nome'] : 'Sponsor';
					?>
					<li class="sponsor reveal">
						<?php if ( $s['link'] ) : ?>
							<a href="<?php echo esc_url( $s['link'] ); ?>" target="_blank" rel="noopener sponsored" aria-label="<?php echo esc_attr( $alt ); ?> (si apre il sito)">
								<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
								<span class="sponsor__vai" aria-hidden="true">Visita il sito ↗</span>
							</a>
						<?php else : ?>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
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
if ( vd( 'promo_attiva' ) && vd( 'promo_finestra' ) ) {
	get_template_part( 'parti/clinic' );
}
get_footer();
