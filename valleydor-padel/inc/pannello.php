<?php
/**
 * Pannello "Valley d'Or" nella bacheca: una sola pagina per le cose che
 * cambiano spesso: tariffe del campo, extra, corsi, orari, galleria, sponsor.
 * Tariffe, extra e corsi sono elenchi (inc/listini.php); il resto è salvato
 * come theme_mod, gli stessi valori letti da vd().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vd_pannello_url() {
	return admin_url( 'admin.php?page=valleydor' );
}

add_action( 'admin_menu', function () {
	add_menu_page(
		"Valley d'Or – gestione del sito",
		"Valley d'Or",
		'edit_theme_options',
		'valleydor',
		'vd_pannello_pagina',
		'dashicons-admin-site-alt3',
		2
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'toplevel_page_valleydor' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'vd-pannello', get_template_directory_uri() . '/assets/css/pannello.css', array(), VD_VERSION );
	wp_enqueue_script( 'vd-pannello', get_template_directory_uri() . '/assets/js/pannello.js', array( 'jquery', 'jquery-ui-sortable' ), VD_VERSION, true );
} );

/* ----------------------------------------------------------
 * Salvataggio
 * -------------------------------------------------------- */
/* ----------------------------------------------------------
 * Foto predefinite della galleria → libreria media, così si
 * possono togliere e riordinare una per una dal pannello.
 * -------------------------------------------------------- */
add_action( 'admin_post_vd_importa_galleria', function () {
	if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'upload_files' ) ) {
		wp_die( 'Non hai i permessi per caricare foto.', 403 );
	}
	check_admin_referer( 'vd_importa_galleria' );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}

	$dir = get_template_directory() . '/assets/img/';
	$ids = array();
	foreach ( vd_galleria_elenco() as $voce ) {
		list( $file, $alt ) = $voce;
		// Già importata in passato? Si riusa, senza doppioni nella libreria.
		$gia = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_vd_origine',
			'meta_value'     => $file,
		) );
		if ( $gia ) {
			$ids[] = (int) $gia[0];
			continue;
		}
		$percorso = $dir . ( 0 === strpos( $file, 'valleydor-padel-' ) ? 'galleria/' : '' ) . $file;
		if ( ! file_exists( $percorso ) ) {
			continue;
		}
		$tmp = wp_tempnam( $file );
		copy( $percorso, $tmp );
		$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0 );
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		update_post_meta( $id, '_vd_origine', $file );
		$ids[] = (int) $id;
	}
	set_theme_mod( 'galleria_ids', implode( ',', $ids ) );

	wp_safe_redirect( add_query_arg( 'importate', count( $ids ), vd_pannello_url() ) );
	exit;
} );

add_action( 'admin_post_vd_salva', function () {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( 'Non hai i permessi per modificare il sito.', 403 );
	}
	check_admin_referer( 'vd_salva' );

	// Elenchi: se il modulo li conteneva ma sono vuoti, l'elenco è stato svuotato.
	if ( isset( $_POST['vd_listini'] ) ) {
		foreach ( array( 'tariffe', 'extra', 'corsi' ) as $tipo ) {
			$raw = isset( $_POST[ $tipo ] ) ? wp_unslash( $_POST[ $tipo ] ) : array(); // phpcs:ignore -- ripulito in vd_pulisci_voci()
			set_theme_mod( $tipo, vd_pulisci_voci( $raw, $tipo ) );
		}
	}

	if ( isset( $_POST['orari'] ) ) {
		set_theme_mod( 'orari', sanitize_text_field( wp_unslash( $_POST['orari'] ) ) );
	}

	// Pulsante in evidenza nell'apertura del sito.
	if ( isset( $_POST['vd_listini'] ) ) {
		set_theme_mod( 'promo_attiva', empty( $_POST['promo_attiva'] ) ? '' : '1' );
		foreach ( array( 'promo_testo', 'promo_etichetta', 'promo_sotto', 'promo_menu' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				set_theme_mod( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		set_theme_mod( 'promo_finestra', empty( $_POST['promo_finestra'] ) ? '' : '1' );
		$righe_lunghe = array( 'clinic_intro', 'clinic_inclusi', 'clinic_locali', 'clinic_alloggi', 'clinic_finale' );
		foreach ( array( 'clinic_titolo', 'clinic_slogan', 'clinic_giorni', 'clinic_ore', 'clinic_mattina', 'clinic_sera', 'clinic_mesi', 'clinic_prezzo', 'clinic_prezzo_nota', 'clinic_intro', 'clinic_inclusi', 'clinic_locali', 'clinic_alloggi', 'clinic_finale' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$val = wp_unslash( $_POST[ $key ] ); // phpcs:ignore -- ripulito sotto
				set_theme_mod( $key, in_array( $key, $righe_lunghe, true ) ? sanitize_textarea_field( $val ) : sanitize_text_field( $val ) );
			}
		}
		if ( isset( $_POST['promo_link'] ) ) {
			$link = trim( wp_unslash( $_POST['promo_link'] ) ); // phpcs:ignore -- ripulito sotto
			set_theme_mod( 'promo_link', 0 === strpos( $link, '#' ) ? '#' . sanitize_key( substr( $link, 1 ) ) : esc_url_raw( $link ) );
		}
	}

	// Sponsor: elenco con immagine, nome, link, visibile e ordine.
	if ( isset( $_POST['vd_listini'] ) ) {
		$raw = isset( $_POST['sponsor'] ) ? wp_unslash( $_POST['sponsor'] ) : array(); // phpcs:ignore -- ripulito in vd_pulisci_sponsor()
		set_theme_mod( 'sponsor', vd_pulisci_sponsor( $raw ) );
	}

	// Foto della galleria: solo immagini (e video) realmente presenti nella libreria media.
	foreach ( array( 'galleria_ids' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$ammessi = function ( $id ) use ( $key ) {
				return wp_attachment_is_image( $id ) || ( 'galleria_ids' === $key && wp_attachment_is( 'video', $id ) );
			};
			$ids = array_filter( vd_ids( wp_unslash( $_POST[ $key ] ) ), $ammessi );
			set_theme_mod( $key, implode( ',', array_unique( $ids ) ) );
		}
	}

	wp_safe_redirect( add_query_arg( 'salvato', '1', vd_pannello_url() ) );
	exit;
} );

/* ----------------------------------------------------------
 * Pezzi del modulo
 * -------------------------------------------------------- */

/** Campo di testo. */
function vd_campo( $nome, $label, $valore, $classe = '', $attr = '' ) {
	printf(
		'<label class="vd-f %1$s"><span>%2$s</span><input type="text" name="%3$s" value="%4$s" %5$s></label>',
		esc_attr( $classe ),
		esc_html( $label ),
		esc_attr( $nome ),
		esc_attr( $valore ),
		$attr // phpcs:ignore -- attributi fissi scritti nel codice
	);
}

/** Area di testo su tutta la riga. */
function vd_area( $nome, $label, $valore, $righe = 3 ) {
	printf(
		'<label class="vd-f vd-f--wide"><span>%1$s</span><textarea name="%2$s" rows="%3$d">%4$s</textarea></label>',
		esc_html( $label ),
		esc_attr( $nome ),
		(int) $righe,
		esc_textarea( $valore )
	);
}

/** Campo prezzo con il simbolo €. */
function vd_campo_euro( $nome, $label, $valore, $placeholder = '', $attr = '' ) {
	printf(
		'<label class="vd-f vd-f--euro"><span>%1$s</span><span class="vd-euro"><input type="text" inputmode="decimal" name="%2$s" value="%3$s" placeholder="%4$s" %5$s></span></label>',
		esc_html( $label ),
		esc_attr( $nome ),
		esc_attr( $valore ),
		esc_attr( $placeholder ),
		$attr // phpcs:ignore -- attributi fissi scritti nel codice
	);
}

/** Interruttori Visibile / In evidenza, etichetta e pulsante Togli. */
function vd_flags( $base, $v, $etichetta_default ) {
	?>
	<div class="vd-row__flags">
		<label class="vd-toggle" title="Se spento, la voce resta salvata ma non compare sul sito">
			<input type="checkbox" class="vd-js-visibile" name="<?php echo esc_attr( $base ); ?>[visibile]" value="1" <?php checked( ! empty( $v['visibile'] ) ); ?>>
			<span class="vd-toggle__ui" aria-hidden="true"></span> Visibile
		</label>
		<label class="vd-toggle vd-toggle--star" title="Mette la voce in risalto con un'etichetta">
			<input type="checkbox" class="vd-js-evidenza" name="<?php echo esc_attr( $base ); ?>[evidenza]" value="1" <?php checked( ! empty( $v['evidenza'] ) ); ?>>
			<span class="vd-toggle__ui" aria-hidden="true"></span> In evidenza
		</label>
		<input type="text" class="vd-etichetta" name="<?php echo esc_attr( $base ); ?>[etichetta]" value="<?php echo esc_attr( $v['etichetta'] ); ?>" placeholder="<?php echo esc_attr( $etichetta_default ); ?>" aria-label="Etichetta (es. <?php echo esc_attr( $etichetta_default ); ?>)">
		<span class="vd-badge-offerta">In offerta</span>
		<button type="button" class="vd-row__del" title="Togli questa voce"><span class="dashicons dashicons-trash"></span><span class="screen-reader-text">Togli</span></button>
	</div>
	<?php
}

/** Classi di stato della riga. */
function vd_row_class( $v ) {
	$c = array( 'vd-row' );
	if ( empty( $v['visibile'] ) ) {
		$c[] = 'is-off';
	}
	if ( ! empty( $v['evidenza'] ) ) {
		$c[] = 'is-star';
	}
	if ( vd_in_offerta( $v ) ) {
		$c[] = 'is-sale';
	}
	return implode( ' ', $c );
}

function vd_riga_tariffa( $k, $v ) {
	$b = 'tariffe[' . $k . ']';
	?>
	<div class="<?php echo esc_attr( vd_row_class( $v ) ); ?>">
		<span class="vd-row__handle dashicons dashicons-move" title="Trascina per cambiare l'ordine"></span>
		<div class="vd-row__fields">
			<?php vd_campo( $b . '[nome]', 'Nome', $v['nome'], 'vd-f--nome', 'placeholder="es. 1 ora" required' ); ?>
			<?php vd_campo_euro( $b . '[prezzo]', 'Prezzo del campo', $v['prezzo'], '32', 'class="vd-js-prezzo"' ); ?>
			<?php vd_campo_euro( $b . '[offerta]', 'In offerta a', $v['offerta'], 'nessuna', 'class="vd-js-offerta"' ); ?>
			<label class="vd-f vd-f--num"><span>Giocatori</span>
				<input type="number" min="1" max="8" class="vd-js-persone" name="<?php echo esc_attr( $b ); ?>[persone]" value="<?php echo esc_attr( $v['persone'] ? $v['persone'] : 4 ); ?>">
			</label>
			<label class="vd-f vd-f--wide"><span>Cosa include <small>(una riga per voce)</small></span>
				<textarea rows="2" name="<?php echo esc_attr( $b ); ?>[note]"><?php echo esc_textarea( $v['note'] ); ?></textarea>
			</label>
			<small class="vd-persona"></small>
		</div>
		<?php vd_flags( $b, $v, 'Più scelto' ); ?>
	</div>
	<?php
}

function vd_riga_extra( $k, $v ) {
	$b = 'extra[' . $k . ']';
	?>
	<div class="<?php echo esc_attr( vd_row_class( $v ) ); ?>">
		<span class="vd-row__handle dashicons dashicons-move" title="Trascina per cambiare l'ordine"></span>
		<div class="vd-row__fields">
			<?php vd_campo( $b . '[nome]', 'Nome', $v['nome'], 'vd-f--nome', 'placeholder="es. Acqua 50 cl" required' ); ?>
			<?php vd_campo_euro( $b . '[prezzo]', 'Prezzo', $v['prezzo'], '2,50', 'class="vd-js-prezzo"' ); ?>
			<?php vd_campo_euro( $b . '[offerta]', 'In offerta a', $v['offerta'], 'nessuna', 'class="vd-js-offerta"' ); ?>
		</div>
		<?php vd_flags( $b, $v, 'Novità' ); ?>
	</div>
	<?php
}

/** Riga di prezzo dentro un corso. */
function vd_riga_corso_prezzo( $k, $j, $r ) {
	$b = 'corsi[' . $k . '][righe][' . $j . ']';
	?>
	<div class="vd-subrow<?php echo vd_in_offerta( $r ) ? ' is-sale' : ''; ?>">
		<span class="vd-row__handle dashicons dashicons-move" title="Trascina per cambiare l'ordine"></span>
		<?php vd_campo( $b . '[chi]', 'Per chi', $r['chi'], 'vd-f--nome', 'placeholder="es. 1 persona"' ); ?>
		<?php vd_campo_euro( $b . '[prezzo]', 'Prezzo', $r['prezzo'], '35', 'class="vd-js-prezzo"' ); ?>
		<?php vd_campo( $b . '[unita]', 'Dopo il prezzo', $r['unita'], 'vd-f--unita', 'placeholder="es. / ora"' ); ?>
		<?php vd_campo_euro( $b . '[offerta]', 'In offerta a', $r['offerta'], '', 'class="vd-js-offerta"' ); ?>
		<button type="button" class="vd-row__del" title="Togli questo prezzo"><span class="dashicons dashicons-no-alt"></span><span class="screen-reader-text">Togli</span></button>
	</div>
	<?php
}

function vd_riga_corso( $k, $v ) {
	$b = 'corsi[' . $k . ']';
	?>
	<div class="<?php echo esc_attr( vd_row_class( $v ) ); ?> vd-row--corso">
		<span class="vd-row__handle dashicons dashicons-move" title="Trascina per cambiare l'ordine"></span>
		<div class="vd-row__fields">
			<?php vd_campo( $b . '[nome]', 'Nome del corso', $v['nome'], 'vd-f--nome vd-f--big', 'placeholder="es. Corso estivo per adulti" required' ); ?>
			<label class="vd-f vd-f--wide"><span>Descrizione</span>
				<textarea rows="2" name="<?php echo esc_attr( $b ); ?>[descrizione]" placeholder="Due righe per spiegare a chi è rivolto e come funziona"><?php echo esc_textarea( $v['descrizione'] ); ?></textarea>
			</label>
			<div class="vd-f vd-f--wide">
				<span>Prezzi <small>(se non metti prezzi, sul sito compare "chiedi info")</small></span>
				<div class="vd-sublist">
					<?php foreach ( array_values( $v['righe'] ) as $j => $r ) : ?>
						<?php vd_riga_corso_prezzo( $k, 'r' . $j, vd_riga( $r ) ); ?>
					<?php endforeach; ?>
				</div>
				<template class="vd-tpl-sub"><?php vd_riga_corso_prezzo( $k, '__J__', vd_riga( array() ) ); ?></template>
				<button type="button" class="button-link vd-add-sub"><span class="dashicons dashicons-plus-alt2"></span> Aggiungi un prezzo</button>
			</div>
		</div>
		<?php vd_flags( $b, $v, 'Novità' ); ?>
	</div>
	<?php
}

/** Riga di uno sponsor: banner, nome, link, visibile. */
function vd_riga_sponsor( $k, $v ) {
	$v   = vd_sponsor_voce( $v );
	$b   = 'sponsor[' . $k . ']';
	$img = vd_sponsor_img( $v, 'medium_large' );
	?>
	<div class="<?php echo esc_attr( vd_row_class( vd_voce( $v ) ) ); ?> vd-row--sponsor">
		<span class="vd-row__handle dashicons dashicons-move" title="Trascina per cambiare l'ordine"></span>
		<div class="vd-row__fields">
			<div class="vd-f vd-f--wide vd-sponsor">
				<span>Banner</span>
				<button type="button" class="vd-sponsor__img" title="Cambia immagine">
					<img src="<?php echo esc_url( $img ); ?>" alt="" <?php echo $img ? '' : 'hidden'; ?>>
					<span class="vd-sponsor__vuoto" <?php echo $img ? 'hidden' : ''; ?>><span class="dashicons dashicons-format-image"></span> Scegli il banner</span>
				</button>
				<input type="hidden" class="vd-js-img-id" name="<?php echo esc_attr( $b ); ?>[img_id]" value="<?php echo (int) $v['img_id']; ?>">
				<input type="hidden" class="vd-js-img-url" name="<?php echo esc_attr( $b ); ?>[img_url]" value="<?php echo esc_attr( $v['img_url'] ); ?>">
			</div>
			<?php vd_campo( $b . '[nome]', 'Nome (per Google e lettori di schermo)', $v['nome'], 'vd-f--nome', 'placeholder="es. Bar Aurelio"' ); ?>
			<label class="vd-f vd-f--link"><span>Link al sito <small>(facoltativo)</small></span>
				<input type="url" name="<?php echo esc_attr( $b ); ?>[link]" value="<?php echo esc_attr( $v['link'] ); ?>" placeholder="https://…">
			</label>
		</div>
		<div class="vd-row__flags">
			<label class="vd-toggle" title="Se spento, lo sponsor resta salvato ma non compare sul sito">
				<input type="checkbox" class="vd-js-visibile" name="<?php echo esc_attr( $b ); ?>[visibile]" value="1" <?php checked( ! empty( $v['visibile'] ) ); ?>>
				<span class="vd-toggle__ui" aria-hidden="true"></span> Visibile
			</label>
			<button type="button" class="vd-row__del" title="Togli questo sponsor"><span class="dashicons dashicons-trash"></span><span class="screen-reader-text">Togli</span></button>
		</div>
	</div>
	<?php
}

/** Elenco ordinabile con il modello per le righe nuove. */
function vd_elenco( $tipo, $voci, $render, $aggiungi ) {
	?>
	<div class="vd-list" data-tipo="<?php echo esc_attr( $tipo ); ?>">
		<div class="vd-list__rows">
			<?php foreach ( array_values( $voci ) as $i => $v ) : ?>
				<?php call_user_func( $render, 'v' . $i, vd_voce( $v ) ); ?>
			<?php endforeach; ?>
		</div>
		<p class="vd-list__empty" <?php echo $voci ? 'hidden' : ''; ?>>Nessuna voce: sul sito questa parte non compare.</p>
		<template class="vd-tpl"><?php call_user_func( $render, '__K__', vd_voce( array() ) ); ?></template>
		<button type="button" class="button vd-add"><span class="dashicons dashicons-plus-alt2"></span> <?php echo esc_html( $aggiungi ); ?></button>
	</div>
	<?php
}

/** Riquadro per scegliere e ordinare le foto dalla libreria media. */
function vd_pannello_foto( $key, $titolo, $aiuto, $vuoto, $video = false ) {
	$ids = vd_ids( vd( $key ) );
	?>
	<div class="vd-media" data-title="<?php echo esc_attr( $titolo ); ?>" data-tipi="<?php echo $video ? 'image,video' : 'image'; ?>">
		<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<p class="vd-help"><?php echo esc_html( $aiuto ); ?></p>
		<p class="vd-media__empty" <?php echo $ids ? 'hidden' : ''; ?>>
			<?php echo esc_html( $vuoto ); ?>
			<?php if ( 'galleria_ids' === $key && current_user_can( 'upload_files' ) ) : ?>
				<br><a class="button vd-importa" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vd_importa_galleria' ), 'vd_importa_galleria' ) ); ?>">
					<span class="dashicons dashicons-images-alt2"></span> Porta le <?php echo (int) count( vd_galleria_elenco() ); ?> foto predefinite nella libreria
				</a>
				<small class="vd-importa__nota">Così puoi toglierle, riordinarle e aggiungerne altre. Ci vuole qualche secondo.</small>
			<?php endif; ?>
		</p>
		<ul class="vd-media__list">
			<?php foreach ( $ids as $id ) : ?>
				<?php if ( wp_attachment_is( 'video', $id ) ) : ?>
					<li data-id="<?php echo (int) $id; ?>" class="is-video">
						<video src="<?php echo esc_url( wp_get_attachment_url( $id ) ); ?>" muted preload="metadata"></video>
						<button type="button" class="vd-media__remove" aria-label="Togli questo video">×</button>
					</li>
				<?php else : ?>
					<?php $thumb = wp_get_attachment_image_url( $id, 'thumbnail' ); ?>
					<?php if ( $thumb ) : ?>
						<li data-id="<?php echo (int) $id; ?>">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="">
							<button type="button" class="vd-media__remove" aria-label="Togli questa foto">×</button>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<button type="button" class="button vd-media__add"><span class="dashicons dashicons-plus-alt2"></span> Aggiungi foto</button>
		<button type="button" class="button-link vd-media__clear" <?php echo $ids ? '' : 'hidden'; ?>>Togli tutte</button>
	</div>
	<?php
}

/* ----------------------------------------------------------
 * Pagina
 * -------------------------------------------------------- */
function vd_pannello_pagina() {
	?>
	<div class="wrap vd-panel">
		<header class="vd-panel__head">
			<div>
				<h1>Gestione del sito</h1>
				<p>Cambia prezzi, corsi, orari e foto: le modifiche sono online appena premi <strong>Salva</strong>.</p>
			</div>
			<a class="button vd-btn-ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">
				<span class="dashicons dashicons-external"></span> Vedi il sito
			</a>
		</header>

		<?php if ( isset( $_GET['importate'] ) ) : // phpcs:ignore -- solo messaggio ?>
			<div class="notice notice-success is-dismissible vd-notice"><p><strong>Fatto!</strong> <?php echo (int) $_GET['importate']; // phpcs:ignore ?> foto sono ora nella libreria e nella galleria: puoi toglierle, riordinarle o aggiungerne altre.</p></div>
		<?php endif; ?>

		<?php if ( isset( $_GET['salvato'] ) ) : // phpcs:ignore -- solo messaggio ?>
			<div class="notice notice-success is-dismissible vd-notice"><p><strong>Fatto!</strong> Le modifiche sono già visibili sul sito.</p></div>
		<?php endif; ?>

		<nav class="vd-shortcuts" aria-label="Altre sezioni">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=torneo' ) ); ?>">
				<span class="dashicons dashicons-awards"></span><strong>Aggiungi un torneo</strong><small>Compare da solo in home finché la data non è passata</small>
			</a>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=torneo' ) ); ?>">
				<span class="dashicons dashicons-list-view"></span><strong>Tutti i tornei</strong><small>Modifica o cancella i tornei inseriti</small>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=vd' ) ); ?>">
				<span class="dashicons dashicons-admin-appearance"></span><strong>Testi, contatti e foto principali</strong><small>Nome, frase iniziale, telefono, indirizzo, link Playtomic</small>
			</a>
		</nav>

		<div class="vd-legend">
			<span><span class="dashicons dashicons-move"></span> trascina per cambiare l'ordine</span>
			<span><i class="vd-dot vd-dot--off"></i> spento = nascosto dal sito</span>
			<span><i class="vd-dot vd-dot--star"></i> in evidenza</span>
			<span><i class="vd-dot vd-dot--sale"></i> in offerta: il prezzo vecchio compare barrato</span>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="vd-form">
			<input type="hidden" name="action" value="vd_salva">
			<input type="hidden" name="vd_listini" value="1">
			<?php wp_nonce_field( 'vd_salva' ); ?>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-megaphone"></span> Pulsante in evidenza</h2>
				<p class="vd-help">Il cartello che "trema" nella prima schermata del sito, per lanciare una novità (un clinic, un evento, un'offerta).</p>
				<div class="vd-row<?php echo vd( 'promo_attiva' ) ? ' is-star' : ' is-off'; ?> vd-row--promo">
					<span class="vd-row__handle dashicons dashicons-megaphone" aria-hidden="true"></span>
					<div class="vd-row__fields">
						<?php vd_campo( 'promo_testo', 'Testo', vd( 'promo_testo' ), 'vd-f--nome', 'placeholder="es. Clinic di padel"' ); ?>
						<?php vd_campo( 'promo_etichetta', 'Targhetta', vd( 'promo_etichetta' ), '', 'placeholder="es. Novità"' ); ?>
						<?php vd_campo( 'promo_sotto', 'Testo piccolo', vd( 'promo_sotto' ), '', 'placeholder="es. Scopri di più"' ); ?>
						<?php vd_campo( 'promo_menu', 'Nome nel menu (vuoto = non compare)', vd( 'promo_menu' ), '', 'placeholder="es. Clinic"' ); ?>
						<label class="vd-f vd-f--wide"><span>Link <small>(vuoto = apre WhatsApp con la richiesta di informazioni; si può mettere anche #corsi o #tornei)</small></span>
							<input type="text" name="promo_link" value="<?php echo esc_attr( vd( 'promo_link' ) ); ?>" placeholder="https://… oppure #corsi">
						</label>
					</div>
					<div class="vd-row__flags">
						<label class="vd-toggle" title="Se spento, il pulsante non compare sul sito">
							<input type="checkbox" class="vd-js-visibile" name="promo_attiva" value="1" <?php checked( (bool) vd( 'promo_attiva' ) ); ?>>
							<span class="vd-toggle__ui" aria-hidden="true"></span> Visibile
						</label>
					</div>
				</div>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-format-aside"></span> Finestra della novità</h2>
				<p class="vd-help">Si apre toccando il pulsante in evidenza. Se la spegni, il pulsante usa il link indicato sopra.</p>
				<label class="vd-toggle vd-toggle--riga">
					<input type="checkbox" name="promo_finestra" value="1" <?php checked( (bool) vd( 'promo_finestra' ) ); ?>>
					<span class="vd-toggle__ui" aria-hidden="true"></span> Al clic apri la finestra
				</label>
				<div class="vd-grid-campi">
					<?php vd_campo( 'clinic_titolo', 'Titolo', vd( 'clinic_titolo' ), 'vd-f--nome', 'placeholder="Padel Clinic"' ); ?>
					<?php vd_campo( 'clinic_slogan', 'Frase sotto il titolo', vd( 'clinic_slogan' ), 'vd-f--due' ); ?>
					<?php vd_area( 'clinic_intro', 'Presentazione', vd( 'clinic_intro' ), 3 ); ?>
					<?php vd_campo( 'clinic_giorni', 'Giorni', vd( 'clinic_giorni' ), '', 'placeholder="es. 4-7"' ); ?>
					<?php vd_campo( 'clinic_ore', 'Ore al giorno', vd( 'clinic_ore' ), '', 'placeholder="4"' ); ?>
					<?php vd_campo( 'clinic_mattina', 'Mattina', vd( 'clinic_mattina' ), '' ); ?>
					<?php vd_campo( 'clinic_sera', 'Sera', vd( 'clinic_sera' ), '' ); ?>
					<?php vd_campo( 'clinic_mesi', 'Mesi (separati da virgola)', vd( 'clinic_mesi' ), 'vd-f--due', 'placeholder="Maggio, Giugno, Settembre, Ottobre"' ); ?>
					<?php vd_campo_euro( 'clinic_prezzo', 'Prezzo "da"', vd( 'clinic_prezzo' ), '400' ); ?>
					<?php vd_campo( 'clinic_prezzo_nota', 'Sotto il prezzo', vd( 'clinic_prezzo_nota' ), 'vd-f--due' ); ?>
					<?php vd_area( 'clinic_inclusi', 'Cosa comprende (una riga per voce; inizia con - per "non incluso")', vd( 'clinic_inclusi' ), 5 ); ?>
					<?php vd_area( 'clinic_locali', 'Partite con i giocatori del posto (vuoto = non compare)', vd( 'clinic_locali' ), 2 ); ?>
					<?php vd_area( 'clinic_alloggi', 'Alloggio: lo troviamo noi (vuoto = non compare)', vd( 'clinic_alloggi' ), 2 ); ?>
					<?php vd_area( 'clinic_finale', 'Frase finale, vicino ai pulsanti', vd( 'clinic_finale' ), 2 ); ?>
				</div>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-tag"></span> Tariffe del campo</h2>
				<p class="vd-help">Prezzo del campo intero. Il prezzo a persona sul sito viene calcolato da solo in base ai giocatori.</p>
				<?php vd_elenco( 'tariffe', vd_tariffe(), 'vd_riga_tariffa', 'Aggiungi una tariffa' ); ?>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-cart"></span> Extra</h2>
				<p class="vd-help">Palline, noleggi, bevande, accessori: tutto ciò che si può aggiungere alla partita.</p>
				<?php vd_elenco( 'extra', vd_extra(), 'vd_riga_extra', 'Aggiungi un extra' ); ?>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-welcome-learn-more"></span> Corsi</h2>
				<p class="vd-help">Ogni corso ha la sua descrizione e i suoi prezzi. Sul sito il primo corso in evidenza si apre da solo.</p>
				<?php vd_elenco( 'corsi', vd_corsi(), 'vd_riga_corso', 'Aggiungi un corso' ); ?>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-clock"></span> Orari</h2>
				<label class="vd-f vd-f--wide vd-f--orari">
					<span>Orari di apertura</span>
					<input type="text" name="orari" value="<?php echo esc_attr( vd( 'orari' ) ); ?>" placeholder="es. Tutti i giorni, dalle 8:00 alle 23:00">
				</label>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-format-gallery"></span> Galleria</h2>
				<?php
				vd_pannello_foto(
					'galleria_ids',
					'Scegli foto e video della galleria',
					'Foto e video (mp4). Trascinali per cambiare l\'ordine: i primi riempiono la griglia in home, gli altri si vedono con "Vedi tutte". I video partono da soli, senza audio; per l\'anteprima puoi dare al video un\'immagine in evidenza nella libreria media.',
					'Ora il sito mostra le foto e i video predefiniti del tema. Aggiungi i tuoi per sostituirli.',
					true
				);
				?>
			</section>

			<section class="vd-card">
				<h2><span class="dashicons dashicons-star-filled"></span> Sponsor</h2>
				<p class="vd-help">Banner orizzontali (circa 1600 × 240): sul sito si vedono per intero, uno sotto l'altro, nell'ordine scelto qui. Con il link, toccando il banner si apre il sito dello sponsor.</p>
				<?php vd_elenco( 'sponsor', vd_sponsor(), 'vd_riga_sponsor', 'Aggiungi uno sponsor' ); ?>
			</section>

			<div class="vd-savebar">
				<button type="submit" class="button button-primary button-hero">Salva le modifiche</button>
			</div>
		</form>
	</div>
	<?php
}
