<?php
/**
 * Area riservata: pagina di accesso con la grafica del sito, ruolo
 * "Gestore del centro" e bacheca semplificata per chi gestisce il centro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ----------------------------------------------------------
 * Pagina di accesso (wp-login.php)
 * -------------------------------------------------------- */
add_action( 'login_enqueue_scripts', function () {
	wp_enqueue_style(
		'vd-fonts',
		'https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,500;0,700;0,800;1,800&family=Mulish:wght@400;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'vd-login', get_template_directory_uri() . '/assets/css/login.css', array( 'login' ), VD_VERSION );
	wp_add_inline_style(
		'vd-login',
		'body.login{--vd-logo:url("' . esc_url_raw( vd_logo_url() ) . '");--vd-bg:url("' . esc_url_raw( vd( 'hero_img' ) ) . '");}'
	);
} );

add_filter( 'login_headerurl', function () {
	return home_url( '/' );
} );

add_filter( 'login_headertext', function () {
	return get_bloginfo( 'name' );
} );

add_filter( 'login_message', function ( $message ) {
	if ( isset( $_GET['action'] ) && 'login' !== $_GET['action'] ) { // phpcs:ignore -- solo lettura
		return $message;
	}
	return '<p class="vd-login-intro"><strong>Area riservata</strong>Accedi per gestire prezzi, foto e tornei.</p>' . $message;
} );

/* ----------------------------------------------------------
 * Ruolo "Gestore del centro": può gestire prezzi, foto, tornei
 * e notizie, ma non plugin, utenti, temi o impostazioni.
 * -------------------------------------------------------- */
function vd_capacita_gestore() {
	return array(
		'read'                   => true,
		'upload_files'           => true,
		'edit_theme_options'     => true,
		'edit_posts'             => true,
		'edit_others_posts'      => true,
		'edit_published_posts'   => true,
		'publish_posts'          => true,
		'delete_posts'           => true,
		'delete_others_posts'    => true,
		'delete_published_posts' => true,
		'manage_categories'      => true,
	);
}

function vd_crea_ruolo() {
	remove_role( 'vd_gestore' );
	add_role( 'vd_gestore', 'Gestore del centro', vd_capacita_gestore() );
}

// Alla attivazione del tema: ruolo e indirizzi dei tornei (/tornei/).
add_action( 'after_switch_theme', function () {
	vd_crea_ruolo();
	vd_registra_tornei();
	flush_rewrite_rules();
} );

// Aggiornamento del tema senza riattivarlo: ricrea ruolo e indirizzi una volta per versione.
add_action( 'init', function () {
	if ( get_option( 'vd_versione' ) !== VD_VERSION ) {
		vd_crea_ruolo();
		flush_rewrite_rules();
		update_option( 'vd_versione', VD_VERSION );
	}
}, 20 );

add_action( 'switch_theme', function () {
	remove_role( 'vd_gestore' );
	delete_option( 'vd_versione' );
} );

/** L'utente collegato è un gestore (e non un amministratore)? */
function vd_is_gestore() {
	return is_user_logged_in() && ! current_user_can( 'manage_options' ) && current_user_can( 'edit_theme_options' );
}

/* ----------------------------------------------------------
 * Dopo l'accesso il gestore arriva direttamente al pannello.
 * -------------------------------------------------------- */
add_filter( 'login_redirect', function ( $redirect, $requested, $user ) {
	if ( $user instanceof WP_User && ! $user->has_cap( 'manage_options' ) && $user->has_cap( 'edit_theme_options' ) ) {
		if ( ! $requested || admin_url() === $requested || admin_url( '/' ) === $requested ) {
			return vd_pannello_url();
		}
	}
	return $redirect;
}, 10, 3 );

/* ----------------------------------------------------------
 * Bacheca semplificata per il gestore
 * -------------------------------------------------------- */
add_action( 'admin_menu', function () {
	if ( ! vd_is_gestore() ) {
		return;
	}
	remove_menu_page( 'index.php' );          // Bacheca
	remove_menu_page( 'edit-comments.php' );  // Commenti
	remove_menu_page( 'tools.php' );          // Strumenti
	remove_submenu_page( 'themes.php', 'widgets.php' );
	remove_submenu_page( 'themes.php', 'nav-menus.php' );
}, 999 );

// La bacheca vuota di WordPress non serve al gestore: si va al pannello.
add_action( 'load-index.php', function () {
	if ( vd_is_gestore() ) {
		wp_safe_redirect( vd_pannello_url() );
		exit;
	}
} );

/* ----------------------------------------------------------
 * Barra in alto sul sito: scorciatoie per chi può gestirlo
 * -------------------------------------------------------- */
add_action( 'admin_bar_menu', function ( $bar ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$bar->add_node( array(
		'id'    => 'vd-pannello',
		'title' => '<span class="ab-icon dashicons dashicons-admin-site-alt3" style="top:2px"></span>Gestisci il sito',
		'href'  => vd_pannello_url(),
	) );
	$bar->add_node( array(
		'id'     => 'vd-nuovo-torneo',
		'parent' => 'vd-pannello',
		'title'  => 'Aggiungi un torneo',
		'href'   => admin_url( 'post-new.php?post_type=torneo' ),
	) );
	$bar->add_node( array(
		'id'     => 'vd-foto',
		'parent' => 'vd-pannello',
		'title'  => 'Prezzi, galleria e sponsor',
		'href'   => vd_pannello_url(),
	) );
}, 80 );
