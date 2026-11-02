<?php
/**
 * Prepara la demo su WordPress Playground (eseguito dal passo runPHP di demo/blueprint.json,
 * che è generato da demo/genera-blueprint.py: modificare questo file e rigenerare).
 *
 * Crea l'utente "gestore" (ruolo Gestore del centro) e ricrea la configurazione del sito
 * esportata in demo/dati.json (demo/esporta-dal-sito.php): impostazioni del tema, galleria,
 * sponsor, tornei e foto caricate a mano (scaricate da Playground in /wordpress/wp-content/vd-demo/).
 * Tutto vive solo nel browser di chi apre la demo e sparisce chiudendo la scheda.
 */
require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

update_option( 'permalink_structure', '/%postname%/' );
update_option( 'timezone_string', 'Europe/Rome' );
update_option( 'blogdescription', 'Padel a 100 metri dal mare' );

// Ruolo e indirizzi /tornei/ (li crea anche il tema al primo caricamento).
if ( function_exists( 'vd_crea_ruolo' ) ) {
	vd_crea_ruolo();
	vd_registra_tornei();
}

// Utente demo del titolare.
if ( ! get_user_by( 'login', 'gestore' ) ) {
	wp_insert_user( array(
		'user_login'   => 'gestore',
		'user_pass'    => 'demo',
		'user_email'   => 'gestore@example.test',
		'display_name' => 'Titolare',
		'role'         => 'vd_gestore',
		'locale'       => 'it_IT',
	) );
}

$cartella = WP_CONTENT_DIR . '/vd-demo/';
$dati     = json_decode( (string) @file_get_contents( $cartella . 'dati.json' ), true );
if ( ! is_array( $dati ) ) {
	$dati = array();
}
$dati = array_merge( array( 'mods' => array(), 'galleria' => array(), 'tornei' => array(), 'media' => array() ), $dati );

// Carica un file nella libreria media.
$carica = function ( $percorso, $alt = '', $titolo = '' ) {
	if ( ! file_exists( $percorso ) ) {
		return 0;
	}
	$nome = basename( $percorso );
	$tmp  = wp_tempnam( $nome );
	copy( $percorso, $tmp );
	$id = media_handle_sideload( array( 'name' => $nome, 'tmp_name' => $tmp ), 0, $titolo ? $titolo : null );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
};

$img_tema = get_template_directory() . '/assets/img/';
$ids_tema = array(); // nome file del tema => id nella libreria
$media    = array(); // nome file in vd-demo => id nella libreria

// Voce dell'esportazione ({tema|media, alt}) → id nella libreria, senza doppioni.
$importa = function ( $v ) use ( $carica, $img_tema, $cartella, &$ids_tema, &$media ) {
	$alt = isset( $v['alt'] ) ? $v['alt'] : '';
	if ( ! empty( $v['tema'] ) ) {
		$f = $v['tema'];
		if ( ! isset( $ids_tema[ $f ] ) ) {
			$sotto          = 0 === strpos( $f, 'valleydor-padel-' ) ? 'galleria/' : '';
			$ids_tema[ $f ] = $carica( $img_tema . $sotto . $f, $alt );
			if ( $ids_tema[ $f ] ) {
				update_post_meta( $ids_tema[ $f ], '_vd_origine', $f );
			}
		}
		return $ids_tema[ $f ];
	}
	if ( ! empty( $v['media'] ) ) {
		$f = $v['media'];
		if ( ! isset( $media[ $f ] ) ) {
			$media[ $f ] = $carica( $cartella . $f, $alt, isset( $v['titolo'] ) ? $v['titolo'] : '' );
		}
		return $media[ $f ];
	}
	return 0;
};

// Galleria nell'ordine esportato (senza esportazione: quella predefinita del tema).
$elenco_galleria = $dati['galleria'];
if ( ! $elenco_galleria ) {
	foreach ( vd_galleria_elenco() as $voce ) {
		$elenco_galleria[] = array( 'tema' => $voce[0], 'alt' => $voce[1] );
	}
}
$galleria = array();
foreach ( $elenco_galleria as $v ) {
	$id = $importa( $v );
	if ( $id ) {
		$galleria[] = $id;
	}
}
set_theme_mod( 'galleria_ids', implode( ',', $galleria ) );

// Foto caricate a mano usate nelle impostazioni (es. foto dell'istruttore).
foreach ( $dati['media'] as $f ) {
	$importa( array( 'media' => $f ) );
}

// Impostazioni del tema: segnaposto {tema} e {media:file} → indirizzi della demo.
$tema_url    = get_template_directory_uri() . '/';
$sostituisci = function ( $v ) use ( &$sostituisci, $tema_url, &$media ) {
	if ( is_array( $v ) ) {
		return array_map( $sostituisci, $v );
	}
	if ( ! is_string( $v ) ) {
		return $v;
	}
	$v = str_replace( '{tema}', $tema_url, $v );
	return preg_replace_callback( '/\{media:([^}]+)\}/', function ( $m ) use ( &$media ) {
		return ! empty( $media[ $m[1] ] ) ? wp_get_attachment_url( $media[ $m[1] ] ) : '';
	}, $v );
};
foreach ( $dati['mods'] as $k => $v ) {
	set_theme_mod( $k, $sostituisci( $v ) );
}

// Tornei: quelli esportati; senza esportazione, tre di esempio con date future.
$tornei = $dati['tornei'];
if ( ! $tornei ) {
	foreach ( array(
		array( 'Torneo del weekend', '+12 days', 'Doppio misto', '20 € a coppia' ),
		array( "Coppa Valley d'Or", '+30 days', 'Doppio maschile', '25 € a coppia' ),
		array( 'Torneo rosa', '+50 days', 'Doppio femminile', '15 € a coppia' ),
	) as $t ) {
		$tornei[] = array(
			'titolo' => $t[0],
			'meta'   => array( '_vd_data' => wp_date( 'Y-m-d', strtotime( $t[1] ) ), '_vd_categoria' => $t[2], '_vd_quota' => $t[3] ),
		);
	}
}
foreach ( $tornei as $t ) {
	$id = wp_insert_post( array(
		'post_type'    => 'torneo',
		'post_status'  => 'publish',
		'post_title'   => $t['titolo'],
		'post_content' => isset( $t['testo'] ) ? $t['testo'] : '',
		'post_excerpt' => isset( $t['riassunto'] ) ? $t['riassunto'] : '',
	) );
	if ( ! $id || is_wp_error( $id ) ) {
		continue;
	}
	foreach ( $t['meta'] as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	if ( ! empty( $t['foto'] ) ) {
		$foto = $importa( $t['foto'] );
		if ( $foto ) {
			set_post_thumbnail( $id, $foto );
		}
	}
}

flush_rewrite_rules();
