<?php
/**
 * Project CPT archive.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main section">
	<div class="wrap">
		<header class="page-header reveal">
			<p class="eyebrow"><?php esc_html_e( 'Selected work', 'corten-steel' ); ?></p>
			<h1><?php esc_html_e( 'Projects', 'corten-steel' ); ?></h1>
		</header>
		<div class="content-grid">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'content-card reveal' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'corten-card' ); ?></a>
						<?php endif; ?>
						<p class="case-card__client"><?php echo esc_html( (string) get_post_meta( get_the_ID(), 'client', true ) ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No projects published yet.', 'corten-steel' ); ?></p>
			<?php endif; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</main>
<?php
get_footer();
