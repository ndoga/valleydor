<?php
/**
 * Valley d'Or Padel – funzioni del tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VD_VERSION', '1.2.0' );

require get_template_directory() . '/inc/listini.php';
require get_template_directory() . '/inc/tornei.php';
require get_template_directory() . '/inc/pannello.php';
require get_template_directory() . '/inc/accesso.php';

/* ----------------------------------------------------------
 * Impostazioni di base
 * -------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'vd-fonts',
		'https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,500;0,700;0,800;1,700;1,800&family=Mulish:wght@400;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'vd-style', get_stylesheet_uri(), array( 'vd-fonts' ), VD_VERSION );
	wp_enqueue_script( 'vd-main', get_template_directory_uri() . '/assets/js/main.js', array(), VD_VERSION, true );
} );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );

/* ----------------------------------------------------------
 * Valori predefiniti (quelli del sito attuale)
 * -------------------------------------------------------- */
function vd_defaults() {
	$img = get_template_directory_uri() . '/assets/img/';
	$d   = array(
		// Contatti
		'telefono'    => '+39 388 489 1265',
		'whatsapp'    => '393884891265',
		'email'       => 'info@valleydor.it',
		'indirizzo'   => 'Via Marco Polo 2A, 07039 La Ciaccia – Valledoria (SS)',
		'orari'       => 'Tutti i giorni, dalle 8:00 alle 23:00',
		'lat'         => '40.9165323',
		'lng'         => '8.7817742',
		'playtomic'   => 'https://playtomic.io/tenant/d0b31b20-5115-4337-ac8f-0f835e87c6d9',
		'facebook'    => 'https://www.facebook.com/valleydorpadel',
		'instagram'   => 'https://www.instagram.com/valleydorpadel/',
		'modulo'      => '',
		// Apertura
		'hero_nome'   => "Valley d'Or",
		'hero_luogo'  => 'La Ciaccia · Valledoria · Sardegna',
		'hero_testo'  => "Due campi panoramici, istruttori federali e tornei tutto l'anno. Scegli l'orario, porta gli amici: al resto pensiamo noi.",
		'hero_img'    => $img . 'notte-1.jpg',
		// Prezzi e corsi della versione 1.1: valori iniziali dei listini (inc/listini.php)
		'prezzo_60'   => '32',
		'prezzo_90'   => '48',
		'palline'     => '7,50',
		'racchetta'   => '2,50',
		'overgrip'    => '2,50',
		// Corsi (prezzi delle lezioni facoltativi: se vuoti si mostra solo "chiedi info")
		'corsi_img'   => $img . 'corsi.jpg',
		'junior'      => '100',
		'lez_1'       => '',
		'lez_2'       => '',
		'lez_3'       => '',
		'all_1'       => '',
		'all_2'       => '',
		'cta_img'     => $img . 'notte-2.jpg',
		// Foto scelte dal pannello (ID della libreria media, separati da virgola)
		'galleria_ids' => '',
		'sponsor_ids'  => '',
	);

	$galleria = array( 'notte-2', 'campo-1', 'torneo-1', 'torneo-2', 'foto-6', 'campo-2', 'foto-3', 'foto-4', 'foto-5' );
	foreach ( $galleria as $i => $nome ) {
		$d[ 'galleria_' . ( $i + 1 ) ] = $img . $nome . '.jpg';
	}
	for ( $i = 1; $i <= 12; $i++ ) {
		$d[ 'sponsor_' . $i ] = $img . 'sponsor-' . $i . '.jpg';
	}
	return $d;
}

/** Valore di un'impostazione del Personalizza, con il predefinito. */
function vd( $key ) {
	$d = vd_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/**
 * Foto della galleria o degli sponsor.
 * Se dal pannello sono state scelte foto della libreria media si usano quelle,
 * altrimenti le foto predefinite del tema.
 *
 * @param string $tipo 'galleria' o 'sponsor'.
 * @return array Elenco di array( 'full' => url, 'thumb' => url, 'alt' => testo, 'id' => int ).
 */
function vd_foto( $tipo ) {
	$foto = array();
	foreach ( vd_ids( vd( $tipo . '_ids' ) ) as $id ) {
		$full = wp_get_attachment_image_url( $id, 'full' );
		if ( ! $full ) {
			continue;
		}
		$alt    = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$foto[] = array(
			'id'    => $id,
			'full'  => $full,
			'thumb' => wp_get_attachment_image_url( $id, 'sponsor' === $tipo ? 'medium_large' : 'large' ),
			'alt'   => $alt ? $alt : get_the_title( $id ),
		);
	}
	if ( $foto ) {
		return $foto;
	}

	$max = 'sponsor' === $tipo ? 12 : 9;
	for ( $i = 1; $i <= $max; $i++ ) {
		$url = vd( $tipo . '_' . $i );
		if ( $url ) {
			$foto[] = array(
				'id'    => 0,
				'full'  => $url,
				'thumb' => $url,
				'alt'   => 'sponsor' === $tipo ? 'Sponsor' : "Il centro Valley d'Or",
			);
		}
	}
	return $foto;
}

/** Elenco di ID numerici da una stringa "12,5,9". */
function vd_ids( $csv ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) $csv ) ) ) );
}

/** Collegamento a una sezione della home, valido da qualsiasi pagina. */
function vd_anchor( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}

/** Numero di telefono solo cifre, per i link tel:. */
function vd_tel() {
	return preg_replace( '/[^0-9+]/', '', vd( 'telefono' ) );
}

/** Logo: quello caricato in Personalizza oppure quello del tema. */
function vd_logo_url() {
	$id = get_theme_mod( 'custom_logo' );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return get_template_directory_uri() . '/assets/img/logo.png';
}

/** Icona del sito predefinita se non è stata impostata. */
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ) . '">' . "\n";
	}
	echo '<meta name="theme-color" content="#0b1214">' . "\n";
}, 2 );

/* ----------------------------------------------------------
 * Dati strutturati per Google (attività sportiva locale)
 * -------------------------------------------------------- */
add_action( 'wp_head', function () {
	if ( ! is_front_page() ) {
		return;
	}
	$data = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'SportsActivityLocation',
		'name'         => get_bloginfo( 'name' ),
		'url'          => home_url( '/' ),
		'image'        => vd( 'hero_img' ),
		'telephone'    => vd_tel(),
		'email'        => vd( 'email' ),
		'address'      => vd( 'indirizzo' ),
		'geo'          => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => vd( 'lat' ),
			'longitude' => vd( 'lng' ),
		),
		'sameAs'       => array_values( array_filter( array( vd( 'facebook' ), vd( 'instagram' ) ) ) ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
} );

/* ----------------------------------------------------------
 * Personalizza: testi, contatti e foto che cambiano di rado.
 * Prezzi, orari, corsi, galleria e sponsor si gestiscono dal
 * pannello "Valley d'Or" (inc/pannello.php), i tornei dal menu Tornei.
 * -------------------------------------------------------- */
add_action( 'customize_register', function ( $wp_customize ) {
	$d = vd_defaults();

	$wp_customize->add_panel( 'vd', array(
		'title'       => "Valley d'Or",
		'description' => 'Prezzi, orari, galleria e sponsor si cambiano dal menu "Valley d\'Or" della bacheca; i tornei dal menu "Tornei".',
		'priority'    => 30,
	) );

	$sezioni = array(
		'vd_contatti' => 'Contatti e prenotazioni',
		'vd_apertura' => 'Apertura della pagina',
		'vd_corsi'    => 'Corsi',
	);
	foreach ( $sezioni as $id => $titolo ) {
		$wp_customize->add_section( $id, array( 'title' => $titolo, 'panel' => 'vd' ) );
	}

	$testo = function ( $key, $section, $label, $type = 'text', $desc = '' ) use ( $wp_customize, $d ) {
		$sanitize = 'sanitize_text_field';
		if ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'sanitize_textarea_field';
		}
		$wp_customize->add_setting( $key, array(
			'default'           => $d[ $key ],
			'sanitize_callback' => $sanitize,
		) );
		$wp_customize->add_control( $key, array(
			'label'       => $label,
			'description' => $desc,
			'section'     => $section,
			'type'        => $type,
		) );
	};

	$foto = function ( $key, $section, $label ) use ( $wp_customize, $d ) {
		$wp_customize->add_setting( $key, array(
			'default'           => $d[ $key ],
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $key, array(
			'label'   => $label,
			'section' => $section,
		) ) );
	};

	// Contatti
	$testo( 'telefono', 'vd_contatti', 'Telefono' );
	$testo( 'whatsapp', 'vd_contatti', 'Numero WhatsApp', 'text', 'Solo cifre con prefisso internazionale, senza + (es. 393884891265).' );
	$testo( 'email', 'vd_contatti', 'Email', 'email' );
	$testo( 'indirizzo', 'vd_contatti', 'Indirizzo' );
	$testo( 'lat', 'vd_contatti', 'Latitudine (mappa)' );
	$testo( 'lng', 'vd_contatti', 'Longitudine (mappa)' );
	$testo( 'playtomic', 'vd_contatti', 'Link prenotazione Playtomic', 'url' );
	$testo( 'facebook', 'vd_contatti', 'Pagina Facebook', 'url' );
	$testo( 'instagram', 'vd_contatti', 'Profilo Instagram', 'url' );
	$testo( 'modulo', 'vd_contatti', 'Modulo contatti (shortcode)', 'text', 'Facoltativo. Es. lo shortcode di Contact Form 7: compare sotto i contatti.' );

	// Apertura
	$testo( 'hero_nome', 'vd_apertura', 'Nome in grande', 'text', "L'ultima parola viene evidenziata in verde." );
	$testo( 'hero_luogo', 'vd_apertura', 'Riga sopra il titolo' );
	$testo( 'hero_testo', 'vd_apertura', 'Testo sotto il titolo', 'textarea' );
	$foto( 'hero_img', 'vd_apertura', 'Foto di sfondo' );
	$foto( 'cta_img', 'vd_apertura', 'Foto della chiamata finale ("Pronto a scendere in campo?")' );

	// Corsi
	$foto( 'corsi_img', 'vd_corsi', 'Foto dell\'istruttore' );
} );
