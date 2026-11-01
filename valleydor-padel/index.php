<?php
/**
 * Pagine interne, articoli e archivi (es. notizie sui tornei).
 */

get_header();
?>

<main class="inner">
	<div class="inner__head">
		<div class="wrap">
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1>
				<?php
				if ( is_singular() ) {
					single_post_title();
				} elseif ( is_search() ) {
					echo 'Risultati per: ' . esc_html( get_search_query() );
				} elseif ( is_404() ) {
					echo 'Pagina non trovata';
				} elseif ( is_home() ) {
					echo 'Notizie';
				} elseif ( is_post_type_archive() ) {
					post_type_archive_title();
				} else {
					the_archive_title();
				}
				?>
			</h1>
		</div>
	</div>

	<div class="wrap inner__body">
		<?php if ( is_404() ) : ?>
			<p>La pagina che cerchi non esiste o è stata spostata.</p>
			<p><a class="btn btn--lime" href="<?php echo esc_url( home_url( '/' ) ); ?>">Torna alla home</a></p>

		<?php elseif ( is_singular() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'entry' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry__img"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
					<?php if ( 'torneo' === get_post_type() ) : ?>
						<?php
						$categoria = get_post_meta( get_the_ID(), '_vd_categoria', true );
						$orario    = get_post_meta( get_the_ID(), '_vd_orario', true );
						$quota     = get_post_meta( get_the_ID(), '_vd_quota', true );
						$futuro    = get_post_meta( get_the_ID(), '_vd_data', true ) >= wp_date( 'Y-m-d' );
						?>
						<div class="torneo-info">
							<dl>
								<div><dt>Data</dt><dd><?php echo esc_html( ucfirst( vd_torneo_data( get_the_ID(), 'l j F Y' ) ) ); ?></dd></div>
								<?php if ( $orario ) : ?><div><dt>Orario</dt><dd><?php echo esc_html( $orario ); ?></dd></div><?php endif; ?>
								<?php if ( $categoria ) : ?><div><dt>Categoria</dt><dd><?php echo esc_html( $categoria ); ?></dd></div><?php endif; ?>
								<?php if ( $quota ) : ?><div><dt>Iscrizione</dt><dd><?php echo esc_html( $quota ); ?></dd></div><?php endif; ?>
							</dl>
							<?php if ( $futuro ) : ?>
								<a href="<?php echo esc_url( vd_torneo_iscrizione( get_the_ID() ) ); ?>" class="btn btn--lime" target="_blank" rel="noopener">Iscriviti</a>
							<?php else : ?>
								<p class="torneo-info__fine">Torneo concluso</p>
							<?php endif; ?>
						</div>
					<?php elseif ( is_single() ) : ?>
						<p class="entry__date"><?php echo esc_html( get_the_date() ); ?></p>
					<?php endif; ?>
					<div class="entry__content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>

		<?php elseif ( have_posts() ) : ?>
			<div class="cards">
				<?php while ( have_posts() ) : the_post(); ?>
					<a <?php post_class( 'card' ); ?> href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium_large' ); ?>
						<?php endif; ?>
						<div class="card__body">
							<p class="entry__date"><?php echo esc_html( 'torneo' === get_post_type() ? vd_torneo_data( get_the_ID() ) : get_the_date() ); ?></p>
							<h2><?php the_title(); ?></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
			<div class="pager"><?php the_posts_pagination( array( 'prev_text' => '‹', 'next_text' => '›' ) ); ?></div>

		<?php else : ?>
			<p>Nessun contenuto trovato.</p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
