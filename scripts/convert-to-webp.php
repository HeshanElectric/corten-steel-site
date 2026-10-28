<?php
/**
 * Convert corten-steel raster images to WebP and point WordPress at the new files.
 *
 * Run inside the wordpress container:
 *   php /tmp/convert-to-webp.php --sample
 *   php /tmp/convert-to-webp.php --apply
 *
 * SVG icons are left as vectors. JPEG and PNG files are encoded to WebP.
 * Original JPEG and PNG files are kept on disk.
 */

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "CLI only.\n" );
	exit( 1 );
}

$apply  = in_array( '--apply', $argv, true );
$sample = in_array( '--sample', $argv, true );

if ( ! $apply && ! $sample ) {
	fwrite( STDERR, "Usage: php convert-to-webp.php --sample|--apply\n" );
	exit( 1 );
}

if ( ! class_exists( 'Imagick' ) ) {
	fwrite( STDERR, "Imagick is not loaded.\n" );
	exit( 1 );
}

$webp_formats = Imagick::queryFormats( 'WEBP' );
if ( ! $webp_formats ) {
	fwrite( STDERR, "Imagick was built without WebP support.\n" );
	exit( 1 );
}

/**
 * Encode one JPEG/PNG to a sibling .webp file.
 *
 * @param string $path Absolute source path.
 * @return array{src:int,dst:int,webp:string}|null
 */
function corten_encode_webp( $path ) {
	$webp = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $path );
	if ( ! is_string( $webp ) || $webp === $path ) {
		return null;
	}

	$image = new Imagick();
	$image->readImage( $path );
	if ( $image->getImageColorspace() === Imagick::COLORSPACE_CMYK ) {
		$image->transformImageColorspace( Imagick::COLORSPACE_SRGB );
	}
	$image->stripImage();
	$image->setImageFormat( 'webp' );
	$image->setOption( 'webp:method', '6' );
	$image->setImageCompressionQuality( 82 );
	$image->writeImage( $webp );
	$image->clear();
	$image->destroy();

	$written = is_file( $webp ) ? (int) filesize( $webp ) : 0;
	if ( $written < 1 ) {
		throw new RuntimeException( "Empty WebP for {$path}" );
	}

	return array(
		'src'  => (int) filesize( $path ),
		'dst'  => $written,
		'webp' => $webp,
	);
}

if ( $sample ) {
	$src = '/var/www/html/wp-content/uploads/2026/09/hero-scene-factory.jpg';
	if ( ! is_file( $src ) ) {
		fwrite( STDERR, "Sample source missing: {$src}\n" );
		exit( 1 );
	}
	copy( $src, '/tmp/preview-before.jpg' );
	$image = new Imagick();
	$image->readImage( $src );
	if ( $image->getImageColorspace() === Imagick::COLORSPACE_CMYK ) {
		$image->transformImageColorspace( Imagick::COLORSPACE_SRGB );
	}
	$image->stripImage();
	$image->setImageFormat( 'webp' );
	$image->setOption( 'webp:method', '6' );
	$image->setImageCompressionQuality( 82 );
	$image->writeImage( '/tmp/preview-after.webp' );
	$image->clear();
	$image->destroy();
	$before = filesize( '/tmp/preview-before.jpg' );
	$after  = filesize( '/tmp/preview-after.webp' );
	$pct    = $before > 0 ? round( ( 1 - ( $after / $before ) ) * 100, 1 ) : 0;
	echo "sample_before={$before}\n";
	echo "sample_after={$after}\n";
	echo "sample_saved_pct={$pct}\n";
	exit( 0 );
}

set_time_limit( 0 );
ini_set( 'memory_limit', '512M' );

$roots = array(
	'/var/www/html/wp-content/uploads',
	'/var/www/html/wp-content/themes/corten-steel/assets/img',
);

$converted = 0;
$failed    = 0;
$src_bytes = 0;
$dst_bytes = 0;
$made      = array();

foreach ( $roots as $root ) {
	if ( ! is_dir( $root ) ) {
		continue;
	}
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $iterator as $file ) {
		if ( ! $file->isFile() ) {
			continue;
		}
		$path = $file->getPathname();
		if ( ! preg_match( '/\.(jpe?g|png)$/i', $path ) ) {
			continue;
		}
		try {
			$result = corten_encode_webp( $path );
		} catch ( Throwable $error ) {
			$failed++;
			fwrite( STDERR, "FAIL {$path}: {$error->getMessage()}\n" );
			continue;
		}
		if ( ! $result ) {
			continue;
		}
		$converted++;
		$src_bytes += $result['src'];
		$dst_bytes += $result['dst'];
		$made[]     = $path;
		if ( 0 === $converted % 25 ) {
			echo "converted={$converted}\n";
		}
	}
}

require '/var/www/html/wp-load.php';

/**
 * Replace JPEG/PNG wp-content URLs with .webp.
 *
 * @param mixed $value Stored content.
 * @return mixed
 */
function corten_rewrite_webp_urls( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = corten_rewrite_webp_urls( $item );
		}
		return $value;
	}
	if ( is_object( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value->$key = corten_rewrite_webp_urls( $item );
		}
		return $value;
	}
	if ( ! is_string( $value ) || ! preg_match( '/\.(jpe?g|png)/i', $value ) ) {
		return $value;
	}
	return preg_replace(
		'#((?:https?:)?//[^"\'\s<>]*/wp-content/[^"\'\s<>]+|/wp-content/[^"\'\s<>]+)\.(jpe?g|png)\b#i',
		'$1.webp',
		$value
	);
}

global $wpdb;

$attachments = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_mime_type' => array( 'image/jpeg', 'image/jpg', 'image/png' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$attachments_updated = 0;
foreach ( $attachments as $attachment_id ) {
	$file = get_attached_file( $attachment_id );
	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( ! is_string( $file ) || ! preg_match( '/\.(jpe?g|png)$/i', $file ) ) {
		continue;
	}
	$webp_file = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $file );
	if ( ! is_file( $webp_file ) ) {
		fwrite( STDERR, "SKIP attachment {$attachment_id}: missing {$webp_file}\n" );
		continue;
	}
	update_attached_file( $attachment_id, $webp_file );
	if ( is_array( $meta ) ) {
		foreach ( array( 'file', 'original_image' ) as $meta_key ) {
			if ( ! empty( $meta[ $meta_key ] ) && is_string( $meta[ $meta_key ] ) ) {
				$meta[ $meta_key ] = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $meta[ $meta_key ] );
			}
		}
		if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size_name => $size ) {
				if ( ! is_array( $size ) || empty( $size['file'] ) || ! is_string( $size['file'] ) ) {
					continue;
				}
				$meta['sizes'][ $size_name ]['file']      = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $size['file'] );
				$meta['sizes'][ $size_name ]['mime-type'] = 'image/webp';
			}
		}
		wp_update_attachment_metadata( $attachment_id, $meta );
	}
	wp_update_post(
		array(
			'ID'             => $attachment_id,
			'post_mime_type' => 'image/webp',
		)
	);
	$attachments_updated++;
}

$content_updated = 0;
$content_rows    = $wpdb->get_results( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%.jpg%' OR post_content LIKE '%.jpeg%' OR post_content LIKE '%.png%'" );
foreach ( $content_rows as $row ) {
	$rewritten = corten_rewrite_webp_urls( $row->post_content );
	if ( $rewritten !== $row->post_content ) {
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $rewritten ),
			array( 'ID' => (int) $row->ID ),
			array( '%s' ),
			array( '%d' )
		);
		$content_updated++;
	}
}

$guid_updated = 0;
$guid_rows    = $wpdb->get_results( "SELECT ID, guid FROM {$wpdb->posts} WHERE guid LIKE '%.jpg%' OR guid LIKE '%.jpeg%' OR guid LIKE '%.png%'" );
foreach ( $guid_rows as $row ) {
	$rewritten = corten_rewrite_webp_urls( $row->guid );
	if ( $rewritten !== $row->guid ) {
		$wpdb->update(
			$wpdb->posts,
			array( 'guid' => $rewritten ),
			array( 'ID' => (int) $row->ID ),
			array( '%s' ),
			array( '%d' )
		);
		$guid_updated++;
	}
}

$meta_updated = 0;
$meta_rows    = $wpdb->get_results( "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE '%.jpg%' OR meta_value LIKE '%.jpeg%' OR meta_value LIKE '%.png%'" );
foreach ( $meta_rows as $row ) {
	$current   = maybe_unserialize( $row->meta_value );
	$before    = maybe_serialize( $current );
	$rewritten = corten_rewrite_webp_urls( $current );
	if ( maybe_serialize( $rewritten ) !== $before ) {
		update_post_meta( (int) $row->post_id, $row->meta_key, $rewritten );
		$meta_updated++;
	}
}

$option_updated = 0;
$option_rows    = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_value LIKE '%.jpg%' OR option_value LIKE '%.jpeg%' OR option_value LIKE '%.png%'" );
foreach ( $option_rows as $row ) {
	$current   = maybe_unserialize( $row->option_value );
	$before    = maybe_serialize( $current );
	$rewritten = corten_rewrite_webp_urls( $current );
	if ( maybe_serialize( $rewritten ) !== $before ) {
		update_option( $row->option_name, $rewritten );
		$option_updated++;
	}
}

if ( function_exists( 'wp_cache_flush' ) ) {
	wp_cache_flush();
}
if ( function_exists( 'wp_cache_clear_cache' ) ) {
	wp_cache_clear_cache();
}

$saved = $src_bytes - $dst_bytes;
echo "converted={$converted}\n";
echo "failed={$failed}\n";
echo "src_bytes={$src_bytes}\n";
echo "dst_bytes={$dst_bytes}\n";
echo "saved_bytes={$saved}\n";
echo "attachments_updated={$attachments_updated}\n";
echo "content_updated={$content_updated}\n";
echo "guid_updated={$guid_updated}\n";
echo "meta_updated={$meta_updated}\n";
echo "option_updated={$option_updated}\n";
echo "originals_kept=" . count( $made ) . "\n";
