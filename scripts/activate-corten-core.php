<?php
/**
 * Activate corten-core and confirm sample posts.
 *
 * @package Corten_Core
 */

$_SERVER['HTTP_HOST']       = getenv( 'HTTP_HOST' ) ?: 'example.com';
$_SERVER['REQUEST_URI']     = '/';
$_SERVER['HTTPS']           = 'on';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin = 'corten-core/corten-core.php';
if ( ! is_plugin_active( $plugin ) ) {
	$result = activate_plugin( $plugin );
	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, $result->get_error_message() . "\n" );
		exit( 1 );
	}
	echo "activated\n";
} else {
	echo "already-active\n";
	corten_core_seed_sample_data( false );
}

flush_rewrite_rules();

$products = wp_count_posts( 'product' );
$projects = wp_count_posts( 'project' );
echo 'products=' . (int) $products->publish . "\n";
echo 'projects=' . (int) $projects->publish . "\n";
echo 'theme=' . get_option( 'stylesheet' ) . "\n";
