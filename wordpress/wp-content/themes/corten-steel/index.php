<?php
/**
 * Fallback index for posts and unassigned views.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main section">
	<div class="wrap">
		<header class="page-header reveal">
			<h1><?php echo esc_html( wp_get_document_title() ); ?></h1>
		</header>
		<div class="content-grid">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'content-card reveal' ); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nothing found.', 'corten-steel' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php
get_footer();
