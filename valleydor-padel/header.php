<?php
/**
 * Intestazione: menu fisso in alto.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( is_front_page() ? '' : 'is-inner' ); ?>>
<?php wp_body_open(); ?>

<header class="header" id="top">
	<div class="header__inner wrap">
		<a href="<?php echo esc_url( is_front_page() ? '#top' : home_url( '/' ) ); ?>" class="header__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> – inizio">
			<img src="<?php echo esc_url( vd_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="160" height="60">
		</a>
		<nav class="nav" id="nav" aria-label="Menu principale">
			<a href="<?php echo esc_url( vd_anchor( 'centro' ) ); ?>">Il centro</a>
			<a href="<?php echo esc_url( vd_anchor( 'prezzi' ) ); ?>">Prezzi</a>
			<a href="<?php echo esc_url( vd_anchor( 'corsi' ) ); ?>">Corsi</a>
			<a href="<?php echo esc_url( vd_anchor( 'galleria' ) ); ?>">Galleria</a>
			<a href="<?php echo esc_url( vd_anchor( 'dove' ) ); ?>">Dove siamo</a>
			<a href="<?php echo esc_url( vd( 'playtomic' ) ); ?>" class="btn btn--lime nav__cta" target="_blank" rel="noopener">Prenota</a>
		</nav>
		<button class="burger" id="burger" aria-label="Apri il menu" aria-expanded="false" aria-controls="nav">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>
