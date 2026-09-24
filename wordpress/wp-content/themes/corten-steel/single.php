<?php
/**
 * Single post template.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main">
	<?php
	if ( ! corten_elementor_location_done( 'single' ) ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'section' ); ?>>
				<div class="wrap narrow">
					<header class="page-header reveal">
						<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
						<h1><?php the_title(); ?></h1>
					</header>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry-hero reveal"><?php the_post_thumbnail( 'corten-hero' ); ?></div>
					<?php endif; ?>
					<div class="entry-content reveal">
						<?php the_content(); ?>
					</div>
				</div>
			</article>
			<?php
		endwhile;
	endif;
	?>
</main>
<?php
get_footer();
