<?php
/**
 * Valley d'Or Padel – funzioni del tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VD_VERSION', '1.3.2' );

require get_template_directory() . '/inc/listini.php';
require get_template_directory() . '/inc/galleria.php';
require get_template_directory() . '/inc/sponsor.php';
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
		'hero_sotto'  => 'Padel',
		// Pulsante in evidenza nell'apertura (gestito dal pannello)
		'promo_attiva'    => '1',
		'promo_testo'     => 'Clinic di padel',
		'promo_etichetta' => 'Novità',
		'promo_sotto'     => 'Scopri di più',
		'promo_link'      => '',
		'promo_finestra'  => '1', // al clic apre la finestra della Clinic invece del link
		'promo_menu'      => 'Clinic', // voce nel menu (vuoto = non compare nel menu)
		// Finestra "Padel Clinic"
		'clinic_titolo'   => 'Padel Clinic',
		'clinic_slogan'   => 'Vivi il padel, migliora il tuo gioco.',
		'clinic_intro'    => "Una vacanza di padel in Sardegna, a 100 metri dal mare, pensata per chi arriva da fuori: tu porti la voglia di giocare, al resto pensiamo noi. Tecnica, tattica e tanta pratica in campo, seguiti passo passo da un istruttore federale, per tornare a casa con un gioco nuovo.",
		'clinic_giorni'   => '4-7',
		'clinic_ore'      => '4',
		'clinic_mattina'  => '2 ore in campo: tecnica e colpi',
		'clinic_sera'     => '2 ore in campo: tattica e gioco',
		'clinic_mesi'     => 'Maggio, Giugno, Settembre, Ottobre',
		'clinic_prezzo'   => '400',
		'clinic_prezzo_nota' => 'a persona · clinic di 4 giorni',
		'clinic_inclusi'  => "Da 4 a 7 giorni di clinic\n4 ore di allenamento al giorno\nIstruttore federale sempre con voi\nPartite con i giocatori del posto\nTi troviamo noi l'alloggio\nGruppi da almeno 4 partecipanti\n-Costo dell'alloggio a parte",
		'clinic_locali'   => "Fuori dagli allenamenti si gioca sul serio: organizziamo partite con i giocatori del posto, per mettere subito in pratica quello che hai imparato e conoscere la community di Valley d'Or.",
		'clinic_alloggi'  => 'Arrivi da fuori? Non devi cercare niente: ti aiutiamo noi a trovare la sistemazione giusta vicino al centro, in base al gruppo e al budget. Tu pensi solo al padel.',
		'clinic_finale'   => 'Raduna il tuo gruppo: clinic, partite e alloggio, ti prepariamo noi un preventivo su misura.',
		'clinic_img'      => $img . 'galleria/valleydor-padel-valledoria-campi-illuminati-tramonto.jpg',
		'hero_luogo'  => 'La Ciaccia · Valledoria · Sardegna',
		'hero_testo'  => "Due campi panoramici, istruttori federali e tornei tutto l'anno. Scegli l'orario, porta gli amici: al resto pensiamo noi.",
		'hero_img'    => $img . 'galleria/valleydor-padel-valledoria-vista-aerea-club-mare.jpg',
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
		'cta_img'     => $img . 'galleria/valleydor-padel-valledoria-tramonto-sardegna.jpg',
		// Foto scelte dal pannello (ID della libreria media, separati da virgola)
		'galleria_ids' => '',
		'sponsor_ids'  => '',
	);

	// La galleria predefinita è in inc/galleria.php (foto e video).
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
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$alt = $alt ? $alt : get_the_title( $id );

		// Video della libreria (solo in galleria): anteprima = immagine in evidenza, se c'è.
		if ( 'galleria' === $tipo && wp_attachment_is( 'video', $id ) ) {
			$url  = wp_get_attachment_url( $id );
			$meta = wp_get_attachment_metadata( $id );
			if ( ! $url ) {
				continue;
			}
			$poster = get_the_post_thumbnail_url( $id, 'large' );
			$foto[] = array(
				'id'        => $id,
				'tipo'      => 'video',
				'full'      => $url,
				'thumb'     => $poster ? $poster : '',
				'poster'    => $poster ? $poster : '',
				'alt'       => $alt,
				'verticale' => ! empty( $meta['height'] ) && ! empty( $meta['width'] ) && $meta['height'] > $meta['width'],
			);
			continue;
		}

		$full = wp_get_attachment_image_url( $id, 'full' );
		if ( ! $full ) {
			continue;
		}
		$meta   = wp_get_attachment_metadata( $id );
		$foto[] = array(
			'id'        => $id,
			'tipo'      => 'foto',
			'full'      => $full,
			'thumb'     => wp_get_attachment_image_url( $id, 'sponsor' === $tipo ? 'medium_large' : 'large' ),
			'poster'    => '',
			'alt'       => $alt,
			'verticale' => ! empty( $meta['height'] ) && ! empty( $meta['width'] ) && $meta['height'] > $meta['width'],
		);
	}
	if ( $foto ) {
		return $foto;
	}

	if ( 'galleria' === $tipo ) {
		return vd_galleria_predefinita();
	}
	for ( $i = 1; $i <= 12; $i++ ) {
		$url = vd( $tipo . '_' . $i );
		if ( $url ) {
			$foto[] = array(
				'id'        => 0,
				'tipo'      => 'foto',
				'full'      => $url,
				'thumb'     => $url,
				'poster'    => '',
				'alt'       => 'Sponsor',
				'verticale' => false,
			);
		}
	}
	return $foto;
}

/**
 * Crenatura (avvicinamento) tra due lettere maiuscole del carattere Kanit 800, in em.
 * Il nome in apertura è diviso in lettere separate per l'animazione e il browser non
 * applica più la crenatura del carattere: questi valori sono misurati da Kanit stesso
 * (coppie con almeno 0,03 em; es. VA = -0,135 em).
 */
function vd_crenatura( $a, $b ) {
	static $tabella = null;
	if ( null === $tabella ) {
		$righe   = array(
			"A:A-.035 C-.055 G-.055 O-.055 Q-.055 T-.085 U-.045 V-.135 W-.095 X-.030 Y-.145 '-.085",
			'B:A-.035 V-.065 X-.055 Y-.075',
			'C:V-.030',
			'D:A-.055 V-.050 X-.065 Y-.085',
			'F:A-.060 J-.030',
			'G:V-.035 Y-.045',
			'J:A-.035',
			'K:A-.045 C-.065 G-.065 O-.065 Q-.065 S-.035 T-.035 U-.050 V-.035 W-.030 X-.035 Y-.035',
			"L:T-.070 V-.125 W-.060 Y-.145 '-.045",
			'O:A-.055 V-.050 X-.065 Y-.085',
			'P:A-.080 J-.040 V-.045 W-.035 X-.055 Y-.045',
			'Q:A-.055 V-.050 X-.065 Y-.085',
			'R:A-.045 V-.065 W-.030 Y-.065',
			'S:A-.045 V-.045 Y-.045',
			'T:A-.085 J-.060 T.035',
			'U:A-.045 X-.035 Y-.035',
			'V:A-.135 C-.050 G-.050 J-.100 O-.050 Q-.050 S-.045 W-.035 Y-.035',
			'W:A-.095 J-.060 V-.035 X-.035 Y-.035',
			'X:A-.030 C-.065 G-.065 J-.055 O-.065 Q-.065 S-.045 W-.035 Y-.035 Z-.035',
			'Y:A-.145 C-.085 G-.085 J-.120 O-.085 Q-.085 S-.065 V-.035 W-.035 X-.035 Y-.035 Z-.035',
			'Z:W-.035 X-.035',
			"':A-.125 J-.085",
		);
		$tabella = array();
		foreach ( $righe as $riga ) {
			list( $prima, $coppie ) = explode( ':', $riga, 2 );
			foreach ( explode( ' ', $coppie ) as $c ) {
				$tabella[ $prima . $c[0] ] = (float) substr( $c, 1 );
			}
		}
	}
	// Il titolo è in maiuscolo; l'apostrofo tipografico vale come quello dritto.
	$coppia = str_replace( '’', "'", strtoupper( $a ) . strtoupper( $b ) );
	return isset( $tabella[ $coppia ] ) ? $tabella[ $coppia ] : 0;
}

/** Elenco di ID numerici da una stringa "12,5,9". */
function vd_ids( $csv ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) $csv ) ) ) );
}

/** Collegamento a una sezione della home, valido da qualsiasi pagina. */
function vd_anchor( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}

/**
 * Voce del menu per la novità in evidenza (es. "Clinic"), o null se non va mostrata.
 * Con la finestra attiva apre la finestra: nella home direttamente, dalle altre pagine
 * porta alla home con #clinic e la finestra si apre all'arrivo (assets/js/main.js).
 */
function vd_promo_menu() {
	if ( ! vd( 'promo_attiva' ) || ! vd( 'promo_testo' ) || ! vd( 'promo_menu' ) ) {
		return null;
	}
	if ( vd( 'promo_finestra' ) ) {
		$href  = is_front_page() ? '#clinic' : home_url( '/#clinic' );
		$extra = ' data-finestra="clinic" aria-haspopup="dialog"';
	} else {
		$whatsapp = 'https://wa.me/' . preg_replace( '/\D/', '', vd( 'whatsapp' ) );
		$href     = vd( 'promo_link' ) ? vd( 'promo_link' ) : $whatsapp . '?text=' . rawurlencode( 'Ciao, vorrei informazioni su: ' . vd( 'promo_testo' ) );
		if ( 0 === strpos( $href, '#' ) && ! is_front_page() ) {
			$href = home_url( '/' . $href );
		}
		$extra = 0 === strpos( $href, 'http' ) && false === strpos( $href, home_url() ) ? ' target="_blank" rel="noopener"' : '';
	}
	return array(
		'href'      => $href,
		'attr'      => $extra,
		'testo'     => vd( 'promo_menu' ),
		'etichetta' => vd( 'promo_etichetta' ),
	);
}

/** Stampa la voce del menu per la novità (vedi vd_promo_menu()). */
function vd_promo_menu_link( $classe = 'nav__new' ) {
	$m = vd_promo_menu();
	if ( ! $m ) {
		return;
	}
	echo '<a href="' . esc_url( $m['href'] ) . '" class="' . esc_attr( $classe ) . '"' . $m['attr'] . '>' . esc_html( $m['testo'] ); // phpcs:ignore -- attributi fissi
	if ( $m['etichetta'] ) {
		echo ' <span class="nav__bollino">' . esc_html( $m['etichetta'] ) . '</span>';
	}
	echo '</a>';
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
	$testo( 'hero_sotto', 'vd_apertura', 'Parola sotto il nome', 'text', 'Come nel logo, es. "Padel". Vuoto = non compare.' );
	$testo( 'hero_luogo', 'vd_apertura', 'Riga sopra il titolo' );
	$testo( 'hero_testo', 'vd_apertura', 'Testo sotto il titolo', 'textarea' );
	$foto( 'hero_img', 'vd_apertura', 'Foto di sfondo' );
	$foto( 'cta_img', 'vd_apertura', 'Foto della chiamata finale ("Pronto a scendere in campo?")' );

	// Corsi
	$foto( 'corsi_img', 'vd_corsi', 'Foto dell\'istruttore' );
} );
