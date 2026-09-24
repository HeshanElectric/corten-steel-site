<?php
/**
 * Native product metaboxes for gallery, specs, and short summary.
 * Used when ACF is not active so wp-admin always has edit fields.
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether ACF is already providing the Product Details UI.
 *
 * @return bool
 */
function corten_core_has_acf_product_ui() {
	return function_exists( 'acf_add_local_field_group' );
}

/**
 * Select options shared with the ACF field group.
 *
 * @return array<string, array<string, string>>
 */
function corten_core_product_choice_maps() {
	return array(
		'material'       => array(
			'Corten Steel' => __( 'Corten Steel', 'corten-core' ),
			'Mild Steel'   => __( 'Mild Steel', 'corten-core' ),
			'Aluminum'     => __( 'Aluminum', 'corten-core' ),
			'Galvanized'   => __( 'Galvanized', 'corten-core' ),
		),
		'surface_finish' => array(
			'Natural Rust'  => __( 'Natural Rust', 'corten-core' ),
			'Powder Coated' => __( 'Powder Coated', 'corten-core' ),
			'Raw'           => __( 'Raw', 'corten-core' ),
		),
		'size_range'     => array(
			'compact'       => __( 'Compact · up to 400 mm', 'corten-core' ),
			'standard'      => __( 'Standard · 400–900 mm', 'corten-core' ),
			'architectural' => __( 'Architectural · 900 mm+', 'corten-core' ),
		),
		'customization'  => array(
			'Size'      => __( 'Size', 'corten-core' ),
			'Logo'      => __( 'Logo', 'corten-core' ),
			'Packaging' => __( 'Packaging', 'corten-core' ),
			'Color'     => __( 'Color', 'corten-core' ),
		),
	);
}

/**
 * Register product metaboxes.
 */
function corten_core_register_product_metaboxes() {
	add_meta_box(
		'corten_product_excerpt',
		__( 'Short summary', 'corten-core' ),
		'corten_core_render_product_excerpt_metabox',
		'product',
		'normal',
		'high'
	);

	if ( corten_core_has_acf_product_ui() ) {
		return;
	}

	add_meta_box(
		'corten_product_gallery',
		__( 'Product gallery / carousel', 'corten-core' ),
		'corten_core_render_product_gallery_metabox',
		'product',
		'normal',
		'high'
	);
	add_meta_box(
		'corten_product_specs',
		__( 'Product specifications', 'corten-core' ),
		'corten_core_render_product_specs_metabox',
		'product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_product', 'corten_core_register_product_metaboxes' );

/**
 * Short catalog excerpt. Always shown so it is not buried in Screen Options.
 *
 * @param WP_Post $post Product post.
 */
function corten_core_render_product_excerpt_metabox( $post ) {
	wp_nonce_field( 'corten_save_product_meta', 'corten_product_meta_nonce' );
	echo '<p>' . esc_html__( 'Shown on catalog cards and under the product title. The main editor above is the full product description on the product page.', 'corten-core' ) . '</p>';
	echo '<textarea class="widefat" rows="4" name="corten_product_excerpt" id="corten_product_excerpt">' . esc_textarea( (string) $post->post_excerpt ) . '</textarea>';
}

/**
 * Gallery picker. IDs stored in the same `gallery` meta key ACF uses.
 *
 * @param WP_Post $post Product post.
 */
function corten_core_render_product_gallery_metabox( $post ) {
	wp_nonce_field( 'corten_save_product_meta', 'corten_product_meta_nonce' );
	$ids = corten_core_product_gallery_ids( $post->ID );

	echo '<p>' . esc_html__( 'Carousel images on the product page. The featured image is the catalog cover. Add several photographs for the thumbnail strip.', 'corten-core' ) . '</p>';
	echo '<input type="hidden" id="corten_gallery" name="corten_gallery" value="' . esc_attr( implode( ',', $ids ) ) . '">';
	echo '<div id="corten_gallery-preview" class="corten-gallery-preview" style="display:flex;flex-wrap:wrap;gap:0.6rem;margin:0.75rem 0;">';
	foreach ( $ids as $id ) {
		$thumb = wp_get_attachment_image_url( $id, 'medium' );
		if ( ! $thumb ) {
			continue;
		}
		echo '<span class="corten-gallery-preview__item" data-id="' . esc_attr( (string) $id ) . '" style="position:relative;width:7.5rem;">';
		echo '<img src="' . esc_url( $thumb ) . '" alt="" style="width:7.5rem;height:7.5rem;object-fit:cover;display:block;background:#1a1a1a;">';
		echo '<button type="button" class="button-link" data-corten-gallery-remove="' . esc_attr( (string) $id ) . '" style="position:absolute;top:0.2rem;right:0.35rem;color:#fff;background:#111;padding:0 0.35rem;">×</button>';
		echo '</span>';
	}
	echo '</div>';
	echo '<p><button type="button" class="button" data-corten-gallery-add>' . esc_html__( 'Add images', 'corten-core' ) . '</button> ';
	echo '<button type="button" class="button" data-corten-gallery-clear>' . esc_html__( 'Clear gallery', 'corten-core' ) . '</button></p>';
}

/**
 * Spec fields: material, finish, MOQ, lead time, size, customization, dimensions.
 *
 * @param WP_Post $post Product post.
 */
function corten_core_render_product_specs_metabox( $post ) {
	wp_nonce_field( 'corten_save_product_meta', 'corten_product_meta_nonce' );
	$choices = corten_core_product_choice_maps();
	$material = (string) get_post_meta( $post->ID, 'material', true );
	$finish   = (string) get_post_meta( $post->ID, 'surface_finish', true );
	$size     = (string) get_post_meta( $post->ID, 'size_range', true );
	$moq      = get_post_meta( $post->ID, 'moq', true );
	$lead     = (string) get_post_meta( $post->ID, 'lead_time', true );
	$custom   = get_post_meta( $post->ID, 'customization', true );
	$rows     = get_post_meta( $post->ID, 'dimensions', true );

	if ( ! is_array( $custom ) ) {
		$custom = array_filter( array_map( 'trim', explode( ',', (string) $custom ) ) );
	}
	if ( ! is_array( $rows ) || ! $rows ) {
		$rows = array(
			array( 'label' => '', 'value' => '' ),
			array( 'label' => '', 'value' => '' ),
			array( 'label' => '', 'value' => '' ),
		);
	}

	echo '<p>' . esc_html__( 'These values appear in the specification table on the product page and feed the catalog filters.', 'corten-core' ) . '</p>';

	echo '<p><label for="corten_material"><strong>' . esc_html__( 'Material', 'corten-core' ) . '</strong></label><br>';
	echo '<select id="corten_material" name="corten_material">';
	echo '<option value="">' . esc_html__( 'Select', 'corten-core' ) . '</option>';
	foreach ( $choices['material'] as $value => $label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $material, $value, false ), esc_html( $label ) );
	}
	echo '</select></p>';

	echo '<p><label for="corten_surface_finish"><strong>' . esc_html__( 'Surface finish', 'corten-core' ) . '</strong></label><br>';
	echo '<select id="corten_surface_finish" name="corten_surface_finish">';
	echo '<option value="">' . esc_html__( 'Select', 'corten-core' ) . '</option>';
	foreach ( $choices['surface_finish'] as $value => $label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $finish, $value, false ), esc_html( $label ) );
	}
	echo '</select></p>';

	echo '<p><label for="corten_size_range"><strong>' . esc_html__( 'Size range', 'corten-core' ) . '</strong></label><br>';
	echo '<select id="corten_size_range" name="corten_size_range">';
	echo '<option value="">' . esc_html__( 'Select', 'corten-core' ) . '</option>';
	foreach ( $choices['size_range'] as $value => $label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $size, $value, false ), esc_html( $label ) );
	}
	echo '</select></p>';

	echo '<p><label for="corten_moq"><strong>' . esc_html__( 'MOQ', 'corten-core' ) . '</strong></label><br>';
	echo '<input type="number" min="1" step="1" id="corten_moq" name="corten_moq" value="' . esc_attr( (string) $moq ) . '"></p>';

	echo '<p><label for="corten_lead_time"><strong>' . esc_html__( 'Lead time', 'corten-core' ) . '</strong></label><br>';
	echo '<input class="regular-text" type="text" id="corten_lead_time" name="corten_lead_time" value="' . esc_attr( $lead ) . '" placeholder="4–6 weeks"></p>';

	echo '<p><strong>' . esc_html__( 'Customizable', 'corten-core' ) . '</strong><br>';
	foreach ( $choices['customization'] as $value => $label ) {
		$checked = in_array( $value, $custom, true ) ? ' checked' : '';
		echo '<label style="margin-right:1rem;"><input type="checkbox" name="corten_customization[]" value="' . esc_attr( $value ) . '"' . $checked . '> ' . esc_html( $label ) . '</label>';
	}
	echo '</p>';

	echo '<p><strong>' . esc_html__( 'Dimensions & process', 'corten-core' ) . '</strong></p>';
	echo '<table class="widefat striped" style="max-width:40rem;"><thead><tr><th>' . esc_html__( 'Label', 'corten-core' ) . '</th><th>' . esc_html__( 'Value', 'corten-core' ) . '</th><th></th></tr></thead>';
	echo '<tbody id="corten-dim-rows">';
	foreach ( array_values( $rows ) as $index => $row ) {
		$label = isset( $row['label'] ) ? (string) $row['label'] : '';
		$value = isset( $row['value'] ) ? (string) $row['value'] : '';
		corten_core_print_dimension_row( $index, $label, $value );
	}
	echo '</tbody></table>';
	echo '<p><button type="button" class="button" data-corten-dim-add>' . esc_html__( 'Add row', 'corten-core' ) . '</button></p>';
	echo '<template id="corten-dim-template">';
	corten_core_print_dimension_row( '__i__', '', '' );
	echo '</template>';
}

/**
 * One dimensions table row.
 *
 * @param int|string $index Row index or placeholder.
 * @param string     $label Label.
 * @param string     $value Value.
 */
function corten_core_print_dimension_row( $index, $label, $value ) {
	echo '<tr>';
	echo '<td><input class="widefat" type="text" name="corten_dimensions[' . esc_attr( (string) $index ) . '][label]" value="' . esc_attr( $label ) . '" placeholder="Width"></td>';
	echo '<td><input class="widefat" type="text" name="corten_dimensions[' . esc_attr( (string) $index ) . '][value]" value="' . esc_attr( $value ) . '" placeholder="600 mm"></td>';
	echo '<td><button type="button" class="button" data-corten-dim-remove>' . esc_html__( 'Remove', 'corten-core' ) . '</button></td>';
	echo '</tr>';
}

/**
 * Attachment IDs stored in gallery meta.
 *
 * @param int $post_id Product ID.
 * @return int[]
 */
function corten_core_product_gallery_ids( $post_id ) {
	$raw = get_post_meta( $post_id, 'gallery', true );
	$ids = array();
	if ( is_array( $raw ) ) {
		$ids = $raw;
	} elseif ( is_string( $raw ) && $raw ) {
		$ids = explode( ',', $raw );
	}
	return array_values( array_filter( array_map( 'absint', $ids ) ) );
}

/**
 * Persist excerpt, gallery, and specification meta.
 *
 * @param int $post_id Product ID.
 */
function corten_core_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['corten_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['corten_product_meta_nonce'] ) ), 'corten_save_product_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['corten_product_excerpt'] ) ) {
		$excerpt = sanitize_textarea_field( wp_unslash( $_POST['corten_product_excerpt'] ) );
		remove_action( 'save_post_product', 'corten_core_save_product_meta' );
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_excerpt' => $excerpt,
			)
		);
		add_action( 'save_post_product', 'corten_core_save_product_meta' );
	}

	if ( corten_core_has_acf_product_ui() ) {
		return;
	}

	$gallery = array();
	if ( isset( $_POST['corten_gallery'] ) ) {
		$gallery = array_values( array_filter( array_map( 'absint', explode( ',', (string) wp_unslash( $_POST['corten_gallery'] ) ) ) ) );
	}
	update_post_meta( $post_id, 'gallery', $gallery );

	$material = isset( $_POST['corten_material'] ) ? sanitize_text_field( wp_unslash( $_POST['corten_material'] ) ) : '';
	update_post_meta( $post_id, 'material', $material );

	$finish = isset( $_POST['corten_surface_finish'] ) ? sanitize_text_field( wp_unslash( $_POST['corten_surface_finish'] ) ) : '';
	update_post_meta( $post_id, 'surface_finish', $finish );

	$size = isset( $_POST['corten_size_range'] ) ? sanitize_text_field( wp_unslash( $_POST['corten_size_range'] ) ) : '';
	update_post_meta( $post_id, 'size_range', $size );

	$moq = isset( $_POST['corten_moq'] ) ? absint( $_POST['corten_moq'] ) : 0;
	update_post_meta( $post_id, 'moq', $moq );

	$lead = isset( $_POST['corten_lead_time'] ) ? sanitize_text_field( wp_unslash( $_POST['corten_lead_time'] ) ) : '';
	update_post_meta( $post_id, 'lead_time', $lead );

	$custom = array();
	if ( isset( $_POST['corten_customization'] ) && is_array( $_POST['corten_customization'] ) ) {
		$allowed = array_keys( corten_core_product_choice_maps()['customization'] );
		foreach ( wp_unslash( $_POST['corten_customization'] ) as $item ) {
			$item = sanitize_text_field( $item );
			if ( in_array( $item, $allowed, true ) ) {
				$custom[] = $item;
			}
		}
	}
	update_post_meta( $post_id, 'customization', $custom );

	$dimensions = array();
	if ( isset( $_POST['corten_dimensions'] ) && is_array( $_POST['corten_dimensions'] ) ) {
		foreach ( wp_unslash( $_POST['corten_dimensions'] ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '';
			$value = isset( $row['value'] ) ? sanitize_text_field( $row['value'] ) : '';
			if ( '' === $label && '' === $value ) {
				continue;
			}
			$dimensions[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
	}
	update_post_meta( $post_id, 'dimensions', $dimensions );
}
add_action( 'save_post_product', 'corten_core_save_product_meta' );

/**
 * Point editors to the description, gallery, and spec boxes.
 */
function corten_core_product_editor_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'product' !== $screen->id ) {
		return;
	}
	echo '<div class="notice notice-info"><p>';
	echo esc_html__( 'The main editor is the full product description on the product page. Use Short summary for catalog cards, Product gallery / carousel for the photo strip, and Product specifications for material, MOQ, lead time, and dimensions.', 'corten-core' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'corten_core_product_editor_notice' );

/**
 * Media modal + gallery/dimensions helpers on the product editor.
 *
 * @param string $hook Current admin screen.
 */
function corten_core_enqueue_product_admin( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'corten-product-admin',
		CORTEN_CORE_URL . 'assets/js/product-admin.js',
		array(),
		CORTEN_CORE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'corten_core_enqueue_product_admin' );
