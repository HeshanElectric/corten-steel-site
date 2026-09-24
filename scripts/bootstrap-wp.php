<?php
/**
 * Local WordPress bootstrap: install if needed, activate Oxiron, seed pages.
 * Run: docker compose exec wordpress php /var/www/html/wp-content/themes/corten-steel/../../../.. 
 * Actual invocation from compose:
 *   docker compose exec -e HTTP_HOST=example.com wordpress php /tmp/bootstrap-wp.php
 *
 * @package Corten_Steel
 */

$_SERVER['HTTP_HOST']       = getenv( 'HTTP_HOST' ) ?: 'example.com';
$_SERVER['REQUEST_URI']     = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTPS']           = 'on';

define( 'WP_INSTALLING', true );

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_blog_installed() ) {
	$password = getenv( 'WP_ADMIN_PASSWORD' ) ?: wp_generate_password( 20, true, true );
	$result   = wp_install(
		'Oxiron Fabrication',
		'oxiron_admin',
		'admin@example.com',
		true,
		'',
		$password,
		'en_US'
	);
	echo "installed user=oxiron_admin password={$password}\n";
	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, $result->get_error_message() . "\n" );
		exit( 1 );
	}
} else {
	echo "already-installed\n";
}

switch_theme( 'corten-steel' );
echo 'theme=' . get_option( 'stylesheet' ) . "\n";

$pages = array(
	array(
		'title'    => 'Capabilities',
		'slug'     => 'capabilities',
		'template' => 'page-capabilities.php',
		'content'  => 'Laser cutting, forming, welding, and finishing for weathering steel programs.',
	),
	array(
		'title'    => 'Contact',
		'slug'     => 'contact',
		'template' => 'page-contact.php',
		'content'  => 'Request a quote for planters, enclosures, or custom fabrication.',
	),
	array(
		'title'    => 'About',
		'slug'     => 'about',
		'template' => 'page-about.php',
		'content'  => 'Oxiron fabricates weathering-steel planters, enclosures, and OEM sheet metal for EU and US specifiers.',
	),
	array(
		'title'    => 'Resources',
		'slug'     => 'resources',
		'template' => 'page-resources.php',
		'content'  => 'Spec sheets, packing notes, and finishing guides will live here.',
	),
	array(
		'title'    => 'Projects',
		'slug'     => 'projects',
		'content'  => 'Selected landscape, architecture, and OEM programs.',
	),
);

foreach ( $pages as $page ) {
	$existing = get_page_by_path( $page['slug'] );
	if ( $existing ) {
		continue;
	}
	$id = wp_insert_post(
		array(
			'post_title'   => $page['title'],
			'post_name'    => $page['slug'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => $page['content'],
		)
	);
	if ( ! is_wp_error( $id ) && ! empty( $page['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $page['template'] );
	}
}

$menu_name = 'Primary';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
	$items   = array( 'home', 'products', 'capabilities', 'projects', 'about', 'resources', 'contact' );
	$order   = 1;
	foreach ( $items as $slug ) {
		if ( 'home' === $slug ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => 'Home',
					'menu-item-url'      => home_url( '/' ),
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
					'menu-item-position' => $order,
				)
			);
		} elseif ( 'products' === $slug || 'projects' === $slug ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => ucfirst( $slug ),
					'menu-item-url'      => home_url( '/' . $slug . '/' ),
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
					'menu-item-position' => $order,
				)
			);
		} else {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $page->post_title,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page->ID,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order,
					)
				);
			}
		}
		++$order;
	}
	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = (int) $menu_id;
	$locations['footer']  = (int) $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules();
echo "bootstrap-complete\n";
