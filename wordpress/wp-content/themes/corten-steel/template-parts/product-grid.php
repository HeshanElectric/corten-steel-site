<?php
/**
 * Featured catalog cards on the homepage. Uses live product posts and
 * the same cover / gallery photos as the product templates.
 *
 * @package Corten_Steel
 */

$products = corten_filter_catalog( array( 'limit' => 3 ) );
if ( ! $products ) {
	$products = array_slice( corten_get_demo_products(), 0, 3 );
}
?>
<section class="section product-categories" aria-labelledby="catalog-title">
	<div class="wrap">
		<header class="section-head reveal">
			<p class="eyebrow"><?php esc_html_e( 'Catalog', 'corten-steel' ); ?></p>
			<h2 id="catalog-title"><?php esc_html_e( 'Planters, enclosures, custom work', 'corten-steel' ); ?></h2>
		</header>
		<div class="product-grid product-grid--featured">
			<?php echo corten_render_product_cards( $products ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
