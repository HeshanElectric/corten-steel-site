<?php
/**
 * REST catalog filter used by the product archive AJAX grid.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query published products by catalog facets.
 *
 * @param array<string, mixed> $filters Filter map.
 * @return WP_Query
 */
function corten_core_query_products( $filters ) {
	$limit       = isset( $filters['limit'] ) ? max( 1, (int) $filters['limit'] ) : 24;
	$material    = isset( $filters['material'] ) ? sanitize_text_field( (string) $filters['material'] ) : '';
	$application = isset( $filters['application'] ) ? sanitize_text_field( (string) $filters['application'] ) : '';
	$category    = isset( $filters['category'] ) ? sanitize_text_field( (string) $filters['category'] ) : '';
	$size        = isset( $filters['size'] ) ? sanitize_text_field( (string) $filters['size'] ) : '';

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
	);

	$tax_query = array( 'relation' => 'AND' );
	if ( $material && taxonomy_exists( 'material' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'material',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( $material ) ),
		);
	}
	if ( $application && taxonomy_exists( 'application' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'application',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( $application ) ),
		);
	}
	if ( $category && taxonomy_exists( 'product_category' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_category',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( $category ) ),
		);
	}
	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	}
	if ( $size ) {
		$args['meta_query'] = array(
			array(
				'key'   => 'size_range',
				'value' => $size,
			),
		);
	}

	return new WP_Query( $args );
}

/**
 * Build the JSON payload for REST and AJAX responses.
 *
 * @param array<string, mixed> $filters Filter map.
 * @return array<string, mixed>
 */
function corten_core_filter_payload( $filters ) {
	$loop  = corten_core_query_products( $filters );
	$items = array();

	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			if ( function_exists( 'corten_normalize_product_post' ) ) {
				$items[] = corten_normalize_product_post( get_post() );
			} else {
				$items[] = array(
					'id'        => get_the_ID(),
					'title'     => get_the_title(),
					'permalink' => get_permalink(),
				);
			}
		}
		wp_reset_postdata();
	}

	$html = '';
	if ( $items && function_exists( 'corten_render_product_cards' ) ) {
		$html = corten_render_product_cards( $items );
	} elseif ( ! $items ) {
		$html = '<p class="empty-state">' . esc_html__( 'No products match these filters.', 'corten-core' ) . '</p>';
	}

	return array(
		'html'  => $html,
		'count' => count( $items ),
		'items' => $items,
	);
}

/**
 * Register GET /wp-json/corten/v1/products
 */
function corten_core_register_rest_routes() {
	register_rest_route(
		'corten/v1',
		'/products',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => static function ( WP_REST_Request $request ) {
				return rest_ensure_response(
					corten_core_filter_payload(
						array(
							'material'    => (string) $request->get_param( 'material' ),
							'application' => (string) $request->get_param( 'application' ),
							'category'    => (string) $request->get_param( 'category' ),
							'size'        => (string) $request->get_param( 'size' ),
							'limit'       => $request->get_param( 'limit' ),
						)
					)
				);
			},
			'args'                => array(
				'material'    => array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				'application' => array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				'category'    => array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				'size'        => array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				'limit'       => array(
					'sanitize_callback' => 'absint',
					'default'           => 24,
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'corten_core_register_rest_routes' );

/**
 * AJAX action used by archive-product.php (same name as the theme fallback).
 */
function corten_core_ajax_filter_products() {
	check_ajax_referer( 'corten_filter', 'nonce' );

	$payload = corten_core_filter_payload(
		array(
			'material'    => isset( $_POST['material'] ) ? wp_unslash( $_POST['material'] ) : '',
			'application' => isset( $_POST['application'] ) ? wp_unslash( $_POST['application'] ) : '',
			'category'    => isset( $_POST['category'] ) ? wp_unslash( $_POST['category'] ) : '',
			'size'        => isset( $_POST['size'] ) ? wp_unslash( $_POST['size'] ) : '',
			'limit'       => isset( $_POST['limit'] ) ? wp_unslash( $_POST['limit'] ) : 24,
		)
	);

	wp_send_json_success( $payload );
}

/**
 * Prefer the plugin AJAX handler when both theme and plugin are loaded.
 */
function corten_core_override_theme_ajax() {
	remove_action( 'wp_ajax_corten_filter_products', 'corten_ajax_filter_products' );
	remove_action( 'wp_ajax_nopriv_corten_filter_products', 'corten_ajax_filter_products' );
	add_action( 'wp_ajax_corten_filter_products', 'corten_core_ajax_filter_products' );
	add_action( 'wp_ajax_nopriv_corten_filter_products', 'corten_core_ajax_filter_products' );
}
add_action( 'init', 'corten_core_override_theme_ajax', 20 );
