<?php
/**
 * Catalog taxonomies for products.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register hierarchical and non-hierarchical product taxonomies.
 */
function corten_core_register_taxonomies() {
	register_taxonomy(
		'product_category',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Product Categories', 'corten-core' ),
				'singular_name' => __( 'Product Category', 'corten-core' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'product-category',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'application',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Applications', 'corten-core' ),
				'singular_name' => __( 'Application', 'corten-core' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'application',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'material',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Materials', 'corten-core' ),
				'singular_name' => __( 'Material', 'corten-core' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'material',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'corten_core_register_taxonomies', 5 );
