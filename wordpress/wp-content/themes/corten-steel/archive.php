<?php
/**
 * Generic archive.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main section">
	<div class="wrap">
		<header class="page-header reveal">
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<p class="page-header__desc">', '</p>' ); ?>
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
			<?php else : ?>
				<p><?php esc_html_e( 'No entries in this archive yet.', 'corten-steel' ); ?></p>
			<?php endif; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</main>
<?php
get_footer();
