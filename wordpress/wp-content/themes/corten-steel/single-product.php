<?php
/**
 * Single product template. Elementor Single location can replace this layout.
 *
 * @package Corten_Steel
 */

get_header();

if ( corten_elementor_location_done( 'single' ) ) {
	get_footer();
	return;
}

$product = corten_get_current_product_data();
$related = corten_get_related_products( $product, 3 );
$gallery = ! empty( $product['gallery'] ) ? $product['gallery'] : array( $product['image'] );
?>
<main id="content" class="site-main product-single">
	<section class="section product-hero">
		<div class="wrap product-hero__grid">
			<div class="product-gallery reveal" data-product-gallery>
				<div class="product-gallery__viewport">
					<button class="product-gallery__stage" type="button" data-gallery-open>
						<img src="<?php echo esc_url( $gallery[0] ); ?>" alt="<?php echo esc_attr( $product['title'] ); ?>" data-gallery-main width="900" height="1125">
					</button>
					<?php if ( count( $gallery ) > 1 ) : ?>
						<button class="product-gallery__nav product-gallery__nav--prev" type="button" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'corten-steel' ); ?>">‹</button>
						<button class="product-gallery__nav product-gallery__nav--next" type="button" data-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'corten-steel' ); ?>">›</button>
					<?php endif; ?>
				</div>
				<?php if ( count( $gallery ) > 1 ) : ?>
					<div class="product-gallery__thumbs">
						<?php foreach ( $gallery as $index => $src ) : ?>
							<button type="button" data-gallery-thumb="<?php echo esc_url( $src ); ?>" <?php echo 0 === $index ? 'aria-current="true"' : ''; ?>>
								<img src="<?php echo esc_url( $src ); ?>" alt="" width="160" height="160">
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="product-summary reveal">
				<p class="eyebrow"><?php echo esc_html( $product['material'] ); ?></p>
				<h1><?php echo esc_html( $product['title'] ); ?></h1>
				<p><?php echo esc_html( $product['excerpt'] ); ?></p>
				<dl class="spec-table">
					<div>
						<dt><?php esc_html_e( 'Material', 'corten-steel' ); ?></dt>
						<dd><?php echo esc_html( $product['material'] ); ?></dd>
					</div>
					<div>
						<dt><?php esc_html_e( 'Surface', 'corten-steel' ); ?></dt>
						<dd><?php echo esc_html( $product['surface_finish'] ); ?></dd>
					</div>
					<div>
						<dt><?php esc_html_e( 'MOQ', 'corten-steel' ); ?></dt>
						<dd><?php echo esc_html( (string) $product['moq'] ); ?></dd>
					</div>
					<div>
						<dt><?php esc_html_e( 'Lead time', 'corten-steel' ); ?></dt>
						<dd><?php echo esc_html( $product['lead_time'] ); ?></dd>
					</div>
				</dl>
				<?php if ( ! empty( $product['dimensions'] ) ) : ?>
					<table class="dimensions">
						<caption><?php esc_html_e( 'Dimensions & process', 'corten-steel' ); ?></caption>
						<tbody>
							<?php foreach ( $product['dimensions'] as $row ) : ?>
								<tr>
									<th><?php echo esc_html( $row['label'] ); ?></th>
									<td><?php echo esc_html( $row['value'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				<?php if ( ! empty( $product['customization'] ) ) : ?>
					<p class="custom-tags">
						<?php esc_html_e( 'Customizable:', 'corten-steel' ); ?>
						<?php echo esc_html( implode( ' · ', $product['customization'] ) ); ?>
					</p>
				<?php endif; ?>
				<div class="hero-actions">
					<a class="btn btn--primary btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic>
						<span class="btn__label"><?php esc_html_e( 'Request a quote', 'corten-steel' ); ?></span>
						<span class="btn__shine" aria-hidden="true"></span>
					</a>
					<a class="btn btn--ghost btn--magnetic" href="<?php echo esc_url( home_url( '/products/' ) ); ?>" data-magnetic>
						<span class="btn__label"><?php esc_html_e( 'Back to catalog', 'corten-steel' ); ?></span>
						<span class="btn__shine" aria-hidden="true"></span>
					</a>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $product['content'] ) ) : ?>
		<section class="section product-body">
			<div class="wrap narrow">
				<header class="section-head reveal">
					<p class="eyebrow"><?php esc_html_e( 'Details', 'corten-steel' ); ?></p>
					<h2><?php esc_html_e( 'Product description', 'corten-steel' ); ?></h2>
				</header>
				<div class="entry-content reveal">
					<?php echo wp_kses_post( apply_filters( 'the_content', $product['content'] ) ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $related ) : ?>
		<section class="section related-products">
			<div class="wrap">
				<p class="eyebrow"><?php esc_html_e( 'Also specified', 'corten-steel' ); ?></p>
				<h2><?php esc_html_e( 'Related products', 'corten-steel' ); ?></h2>
				<div class="product-grid">
					<?php echo corten_render_product_cards( array_slice( $related, 0, 3 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<div class="rfq-dock" data-rfq-dock>
		<p><?php echo esc_html( $product['title'] ); ?> · MOQ <?php echo esc_html( (string) $product['moq'] ); ?></p>
		<a class="btn btn--primary btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic>
			<span class="btn__label"><?php esc_html_e( 'Get a Quote', 'corten-steel' ); ?></span>
			<span class="btn__shine" aria-hidden="true"></span>
		</a>
	</div>

	<dialog class="gallery-dialog" data-gallery-dialog>
		<button type="button" class="gallery-dialog__close" data-gallery-close>&times;</button>
		<img src="" alt="<?php echo esc_attr( $product['title'] ); ?>" data-gallery-zoom>
	</dialog>
</main>
<?php
get_footer();
