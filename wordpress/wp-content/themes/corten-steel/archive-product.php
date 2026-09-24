<?php
/**
 * Product CPT archive with AJAX facet filters.
 *
 * @package Corten_Steel
 */

get_header();

$filters = array(
	'material'    => isset( $_GET['material'] ) ? sanitize_text_field( wp_unslash( $_GET['material'] ) ) : '',
	'application' => isset( $_GET['application'] ) ? sanitize_text_field( wp_unslash( $_GET['application'] ) ) : '',
	'size'        => isset( $_GET['size'] ) ? sanitize_text_field( wp_unslash( $_GET['size'] ) ) : '',
);
$products = corten_filter_catalog( $filters );
?>
<main id="content" class="site-main section catalog">
	<div class="wrap">
		<header class="page-header reveal">
			<p class="eyebrow"><?php esc_html_e( 'B2B catalog', 'corten-steel' ); ?></p>
			<h1><?php esc_html_e( 'Products', 'corten-steel' ); ?></h1>
			<p class="page-header__desc"><?php esc_html_e( 'Filter by material, application, and size. Cards load without a full page refresh.', 'corten-steel' ); ?></p>
		</header>

		<form class="filter-bar reveal" data-product-filters>
			<label>
				<span><?php esc_html_e( 'Material', 'corten-steel' ); ?></span>
				<select name="material">
					<option value=""><?php esc_html_e( 'All materials', 'corten-steel' ); ?></option>
					<option value="Corten Steel" <?php selected( $filters['material'], 'Corten Steel' ); ?>><?php esc_html_e( 'Corten Steel', 'corten-steel' ); ?></option>
					<option value="Mild Steel" <?php selected( $filters['material'], 'Mild Steel' ); ?>><?php esc_html_e( 'Mild Steel', 'corten-steel' ); ?></option>
					<option value="Aluminum" <?php selected( $filters['material'], 'Aluminum' ); ?>><?php esc_html_e( 'Aluminum', 'corten-steel' ); ?></option>
					<option value="Galvanized" <?php selected( $filters['material'], 'Galvanized' ); ?>><?php esc_html_e( 'Galvanized', 'corten-steel' ); ?></option>
				</select>
			</label>
			<label>
				<span><?php esc_html_e( 'Application', 'corten-steel' ); ?></span>
				<select name="application">
					<option value=""><?php esc_html_e( 'All applications', 'corten-steel' ); ?></option>
					<option value="landscape" <?php selected( $filters['application'], 'landscape' ); ?>><?php esc_html_e( 'Landscape / garden', 'corten-steel' ); ?></option>
					<option value="industrial" <?php selected( $filters['application'], 'industrial' ); ?>><?php esc_html_e( 'Industrial enclosure', 'corten-steel' ); ?></option>
					<option value="oem" <?php selected( $filters['application'], 'oem' ); ?>><?php esc_html_e( 'OEM / custom', 'corten-steel' ); ?></option>
				</select>
			</label>
			<label>
				<span><?php esc_html_e( 'Size range', 'corten-steel' ); ?></span>
				<select name="size">
					<option value=""><?php esc_html_e( 'All sizes', 'corten-steel' ); ?></option>
					<option value="compact" <?php selected( $filters['size'], 'compact' ); ?>><?php esc_html_e( 'Compact · up to 400 mm', 'corten-steel' ); ?></option>
					<option value="standard" <?php selected( $filters['size'], 'standard' ); ?>><?php esc_html_e( 'Standard · 400–900 mm', 'corten-steel' ); ?></option>
					<option value="architectural" <?php selected( $filters['size'], 'architectural' ); ?>><?php esc_html_e( 'Architectural · 900 mm+', 'corten-steel' ); ?></option>
				</select>
			</label>
			<p class="filter-count" data-filter-count>
				<?php
				printf(
					/* translators: %d: product count */
					esc_html( _n( '%d product', '%d products', count( $products ), 'corten-steel' ) ),
					count( $products )
				);
				?>
			</p>
		</form>

		<div class="product-grid" data-product-grid>
			<?php
			if ( $products ) {
				echo corten_render_product_cards( $products ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cards are escaped in the template.
			} else {
				echo '<p class="empty-state">' . esc_html__( 'No products match these filters.', 'corten-steel' ) . '</p>';
			}
			?>
		</div>
	</div>
</main>
<?php
get_footer();
