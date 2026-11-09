<?php
/**
 * Homepage featured blog section — shows latest 3 posts.
 * Falls back gracefully when no posts exist yet.
 *
 * @package Corten_Steel
 */

$blog_posts = get_posts( array(
	'numberposts' => 3,
	'orderby'     => 'date',
	'order'       => 'DESC',
	'post_status' => 'publish',
) );

if ( ! $blog_posts ) {
	return;
}
?>
<section class="section blog-home" aria-label="<?php esc_attr_e( 'Latest journal entries', 'corten-steel' ); ?>">
	<div class="wrap">
		<header class="section-head reveal">
			<p class="eyebrow"><?php esc_html_e( 'Journal', 'corten-steel' ); ?></p>
			<h2><?php esc_html_e( 'From the shop floor', 'corten-steel' ); ?></h2>
			<p class="section-head__intro">
				<?php esc_html_e( 'Trade-show coverage, new-facility updates, and technical notes on weathering steel and sheet-metal fabrication.', 'corten-steel' ); ?>
			</p>
			<a class="section-head__cta" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
				<?php esc_html_e( 'View all articles', 'corten-steel' ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</header>

		<div class="blog-card-grid">
			<?php
			$i = 0;
			foreach ( $blog_posts as $post ) :
				setup_postdata( $post );
				$i++;
				$delay = 'reveal--delay-' . min( $i, 5 );
				get_template_part( 'template-parts/blog-card', null, array( 'delay' => $delay ) );
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
