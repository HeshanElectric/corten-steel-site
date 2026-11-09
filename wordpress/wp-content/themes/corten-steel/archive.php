<?php
/**
 * Blog archive — lists blog posts with category filter chips and card grid.
 * Handles the main /blog/ page plus all category / tag / author / date archives.
 *
 * @package Corten_Steel
 */

get_header();

$archive_title = single_cat_title( '', false );
if ( ! $archive_title ) {
	$archive_title = post_type_archive_title( '', false );
}
if ( ! $archive_title ) {
	$archive_title = is_home() ? __( 'Journal', 'corten-steel' ) : wp_get_document_title();
}
?>
<main id="content" class="site-main blog-archive">
	<section class="section blog-archive__hero">
		<div class="wrap">
			<header class="page-header reveal">
				<p class="eyebrow"><?php esc_html_e( 'Journal', 'corten-steel' ); ?></p>
				<h1><?php echo esc_html( $archive_title ); ?></h1>
				<p class="page-header__intro">
					<?php esc_html_e( 'Company news, trade-show coverage, and technical notes on weathering steel fabrication.', 'corten-steel' ); ?>
				</p>
			</header>

			<?php
			$categories = get_categories( array(
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'parent'     => 0,
			) );
			$current_cat = get_queried_object();
			?>
			<?php if ( $categories ) : ?>
				<nav class="blog-filters reveal reveal--delay-1" aria-label="<?php esc_attr_e( 'Filter by category', 'corten-steel' ); ?>">
					<a class="blog-filter-chip <?php echo ( is_home() || is_post_type_archive( 'post' ) ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
						<?php esc_html_e( 'All', 'corten-steel' ); ?>
					</a>
					<?php foreach ( $categories as $cat ) : ?>
						<a class="blog-filter-chip <?php echo ( is_category() && $current_cat && $current_cat->term_id === $cat->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>">
							<?php echo esc_html( $cat->name ); ?>
							<span class="blog-filter-chip__count"><?php echo esc_html( $cat->count ); ?></span>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>

	<section class="section blog-archive__grid">
		<div class="wrap">
			<?php if ( have_posts() ) : ?>
				<div class="blog-card-grid">
					<?php
					$i = 0;
					while ( have_posts() ) :
						the_post();
						$i++;
						$delay_class = 'reveal--delay-' . min( $i, 5 );
						get_template_part( 'template-parts/blog-card', null, array( 'delay' => $delay_class ) );
					endwhile;
					?>
				</div>

				<nav class="pagination" aria-label="<?php esc_attr_e( 'Blog pagination', 'corten-steel' ); ?>">
					<?php
					the_posts_pagination( array(
						'mid_size'  => 2,
						'prev_text' => esc_html__( 'Previous', 'corten-steel' ),
						'next_text' => esc_html__( 'Next', 'corten-steel' ),
					) );
					?>
				</nav>
			<?php else : ?>
				<div class="empty-state reveal">
					<h2><?php esc_html_e( 'No articles yet', 'corten-steel' ); ?></h2>
					<p><?php esc_html_e( 'We are preparing stories on our latest projects and industry insights. Check back soon.', 'corten-steel' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
