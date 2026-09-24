<?php
/**
 * Template Name: Capabilities
 * Plant process page with photograph slots for hero, four bays, and shop floor.
 *
 * @package Corten_Steel
 */

get_header();

while ( have_posts() ) :
	the_post();
	$hero = corten_meta_image_url( get_the_ID(), 'corten_cap_hero', 'hero-scene.svg' );
	$plant = corten_meta_image_url( get_the_ID(), 'corten_cap_plant', 'plant-floor.svg' );
	$bays  = array(
		array(
			'key'   => 'corten_cap_cutting',
			'file'  => 'process-cutting.svg',
			'title' => __( 'Cutting', 'corten-steel' ),
			'text'  => __( 'Fiber laser up to 8 mm. Typical cutting tolerance ±0.1 mm on Corten A/B and mild steel. Nesting for planter skins, enclosure doors, and OEM blanks.', 'corten-steel' ),
		),
		array(
			'key'   => 'corten_cap_forming',
			'file'  => 'process-forming.svg',
			'title' => __( 'Forming', 'corten-steel' ),
			'text'  => __( 'Press-brake programs for square planters, mitred boxes, and enclosure returns. Inside radii documented on the spec sheet you approve.', 'corten-steel' ),
		),
		array(
			'key'   => 'corten_cap_joinery',
			'file'  => 'process-joinery.svg',
			'title' => __( 'Joinery', 'corten-steel' ),
			'text'  => __( 'Continuous and stitch welds, ground or left as a visual bead. Leak-tested planter seams. Enclosure frames jigged for repeat OEM lots.', 'corten-steel' ),
		),
		array(
			'key'   => 'corten_cap_finish',
			'file'  => 'process-finish.svg',
			'title' => __( 'Finish & pack', 'corten-steel' ),
			'text'  => __( 'Mill scale, pre-oxidized rust, powder coat, or raw oil. Export crates with corner protection, labels, and packing lists for garden retail or plant rooms.', 'corten-steel' ),
		),
	);
	?>
<main id="content" class="site-main">
	<section class="page-hero page-hero--split">
		<div class="page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( $hero ); ?>" alt="" width="1600" height="900">
			<span class="page-hero__wash"></span>
		</div>
		<div class="wrap page-hero__copy reveal">
			<p class="eyebrow"><?php esc_html_e( 'Plant & process', 'corten-steel' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<div class="page-hero__lede entry-content">
				<?php
				if ( get_the_content() ) {
					the_content();
				} else {
					echo '<p>' . esc_html__( 'Laser cutting, CNC bending, MIG/TIG welding, and finishing for weathering steel, mild steel, aluminum, and galvanized sheet. Drawings in, crates out — EU and US programs.', 'corten-steel' ) . '</p>';
				}
				?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/capabilities' ); ?>

	<section class="section process-stage">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Bays', 'corten-steel' ); ?></p>
				<h2><?php esc_html_e( 'Four photograph slots on the shop floor', 'corten-steel' ); ?></h2>
			</header>
			<div class="process-grid process-grid--photos">
				<?php foreach ( $bays as $index => $bay ) : ?>
					<article class="process-card oxidized-frame reveal">
						<figure class="process-card__media">
							<img src="<?php echo esc_url( corten_meta_image_url( get_the_ID(), $bay['key'], $bay['file'] ) ); ?>" alt="" width="900" height="640">
							<span class="process-card__index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						</figure>
						<div class="process-card__body">
							<h2><?php echo esc_html( $bay['title'] ); ?></h2>
							<p><?php echo esc_html( $bay['text'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section plant-band">
		<div class="wrap">
			<header class="section-head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Plant', 'corten-steel' ); ?></p>
				<h2><?php esc_html_e( 'Shop floor', 'corten-steel' ); ?></h2>
			</header>
			<figure class="plant-band__figure oxidized-frame reveal">
				<img src="<?php echo esc_url( $plant ); ?>" alt="<?php esc_attr_e( 'Shop floor', 'corten-steel' ); ?>" width="1600" height="900">
			</figure>
		</div>
	</section>

	<?php get_template_part( 'template-parts/stats' ); ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
</main>
	<?php
endwhile;

get_footer();
