<?php
/**
 * Native image (and resource-card) metaboxes for About, Resources, and Capabilities.
 *
 * @package Corten_Steel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image slots grouped by page template.
 *
 * @return array<string, array<string, string>>
 */
function corten_page_image_fields() {
	return array(
		'page-capabilities.php' => array(
			'corten_cap_hero'     => __( 'Hero photograph', 'corten-steel' ),
			'corten_cap_cutting'  => __( 'Cutting bay', 'corten-steel' ),
			'corten_cap_forming'  => __( 'Forming bay', 'corten-steel' ),
			'corten_cap_joinery'  => __( 'Joinery / weld bay', 'corten-steel' ),
			'corten_cap_finish'   => __( 'Finish & pack', 'corten-steel' ),
			'corten_cap_plant'    => __( 'Shop-floor photograph', 'corten-steel' ),
		),
		'page-about.php'        => array(
			'corten_about_hero'     => __( 'Hero photograph', 'corten-steel' ),
			'corten_about_story'    => __( 'Portrait / founder image', 'corten-steel' ),
			'corten_about_workshop' => __( 'Workshop photograph (large)', 'corten-steel' ),
			'corten_about_atelier'  => __( 'Atelier photograph (tall)', 'corten-steel' ),
			'corten_about_shop_1'   => __( 'Workshop and atelier — extra photo 1', 'corten-steel' ),
			'corten_about_shop_2'   => __( 'Workshop and atelier — extra photo 2', 'corten-steel' ),
			'corten_about_shop_3'   => __( 'Workshop and atelier — extra photo 3', 'corten-steel' ),
			'corten_about_shop_4'   => __( 'Workshop and atelier — extra photo 4', 'corten-steel' ),
			'corten_about_cert_1'   => __( 'Quality certificate 1', 'corten-steel' ),
			'corten_about_cert_2'   => __( 'Quality certificate 2', 'corten-steel' ),
			'corten_about_cert_3'   => __( 'Quality certificate 3', 'corten-steel' ),
			'corten_about_cert_4'   => __( 'Quality certificate 4', 'corten-steel' ),
			'corten_about_cert_5'   => __( 'Quality certificate 5', 'corten-steel' ),
			'corten_about_cert_6'   => __( 'Quality certificate 6', 'corten-steel' ),
		),
		'page-resources.php'    => array(
			'corten_res_hero'    => __( 'Hero photograph', 'corten-steel' ),
			'corten_res_image_1' => __( 'Resource card 1 image', 'corten-steel' ),
			'corten_res_image_2' => __( 'Resource card 2 image', 'corten-steel' ),
			'corten_res_image_3' => __( 'Resource card 3 image', 'corten-steel' ),
		),
	);
}

/**
 * Text / file fields for resource download cards.
 *
 * @return array<string, string>
 */
function corten_resource_text_fields() {
	return array(
		'corten_res_title_1' => __( 'Resource card 1 title', 'corten-steel' ),
		'corten_res_file_1'  => __( 'Resource card 1 PDF', 'corten-steel' ),
		'corten_res_url_1'   => __( 'Resource card 1 external PDF URL (optional)', 'corten-steel' ),
		'corten_res_title_2' => __( 'Resource card 2 title', 'corten-steel' ),
		'corten_res_file_2'  => __( 'Resource card 2 PDF', 'corten-steel' ),
		'corten_res_url_2'   => __( 'Resource card 2 external PDF URL (optional)', 'corten-steel' ),
		'corten_res_title_3' => __( 'Resource card 3 title', 'corten-steel' ),
		'corten_res_file_3'  => __( 'Resource card 3 PDF', 'corten-steel' ),
		'corten_res_url_3'   => __( 'Resource card 3 external PDF URL (optional)', 'corten-steel' ),
	);
}

/**
 * Resolve a resource card download URL. Prefers an uploaded attachment, then an external URL.
 *
 * @param int $post_id Page ID.
 * @param int $index   Card number 1–3.
 * @return string
 */
function corten_get_resource_download_url( $post_id, $index ) {
	$file_id = (int) get_post_meta( $post_id, 'corten_res_file_' . $index, true );
	if ( $file_id ) {
		$url = wp_get_attachment_url( $file_id );
		if ( $url ) {
			return $url;
		}
	}

	$external = (string) get_post_meta( $post_id, 'corten_res_url_' . $index, true );
	if ( $external && home_url( '/contact/' ) !== untrailingslashit( $external ) && home_url( '/contact' ) !== untrailingslashit( $external ) ) {
		return $external;
	}

	return '';
}

/**
 * Editable copy for the About page "Why we exist" block.
 *
 * @return array<string, array<string, string>>
 */
function corten_about_copy_fields() {
	return array(
		'corten_about_why_eyebrow' => array(
			'label'       => __( 'Why we exist — section label', 'corten-steel' ),
			'type'        => 'text',
			'placeholder' => __( 'Why we exist', 'corten-steel' ),
		),
		'corten_about_why_title'   => array(
			'label'       => __( 'Why we exist — heading', 'corten-steel' ),
			'type'        => 'text',
			'placeholder' => __( 'A fabrication desk for people who specify rust', 'corten-steel' ),
		),
		'corten_about_why_text'    => array(
			'label'       => __( 'Why we exist — body', 'corten-steel' ),
			'type'        => 'textarea',
			'rows'        => '8',
			'placeholder' => __( 'Landscape architects and OEM buyers do not want a painted box that peels. They want a living surface that settles into the site — folded corners, drained bases, and weld maps that weather evenly. Oxiron is the plant that treats that surface as architecture.', 'corten-steel' ),
		),
	);
}

/**
 * Default milestones for the About timeline.
 *
 * @return array<int, array<string, string>>
 */
function corten_about_journey_defaults() {
	return array(
		1 => array(
			'year'  => '2012',
			'title' => __( 'Plant founded', 'corten-steel' ),
			'text'  => __( 'Oxiron opened as a sheet-metal shop for outdoor steel — folded corners, drained bases, and weld maps built for weather.', 'corten-steel' ),
		),
		2 => array(
			'year'  => '2016',
			'title' => __( 'Weathering-steel line', 'corten-steel' ),
			'text'  => __( 'Dedicated Corten planter and enclosure programs, with even rust maps specified for landscape and façade teams.', 'corten-steel' ),
		),
		3 => array(
			'year'  => '2019',
			'title' => __( 'EU & US programs', 'corten-steel' ),
			'text'  => __( 'First staged lots for European sites and North American distribution, with export crates and packing lists as standard.', 'corten-steel' ),
		),
		4 => array(
			'year'  => '2023',
			'title' => __( 'Drawing-controlled OEM', 'corten-steel' ),
			'text'  => __( 'Repeat jigs, private-label packing, and drawing control for equipment and furniture brands.', 'corten-steel' ),
		),
		5 => array(
			'year'  => 'Today',
			'title' => __( 'Spec-led fabrication', 'corten-steel' ),
			'text'  => __( 'Continuous improvement of finish, weld quality, and plant logistics for architects and OEM buyers.', 'corten-steel' ),
		),
	);
}

/**
 * Timeline field map for the About editor.
 *
 * @return array<string, array<string, string>>
 */
function corten_about_journey_fields() {
	$fields = array();
	foreach ( corten_about_journey_defaults() as $index => $def ) {
		$fields[ 'corten_about_journey_year_' . $index ]  = array(
			'label'       => sprintf( /* translators: %d: milestone number */ __( 'Journey %d — year', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $def['year'],
		);
		$fields[ 'corten_about_journey_title_' . $index ] = array(
			'label'       => sprintf( /* translators: %d: milestone number */ __( 'Journey %d — heading', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $def['title'],
		);
		$fields[ 'corten_about_journey_text_' . $index ]  = array(
			'label'       => sprintf( /* translators: %d: milestone number */ __( 'Journey %d — text', 'corten-steel' ), $index ),
			'type'        => 'textarea',
			'placeholder' => $def['text'],
		);
	}
	return $fields;
}

/**
 * Default titles under Quality Certificate images.
 *
 * @return array<int, string>
 */
function corten_about_cert_defaults() {
	return array(
		1 => __( 'ISO 9001', 'corten-steel' ),
		2 => __( 'EN 1090 / CE', 'corten-steel' ),
		3 => __( 'ISO 14001', 'corten-steel' ),
		4 => __( 'ISO 3834 welding', 'corten-steel' ),
		5 => __( 'Mill certificates', 'corten-steel' ),
		6 => __( 'Packing inspection', 'corten-steel' ),
	);
}

/**
 * Caption fields for certificate scans.
 *
 * @return array<string, array<string, string>>
 */
function corten_about_cert_title_fields() {
	$fields = array();
	foreach ( corten_about_cert_defaults() as $index => $title ) {
		$fields[ 'corten_about_cert_title_' . $index ] = array(
			'label'       => sprintf( /* translators: %d: certificate number */ __( 'Certificate %d — caption', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $title,
		);
	}
	return $fields;
}

/**
 * Stored meta or a default when the field has never been saved.
 *
 * @param int    $post_id  Page ID.
 * @param string $meta_key Meta key.
 * @param string $fallback Default copy.
 * @return string
 */
function corten_meta_or_default( $post_id, $meta_key, $fallback = '' ) {
	if ( ! metadata_exists( 'post', $post_id, $meta_key ) ) {
		return $fallback;
	}
	return (string) get_post_meta( $post_id, $meta_key, true );
}

/**
 * Journey milestones for the About template.
 *
 * @param int $post_id Page ID.
 * @return array<int, array<string, string>>
 */
function corten_get_about_journey( $post_id ) {
	$items = array();
	foreach ( corten_about_journey_defaults() as $index => $def ) {
		$year  = corten_meta_or_default( $post_id, 'corten_about_journey_year_' . $index, $def['year'] );
		$title = corten_meta_or_default( $post_id, 'corten_about_journey_title_' . $index, $def['title'] );
		$text  = corten_meta_or_default( $post_id, 'corten_about_journey_text_' . $index, $def['text'] );
		if ( '' === trim( $year . $title . $text ) ) {
			continue;
		}
		$items[] = array(
			'year'  => $year,
			'title' => $title,
			'text'  => $text,
		);
	}
	return $items;
}

/**
 * Certificate cards for the About template.
 *
 * @param int $post_id Page ID.
 * @return array<int, array<string, string>>
 */
function corten_get_about_certs( $post_id ) {
	$cards = array();
	foreach ( corten_about_cert_defaults() as $index => $title ) {
		$cards[] = array(
			'image' => corten_meta_image_url( $post_id, 'corten_about_cert_' . $index, 'certificate-plate.svg' ),
			'title' => corten_meta_or_default( $post_id, 'corten_about_cert_title_' . $index, $title ),
		);
	}
	return $cards;
}

/**
 * Default Direct Contacts rows.
 *
 * @return array<int, array<string, string>>
 */
function corten_contact_direct_defaults() {
	return array(
		1 => array(
			'label' => __( 'Sales / Export', 'corten-steel' ),
			'value' => 'sales@heshanpower.com',
		),
		2 => array(
			'label' => __( 'Engineering / OEM', 'corten-steel' ),
			'value' => 'sales@heshanpower.com',
		),
		3 => array(
			'label' => __( 'Phone', 'corten-steel' ),
			'value' => '+44-7593-196114',
		),
		4 => array(
			'label' => __( 'WhatsApp', 'corten-steel' ),
			'value' => '+44-7593-196114',
		),
		5 => array(
			'label' => __( 'Address', 'corten-steel' ),
			'value' => __( 'Southwest corner of the intersection of Changjiang Avenue and Chaoyang Road, Development Zone, Anyang City, Henan Province, China', 'corten-steel' ),
		),
	);
}

/**
 * Default working-hours rows.
 *
 * @return array<int, array<string, string>>
 */
function corten_contact_hours_defaults() {
	return array(
		1 => array(
			'region' => __( 'China (GMT+8)', 'corten-steel' ),
			'hours'  => __( 'Mon–Fri 09:00–18:00', 'corten-steel' ),
		),
		2 => array(
			'region' => __( 'Europe CET/CEST', 'corten-steel' ),
			'hours'  => __( 'Morning reply guaranteed within 24h', 'corten-steel' ),
		),
		3 => array(
			'region' => __( 'US Eastern EST/EDT', 'corten-steel' ),
			'hours'  => __( 'Reply by next business morning', 'corten-steel' ),
		),
		4 => array(
			'region' => __( 'UK GMT/BST', 'corten-steel' ),
			'hours'  => __( 'Reply within 24h on business days', 'corten-steel' ),
		),
	);
}

/**
 * Text fields for Contact titles and table rows.
 *
 * @return array<string, array<string, string>>
 */
function corten_contact_copy_fields() {
	$fields = array(
		'corten_contact_direct_title' => array(
			'label'       => __( 'Direct Contacts — heading', 'corten-steel' ),
			'type'        => 'text',
			'placeholder' => __( 'Direct Contacts', 'corten-steel' ),
		),
		'corten_contact_hours_title'  => array(
			'label'       => __( 'Working Hours — heading', 'corten-steel' ),
			'type'        => 'text',
			'placeholder' => __( 'Working Hours (Your Timezone)', 'corten-steel' ),
		),
	);

	foreach ( corten_contact_direct_defaults() as $index => $row ) {
		$fields[ 'corten_contact_label_' . $index ] = array(
			'label'       => sprintf( /* translators: %d: row number */ __( 'Contact %d — label', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $row['label'],
		);
		$fields[ 'corten_contact_value_' . $index ] = array(
			'label'       => sprintf( /* translators: %d: row number */ __( 'Contact %d — value', 'corten-steel' ), $index ),
			'type'        => 'textarea',
			'rows'        => '2',
			'placeholder' => $row['value'],
		);
	}

	foreach ( corten_contact_hours_defaults() as $index => $row ) {
		$fields[ 'corten_hours_region_' . $index ] = array(
			'label'       => sprintf( /* translators: %d: row number */ __( 'Hours %d — region', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $row['region'],
		);
		$fields[ 'corten_hours_value_' . $index ]  = array(
			'label'       => sprintf( /* translators: %d: row number */ __( 'Hours %d — working hours', 'corten-steel' ), $index ),
			'type'        => 'text',
			'placeholder' => $row['hours'],
		);
	}

	return $fields;
}

/**
 * Direct contact rows for the Contact template.
 *
 * @param int $post_id Page ID.
 * @return array<int, array<string, string>>
 */
function corten_get_contact_direct_rows( $post_id ) {
	$rows = array();
	foreach ( corten_contact_direct_defaults() as $index => $def ) {
		$label = corten_meta_or_default( $post_id, 'corten_contact_label_' . $index, $def['label'] );
		$value = corten_meta_or_default( $post_id, 'corten_contact_value_' . $index, $def['value'] );
		if ( '' === trim( $label . $value ) ) {
			continue;
		}
		$rows[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
	return $rows;
}

/**
 * Working-hours rows for the Contact template.
 *
 * @param int $post_id Page ID.
 * @return array<int, array<string, string>>
 */
function corten_get_contact_hours_rows( $post_id ) {
	$rows = array();
	foreach ( corten_contact_hours_defaults() as $index => $def ) {
		$region = corten_meta_or_default( $post_id, 'corten_hours_region_' . $index, $def['region'] );
		$hours  = corten_meta_or_default( $post_id, 'corten_hours_value_' . $index, $def['hours'] );
		if ( '' === trim( $region . $hours ) ) {
			continue;
		}
		$rows[] = array(
			'region' => $region,
			'hours'  => $hours,
		);
	}
	return $rows;
}

/**
 * Turn a contact value into a mailto / tel / WhatsApp link when it matches.
 *
 * @param string $label Row label.
 * @param string $value Raw value.
 * @return string Escaped HTML.
 */
function corten_format_contact_value( $label, $value ) {
	$value = trim( $value );
	if ( ! $value ) {
		return '';
	}

	$haystack = strtolower( $label );
	$digits   = preg_replace( '/\D+/', '', $value );

	if ( false !== strpos( $value, '@' ) && is_email( $value ) ) {
		return '<a href="' . esc_url( 'mailto:' . $value ) . '">' . esc_html( $value ) . '</a>';
	}

	if ( false !== strpos( $haystack, 'whatsapp' ) && $digits ) {
		return '<a href="' . esc_url( 'https://wa.me/' . $digits ) . '" rel="noopener noreferrer" target="_blank">' . esc_html( $value ) . '</a>';
	}

	if ( ( false !== strpos( $haystack, 'phone' ) || false !== strpos( $haystack, 'tel' ) ) && $digits ) {
		return '<a href="' . esc_url( 'tel:' . $digits ) . '">' . esc_html( $value ) . '</a>';
	}

	return nl2br( esc_html( $value ) );
}

/**
 * Resolve the page template for a screen, including the slug fallback.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function corten_editor_page_template( $post_id ) {
	$template = get_page_template_slug( $post_id );
	if ( $template ) {
		return $template;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$map = array(
		'about'        => 'page-about.php',
		'resources'    => 'page-resources.php',
		'capabilities' => 'page-capabilities.php',
		'contact'      => 'page-contact.php',
	);

	return isset( $map[ $post->post_name ] ) ? $map[ $post->post_name ] : '';
}

/**
 * Register the Oxiron Images metabox on editorial pages.
 */
function corten_register_page_metaboxes() {
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$post_id = absint( $_GET['post'] );
	} elseif ( isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
		$post_id = (int) $GLOBALS['post']->ID;
	}

	if ( $post_id && 'page-about.php' === corten_editor_page_template( $post_id ) ) {
		add_meta_box(
			'corten_about_copy',
			__( 'Why we exist', 'corten-steel' ),
			'corten_render_about_copy_metabox',
			'page',
			'normal',
			'high'
		);
		add_meta_box(
			'corten_about_journey',
			__( 'Our Journey', 'corten-steel' ),
			'corten_render_about_journey_metabox',
			'page',
			'normal',
			'high'
		);
		add_meta_box(
			'corten_about_certs',
			__( 'Quality Certificate', 'corten-steel' ),
			'corten_render_about_certs_metabox',
			'page',
			'normal',
			'high'
		);
	}

	if ( $post_id && 'page-contact.php' === corten_editor_page_template( $post_id ) ) {
		add_meta_box(
			'corten_contact_directory',
			__( 'Direct Contacts & Working Hours', 'corten-steel' ),
			'corten_render_contact_directory_metabox',
			'page',
			'normal',
			'high'
		);
	}

	add_meta_box(
		'corten_page_images',
		__( 'Oxiron Images', 'corten-steel' ),
		'corten_render_page_metabox',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'corten_register_page_metaboxes' );

/**
 * Render the About-only copy fields for the Why we exist section.
 *
 * @param WP_Post $post Current page.
 */
function corten_render_about_copy_metabox( $post ) {
	if ( 'page-about.php' !== corten_editor_page_template( $post->ID ) ) {
		echo '<p>' . esc_html__( 'These fields appear when this page uses the About template.', 'corten-steel' ) . '</p>';
		return;
	}

	wp_nonce_field( 'corten_save_page_meta', 'corten_page_meta_nonce' );

	echo '<p>' . esc_html__( 'The hero title and intro still use the page title and the main editor above. This box edits the Why we exist heading and body.', 'corten-steel' ) . '</p>';
	corten_render_meta_text_fields( $post->ID, corten_about_copy_fields() );
}

/**
 * Render Contact directory fields.
 *
 * @param WP_Post $post Current page.
 */
function corten_render_contact_directory_metabox( $post ) {
	wp_nonce_field( 'corten_save_page_meta', 'corten_page_meta_nonce' );
	echo '<p>' . esc_html__( 'These tables appear below the RFQ form. Clear both fields on a row to hide it.', 'corten-steel' ) . '</p>';
	corten_render_meta_text_fields( $post->ID, corten_contact_copy_fields() );
}

/**
 * Render a list of text / textarea meta fields.
 *
 * @param int                                 $post_id Page ID.
 * @param array<string, array<string, string>> $fields  Field map.
 */
function corten_render_meta_text_fields( $post_id, $fields ) {
	foreach ( $fields as $key => $field ) {
		$value = (string) get_post_meta( $post_id, $key, true );
		echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label><br>';
		if ( 'textarea' === $field['type'] ) {
			$rows = ! empty( $field['rows'] ) ? (int) $field['rows'] : 3;
			echo '<textarea class="widefat" rows="' . esc_attr( (string) $rows ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . esc_textarea( $value ) . '</textarea></p>';
		} else {
			echo '<input class="widefat" type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '"></p>';
		}
	}
}

/**
 * Save a map of text / textarea fields.
 *
 * @param int                                 $post_id Page ID.
 * @param array<string, array<string, string>> $fields  Field map.
 */
function corten_save_meta_text_fields( $post_id, $fields ) {
	foreach ( $fields as $key => $field ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		if ( 'textarea' === $field['type'] ) {
			update_post_meta( $post_id, $key, wp_kses_post( $raw ) );
		} else {
			update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
		}
	}
}

/**
 * Render the Our Journey timeline fields.
 *
 * @param WP_Post $post Current page.
 */
function corten_render_about_journey_metabox( $post ) {
	wp_nonce_field( 'corten_save_page_meta', 'corten_page_meta_nonce' );
	echo '<p>' . esc_html__( 'Five plant milestones. Year, heading, and text are all editable. Clear every field on a row to hide that step.', 'corten-steel' ) . '</p>';
	corten_render_meta_text_fields( $post->ID, corten_about_journey_fields() );
}

/**
 * Render certificate captions. Scans are uploaded in Oxiron Images.
 *
 * @param WP_Post $post Current page.
 */
function corten_render_about_certs_metabox( $post ) {
	wp_nonce_field( 'corten_save_page_meta', 'corten_page_meta_nonce' );
	echo '<p>' . esc_html__( 'Captions for the six certificate cards. Upload the scans in the Oxiron Images box (Quality certificate 1–6).', 'corten-steel' ) . '</p>';
	corten_render_meta_text_fields( $post->ID, corten_about_cert_title_fields() );
}

/**
 * Render image pickers (and resource titles/URLs) for the matching template.
 *
 * @param WP_Post $post Current page.
 */
function corten_render_page_metabox( $post ) {
	$template = corten_editor_page_template( $post->ID );
	$groups   = corten_page_image_fields();

	if ( ! isset( $groups[ $template ] ) ) {
		echo '<p>' . esc_html__( 'Assign the About, Resources, or Capabilities template (Page Attributes) to unlock image slots.', 'corten-steel' ) . '</p>';
		return;
	}

	wp_nonce_field( 'corten_save_page_meta', 'corten_page_meta_nonce' );

	echo '<p>' . esc_html__( 'These photographs appear on the front-end template. If a slot is empty, the theme shows a rust-plate placeholder.', 'corten-steel' ) . '</p>';
	echo '<div class="corten-meta-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));gap:1rem;">';

	foreach ( $groups[ $template ] as $key => $label ) {
		$id    = (int) get_post_meta( $post->ID, $key, true );
		$thumb = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
		echo '<div class="corten-meta-slot" style="border:1px solid #dcdcde;padding:0.85rem;background:#fff;">';
		echo '<p><strong>' . esc_html( $label ) . '</strong></p>';
		echo '<div id="' . esc_attr( $key ) . '-preview" style="min-height:6rem;margin-bottom:0.6rem;background:#1a1a1a;">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" alt="" style="width:100%;height:8rem;object-fit:cover;display:block;">';
		}
		echo '</div>';
		echo '<input type="hidden" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $id ) . '">';
		echo '<p><button type="button" class="button" data-corten-media="' . esc_attr( $key ) . '" data-corten-title="' . esc_attr( $label ) . '">' . esc_html__( 'Select image', 'corten-steel' ) . '</button> ';
		echo '<button type="button" class="button" data-corten-media-clear="' . esc_attr( $key ) . '">' . esc_html__( 'Remove', 'corten-steel' ) . '</button></p>';
		echo '</div>';
	}

	echo '</div>';

	if ( 'page-about.php' === $template ) {
		echo '<p style="margin-top:1.25rem;">' . esc_html__( 'Workshop and atelier uses the two large photos plus the four extra slots. Quality certificate 1–6 are the scans on the About page.', 'corten-steel' ) . '</p>';
	}

	if ( 'page-resources.php' === $template ) {
		echo '<h3 style="margin-top:1.5rem;">' . esc_html__( 'Download cards', 'corten-steel' ) . '</h3>';
		echo '<p>' . esc_html__( 'Upload a PDF for each card. The card downloads that file on the front end. An external PDF URL is used only when no file is uploaded.', 'corten-steel' ) . '</p>';
		for ( $i = 1; $i <= 3; $i++ ) {
			$title_key = 'corten_res_title_' . $i;
			$file_key  = 'corten_res_file_' . $i;
			$url_key   = 'corten_res_url_' . $i;
			$title     = (string) get_post_meta( $post->ID, $title_key, true );
			$file_id   = (int) get_post_meta( $post->ID, $file_key, true );
			$url       = (string) get_post_meta( $post->ID, $url_key, true );
			$file_name = $file_id ? basename( (string) get_attached_file( $file_id ) ) : '';
			$file_url  = $file_id ? wp_get_attachment_url( $file_id ) : '';

			echo '<div style="border:1px solid #dcdcde;padding:1rem;margin:0 0 1rem;background:#fff;">';
			echo '<p><strong>' . esc_html( sprintf( /* translators: %d: card number */ __( 'Card %d', 'corten-steel' ), $i ) ) . '</strong></p>';
			echo '<p><label for="' . esc_attr( $title_key ) . '">' . esc_html__( 'Title', 'corten-steel' ) . '</label><br>';
			echo '<input class="widefat" type="text" id="' . esc_attr( $title_key ) . '" name="' . esc_attr( $title_key ) . '" value="' . esc_attr( $title ) . '"></p>';

			echo '<p><strong>' . esc_html__( 'PDF file', 'corten-steel' ) . '</strong></p>';
			echo '<input type="hidden" id="' . esc_attr( $file_key ) . '" name="' . esc_attr( $file_key ) . '" value="' . esc_attr( (string) $file_id ) . '">';
			echo '<p id="' . esc_attr( $file_key ) . '-preview" style="min-height:1.5rem;">';
			if ( $file_url ) {
				echo '<a href="' . esc_url( $file_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $file_name ? $file_name : __( 'View PDF', 'corten-steel' ) ) . '</a>';
			} else {
				echo '<span style="color:#646970;">' . esc_html__( 'No PDF selected', 'corten-steel' ) . '</span>';
			}
			echo '</p>';
			echo '<p><button type="button" class="button" data-corten-file="' . esc_attr( $file_key ) . '" data-corten-title="' . esc_attr( sprintf( /* translators: %d: card number */ __( 'Select PDF for card %d', 'corten-steel' ), $i ) ) . '">' . esc_html__( 'Select PDF', 'corten-steel' ) . '</button> ';
			echo '<button type="button" class="button" data-corten-file-clear="' . esc_attr( $file_key ) . '">' . esc_html__( 'Remove PDF', 'corten-steel' ) . '</button></p>';

			echo '<p><label for="' . esc_attr( $url_key ) . '">' . esc_html__( 'External PDF URL (optional)', 'corten-steel' ) . '</label><br>';
			echo '<input class="widefat" type="url" id="' . esc_attr( $url_key ) . '" name="' . esc_attr( $url_key ) . '" value="' . esc_attr( $url ) . '" placeholder="https://example.com/files/spec.pdf"></p>';
			echo '</div>';
		}
	}
}

/**
 * Persist image IDs and resource card fields.
 *
 * @param int $post_id Page ID.
 */
function corten_save_page_meta( $post_id ) {
	if ( ! isset( $_POST['corten_page_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['corten_page_meta_nonce'] ) ), 'corten_save_page_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	$template = corten_editor_page_template( $post_id );
	$groups   = corten_page_image_fields();
	$keys     = isset( $groups[ $template ] ) ? array_keys( $groups[ $template ] ) : array();

	foreach ( $keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
		}
	}

	if ( 'page-about.php' === $template ) {
		corten_save_meta_text_fields( $post_id, corten_about_copy_fields() );
		corten_save_meta_text_fields( $post_id, corten_about_journey_fields() );
		corten_save_meta_text_fields( $post_id, corten_about_cert_title_fields() );
	}

	if ( 'page-contact.php' === $template ) {
		corten_save_meta_text_fields( $post_id, corten_contact_copy_fields() );
	}

	if ( 'page-resources.php' === $template ) {
		for ( $i = 1; $i <= 3; $i++ ) {
			$title_key = 'corten_res_title_' . $i;
			$file_key  = 'corten_res_file_' . $i;
			$url_key   = 'corten_res_url_' . $i;

			$title = isset( $_POST[ $title_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $title_key ] ) ) : '';
			update_post_meta( $post_id, $title_key, $title );

			$file_id = isset( $_POST[ $file_key ] ) ? absint( $_POST[ $file_key ] ) : 0;
			update_post_meta( $post_id, $file_key, $file_id );

			$url = isset( $_POST[ $url_key ] ) ? esc_url_raw( wp_unslash( $_POST[ $url_key ] ) ) : '';
			if ( $url && ( home_url( '/contact/' ) === trailingslashit( $url ) || home_url( '/contact' ) === untrailingslashit( $url ) ) ) {
				$url = '';
			}
			update_post_meta( $post_id, $url_key, $url );
		}
	}
}
add_action( 'save_post_page', 'corten_save_page_meta' );

/**
 * Load the media modal on page editors.
 *
 * @param string $hook Current admin screen.
 */
function corten_enqueue_page_metabox_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'corten-admin-media',
		CORTEN_URI . '/assets/js/admin-media.js',
		array(),
		CORTEN_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'corten_enqueue_page_metabox_assets' );
