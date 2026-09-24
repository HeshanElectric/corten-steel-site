<?php
/**
 * Ensure About / Resources / Capabilities pages, primary nav items, and
 * a wp-admin landing that jumps straight to those editors.
 *
 * @package Corten_Steel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default copy for editorial pages when they are first created.
 *
 * @return array<string, array<string, string>>
 */
function corten_editorial_page_defs() {
	return array(
		'about'        => array(
			'title'    => __( 'About', 'corten-steel' ),
			'template' => 'page-about.php',
			'content'  => '<p>Oxiron Fabrication is a weathering-steel and sheet-metal plant serving landscape architects, architecture studios, garden retailers, and industrial OEMs in Europe and North America.</p><p>We fold, weld, and crate Corten planters, equipment enclosures, and drawing-controlled custom parts. The rust is the specification — not a coating to hide.</p>',
		),
		'resources'    => array(
			'title'    => __( 'Resources', 'corten-steel' ),
			'template' => 'page-resources.php',
			'content'  => '<p>Spec sheets, CAD outlines, and weathering notes for landscape and OEM teams. Replace the cards below with your own PDFs from this page in wp-admin.</p>',
		),
		'capabilities' => array(
			'title'    => __( 'Capabilities', 'corten-steel' ),
			'template' => 'page-capabilities.php',
			'content'  => '<p>Laser cutting, CNC bending, MIG/TIG welding, and finishing for weathering steel, mild steel, aluminum, and galvanized sheet.</p>',
		),
	);
}

/**
 * Create missing editorial pages and pin their templates.
 */
function corten_ensure_editorial_pages() {
	foreach ( corten_editorial_page_defs() as $slug => $def ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $def['title'],
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => $def['content'],
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_wp_page_template', $def['template'] );
			}
			continue;
		}

		$current = get_page_template_slug( $page->ID );
		if ( $current !== $def['template'] ) {
			update_post_meta( $page->ID, '_wp_page_template', $def['template'] );
		}

		if ( 'trash' === $page->post_status ) {
			wp_untrash_post( $page->ID );
			wp_publish_post( $page->ID );
		}
	}
}
add_action( 'init', 'corten_ensure_editorial_pages', 20 );

/**
 * Normalize a URL path for menu comparisons.
 *
 * @param string $url Absolute or relative URL.
 * @return string
 */
function corten_nav_path( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path = untrailingslashit( strtolower( (string) $path ) );
	return $path ? $path : '/';
}

/**
 * Insert Home and Projects into the assigned Primary menu when they are missing.
 */
function corten_sync_primary_nav() {
	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) ) {
		return;
	}

	$menu_id = (int) $locations['primary'];
	$items   = wp_get_nav_menu_items( $menu_id );
	if ( false === $items ) {
		return;
	}

	$home_path     = corten_nav_path( home_url( '/' ) );
	$projects_path = corten_nav_path( home_url( '/projects/' ) );
	$has_home      = false;
	$has_projects  = false;

	foreach ( (array) $items as $item ) {
		$path = corten_nav_path( $item->url );
		if ( '/' === $path || $path === $home_path ) {
			$has_home = true;
		}
		if ( $path === $projects_path || false !== strpos( $path, '/projects' ) ) {
			$has_projects = true;
			if ( 'page' === $item->object ) {
				$page = get_post( (int) $item->object_id );
				if ( ! $page || 'trash' === $page->post_status || 'publish' !== $page->post_status ) {
					wp_delete_post( (int) $item->ID, true );
					$has_projects = false;
				}
			}
		}
	}

	if ( ! $has_home ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'    => __( 'Home', 'corten-steel' ),
				'menu-item-url'      => home_url( '/' ),
				'menu-item-status'   => 'publish',
				'menu-item-type'     => 'custom',
				'menu-item-position' => 1,
			)
		);
	}

	if ( ! $has_projects ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'    => __( 'Projects', 'corten-steel' ),
				'menu-item-url'      => home_url( '/projects/' ),
				'menu-item-status'   => 'publish',
				'menu-item-type'     => 'custom',
				'menu-item-position' => 4,
			)
		);
	}
}
add_action( 'init', 'corten_sync_primary_nav', 30 );

/**
 * Guarantee Home and Projects appear even if the assigned menu is stale.
 *
 * @param string   $items Menu HTML.
 * @param stdClass $args  wp_nav_menu args.
 * @return string
 */
function corten_primary_menu_items_html( $items, $args ) {
	if ( ! is_object( $args ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	if ( false === stripos( $items, 'menu-item-home' ) && ! preg_match( '/>(Home)</i', $items ) ) {
		$items = '<li class="menu-item menu-item-home"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'corten-steel' ) . '</a></li>' . $items;
	}

	if ( false === stripos( $items, '/projects' ) ) {
		$items .= '<li class="menu-item menu-item-projects"><a href="' . esc_url( home_url( '/projects/' ) ) . '">' . esc_html__( 'Projects', 'corten-steel' ) . '</a></li>';
	}

	return $items;
}
add_filter( 'wp_nav_menu_items', 'corten_primary_menu_items_html', 10, 2 );

/**
 * Keep Home as the first primary item.
 *
 * @param array    $items Menu items.
 * @param stdClass $args  wp_nav_menu args.
 * @return array
 */
function corten_sort_primary_nav( $items, $args ) {
	if ( ! is_object( $args ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$home = array();
	$rest = array();
	foreach ( $items as $item ) {
		$path    = corten_nav_path( $item->url );
		$classes = isset( $item->classes ) ? (array) $item->classes : array();
		if ( '/' === $path || in_array( 'menu-item-home', $classes, true ) ) {
			$home[] = $item;
		} else {
			$rest[] = $item;
		}
	}

	return array_merge( $home, $rest );
}
add_filter( 'wp_nav_menu_objects', 'corten_sort_primary_nav', 20, 2 );

/**
 * wp-admin menu that surfaces About, Resources, and Capabilities editors.
 */
function corten_register_editorial_admin_menu() {
	add_menu_page(
		__( 'Oxiron Pages', 'corten-steel' ),
		__( 'Oxiron Pages', 'corten-steel' ),
		'edit_pages',
		'corten-pages',
		'corten_render_editorial_admin',
		'dashicons-art',
		26
	);
}
add_action( 'admin_menu', 'corten_register_editorial_admin_menu' );

/**
 * Admin landing: one-click edit links for About, Resources, Capabilities.
 */
function corten_render_editorial_admin() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	corten_ensure_editorial_pages();

	$cards = array(
		'about'        => __( 'Hero, Why we exist, Our Journey, Quality Certificate scans, and workshop photographs.', 'corten-steel' ),
		'resources'    => __( 'Spec sheets and downloads. Upload a PDF for each card so visitors can download it.', 'corten-steel' ),
		'capabilities' => __( 'Plant process page. Upload a hero, four process photos, and a shop-floor image.', 'corten-steel' ),
		'contact'      => __( 'RFQ form page. Edit Direct Contacts and Working Hours tables in the Contact directory box.', 'corten-steel' ),
	);

	echo '<div class="wrap"><h1>' . esc_html__( 'Oxiron Pages', 'corten-steel' ) . '</h1>';
	echo '<p>' . esc_html__( 'Edit the marketing pages that the theme templates read. Images live in the Oxiron Images panel on each editor.', 'corten-steel' ) . '</p>';
	echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(16rem,1fr));gap:1rem;margin-top:1.5rem;">';

	foreach ( $cards as $slug => $hint ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}
		$edit = get_edit_post_link( $page->ID, 'raw' );
		$view = get_permalink( $page->ID );
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:1.25rem 1.4rem;border-radius:4px;">';
		echo '<h2 style="margin-top:0;">' . esc_html( $page->post_title ) . '</h2>';
		echo '<p>' . esc_html( $hint ) . '</p>';
		echo '<p><a class="button button-primary" href="' . esc_url( $edit ) . '">' . esc_html__( 'Edit in wp-admin', 'corten-steel' ) . '</a> ';
		echo '<a class="button" href="' . esc_url( $view ) . '">' . esc_html__( 'View', 'corten-steel' ) . '</a></p>';
		echo '</div>';
	}

	echo '</div>';
	echo '<p style="margin-top:2rem;"><a class="button" href="' . esc_url( admin_url( 'customize.php?autofocus[section]=corten_social' ) ) . '">' . esc_html__( 'Edit social links', 'corten-steel' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'customize.php?autofocus[control]=corten_why_steel_image' ) ) . '">' . esc_html__( 'Edit Material science image', 'corten-steel' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'customize.php?autofocus[section]=corten_backgrounds' ) ) . '">' . esc_html__( 'Edit background images', 'corten-steel' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Edit primary menu', 'corten-steel' ) . '</a></p>';
	echo '</div>';
}

/**
 * Attachment URL from a stored ID, with a theme SVG fallback.
 *
 * @param int    $post_id  Page ID.
 * @param string $meta_key Meta key storing an attachment ID.
 * @param string $fallback Filename under assets/img.
 * @return string
 */
function corten_meta_image_url( $post_id, $meta_key, $fallback = '' ) {
	$id  = (int) get_post_meta( $post_id, $meta_key, true );
	$url = $id ? wp_get_attachment_image_url( $id, 'corten-gallery' ) : '';
	if ( $url ) {
		return $url;
	}
	if ( $fallback ) {
		return CORTEN_URI . '/assets/img/' . ltrim( $fallback, '/' );
	}
	return '';
}

/**
 * Plain or HTML text from post meta, with a theme fallback.
 *
 * @param int    $post_id  Page ID.
 * @param string $meta_key Meta key.
 * @param string $fallback Default copy when the field is empty.
 * @return string
 */
function corten_meta_text( $post_id, $meta_key, $fallback = '' ) {
	$value = get_post_meta( $post_id, $meta_key, true );
	if ( is_string( $value ) && '' !== trim( wp_strip_all_tags( $value ) ) ) {
		return $value;
	}
	return $fallback;
}

/**
 * Social profiles shown in the footer. Empty Customizer URLs hide a network.
 *
 * @return array<int, array<string, string>>
 */
function corten_get_social_profiles() {
	$defaults = array(
		'facebook' => array(
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/',
			'path'  => 'M15 8h-3V6c0-.6.4-1 1-1h2V3h-3a3 3 0 0 0-3 3v2H7v2h2v7h3v-7h2.2L15 8z',
		),
		'linkedin' => array(
			'label' => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/',
			'path'  => 'M6.5 9H4V20h2.5V9zM5.3 4C4.3 4 3.5 4.8 3.5 5.8S4.3 7.5 5.3 7.5 7 6.7 7 5.8 6.2 4 5.3 4zM20 20h-2.5v-5.6c0-1.8-.6-3-2.2-3-1.2 0-1.9.8-2.2 1.6-.1.3-.1.6-.1.9V20H10.5s.03-9.3 0-10.3H13v1.5c.3-.5 1.7-1.8 3.8-1.8 2.7 0 4.7 1.8 4.7 5.6V20z',
		),
		'youtube'  => array(
			'label' => 'YouTube',
			'url'   => 'https://www.youtube.com/',
			'path'  => 'M21.6 7.2a2.7 2.7 0 0 0-1.9-1.9C18 5 12 5 12 5s-6 0-7.7.3A2.7 2.7 0 0 0 2.4 7.2 28 28 0 0 0 2 12a28 28 0 0 0 .4 4.8 2.7 2.7 0 0 0 1.9 1.9C6 19 12 19 12 19s6 0 7.7-.3a2.7 2.7 0 0 0 1.9-1.9A28 28 0 0 0 22 12a28 28 0 0 0-.4-4.8zM10 15.5v-7l6 3.5-6 3.5z',
		),
	);

	$profiles = array();
	foreach ( $defaults as $key => $item ) {
		$url = get_theme_mod( 'corten_social_' . $key, $item['url'] );
		if ( ! $url ) {
			continue;
		}
		$profiles[] = array(
			'key'   => $key,
			'label' => $item['label'],
			'url'   => $url,
			'path'  => $item['path'],
		);
	}

	return $profiles;
}
