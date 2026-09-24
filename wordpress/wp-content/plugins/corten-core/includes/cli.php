<?php
/**
 * WP-CLI: wp corten seed [--force]
 *
 * @package Corten_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Oxiron sample-data commands.
 */
class Corten_Core_CLI {

	/**
	 * Import sample products and projects.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Recreate sample media even if attachments already exist.
	 *
	 * ## EXAMPLES
	 *
	 *     wp corten seed
	 *     wp corten seed --force
	 *
	 * @when after_wp_load
	 *
	 * @param array<int, string>       $args       Positional args.
	 * @param array<string, string>    $assoc_args Flags.
	 */
	public function seed( $args, $assoc_args ) {
		$force  = isset( $assoc_args['force'] );
		$result = corten_core_seed_sample_data( $force );
		update_option( 'corten_core_seeded', '1' );
		WP_CLI::success(
			sprintf(
				'Seeded %d products and %d projects.',
				(int) $result['products'],
				(int) $result['projects']
			)
		);
	}
}

WP_CLI::add_command( 'corten', 'Corten_Core_CLI' );
