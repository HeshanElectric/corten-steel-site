<?php
/**
 * Core capability strip.
 *
 * @package Corten_Steel
 */

$items = array(
	array(
		'title' => __( 'Laser-cut precision', 'corten-steel' ),
		'text'  => __( 'Fiber laser nesting with ±0.1 mm typical cutting tolerance on planter skins and enclosure panels.', 'corten-steel' ),
		'icon'  => 'icon-laser.svg',
	),
	array(
		'title' => __( 'Weathering steel specialist', 'corten-steel' ),
		'text'  => __( 'Corten A/B programs, even rust maps, drained bases, and welds specified for outdoor cycling.', 'corten-steel' ),
		'icon'  => 'icon-patina.svg',
	),
	array(
		'title' => __( 'OEM / ODM', 'corten-steel' ),
		'text'  => __( 'Repeatable jigs, private-label packing, and drawing control for equipment and furniture brands.', 'corten-steel' ),
		'icon'  => 'icon-oem.svg',
	),
	array(
		'title' => __( 'Preferred logistics channels', 'corten-steel' ),
		'text'  => __( 'Curated ocean and air partners, booked lanes, and tracked handoff so crates reach EU and US sites on schedule.', 'corten-steel' ),
		'icon'  => 'icon-ship.svg',
	),
	array(
		'title' => __( 'Bend, weld, finish', 'corten-steel' ),
		'text'  => __( 'Press-brake, MIG/TIG, powder, pre-oxidized rust, or mill-scale — documented on the spec sheet.', 'corten-steel' ),
		'icon'  => 'icon-weld.svg',
	),
);
?>
<section class="section capabilities" aria-labelledby="capabilities-title">
	<div class="wrap">
		<header class="section-head reveal">
			<p class="eyebrow"><?php esc_html_e( 'Plant', 'corten-steel' ); ?></p>
			<h2 id="capabilities-title"><?php esc_html_e( 'Built for specification, not catalog filler', 'corten-steel' ); ?></h2>
			<p class="section-head__intro"><?php
			printf(
				/* translators: 1: capabilities URL, 2: contact URL */
				esc_html__( 'From laser cutting and press-brake bending to MIG/TIG welding and pre-oxidized rust finishes — explore our full %1$s or %2$s to discuss your project.', 'corten-steel' ),
				'<a href="' . esc_url( home_url( '/capabilities/' ) ) . '">' . esc_html__( 'sheet metal capabilities', 'corten-steel' ) . '</a>',
				'<a href="' . esc_url( corten_quote_url() ) . '">' . esc_html__( 'contact our team', 'corten-steel' ) . '</a>'
			);
			?></p>
		</header>
		<ul class="capability-row">
			<?php foreach ( $items as $item ) : ?>
				<li class="capability-card reveal">
					<img src="<?php echo esc_url( CORTEN_URI . '/assets/img/' . $item['icon'] ); ?>" alt="" width="48" height="48">
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<p><?php echo esc_html( $item['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
