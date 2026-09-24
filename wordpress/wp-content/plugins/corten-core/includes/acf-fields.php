<?php
/**
 * Local ACF field group for product details. No-op until ACF is active.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Product Details field group with ACF.
 */
function corten_core_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_corten_product_details',
			'title'    => __( 'Product Details', 'corten-core' ),
			'fields'   => array(
				array(
					'key'           => 'field_corten_gallery',
					'label'         => __( 'Gallery', 'corten-core' ),
					'name'          => 'gallery',
					'type'          => 'gallery',
					'return_format' => 'id',
					'preview_size'  => 'corten-card',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_corten_material',
					'label'         => __( 'Material', 'corten-core' ),
					'name'          => 'material',
					'type'          => 'select',
					'choices'       => array(
						'Corten Steel' => __( 'Corten Steel', 'corten-core' ),
						'Mild Steel'   => __( 'Mild Steel', 'corten-core' ),
						'Aluminum'     => __( 'Aluminum', 'corten-core' ),
						'Galvanized'   => __( 'Galvanized', 'corten-core' ),
					),
					'allow_null'    => 0,
					'ui'            => 1,
				),
				array(
					'key'          => 'field_corten_dimensions',
					'label'        => __( 'Dimensions', 'corten-core' ),
					'name'         => 'dimensions',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add row', 'corten-core' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_corten_dimension_label',
							'label' => __( 'Label', 'corten-core' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_corten_dimension_value',
							'label' => __( 'Value', 'corten-core' ),
							'name'  => 'value',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'     => 'field_corten_surface_finish',
					'label'   => __( 'Surface finish', 'corten-core' ),
					'name'    => 'surface_finish',
					'type'    => 'select',
					'choices' => array(
						'Natural Rust'   => __( 'Natural Rust', 'corten-core' ),
						'Powder Coated'  => __( 'Powder Coated', 'corten-core' ),
						'Raw'            => __( 'Raw', 'corten-core' ),
					),
					'ui'      => 1,
				),
				array(
					'key'     => 'field_corten_customization',
					'label'   => __( 'Customization', 'corten-core' ),
					'name'    => 'customization',
					'type'    => 'checkbox',
					'choices' => array(
						'Size'      => __( 'Size', 'corten-core' ),
						'Logo'      => __( 'Logo', 'corten-core' ),
						'Packaging' => __( 'Packaging', 'corten-core' ),
						'Color'     => __( 'Color', 'corten-core' ),
					),
					'layout'  => 'horizontal',
				),
				array(
					'key'   => 'field_corten_moq',
					'label' => __( 'MOQ', 'corten-core' ),
					'name'  => 'moq',
					'type'  => 'number',
					'min'   => 1,
					'step'  => 1,
				),
				array(
					'key'   => 'field_corten_lead_time',
					'label' => __( 'Lead time', 'corten-core' ),
					'name'  => 'lead_time',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_corten_spec_sheet',
					'label'         => __( 'Download spec sheet', 'corten-core' ),
					'name'          => 'download_spec_sheet',
					'type'          => 'file',
					'return_format' => 'array',
					'mime_types'    => 'pdf,doc,docx',
				),
				array(
					'key'     => 'field_corten_size_range',
					'label'   => __( 'Size range', 'corten-core' ),
					'name'    => 'size_range',
					'type'    => 'select',
					'choices' => array(
						'compact'       => __( 'Compact · up to 400 mm', 'corten-core' ),
						'standard'      => __( 'Standard · 400–900 mm', 'corten-core' ),
						'architectural' => __( 'Architectural · 900 mm+', 'corten-core' ),
					),
					'instructions' => __( 'Used by the catalog AJAX size filter.', 'corten-core' ),
					'ui'      => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'position' => 'normal',
			'style'    => 'default',
		)
	);
}
add_action( 'acf/init', 'corten_core_register_acf_fields' );

/**
 * Keep the material taxonomy in sync with the ACF select.
 *
 * @param int $post_id Post ID.
 */
function corten_core_sync_material_taxonomy( $post_id ) {
	if ( 'product' !== get_post_type( $post_id ) ) {
		return;
	}

	$material = '';
	if ( function_exists( 'get_field' ) ) {
		$material = (string) get_field( 'material', $post_id );
	}
	if ( ! $material ) {
		$material = (string) get_post_meta( $post_id, 'material', true );
	}
	if ( $material && taxonomy_exists( 'material' ) ) {
		wp_set_object_terms( $post_id, $material, 'material', false );
	}
}
add_action( 'acf/save_post', 'corten_core_sync_material_taxonomy', 20 );
add_action( 'save_post_product', 'corten_core_sync_material_taxonomy', 20 );
