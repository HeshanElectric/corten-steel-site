<?php
/**
 * Sample catalog and project importer.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Insert or update sample products and projects.
 *
 * @param bool $force Recreate attachments even when the post exists.
 * @return array{products:int,projects:int}
 */
function corten_core_seed_sample_data( $force = false ) {
	corten_core_register_post_types();
	corten_core_register_taxonomies();

	$projects_page = get_page_by_path( 'projects' );
	if ( $projects_page instanceof WP_Post ) {
		wp_trash_post( $projects_page->ID );
	}

	$product_ids = array();
	foreach ( corten_core_sample_products() as $product ) {
		$product_ids[] = corten_core_upsert_product( $product, $force );
	}

	$project_ids = array();
	foreach ( corten_core_sample_projects() as $project ) {
		$project_ids[] = corten_core_upsert_project( $project, $force );
	}

	flush_rewrite_rules( false );

	return array(
		'products' => count( array_filter( $product_ids ) ),
		'projects' => count( array_filter( $project_ids ) ),
	);
}

/**
 * Sample product definitions.
 *
 * @return array<int, array<string, mixed>>
 */
function corten_core_sample_products() {
	return array(
		array(
			'slug'           => 'corten-steel-square-planter',
			'title'          => __( 'Corten Steel Square Planter', 'corten-core' ),
			'content'        => __( 'Architectural square planters in 3 mm Corten A. Folded corners, hidden drainage, and a stable outdoor patina for courtyards, roof gardens, and civic landscapes.', 'corten-core' ),
			'excerpt'        => __( 'Architectural planters with folded corners, hidden drainage, and a stable patina for courtyards and rooftop gardens.', 'corten-core' ),
			'image'          => 'product-planter.svg',
			'gallery'        => array( 'product-planter.svg', 'product-custom.svg' ),
			'material'       => 'Corten Steel',
			'application'    => 'landscape',
			'category'       => 'planters',
			'size_range'     => 'standard',
			'surface_finish' => 'Natural Rust',
			'customization'  => array( 'Size', 'Logo', 'Packaging' ),
			'moq'            => 10,
			'lead_time'      => '4–6 weeks',
			'dimensions'     => array(
				array(
					'label' => 'Width',
					'value' => '600 mm',
				),
				array(
					'label' => 'Depth',
					'value' => '600 mm',
				),
				array(
					'label' => 'Height',
					'value' => '500 mm',
				),
				array(
					'label' => 'Steel',
					'value' => '3 mm Corten A',
				),
			),
		),
		array(
			'slug'           => 'weathering-steel-enclosure',
			'title'          => __( 'Weathering Steel Enclosure', 'corten-core' ),
			'content'        => __( 'Vented equipment housings laser-cut and welded for outdoor plant rooms, rooftop plant, and street furniture cores. Seams prepared for IP54-ready gaskets.', 'corten-core' ),
			'excerpt'        => __( 'Vented equipment housings laser-cut and welded for outdoor plant rooms, rooftop plant, and street furniture cores.', 'corten-core' ),
			'image'          => 'product-enclosure.svg',
			'gallery'        => array( 'product-enclosure.svg', 'product-custom.svg' ),
			'material'       => 'Corten Steel',
			'application'    => 'industrial',
			'category'       => 'enclosures',
			'size_range'     => 'architectural',
			'surface_finish' => 'Natural Rust',
			'customization'  => array( 'Size', 'Color', 'Logo' ),
			'moq'            => 5,
			'lead_time'      => '6–8 weeks',
			'dimensions'     => array(
				array(
					'label' => 'Width',
					'value' => '1200 mm',
				),
				array(
					'label' => 'Depth',
					'value' => '450 mm',
				),
				array(
					'label' => 'Height',
					'value' => '1800 mm',
				),
				array(
					'label' => 'Ingress',
					'value' => 'IP54-ready seams',
				),
			),
		),
		array(
			'slug'           => 'custom-sheet-metal-fabrication',
			'title'          => __( 'Custom Sheet Metal Fabrication', 'corten-core' ),
			'content'        => __( 'OEM/ODM brackets, panels, and housings in Corten, mild steel, aluminum, or galvanized. Laser, bend, weld, finish, and pack from approved drawings.', 'corten-core' ),
			'excerpt'        => __( 'OEM/ODM brackets, panels, and housings in Corten, mild steel, aluminum, or galvanized — drawings to packed crates.', 'corten-core' ),
			'image'          => 'product-custom.svg',
			'gallery'        => array( 'product-custom.svg', 'product-enclosure.svg' ),
			'material'       => 'Mild Steel',
			'application'    => 'oem',
			'category'       => 'custom-fabrication',
			'size_range'     => 'compact',
			'surface_finish' => 'Powder Coated',
			'customization'  => array( 'Size', 'Logo', 'Packaging', 'Color' ),
			'moq'            => 25,
			'lead_time'      => '3–5 weeks',
			'dimensions'     => array(
				array(
					'label' => 'Process',
					'value' => 'Laser, bend, weld',
				),
				array(
					'label' => 'Tolerance',
					'value' => '±0.1 mm cutting',
				),
				array(
					'label' => 'Max sheet',
					'value' => '1500 × 3000 mm',
				),
				array(
					'label' => 'Thickness',
					'value' => '1.5–8 mm',
				),
			),
		),
	);
}

/**
 * Sample project case studies.
 *
 * @return array<int, array<string, mixed>>
 */
function corten_core_sample_projects() {
	return array(
		array(
			'slug'    => 'riverside-courtyard-copenhagen',
			'title'   => __( 'Riverside courtyard, Copenhagen', 'corten-core' ),
			'content' => __( 'A landscape studio specified a family of square Corten planters with matching seat walls. We nested skins, drained every box, and packed for a single crane window.', 'corten-core' ),
			'excerpt' => __( 'The planters arrived square, drained, and already reading as landscape — not painted metal.', 'corten-core' ),
			'image'   => 'case-courtyard.svg',
			'client'  => 'Atelier Nord',
		),
		array(
			'slug'    => 'rooftop-plant-screen-berlin',
			'title'   => __( 'Rooftop plant screen, Berlin', 'corten-core' ),
			'content' => __( 'Modular weathering-steel enclosure bays aligned to a 600 mm façade grid. Repeat welded frames, vented doors, and a rust map that matched adjacent brick.', 'corten-core' ),
			'excerpt' => __( 'Enclosure modules matched our grid and weathered evenly against the brick.', 'corten-core' ),
			'image'   => 'case-rooftop.svg',
			'client'  => 'Halle Architecture',
		),
	);
}

/**
 * Create or update a product post and its meta/taxonomies.
 *
 * @param array<string, mixed> $product Product payload.
 * @param bool                 $force   Refresh attachments.
 * @return int
 */
function corten_core_upsert_product( $product, $force ) {
	$existing = get_page_by_path( $product['slug'], OBJECT, 'product' );
	$postarr  = array(
		'post_title'   => $product['title'],
		'post_name'    => $product['slug'],
		'post_content' => $product['content'],
		'post_excerpt' => $product['excerpt'],
		'post_status'  => 'publish',
		'post_type'    => 'product',
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$post_id         = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	wp_set_object_terms( $post_id, $product['material'], 'material', false );
	wp_set_object_terms( $post_id, $product['application'], 'application', false );
	wp_set_object_terms( $post_id, $product['category'], 'product_category', false );

	$meta = array(
		'material'       => $product['material'],
		'surface_finish' => $product['surface_finish'],
		'customization'  => $product['customization'],
		'moq'            => $product['moq'],
		'lead_time'      => $product['lead_time'],
		'size_range'     => $product['size_range'],
		'dimensions'     => $product['dimensions'],
	);
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
		if ( function_exists( 'update_field' ) ) {
			update_field( $key, $value, $post_id );
		}
	}

	$featured_id = corten_core_sideload_sample_image( $product['image'], $post_id, $product['title'], $force );
	if ( $featured_id ) {
		set_post_thumbnail( $post_id, $featured_id );
	}

	$gallery_ids = array();
	foreach ( $product['gallery'] as $filename ) {
		$attachment_id = corten_core_sideload_sample_image( $filename, $post_id, $product['title'] . ' gallery', $force );
		if ( $attachment_id ) {
			$gallery_ids[] = $attachment_id;
		}
	}
	if ( $gallery_ids ) {
		update_post_meta( $post_id, 'gallery', $gallery_ids );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'gallery', $gallery_ids, $post_id );
		}
	}

	return (int) $post_id;
}

/**
 * Create or update a project post.
 *
 * @param array<string, mixed> $project Project payload.
 * @param bool                 $force   Refresh attachments.
 * @return int
 */
function corten_core_upsert_project( $project, $force ) {
	$existing = get_page_by_path( $project['slug'], OBJECT, 'project' );
	$postarr  = array(
		'post_title'   => $project['title'],
		'post_name'    => $project['slug'],
		'post_content' => $project['content'],
		'post_excerpt' => $project['excerpt'],
		'post_status'  => 'publish',
		'post_type'    => 'project',
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$post_id         = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, 'client', $project['client'] );
	$image_id = corten_core_sideload_sample_image( $project['image'], $post_id, $project['title'], $force );
	if ( $image_id ) {
		set_post_thumbnail( $post_id, $image_id );
	}

	return (int) $post_id;
}

/**
 * Sideload a bundled SVG into the media library.
 *
 * @param string $filename File under assets/img or the active theme.
 * @param int    $parent_id Parent post ID.
 * @param string $title     Attachment title.
 * @param bool   $force     Recreate if an attachment with this filename exists.
 * @return int
 */
function corten_core_sideload_sample_image( $filename, $parent_id, $title, $force = false ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'post_status'    => 'inherit',
			'meta_key'       => '_corten_sample_file',
			'meta_value'     => $filename,
			'fields'         => 'ids',
		)
	);
	if ( $existing && ! $force ) {
		return (int) $existing[0];
	}

	$path = corten_core_sample_image_path( $filename );
	if ( ! $path ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	add_filter( 'upload_mimes', 'corten_core_allow_svg_upload' );
	add_filter( 'wp_check_filetype_and_ext', 'corten_core_allow_svg_filetype', 10, 5 );

	$tmp = wp_tempnam( $filename );
	if ( ! $tmp || ! copy( $path, $tmp ) ) {
		remove_filter( 'upload_mimes', 'corten_core_allow_svg_upload' );
		remove_filter( 'wp_check_filetype_and_ext', 'corten_core_allow_svg_filetype', 10 );
		return 0;
	}

	$file_array = array(
		'name'     => $filename,
		'tmp_name' => $tmp,
	);

	$attachment_id = media_handle_sideload( $file_array, $parent_id, $title );

	remove_filter( 'upload_mimes', 'corten_core_allow_svg_upload' );
	remove_filter( 'wp_check_filetype_and_ext', 'corten_core_allow_svg_filetype', 10 );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}

	update_post_meta( $attachment_id, '_corten_sample_file', $filename );
	return (int) $attachment_id;
}

/**
 * Allow SVG during sample import only.
 *
 * @param array<string, string> $mimes Mime map.
 * @return array<string, string>
 */
function corten_core_allow_svg_upload( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

/**
 * Force SVG filetype detection during sample import.
 *
 * @param array<string, mixed> $data     Detection result.
 * @param string               $file     Temp file.
 * @param string               $filename Original name.
 * @param array<string, string> $mimes   Allowed mimes.
 * @param string               $real     Unused.
 * @return array<string, mixed>
 */
function corten_core_allow_svg_filetype( $data, $file, $filename, $mimes, $real = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}

/**
 * Resolve a sample image from the plugin, then the theme.
 *
 * @param string $filename Basename.
 * @return string
 */
function corten_core_sample_image_path( $filename ) {
	$filename = basename( $filename );
	$candidates = array(
		CORTEN_CORE_DIR . 'assets/img/' . $filename,
		get_template_directory() . '/assets/img/' . $filename,
	);

	foreach ( $candidates as $path ) {
		if ( is_readable( $path ) ) {
			return $path;
		}
	}

	return '';
}
