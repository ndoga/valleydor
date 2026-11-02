<?php
/**
 * Sponsor: elenco modificabile dal pannello (immagine, nome, link, visibile, ordine).
 * Salvato come theme_mod 'sponsor'. Finché non viene salvato dal pannello si usano
 * i banner scelti con la versione precedente (sponsor_ids) oppure quelli del tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Sponsor predefiniti: banner del tema con nome e sito (quando è scritto sul banner). */
function vd_sponsor_predefiniti() {
	$img   = get_template_directory_uri() . '/assets/img/';
	$elenco = array(
		array( 'Agriturismo Bedda Ista', 'https://www.agriturismobeddaista.it' ),
		array( 'GDV s.r.l.', '' ),
		array( 'Ideal Sistem – infissi in PVC', '' ),
		array( 'Maguledda Costruzioni e Immobiliare', 'https://www.maguleddaimmobiliare.com' ),
		array( 'CL Costruzioni', 'https://www.clcostruzioni.com' ),
		array( 'Homes in Sardinia – vendite immobiliari', 'https://www.homesinsardinia.com' ),
		array( 'Gomme & Service – Centro revisioni Pala', '' ),
		array( 'Bar Aurelio', '' ),
		array( 'Tenda System – living outdoor design', 'https://www.tendaggivalledoria.com' ),
		array( 'STEM Costruzioni', '' ),
		array( 'Ital Home – gruppo agenzie immobiliari', '' ),
		array( 'HB Agency – advertising solution', 'https://www.hbagency.it' ),
	);
	$out = array();
	foreach ( $elenco as $i => $s ) {
		$out[] = vd_sponsor_voce( array(
			'img_url' => $img . 'sponsor-' . ( $i + 1 ) . '.jpg',
			'nome'    => $s[0],
			'link'    => $s[1],
		) );
	}
	return $out;
}

/** Sponsor completo di tutti i campi. */
function vd_sponsor_voce( $s ) {
	return array_merge( array(
		'img_id'   => 0,
		'img_url'  => '',
		'nome'     => '',
		'link'     => '',
		'visibile' => true,
	), $s );
}

/** Tutti gli sponsor, nell'ordine scelto. */
function vd_sponsor() {
	$s = get_theme_mod( 'sponsor' );
	if ( is_array( $s ) ) {
		return array_map( 'vd_sponsor_voce', $s );
	}
	// Versione 1.1–1.3: solo immagini scelte dalla libreria.
	$ids = vd_ids( get_theme_mod( 'sponsor_ids', '' ) );
	if ( $ids ) {
		$out = array();
		foreach ( $ids as $id ) {
			$out[] = vd_sponsor_voce( array( 'img_id' => $id, 'nome' => get_the_title( $id ) ) );
		}
		return $out;
	}
	return vd_sponsor_predefiniti();
}

/** Indirizzo dell'immagine di uno sponsor ('' se manca). */
function vd_sponsor_img( $s, $misura = 'full' ) {
	if ( ! empty( $s['img_id'] ) ) {
		$url = wp_get_attachment_image_url( (int) $s['img_id'], $misura );
		if ( $url ) {
			return $url;
		}
	}
	return $s['img_url'];
}

/** Sponsor da mostrare sul sito: visibili e con un'immagine. */
function vd_sponsor_visibili() {
	return array_values( array_filter( vd_sponsor(), function ( $s ) {
		return ! empty( $s['visibile'] ) && '' !== vd_sponsor_img( $s );
	} ) );
}

/** Pulizia dei dati in arrivo dal pannello. */
function vd_pulisci_sponsor( $raw ) {
	$out = array();
	if ( ! is_array( $raw ) ) {
		return $out;
	}
	foreach ( array_slice( $raw, 0, 60 ) as $s ) {
		if ( ! is_array( $s ) ) {
			continue;
		}
		$id  = isset( $s['img_id'] ) ? absint( $s['img_id'] ) : 0;
		$url = isset( $s['img_url'] ) ? esc_url_raw( $s['img_url'] ) : '';
		if ( $id && ! wp_attachment_is_image( $id ) ) {
			$id = 0;
		}
		// Un indirizzo diretto è ammesso solo per i banner del tema.
		if ( $url && 0 !== strpos( $url, get_template_directory_uri() ) ) {
			$url = '';
		}
		if ( ! $id && ! $url ) {
			continue; // riga senza immagine: non si salva
		}
		$out[] = vd_sponsor_voce( array(
			'img_id'   => $id,
			'img_url'  => $id ? '' : $url,
			'nome'     => sanitize_text_field( isset( $s['nome'] ) ? $s['nome'] : '' ),
			'link'     => esc_url_raw( isset( $s['link'] ) ? trim( $s['link'] ) : '' ),
			'visibile' => ! empty( $s['visibile'] ),
		) );
	}
	return $out;
}
