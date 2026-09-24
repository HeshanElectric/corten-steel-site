<?php
/**
 * Plugin Name: Oxiron Corten Core
 * Plugin URI: https://example.com
 * Description: Product and project CPTs, catalog taxonomies, ACF product details, shortcodes, REST/AJAX filters, and sample data for the Oxiron weathering-steel manufacturer site.
 * Version: 1.1.0
 * Requires at least: 6.8
 * Requires PHP: 8.3
 * Author: Oxiron Fabrication
 * Author URI: https://example.com
 * Text Domain: corten-core
 * License: GPL-2.0-or-later
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CORTEN_CORE_VERSION', '1.1.0' );
define( 'CORTEN_CORE_FILE', __FILE__ );
define( 'CORTEN_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CORTEN_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once CORTEN_CORE_DIR . 'includes/post-types.php';
require_once CORTEN_CORE_DIR . 'includes/taxonomies.php';
require_once CORTEN_CORE_DIR . 'includes/acf-fields.php';
require_once CORTEN_CORE_DIR . 'includes/product-metaboxes.php';
require_once CORTEN_CORE_DIR . 'includes/shortcodes.php';
require_once CORTEN_CORE_DIR . 'includes/rest.php';
require_once CORTEN_CORE_DIR . 'includes/sample-data.php';
require_once CORTEN_CORE_DIR . 'includes/cli.php';

/**
 * Load translations.
 */
function corten_core_load_textdomain() {
	load_plugin_textdomain( 'corten-core', false, dirname( plugin_basename( CORTEN_CORE_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'corten_core_load_textdomain' );

/**
 * Register types then flush permalinks on activation.
 */
function corten_core_activate() {
	corten_core_register_post_types();
	corten_core_register_taxonomies();
	flush_rewrite_rules();

	if ( ! get_option( 'corten_core_seeded' ) ) {
		corten_core_seed_sample_data( false );
		update_option( 'corten_core_seeded', '1' );
	}
}
register_activation_hook( CORTEN_CORE_FILE, 'corten_core_activate' );

/**
 * Flush permalinks on deactivation so leftover CPT rules do not 404.
 */
function corten_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( CORTEN_CORE_FILE, 'corten_core_deactivate' );

/**
 * Tools screen: re-import sample catalog data.
 */
function corten_core_admin_menu() {
	add_management_page(
		__( 'Oxiron Sample Data', 'corten-core' ),
		__( 'Oxiron Sample Data', 'corten-core' ),
		'manage_options',
		'corten-sample-data',
		'corten_core_render_sample_data_page'
	);
}
add_action( 'admin_menu', 'corten_core_admin_menu' );

/**
 * Handle sample-data form post.
 */
function corten_core_handle_sample_data_request() {
	if ( ! isset( $_POST['corten_core_seed'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import sample data.', 'corten-core' ) );
	}

	check_admin_referer( 'corten_core_seed' );
	$result = corten_core_seed_sample_data( true );
	update_option( 'corten_core_seeded', '1' );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'             => 'corten-sample-data',
				'corten-seeded'    => 1,
				'corten-products'  => (int) $result['products'],
				'corten-projects'  => (int) $result['projects'],
			),
			admin_url( 'tools.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'corten_core_handle_sample_data_request' );

/**
 * Render the Tools → Oxiron Sample Data page.
 */
function corten_core_render_sample_data_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Oxiron Sample Data', 'corten-core' ); ?></h1>
		<?php if ( isset( $_GET['corten-seeded'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: product count, 2: project count */
						esc_html__( 'Imported %1$d products and %2$d projects.', 'corten-core' ),
						isset( $_GET['corten-products'] ) ? (int) $_GET['corten-products'] : 0,
						isset( $_GET['corten-projects'] ) ? (int) $_GET['corten-projects'] : 0
					);
					?>
				</p>
			</div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Creates three catalog products and two project case studies. Existing sample slugs are updated. WP-CLI: wp corten seed --force', 'corten-core' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'corten_core_seed' ); ?>
			<?php submit_button( __( 'Import / refresh sample data', 'corten-core' ), 'primary', 'corten_core_seed' ); ?>
		</form>
	</div>
	<?php
}
