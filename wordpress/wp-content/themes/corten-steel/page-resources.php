<?php
/**
 * Template Name: Resources
 * Spec sheets and download cards. Cards download a PDF when one is attached.
 *
 * @package Corten_Steel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$page_id = get_the_ID();
	$hero    = corten_meta_image_url( $page_id, 'corten_res_hero', 'hero-scene.svg' );
	$defaults = array(
		__( 'Corten planter spec sheet', 'corten-steel' ),
		__( 'Enclosure CAD outline', 'corten-steel' ),
		__( 'Weathering & finish notes', 'corten-steel' ),
	);
	$cards = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$title = (string) get_post_meta( $page_id, 'corten_res_title_' . $i, true );
		$cards[] = array(
			'image' => corten_meta_image_url( $page_id, 'corten_res_image_' . $i, 'resource-sheet.svg' ),
			'title' => $title ? $title : $defaults[ $i - 1 ],
			'url'   => corten_get_resource_download_url( $page_id, $i ),
		);
	}
	?>
<main id="content" class="site-main">
	<section class="page-hero page-hero--split">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( $hero ); ?>" alt="" width="1600" height="900">
			<span class="page-hero__wash"></span>
		</div>
		<div class="wrap page-hero__copy reveal">
			<p class="eyebrow"><?php esc_html_e( 'Library', 'corten-steel' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<div class="page-hero__lede entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Downloads', 'corten-steel' ); ?></p>
				<h2><?php esc_html_e( 'Spec sheets for the desk, not the inbox dump', 'corten-steel' ); ?></h2>
			</header>
			<div class="resource-grid">
				<?php foreach ( $cards as $index => $card ) : ?>
					<article class="resource-card oxidized-frame reveal">
						<?php if ( $card['url'] ) : ?>
							<a class="resource-card__link" href="<?php echo esc_url( $card['url'] ); ?>" download target="_blank" rel="noopener noreferrer">
								<img src="<?php echo esc_url( $card['image'] ); ?>" alt="" width="720" height="480">
								<div class="resource-card__body">
									<p class="eyebrow"><?php echo esc_html( sprintf( /* translators: %d: card number */ __( 'PDF %d', 'corten-steel' ), $index + 1 ) ); ?></p>
									<h3><?php echo esc_html( $card['title'] ); ?></h3>
									<p><?php esc_html_e( 'Click to download the PDF.', 'corten-steel' ); ?></p>
									<span class="text-link"><?php esc_html_e( 'Download PDF', 'corten-steel' ); ?></span>
								</div>
							</a>
						<?php else : ?>
							<div class="resource-card__link resource-card__link--empty">
								<img src="<?php echo esc_url( $card['image'] ); ?>" alt="" width="720" height="480">
								<div class="resource-card__body">
									<p class="eyebrow"><?php echo esc_html( sprintf( /* translators: %d: card number */ __( 'PDF %d', 'corten-steel' ), $index + 1 ) ); ?></p>
									<h3><?php echo esc_html( $card['title'] ); ?></h3>
									<p><?php esc_html_e( 'Upload a PDF from Pages → Resources → Oxiron Images to enable download.', 'corten-steel' ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/cta' ); ?>
</main>
	<?php
endwhile;

get_footer();
