<?php
/**
 * Front page: hero, capabilities, catalog, weathering education, stats, cases, CTA.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main">
	<?php get_template_part( 'template-parts/hero' ); ?>
	<?php get_template_part( 'template-parts/capabilities' ); ?>
	<?php get_template_part( 'template-parts/product-grid' ); ?>

	<section class="section why-steel" aria-labelledby="why-steel-title">
		<div class="wrap why-steel__grid">
			<div class="why-steel__visual reveal">
				<img src="<?php echo esc_url( corten_get_background_url( 'corten_why_steel_image', 'why-weathering.svg' ) ); ?>" alt="<?php esc_attr_e( 'Weathering steel', 'corten-steel' ); ?>" width="720" height="900">
			</div>
			<div class="why-steel__copy reveal">
			<p class="eyebrow"><?php esc_html_e( 'Material science', 'corten-steel' ); ?></p>
			<h2 id="why-steel-title"><?php esc_html_e( 'Why weathering steel', 'corten-steel' ); ?></h2>
			<p><?php
			printf(
				/* translators: 1: products URL, 2: capabilities URL */
				esc_html__( 'Corten (weathering) steel is alloyed with copper, chromium, nickel, and phosphorus. After wet-dry cycles the surface oxidizes, then densifies into a tightly adherent patina that slows further corrosion. The finish is the architecture: ochre to deep rust, no paint film to peel in rain, salt, or UV. See our %1$s and %2$s for real-world applications.', 'corten-steel' ),
				'<a href="' . esc_url( home_url( '/products/' ) ) . '">' . esc_html__( 'corten steel products', 'corten-steel' ) . '</a>',
				'<a href="' . esc_url( home_url( '/capabilities/' ) ) . '">' . esc_html__( 'fabrication capabilities', 'corten-steel' ) . '</a>'
			);
			?></p>
			<p><?php
			printf(
				/* translators: 1: projects URL, 2: about URL */
				esc_html__( 'We fabricate for that living surface — folded corners, drained bases, and weld maps that weather evenly. Planters, screens, and enclosures arrive mill-scale or pre-oxidized, then settle into the landscape they were specified for. Explore our %1$s or learn %2$s.', 'corten-steel' ),
				'<a href="' . esc_url( home_url( '/projects/' ) ) . '">' . esc_html__( 'completed projects', 'corten-steel' ) . '</a>',
				'<a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'about our workshop', 'corten-steel' ) . '</a>'
			);
			?></p>
			<ul class="spec-list">
				<li><?php esc_html_e( 'Stable patina after seasonal cycling', 'corten-steel' ); ?></li>
				<li><?php esc_html_e( 'Low-maintenance versus painted mild steel', 'corten-steel' ); ?></li>
				<li><?php esc_html_e( 'Specified by landscape and façade teams across EU/US', 'corten-steel' ); ?></li>
			</ul>
			<p><a class="text-link" href="<?php echo esc_url( corten_quote_url() ); ?>"><?php esc_html_e( 'Request a quote for your corten steel project →', 'corten-steel' ); ?></a></p>
		</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/stats' ); ?>
	<?php get_template_part( 'template-parts/case-studies' ); ?>
	<?php get_template_part( 'template-parts/cta' ); ?>
</main>
<?php
get_footer();
