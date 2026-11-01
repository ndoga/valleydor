<?php
/**
 * Tornei: tipo di contenuto con data, categoria, quota e link di iscrizione.
 * I tornei futuri compaiono da soli in home; l'elenco completo è su /tornei/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vd_registra_tornei() {
	register_post_type( 'torneo', array(
		'labels'        => array(
			'name'               => 'Tornei',
			'singular_name'      => 'Torneo',
			'menu_name'          => 'Tornei',
			'add_new'            => 'Aggiungi torneo',
			'add_new_item'       => 'Nuovo torneo',
			'edit_item'          => 'Modifica torneo',
			'new_item'           => 'Nuovo torneo',
			'view_item'          => 'Vedi torneo',
			'view_items'         => 'Vedi tornei',
			'search_items'       => 'Cerca tornei',
			'not_found'          => 'Nessun torneo',
			'not_found_in_trash' => 'Nessun torneo nel cestino',
			'all_items'          => 'Tutti i tornei',
			'featured_image'     => 'Locandina o foto del torneo',
			'set_featured_image' => 'Scegli locandina',
		),
		'public'        => true,
		'has_archive'   => 'tornei',
		'rewrite'       => array( 'slug' => 'tornei', 'with_front' => false ),
		'menu_icon'     => 'dashicons-awards',
		'menu_position' => 3,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'vd_registra_tornei' );

/** Campi del torneo: chiave meta => etichetta. */
function vd_torneo_campi() {
	return array(
		'_vd_data'       => 'Data',
		'_vd_orario'     => 'Orario',
		'_vd_categoria'  => 'Categoria',
		'_vd_quota'      => 'Quota di iscrizione',
		'_vd_iscrizioni' => 'Link per iscriversi',
	);
}

add_action( 'add_meta_boxes_torneo', function () {
	add_meta_box( 'vd_torneo', 'Dettagli del torneo', 'vd_torneo_box', 'torneo', 'side', 'high' );
} );

function vd_torneo_box( $post ) {
	wp_nonce_field( 'vd_torneo', 'vd_torneo_nonce' );
	$v = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	?>
	<style>
		.vd-tb label { display: block; margin: 12px 0 4px; font-weight: 600; }
		.vd-tb input { width: 100%; }
		.vd-tb small { color: #646970; }
	</style>
	<div class="vd-tb">
		<label for="vd_data">Data *</label>
		<input type="date" id="vd_data" name="_vd_data" value="<?php echo esc_attr( $v( '_vd_data' ) ); ?>" required>
		<small>Dopo questa data il torneo sparisce dalla home ma resta nell'archivio.</small>

		<label for="vd_orario">Orario</label>
		<input type="text" id="vd_orario" name="_vd_orario" value="<?php echo esc_attr( $v( '_vd_orario' ) ); ?>" placeholder="es. dalle 9:00">

		<label for="vd_categoria">Categoria</label>
		<input type="text" id="vd_categoria" name="_vd_categoria" value="<?php echo esc_attr( $v( '_vd_categoria' ) ); ?>" placeholder="es. Doppio misto" list="vd_categorie">
		<datalist id="vd_categorie">
			<option value="Doppio maschile">
			<option value="Doppio femminile">
			<option value="Doppio misto">
		</datalist>

		<label for="vd_quota">Quota di iscrizione</label>
		<input type="text" id="vd_quota" name="_vd_quota" value="<?php echo esc_attr( $v( '_vd_quota' ) ); ?>" placeholder="es. 20 € a coppia">

		<label for="vd_iscrizioni">Link per iscriversi</label>
		<input type="url" id="vd_iscrizioni" name="_vd_iscrizioni" value="<?php echo esc_attr( $v( '_vd_iscrizioni' ) ); ?>" placeholder="https://…">
		<small>Facoltativo: se vuoto, il pulsante apre WhatsApp.</small>
	</div>
	<?php
}

add_action( 'save_post_torneo', function ( $post_id ) {
	if ( ! isset( $_POST['vd_torneo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vd_torneo_nonce'] ), 'vd_torneo' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array_keys( vd_torneo_campi() ) as $k ) {
		if ( ! isset( $_POST[ $k ] ) ) {
			continue;
		}
		$val = wp_unslash( $_POST[ $k ] );
		if ( '_vd_iscrizioni' === $k ) {
			$val = esc_url_raw( $val );
		} elseif ( '_vd_data' === $k ) {
			$val = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $val ) ? $val : '';
		} else {
			$val = sanitize_text_field( $val );
		}
		update_post_meta( $post_id, $k, $val );
	}
} );

/* ----------------------------------------------------------
 * Elenco dei tornei nella bacheca: colonna con la data
 * -------------------------------------------------------- */
add_filter( 'manage_torneo_posts_columns', function ( $cols ) {
	$nuove = array();
	foreach ( $cols as $k => $v ) {
		$nuove[ $k ] = $v;
		if ( 'title' === $k ) {
			$nuove['vd_data']      = 'Data del torneo';
			$nuove['vd_categoria'] = 'Categoria';
		}
	}
	unset( $nuove['date'] );
	return $nuove;
} );

add_action( 'manage_torneo_posts_custom_column', function ( $col, $post_id ) {
	if ( 'vd_data' === $col ) {
		$data = vd_torneo_data( $post_id, 'j F Y' );
		echo $data ? esc_html( $data ) : '<em>senza data</em>';
		if ( $data && get_post_meta( $post_id, '_vd_data', true ) < wp_date( 'Y-m-d' ) ) {
			echo ' <span style="color:#646970">(concluso)</span>';
		}
	} elseif ( 'vd_categoria' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_vd_categoria', true ) );
	}
}, 10, 2 );

add_filter( 'manage_edit-torneo_sortable_columns', function ( $cols ) {
	$cols['vd_data'] = 'vd_data';
	return $cols;
} );

add_action( 'pre_get_posts', function ( $q ) {
	if ( ! $q->is_main_query() ) {
		return;
	}
	if ( is_admin() && 'torneo' === $q->get( 'post_type' ) && in_array( $q->get( 'orderby' ), array( '', 'vd_data' ), true ) ) {
		$q->set( 'meta_key', '_vd_data' );
		$q->set( 'orderby', 'meta_value' );
		$q->set( 'order', $q->get( 'order' ) ? $q->get( 'order' ) : 'DESC' );
	}
	// Archivio pubblico /tornei/: dal più recente.
	if ( ! is_admin() && $q->is_post_type_archive( 'torneo' ) ) {
		$q->set( 'meta_key', '_vd_data' );
		$q->set( 'orderby', 'meta_value' );
		$q->set( 'order', 'DESC' );
	}
} );

/* ----------------------------------------------------------
 * Funzioni per i template
 * -------------------------------------------------------- */

/** Data del torneo formattata in italiano (vuota se non impostata). */
function vd_torneo_data( $post_id, $formato = 'j F Y' ) {
	$d  = get_post_meta( $post_id, '_vd_data', true );
	$dt = $d ? DateTimeImmutable::createFromFormat( '!Y-m-d', $d, wp_timezone() ) : false;
	return $dt ? wp_date( $formato, $dt->getTimestamp() ) : '';
}

/** Prossimi tornei (da oggi in poi), dal più vicino. */
function vd_tornei_prossimi( $quanti = 3 ) {
	return get_posts( array(
		'post_type'      => 'torneo',
		'posts_per_page' => $quanti,
		'meta_key'       => '_vd_data',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => '_vd_data',
				'value'   => wp_date( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	) );
}

/** Link del pulsante "Iscriviti": quello del torneo oppure WhatsApp con messaggio pronto. */
function vd_torneo_iscrizione( $post_id ) {
	$link = get_post_meta( $post_id, '_vd_iscrizioni', true );
	if ( $link ) {
		return $link;
	}
	$testo = sprintf( 'Ciao, vorrei iscrivermi al torneo "%s" del %s', get_the_title( $post_id ), vd_torneo_data( $post_id ) );
	return 'https://wa.me/' . preg_replace( '/\D/', '', vd( 'whatsapp' ) ) . '?text=' . rawurlencode( $testo );
}
