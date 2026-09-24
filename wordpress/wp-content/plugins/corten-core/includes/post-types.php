<?php
/**
 * Product and project custom post types.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register public CPTs used by the catalog and case studies.
 */
function corten_core_register_post_types() {
	register_post_type(
		'product',
		array(
			'labels'             => array(
				'name'               => __( 'Products', 'corten-core' ),
				'singular_name'      => __( 'Product', 'corten-core' ),
				'add_new_item'       => __( 'Add New Product', 'corten-core' ),
				'edit_item'          => __( 'Edit Product', 'corten-core' ),
				'new_item'           => __( 'New Product', 'corten-core' ),
				'view_item'          => __( 'View Product', 'corten-core' ),
				'search_items'       => __( 'Search Products', 'corten-core' ),
				'not_found'          => __( 'No products found.', 'corten-core' ),
				'all_items'          => __( 'All Products', 'corten-core' ),
				'archives'           => __( 'Product Archives', 'corten-core' ),
				'featured_image'     => __( 'Product image', 'corten-core' ),
				'set_featured_image' => __( 'Set product image', 'corten-core' ),
			),
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array(
				'slug'       => 'products',
				'with_front' => false,
			),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-screenoptions',
			'menu_position'      => 20,
			'show_in_nav_menus'  => true,
			'exclude_from_search' => false,
		)
	);

	register_post_type(
		'project',
		array(
			'labels'             => array(
				'name'               => __( 'Projects', 'corten-core' ),
				'singular_name'      => __( 'Project', 'corten-core' ),
				'add_new_item'       => __( 'Add New Project', 'corten-core' ),
				'edit_item'          => __( 'Edit Project', 'corten-core' ),
				'view_item'          => __( 'View Project', 'corten-core' ),
				'all_items'          => __( 'All Projects', 'corten-core' ),
				'archives'           => __( 'Project Archives', 'corten-core' ),
			),
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array(
				'slug'       => 'projects',
				'with_front' => false,
			),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-portfolio',
			'menu_position'      => 21,
			'show_in_nav_menus'  => true,
		)
	);
}
add_action( 'init', 'corten_core_register_post_types', 5 );
