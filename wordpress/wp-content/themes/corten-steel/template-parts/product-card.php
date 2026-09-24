<?php
/**
 * Product card. Expects query var corten_product.
 *
 * @package Corten_Steel
 */

$product = array();
if ( isset( $args['product'] ) && is_array( $args['product'] ) ) {
	$product = $args['product'];
} else {
	$fetched = get_query_var( 'corten_product' );
	if ( is_array( $fetched ) ) {
		$product = $fetched;
	}
}
if ( ! $product ) {
	return;
}
$image = ! empty( $product['image'] ) ? $product['image'] : '';
if ( ! $image && ! empty( $product['gallery'] ) && is_array( $product['gallery'] ) ) {
	$image = (string) $product['gallery'][0];
}
?>
<article class="product-card reveal">
	<a class="product-card__link" href="<?php echo esc_url( $product['permalink'] ); ?>">
		<div class="product-card__media">
			<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product['title'] ); ?>" width="900" height="1125" loading="lazy">
		</div>
		<div class="product-card__body">
			<p class="product-card__meta"><?php echo esc_html( $product['material'] ); ?> · MOQ <?php echo esc_html( (string) $product['moq'] ); ?></p>
			<h3><?php echo esc_html( $product['title'] ); ?></h3>
			<p class="product-card__excerpt"><?php echo esc_html( $product['excerpt'] ); ?></p>
			<span class="text-link"><?php esc_html_e( 'View Details', 'corten-steel' ); ?></span>
		</div>
	</a>
</article>
