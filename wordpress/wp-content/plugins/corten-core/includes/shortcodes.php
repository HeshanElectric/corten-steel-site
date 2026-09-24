<?php
/**
 * Public shortcodes for grids, stats, and RFQ buttons.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [corten_product_grid category="" application="" limit="6"]
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 * @return string
 */
function corten_core_shortcode_product_grid( $atts ) {
	$atts = shortcode_atts(
		array(
			'category'    => '',
			'application' => '',
			'limit'       => '6',
		),
		$atts,
		'corten_product_grid'
	);

	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) $atts['limit'] ),
	);

	$tax_query = array();
	if ( $atts['category'] && taxonomy_exists( 'product_category' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_category',
			'field'    => 'slug',
			'terms'    => sanitize_title( $atts['category'] ),
		);
	}
	if ( $atts['application'] && taxonomy_exists( 'application' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'application',
			'field'    => 'slug',
			'terms'    => sanitize_title( $atts['application'] ),
		);
	}
	if ( $tax_query ) {
		$query_args['tax_query'] = $tax_query;
	}

	$loop = new WP_Query( $query_args );
	$items = array();
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			if ( function_exists( 'corten_normalize_product_post' ) ) {
				$items[] = corten_normalize_product_post( get_post() );
			}
		}
		wp_reset_postdata();
	}

	if ( ! $items ) {
		return '<p class="empty-state">' . esc_html__( 'No products found.', 'corten-core' ) . '</p>';
	}

	$html = '<div class="product-grid">';
	if ( function_exists( 'corten_render_product_cards' ) ) {
		$html .= corten_render_product_cards( $items );
	} else {
		foreach ( $items as $item ) {
			$html .= sprintf(
				'<article class="product-card"><a href="%1$s"><h3>%2$s</h3></a></article>',
				esc_url( $item['permalink'] ),
				esc_html( $item['title'] )
			);
		}
	}
	$html .= '</div>';

	return $html;
}
add_shortcode( 'corten_product_grid', 'corten_core_shortcode_product_grid' );

/**
 * [corten_stats]
 *
 * @return string
 */
function corten_core_shortcode_stats() {
	ob_start();
	if ( function_exists( 'get_template_part' ) ) {
		$part = locate_template( 'template-parts/stats.php' );
		if ( $part ) {
			include $part; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			return (string) ob_get_clean();
		}
	}

	?>
	<section class="section stats">
		<div class="wrap stats-row">
			<div class="stat"><p class="stat__value" data-count="18000" data-suffix="+">0</p><p class="stat__label"><?php esc_html_e( 'Units annual capacity', 'corten-core' ); ?></p></div>
			<div class="stat"><p class="stat__value" data-count="14">0</p><p class="stat__label"><?php esc_html_e( 'Export countries', 'corten-core' ); ?></p></div>
			<div class="stat"><p class="stat__value" data-count="92" data-suffix="%">0</p><p class="stat__label"><?php esc_html_e( 'Repeat-order rate', 'corten-core' ); ?></p></div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'corten_stats', 'corten_core_shortcode_stats' );

/**
 * [corten_rfq_button text="Get a Quote"]
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 * @return string
 */
function corten_core_shortcode_rfq_button( $atts ) {
	$atts = shortcode_atts(
		array(
			'text' => __( 'Get a Quote', 'corten-core' ),
		),
		$atts,
		'corten_rfq_button'
	);

	$url = function_exists( 'corten_quote_url' ) ? corten_quote_url() : home_url( '/contact/' );

	return sprintf(
		'<a class="btn btn--primary" href="%1$s">%2$s</a>',
		esc_url( $url ),
		esc_html( $atts['text'] )
	);
}
add_shortcode( 'corten_rfq_button', 'corten_core_shortcode_rfq_button' );
