<?php
/**
 * Prepara la demo su WordPress Playground (eseguito dal passo runPHP di demo/blueprint.json,
 * che è generato da demo/genera-blueprint.py: modificare questo file e rigenerare).
 *
 * Crea l'utente "gestore" (ruolo Gestore del centro), carica le foto del tema nella
 * libreria media per la galleria, aggiunge tre tornei di esempio.
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

// Foto del tema nella libreria media (stesso elenco della galleria predefinita).
$dir    = get_template_directory() . '/assets/img/';
$carica = function ( $percorso, $alt = '' ) {
	$nome = basename( $percorso );
	$tmp  = wp_tempnam( $nome );
	copy( $percorso, $tmp );
	$id = media_handle_sideload( array( 'name' => $nome, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
};
$foto = function ( $nome ) use ( $carica, $dir ) {
	return $carica( $dir . $nome );
};
$galleria = array();
$torneo   = 0;
foreach ( vd_galleria_elenco() as $voce ) {
	list( $file, $alt ) = $voce;
	$id = $carica( $dir . ( 0 === strpos( $file, 'valleydor-padel-' ) ? 'galleria/' : '' ) . $file, $alt );
	if ( false !== strpos( $file, 'torneo-premiazione' ) && ! $torneo ) {
		$torneo = $id;
	}
	$galleria[] = $id;
}
$foto( 'corsi.jpg' );
set_theme_mod( 'galleria_ids', implode( ',', array_filter( $galleria ) ) );
// Sponsor: si usano quelli predefiniti del tema (con nomi e link).

// Tornei di esempio (date relative a oggi).
$tornei = array(
	array( 'Torneo del weekend', '+12 days', 'Doppio misto', '20 € a coppia', 'dalle 9:00', 'Due giorni di padel a 100 metri dal mare. Premi per le prime tre coppie e grigliata finale per tutti.' ),
	array( "Coppa Valley d'Or", '+30 days', 'Doppio maschile', '25 € a coppia', 'dalle 10:00', 'Il torneo storico del centro: tabellone a eliminazione diretta, finale in notturna.' ),
	array( 'Torneo rosa', '+50 days', 'Doppio femminile', '15 € a coppia', '', 'Torneo dedicato alle giocatrici di tutti i livelli, con gironi e tabellone finale.' ),
);
foreach ( $tornei as $t ) {
	$id = wp_insert_post( array(
		'post_type'    => 'torneo',
		'post_status'  => 'publish',
		'post_title'   => $t[0],
		'post_content' => $t[5],
	) );
	update_post_meta( $id, '_vd_data', wp_date( 'Y-m-d', strtotime( $t[1] ) ) );
	update_post_meta( $id, '_vd_categoria', $t[2] );
	update_post_meta( $id, '_vd_quota', $t[3] );
	update_post_meta( $id, '_vd_orario', $t[4] );
	if ( $torneo ) {
		set_post_thumbnail( $id, $torneo );
	}
}

flush_rewrite_rules();
