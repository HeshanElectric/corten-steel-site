<?php
/**
 * Default page template. Elementor Single location can replace the inner loop.
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
						<h1><?php the_title(); ?></h1>
					</header>
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
