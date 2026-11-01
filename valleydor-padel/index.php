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
					<?php if ( is_single() ) : ?>
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
							<p class="entry__date"><?php echo esc_html( get_the_date() ); ?></p>
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
