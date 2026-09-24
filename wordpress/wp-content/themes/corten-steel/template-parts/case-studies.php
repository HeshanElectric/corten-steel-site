<?php
/**
 * Project case carousel. Uses live project posts and featured photos
 * when they exist; falls back to demo cards only if the CPT is empty.
 *
 * @package Corten_Steel
 */

$cases = corten_get_featured_projects( 6 );
?>
<section class="section cases" aria-labelledby="cases-title">
	<div class="wrap">
		<header class="section-head reveal">
			<p class="eyebrow"><?php esc_html_e( 'Selected work', 'corten-steel' ); ?></p>
			<h2 id="cases-title"><?php esc_html_e( 'Projects specified, not styled', 'corten-steel' ); ?></h2>
		</header>
		<div class="carousel" data-carousel>
			<button class="carousel__nav" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous project', 'corten-steel' ); ?>">‹</button>
			<div class="carousel__track" data-carousel-track>
				<?php foreach ( $cases as $case ) : ?>
					<article class="case-card">
						<a class="case-card__media" href="<?php echo esc_url( $case['permalink'] ); ?>">
							<img src="<?php echo esc_url( $case['image'] ); ?>" alt="<?php echo esc_attr( $case['title'] ); ?>" width="640" height="400">
						</a>
						<?php if ( ! empty( $case['client'] ) ) : ?>
							<p class="case-card__client"><?php echo esc_html( $case['client'] ); ?></p>
						<?php endif; ?>
						<h3><a href="<?php echo esc_url( $case['permalink'] ); ?>"><?php echo esc_html( $case['title'] ); ?></a></h3>
						<?php if ( ! empty( $case['quote'] ) ) : ?>
							<blockquote>
								<p><?php echo esc_html( $case['quote'] ); ?></p>
							</blockquote>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
			<button class="carousel__nav" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Next project', 'corten-steel' ); ?>">›</button>
		</div>
	</div>
</section>
