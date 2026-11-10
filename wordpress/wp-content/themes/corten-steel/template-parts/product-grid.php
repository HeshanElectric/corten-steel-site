<?php
/**
 * Featured catalog cards on the homepage. Uses live product posts and
 * the same cover / gallery photos as the product templates.
 *
 * Rendered as a horizontal, scroll-snapping gallery with arrow controls.
 *
 * @package Corten_Steel
 */

$products = corten_filter_catalog( array( 'limit' => 8 ) );
if ( ! $products ) {
	$products = array_slice( corten_get_demo_products(), 0, 8 );
}
?>
<section class="section product-categories" aria-labelledby="catalog-title">
	<div class="wrap">
		<header class="section-head section-head--split reveal">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Catalog', 'corten-steel' ); ?></p>
				<h2 id="catalog-title"><?php esc_html_e( 'Planters, enclosures, custom work', 'corten-steel' ); ?></h2>
			</div>
			<div class="gallery-nav">
				<button class="gallery-nav__btn" type="button" data-gl-prev aria-label="<?php esc_attr_e( 'Previous items', 'corten-steel' ); ?>">‹</button>
				<button class="gallery-nav__btn" type="button" data-gl-next aria-label="<?php esc_attr_e( 'Next items', 'corten-steel' ); ?>">›</button>
			</div>
		</header>
		<div class="gallery-carousel" data-gl-carousel data-gl-interval="5500">
			<div class="gallery-carousel__track" data-gl-track tabindex="0" role="group" aria-label="<?php esc_attr_e( 'Featured custom work gallery', 'corten-steel' ); ?>">
				<?php echo corten_render_product_cards( $products ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="gallery-carousel__progress" aria-hidden="true"><span data-gl-progress></span></div>
		</div>
	</div>
</section>
