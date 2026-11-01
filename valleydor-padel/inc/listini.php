<?php
/**
 * Listini: tariffe del campo, extra e corsi come elenchi modificabili dal pannello.
 * Ogni voce può essere mostrata o nascosta, messa in offerta e messa in evidenza.
 * Sono salvati come theme_mod ('tariffe', 'extra', 'corsi'); finché non vengono salvati
 * dal pannello si costruiscono dai valori della versione 1.1 (prezzo_60, lez_1, …).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VD_MAX_VOCI', 30 );

/* ----------------------------------------------------------
 * Lettura (con valori iniziali)
 * -------------------------------------------------------- */

function vd_tariffe() {
	$t = get_theme_mod( 'tariffe' );
	if ( is_array( $t ) ) {
		return $t;
	}
	$note = "Disdetta entro 24h\nCampo panoramico";
	return array(
		vd_voce( array( 'nome' => '1 ora', 'prezzo' => vd( 'prezzo_60' ), 'persone' => '4', 'note' => $note ) ),
		vd_voce( array( 'nome' => '1 ora e 30', 'prezzo' => vd( 'prezzo_90' ), 'persone' => '4', 'note' => $note, 'evidenza' => true, 'etichetta' => 'Più scelto' ) ),
	);
}

function vd_extra() {
	$e = get_theme_mod( 'extra' );
	if ( is_array( $e ) ) {
		return $e;
	}
	return array(
		vd_voce( array( 'nome' => 'Tubo di palline', 'prezzo' => vd( 'palline' ) ) ),
		vd_voce( array( 'nome' => 'Noleggio racchetta', 'prezzo' => vd( 'racchetta' ) ) ),
		vd_voce( array( 'nome' => 'Overgrip', 'prezzo' => vd( 'overgrip' ) ) ),
	);
}

function vd_corsi() {
	$c = get_theme_mod( 'corsi' );
	if ( is_array( $c ) ) {
		return $c;
	}
	$righe = function ( $prezzi ) {
		$r = array();
		foreach ( $prezzi as $chi => $prezzo ) {
			$r[] = vd_riga( array( 'chi' => $chi, 'prezzo' => $prezzo ) );
		}
		return $r;
	};
	return array(
		vd_voce( array(
			'nome'        => 'Lezioni con istruttore FITP',
			'descrizione' => 'Lezioni individuali o di gruppo, per imparare i colpi base o perfezionare la tecnica.',
			'righe'       => $righe( array( '1 persona' => vd( 'lez_1' ), '2 persone' => vd( 'lez_2' ), '3 persone' => vd( 'lez_3' ) ) ),
		) ),
		vd_voce( array(
			'nome'        => 'Allenamenti personalizzati',
			'descrizione' => 'Programmi su misura per atleti singoli o coppie che preparano tornei e competizioni.',
			'righe'       => $righe( array( '1 persona' => vd( 'all_1' ), '2 persone' => vd( 'all_2' ) ) ),
		) ),
		vd_voce( array(
			'nome'        => 'Scuola padel junior',
			'descrizione' => 'Il padel per i più giovani: otto lezioni al mese con istruttore federale.',
			'righe'       => array( vd_riga( array( 'chi' => 'Al mese', 'prezzo' => vd( 'junior' ), 'unita' => '8 lezioni' ) ) ),
		) ),
	);
}

/** Voce completa di tutti i campi (tariffa, extra o corso). */
function vd_voce( $v ) {
	return array_merge( array(
		'nome'        => '',
		'prezzo'      => '',
		'offerta'     => '',
		'persone'     => '',
		'note'        => '',
		'descrizione' => '',
		'visibile'    => true,
		'evidenza'    => false,
		'etichetta'   => '',
		'righe'       => array(),
	), $v );
}

/** Riga di prezzo di un corso. */
function vd_riga( $r ) {
	return array_merge( array(
		'chi'     => '',
		'prezzo'  => '',
		'unita'   => '',
		'offerta' => '',
	), $r );
}

/** Solo le voci da mostrare sul sito. */
function vd_visibili( $voci ) {
	return array_values( array_filter( $voci, function ( $v ) {
		return ! empty( $v['visibile'] ) && '' !== trim( $v['nome'] );
	} ) );
}

/* ----------------------------------------------------------
 * Prezzi
 * -------------------------------------------------------- */

/** Numero da un prezzo scritto all'italiana ("7,50" → 7.5); null se non è un numero. */
function vd_num( $prezzo ) {
	$s = str_replace( array( '€', ' ' ), '', (string) $prezzo );
	$s = str_replace( ',', '.', $s );
	return is_numeric( $s ) ? (float) $s : null;
}

/** Formato italiano: 8 → "8", 7.5 → "7,50". */
function vd_fmt( $n ) {
	return ( floor( $n ) == $n ) ? number_format( $n, 0, ',', '.' ) : number_format( $n, 2, ',', '.' );
}

/** Prezzo effettivo: quello in offerta se c'è. */
function vd_prezzo_attuale( $v ) {
	return '' !== trim( (string) $v['offerta'] ) ? $v['offerta'] : $v['prezzo'];
}

/** È in offerta? (prezzo d'offerta compilato) */
function vd_in_offerta( $v ) {
	return '' !== trim( (string) $v['offerta'] ) && '' !== trim( (string) $v['prezzo'] );
}

/** Prezzo a persona di una tariffa ("" se non calcolabile). */
function vd_tariffa_persona( $t ) {
	$n = vd_num( vd_prezzo_attuale( $t ) );
	$p = (int) $t['persone'];
	return ( null !== $n && $p > 1 ) ? vd_fmt( $n / $p ) : '';
}

/** Mostra un prezzo: "7,50 €" oppure, se in offerta, "<del>7,50 €</del> 5 €". */
function vd_prezzo_html( $v, $unita = '' ) {
	$euro = function ( $p ) {
		$n = vd_num( $p );
		return null !== $n ? vd_fmt( $n ) . ' €' : $p;
	};
	$out = '';
	if ( vd_in_offerta( $v ) ) {
		$out .= '<del>' . esc_html( $euro( $v['prezzo'] ) ) . '</del> ';
	}
	$out .= '<b>' . esc_html( $euro( vd_prezzo_attuale( $v ) ) ) . '</b>';
	if ( $unita ) {
		$out .= ' <small>' . esc_html( $unita ) . '</small>';
	}
	return $out;
}

/* ----------------------------------------------------------
 * Pulizia dei dati in arrivo dal pannello
 * -------------------------------------------------------- */

function vd_pulisci_voci( $raw, $tipo ) {
	$out = array();
	if ( ! is_array( $raw ) ) {
		return $out;
	}
	foreach ( array_slice( $raw, 0, VD_MAX_VOCI ) as $v ) {
		if ( ! is_array( $v ) ) {
			continue;
		}
		$voce = vd_voce( array(
			'nome'      => sanitize_text_field( isset( $v['nome'] ) ? $v['nome'] : '' ),
			'prezzo'    => sanitize_text_field( isset( $v['prezzo'] ) ? $v['prezzo'] : '' ),
			'offerta'   => sanitize_text_field( isset( $v['offerta'] ) ? $v['offerta'] : '' ),
			'visibile'  => ! empty( $v['visibile'] ),
			'evidenza'  => ! empty( $v['evidenza'] ),
			'etichetta' => sanitize_text_field( isset( $v['etichetta'] ) ? $v['etichetta'] : '' ),
		) );
		if ( 'tariffe' === $tipo ) {
			$voce['persone'] = (string) absint( isset( $v['persone'] ) ? $v['persone'] : 4 );
			$voce['note']    = sanitize_textarea_field( isset( $v['note'] ) ? $v['note'] : '' );
		}
		if ( 'corsi' === $tipo ) {
			$voce['descrizione'] = sanitize_textarea_field( isset( $v['descrizione'] ) ? $v['descrizione'] : '' );
			$righe               = isset( $v['righe'] ) && is_array( $v['righe'] ) ? $v['righe'] : array();
			foreach ( array_slice( $righe, 0, VD_MAX_VOCI ) as $r ) {
				if ( ! is_array( $r ) ) {
					continue;
				}
				$riga = vd_riga( array(
					'chi'     => sanitize_text_field( isset( $r['chi'] ) ? $r['chi'] : '' ),
					'prezzo'  => sanitize_text_field( isset( $r['prezzo'] ) ? $r['prezzo'] : '' ),
					'unita'   => sanitize_text_field( isset( $r['unita'] ) ? $r['unita'] : '' ),
					'offerta' => sanitize_text_field( isset( $r['offerta'] ) ? $r['offerta'] : '' ),
				) );
				if ( '' !== $riga['chi'] || '' !== $riga['prezzo'] ) {
					$voce['righe'][] = $riga;
				}
			}
		}
		// Le righe lasciate completamente vuote non si salvano.
		if ( '' === $voce['nome'] && '' === $voce['prezzo'] && empty( $voce['righe'] ) ) {
			continue;
		}
		$out[] = $voce;
	}
	return $out;
}
