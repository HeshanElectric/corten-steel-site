<?php
/**
 * Template Name: About
 * Studio / plant story with editable Why we exist copy and workshop photographs.
 *
 * @package Corten_Steel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$page_id     = get_the_ID();
	$hero        = corten_meta_image_url( $page_id, 'corten_about_hero', 'hero-scene.svg' );
	$story       = corten_meta_image_url( $page_id, 'corten_about_story', 'about-atelier.svg' );
	$workshop    = corten_meta_image_url( $page_id, 'corten_about_workshop', 'plant-floor.svg' );
	$atelier     = corten_meta_image_url( $page_id, 'corten_about_atelier', 'why-weathering.svg' );
	$why_eyebrow = corten_meta_text( $page_id, 'corten_about_why_eyebrow', __( 'Why we exist', 'corten-steel' ) );
	$why_title   = corten_meta_text( $page_id, 'corten_about_why_title', __( 'A fabrication desk for people who specify rust', 'corten-steel' ) );
	$why_text    = corten_meta_text(
		$page_id,
		'corten_about_why_text',
		__( 'Landscape architects and OEM buyers do not want a painted box that peels. They want a living surface that settles into the site — folded corners, drained bases, and weld maps that weather evenly. Oxiron is the plant that treats that surface as architecture.', 'corten-steel' )
	);
	$shop_extras = array(
		array( 'key' => 'corten_about_shop_1', 'file' => 'process-cutting.svg' ),
		array( 'key' => 'corten_about_shop_2', 'file' => 'process-forming.svg' ),
		array( 'key' => 'corten_about_shop_3', 'file' => 'process-joinery.svg' ),
		array( 'key' => 'corten_about_shop_4', 'file' => 'process-finish.svg' ),
	);
	?>
<main id="content" class="site-main">
	<section class="page-hero page-hero--split">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( $hero ); ?>" alt="" width="1600" height="900">
			<span class="page-hero__wash"></span>
		</div>
		<div class="wrap page-hero__copy reveal">
			<p class="eyebrow"><?php esc_html_e( 'Plant', 'corten-steel' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<div class="page-hero__lede entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<section class="section editorial-split">
		<div class="wrap editorial-split__grid">
			<figure class="editorial-split__figure reveal oxidized-frame">
				<img src="<?php echo esc_url( $story ); ?>" alt="<?php esc_attr_e( 'Studio portrait', 'corten-steel' ); ?>" width="720" height="900">
			</figure>
			<div class="editorial-split__copy reveal">
				<p class="eyebrow"><?php echo esc_html( $why_eyebrow ); ?></p>
				<h2><?php echo esc_html( $why_title ); ?></h2>
				<div class="editorial-split__body">
					<?php echo wp_kses_post( wpautop( $why_text ) ); ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section gallery-band">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Shop', 'corten-steel' ); ?></p>
				<h2><?php esc_html_e( 'Workshop and atelier', 'corten-steel' ); ?></h2>
			</header>
			<div class="gallery-band__grid">
				<figure class="gallery-band__wide oxidized-frame reveal">
					<img src="<?php echo esc_url( $workshop ); ?>" alt="<?php esc_attr_e( 'Workshop floor', 'corten-steel' ); ?>" width="1400" height="800">
				</figure>
				<figure class="gallery-band__tall oxidized-frame reveal">
					<img src="<?php echo esc_url( $atelier ); ?>" alt="<?php esc_attr_e( 'Atelier detail', 'corten-steel' ); ?>" width="720" height="900">
				</figure>
			</div>
			<div class="gallery-band__quad">
				<?php foreach ( $shop_extras as $index => $slot ) : ?>
					<figure class="gallery-band__tile oxidized-frame reveal">
						<img src="<?php echo esc_url( corten_meta_image_url( $page_id, $slot['key'], $slot['file'] ) ); ?>" alt="<?php echo esc_attr( sprintf( /* translators: %d: photo number */ __( 'Workshop and atelier photograph %d', 'corten-steel' ), $index + 1 ) ); ?>" width="720" height="540">
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	$journey = corten_get_about_journey( $page_id );
	if ( $journey ) :
		?>
	<section class="section journey" aria-labelledby="journey-title">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'History', 'corten-steel' ); ?></p>
				<h2 id="journey-title"><?php esc_html_e( 'Our Journey', 'corten-steel' ); ?></h2>
			</header>
			<ol class="journey-timeline">
				<?php foreach ( $journey as $step ) : ?>
					<li class="journey-item reveal">
						<p class="journey-item__year"><?php echo esc_html( $step['year'] ); ?></p>
						<span class="journey-item__dot" aria-hidden="true"></span>
						<div class="journey-item__copy">
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php endif; ?>

	<section class="section cert-band" aria-labelledby="cert-title">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Assurance', 'corten-steel' ); ?></p>
				<h2 id="cert-title"><?php esc_html_e( 'Quality Certificate', 'corten-steel' ); ?></h2>
			</header>
			<div class="cert-grid">
				<?php foreach ( corten_get_about_certs( $page_id ) as $cert ) : ?>
					<figure class="cert-card oxidized-frame reveal">
						<img src="<?php echo esc_url( $cert['image'] ); ?>" alt="<?php echo esc_attr( $cert['title'] ); ?>" width="480" height="640">
						<?php if ( $cert['title'] ) : ?>
							<figcaption><?php echo esc_html( $cert['title'] ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/cta' ); ?>
</main>
	<?php
endwhile;

get_footer();
