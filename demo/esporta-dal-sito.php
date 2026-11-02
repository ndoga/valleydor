<?php
/**
 * Esporta la configurazione di un sito WordPress con il tema Valley d'Or nella demo
 * Playground: impostazioni del tema, galleria, sponsor, tornei e foto caricate a mano.
 *
 * Uso (dalla cartella del repository):
 *   php demo/esporta-dal-sito.php C:/percorso/del/wordpress
 *   python demo/genera-blueprint.py
 *
 * Scrive demo/dati.json e copia in demo/media/ le foto della libreria che non sono
 * già nel tema (le PNG grandi diventano JPG). La demo le importa all'avvio
 * (demo/prepara-demo.php).
 */

if ( empty( $argv[1] ) || ! file_exists( rtrim( $argv[1], '/\\' ) . '/wp-load.php' ) ) {
	fwrite( STDERR, "Indica la cartella del WordPress: php demo/esporta-dal-sito.php C:/percorso/wordpress\n" );
	exit( 1 );
}
$cartella_wp = rtrim( $argv[1], '/\\' );
$_SERVER['HTTP_HOST']   = 'localhost:8777';
$_SERVER['REQUEST_URI'] = '/';
chdir( $cartella_wp );
require $cartella_wp . '/wp-load.php'; // non $wp: è un oggetto di WordPress

$qui   = __DIR__;
$media = $qui . '/media';
if ( ! is_dir( $media ) ) {
	mkdir( $media );
}
array_map( 'unlink', glob( $media . '/*' ) ); // si riparte da zero a ogni esportazione

$tema    = get_template_directory_uri() . '/';
$uploads = wp_get_upload_dir();
$file_media = array(); // id allegato => nome del file in demo/media

/** Copia un allegato della libreria in demo/media (PNG grandi → JPG) e ne restituisce il nome. */
$copia = function ( $id ) use ( $media, &$file_media ) {
	if ( isset( $file_media[ $id ] ) ) {
		return $file_media[ $id ];
	}
	$src = get_attached_file( $id );
	if ( ! $src || ! file_exists( $src ) ) {
		return '';
	}
	$nome = sanitize_file_name( basename( $src ) );
	if ( preg_match( '/\.png$/i', $nome ) && filesize( $src ) > 400 * 1024 && function_exists( 'imagecreatefrompng' ) ) {
		$im = imagecreatefrompng( $src );
		$w  = imagesx( $im );
		$h  = imagesy( $im );
		$k  = min( 1, 1600 / max( $w, $h ) );
		$out = imagecreatetruecolor( (int) ( $w * $k ), (int) ( $h * $k ) );
		imagefill( $out, 0, 0, imagecolorallocate( $out, 255, 255, 255 ) );
		imagecopyresampled( $out, $im, 0, 0, 0, 0, (int) ( $w * $k ), (int) ( $h * $k ), $w, $h );
		$nome = preg_replace( '/\.png$/i', '.jpg', $nome );
		imagejpeg( $out, $media . '/' . $nome, 82 );
	} else {
		copy( $src, $media . '/' . $nome );
	}
	$file_media[ $id ] = $nome;
	return $nome;
};

/** Descrizione di un allegato: dal tema (assets) o copiato in demo/media. */
$voce_media = function ( $id ) use ( $copia ) {
	$origine = get_post_meta( $id, '_vd_origine', true );
	$alt     = get_post_meta( $id, '_wp_attachment_image_alt', true );
	if ( $origine ) {
		return array( 'tema' => $origine, 'alt' => $alt );
	}
	return array( 'media' => $copia( $id ), 'alt' => $alt, 'titolo' => get_the_title( $id ) );
};

// Indirizzi del sito di partenza → segnaposto, sostituiti nella demo.
$segnaposto = function ( $v ) use ( &$segnaposto, $tema, $uploads, $copia ) {
	if ( is_array( $v ) ) {
		return array_map( $segnaposto, $v );
	}
	if ( ! is_string( $v ) ) {
		return $v;
	}
	$v = str_replace( 'istruttore FITP', 'istruttore federale', $v );
	$v = str_replace( $tema, '{tema}', $v );
	if ( 0 === strpos( $v, $uploads['baseurl'] ) ) {
		$id = attachment_url_to_postid( $v );
		if ( $id ) {
			$v = '{media:' . $copia( $id ) . '}';
		}
	}
	return $v;
};

// Impostazioni del tema (si lasciano fuori quelle di WordPress).
$mods = get_theme_mods();
foreach ( array( 'nav_menu_locations', 'custom_css_post_id', 'sidebars_widgets', 'galleria_ids', 'sponsor_ids', 'custom_logo' ) as $k ) {
	unset( $mods[ $k ] );
}
// Sponsor dalla libreria: l'immagine va copiata come le altre.
if ( isset( $mods['sponsor'] ) && is_array( $mods['sponsor'] ) ) {
	foreach ( $mods['sponsor'] as &$s ) {
		if ( ! empty( $s['img_id'] ) ) {
			$s['img_url'] = '{media:' . $copia( (int) $s['img_id'] ) . '}';
			$s['img_id']  = 0;
		}
	}
	unset( $s );
}
$mods = $segnaposto( $mods );

// Galleria nell'ordine scelto.
$galleria = array();
foreach ( vd_ids( get_theme_mod( 'galleria_ids', '' ) ) as $id ) {
	$galleria[] = $voce_media( $id );
}

// Tornei.
$tornei = array();
foreach ( get_posts( array( 'post_type' => 'torneo', 'post_status' => array( 'publish', 'future' ), 'numberposts' => -1, 'orderby' => 'date', 'order' => 'ASC' ) ) as $p ) {
	$t = array(
		'titolo'    => $p->post_title,
		'testo'     => $p->post_content,
		'riassunto' => $p->post_excerpt,
		'meta'      => array(),
	);
	foreach ( array( '_vd_data', '_vd_orario', '_vd_categoria', '_vd_quota', '_vd_iscrizioni' ) as $k ) {
		$t['meta'][ $k ] = get_post_meta( $p->ID, $k, true );
	}
	if ( get_post_thumbnail_id( $p ) ) {
		$t['foto'] = $voce_media( get_post_thumbnail_id( $p ) );
	}
	$tornei[] = $t;
}

$dati = array(
	'esportato' => wp_date( 'Y-m-d H:i' ),
	'sito'      => array( 'nome' => get_bloginfo( 'name' ), 'descrizione' => get_bloginfo( 'description' ) ),
	'mods'      => $mods,
	'galleria'  => $galleria,
	'tornei'    => $tornei,
	'media'     => array_values( $file_media ),
);
file_put_contents( $qui . '/dati.json', wp_json_encode( $dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );

printf(
	"demo/dati.json: %d impostazioni, galleria %d foto, %d tornei, %d file in demo/media\n",
	count( $mods ),
	count( $galleria ),
	count( $tornei ),
	count( $file_media )
);
