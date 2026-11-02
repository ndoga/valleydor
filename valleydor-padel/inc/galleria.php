<?php
/**
 * Galleria predefinita del tema: foto del centro con i testi alternativi preparati
 * per la SEO. Si usa finché dal pannello non si scelgono foto (e video) della
 * libreria media.
 *
 * Foto nuove in assets/img/galleria/, quelle della galleria di prima in assets/img/.
 * I video si aggiungono dal pannello: il tema li mostra già nella griglia e nella lightbox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Voci della galleria predefinita, nell'ordine in cui compaiono.
 * Le prime 12 riempiono la griglia 4×4 della home senza buchi: una larga, una verticale,
 * quattro normali, una verticale, due normali, una verticale, due normali.
 * Le altre si aprono con "Vedi tutte".
 *
 * @return array Elenco di array( file, alt, verticale ).
 */
function vd_galleria_elenco() {
	return array(
		array( 'valleydor-padel-valledoria-campi-padel-sardegna-mare.jpg', "Due campi da padel affiancati con vista sul mare della Sardegna a Valledoria", false ),
		array( 'valleydor-padel-valledoria-campi-illuminati-tramonto.jpg', "Campi da padel illuminati al tramonto al Valley d'Or Padel di Valledoria", true ),
		array( 'valleydor-padel-valledoria-campo-padel-blu-vista-mare.jpg', "Campo da padel blu con vista mare al Valley d'Or Padel di Valledoria, Sardegna", false ),
		array( 'valleydor-padel-valledoria-club-house-dehors.jpg', "Club house con dehors all'aperto e campi da padel del Valley d'Or Padel, Valledoria", false ),
		array( 'valleydor-padel-valledoria-due-campi-padel-panoramici.jpg', "Due campi da padel panoramici con vetrate al Valley d'Or Padel, Valledoria", false ),
		array( 'valleydor-padel-valledoria-campo-blu-campo-nero.jpg', "Campo da padel blu e campo nero affiancati al Valley d'Or Padel di Valledoria", false ),
		array( 'valleydor-padel-valledoria-torneo-premiazione-doppio-misto.jpg', "Premiazione del torneo di padel doppio misto al Valley d'Or Padel di Valledoria", true ),
		array( 'valleydor-padel-valledoria-panoramica-club-sul-mare.jpg', "Panoramica del circolo Valley d'Or Padel di Valledoria affacciato sul mare", false ),
		array( 'valleydor-padel-valledoria-campo-padel-nero.jpg', "Campo da padel nero in primo piano e campo blu sullo sfondo, Valley d'Or Padel Valledoria", false ),
		array( 'valleydor-padel-valledoria-tramonto-sardegna.jpg', "Tramonto sui campi da padel del Valley d'Or Padel a Valledoria, Sardegna", true ),
		array( 'valleydor-padel-valledoria-vista-aerea-club-mare.jpg', "Vista aerea del Valley d'Or Padel di Valledoria con il mare sullo sfondo", false ),
		array( 'valleydor-padel-valledoria-campi-padel-vista-aerea.jpg', "Vista aerea dei due campi da padel del Valley d'Or Padel a Valledoria, con il paese sullo sfondo", false ),
		// da qui in poi: "Vedi tutte"
		array( 'valleydor-padel-valledoria-torneo-premiazione-doppio-maschile.jpg', "Premiazione del torneo di padel doppio maschile al Valley d'Or Padel di Valledoria", true ),
		array( 'valleydor-padel-valledoria-campi-e-club-house.jpg', "Campi da padel e club house del Valley d'Or Padel a Valledoria, Sardegna", false ),
		array( 'valleydor-padel-valledoria-campi-padel-vicino-al-mare.jpg', "Campi da padel immersi nel verde con il mare all'orizzonte a Valledoria", false ),
		array( 'valleydor-padel-valledoria-campi-padel-dall-alto.jpg', "Campi da padel nero e blu ripresi dall'alto al Valley d'Or Padel di Valledoria", false ),
		array( 'valleydor-padel-valledoria-campi-padel-foto-drone.jpg', "Foto con drone dall'alto dei campi da padel e della club house a Valledoria", false ),
		array( 'valleydor-padel-valledoria-campo-padel-e-club-house.jpg', "Club house e campo da padel nero del Valley d'Or Padel a Valledoria", false ),
		array( 'valleydor-padel-valledoria-campi-padel-all-aperto.jpg', "Due campi da padel all'aperto del Valley d'Or Padel a Valledoria", false ),
		// le foto della galleria di prima
		array( 'notte-2.jpg', "I campi del Valley d'Or visti dal drone", false ),
		array( 'campo-1.jpg', 'Partita al tramonto', false ),
		array( 'torneo-1.jpg', "Campo panoramico in vetro del Valley d'Or", false ),
		array( 'torneo-2.jpg', "Il centro Valley d'Or", false ),
		array( 'foto-6.jpg', "Il centro Valley d'Or", false ),
		array( 'campo-2.jpg', "Il centro Valley d'Or", true ),
		array( 'foto-3.jpg', "Il centro Valley d'Or", false ),
		array( 'foto-4.jpg', "Il centro Valley d'Or", false ),
		array( 'foto-5.jpg', 'Vista aerea del centro e del mare', false ),
	);
}

/** Galleria predefinita nel formato di vd_foto(). */
function vd_galleria_predefinita() {
	$base = get_template_directory_uri() . '/assets/img/';
	$out  = array();
	foreach ( vd_galleria_elenco() as $v ) {
		list( $file, $alt, $verticale ) = $v;
		$url   = $base . ( 0 === strpos( $file, 'valleydor-padel-' ) ? 'galleria/' : '' ) . $file;
		$out[] = array(
			'id'        => 0,
			'tipo'      => 'foto',
			'full'      => $url,
			'thumb'     => $url,
			'poster'    => '',
			'alt'       => $alt,
			'verticale' => $verticale,
		);
	}
	return $out;
}

// I video della libreria media possono avere un'immagine in evidenza da usare come anteprima.
add_action( 'init', function () {
	add_post_type_support( 'attachment:video', 'thumbnail' );
} );
