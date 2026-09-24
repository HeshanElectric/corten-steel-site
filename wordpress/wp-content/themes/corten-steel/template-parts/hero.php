<?php
/**
 * Full-viewport hero.
 *
 * @package Corten_Steel
 */

$hero_bg = corten_get_background_url( 'corten_hero_bg', 'hero-scene.jpg' );
?>
<section class="hero" aria-label="<?php esc_attr_e( 'Introduction', 'corten-steel' ); ?>">
	<div class="hero__media" data-parallax aria-hidden="true">
		<img src="<?php echo esc_url( $hero_bg ); ?>" alt="" width="1920" height="1080" fetchpriority="high">
	</div>
	<div class="hero__patina" aria-hidden="true"></div>
	<div class="hero__bloom" aria-hidden="true"></div>
	<div class="hero__overlay"></div>
	<div class="hero__grid" aria-hidden="true"></div>
	<div class="wrap hero__content">
		<p class="eyebrow reveal reveal--delay-1"><?php esc_html_e( 'Weathering steel · sheet metal · OEM', 'corten-steel' ); ?></p>
		<h1 class="hero__title reveal reveal--delay-2"><?php esc_html_e( 'Sheet metal that weathers with the landscape', 'corten-steel' ); ?></h1>
		<p class="hero__lede reveal reveal--delay-3"><?php esc_html_e( 'Precision Corten planters, equipment enclosures, and custom fabricated parts for landscape architects, architecture studios, garden retailers, and industrial buyers across Europe and North America.', 'corten-steel' ); ?></p>
		<div class="hero-actions reveal reveal--delay-4">
			<a class="btn btn--primary btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic>
				<span class="btn__label"><?php esc_html_e( 'Get a Quote', 'corten-steel' ); ?></span>
				<span class="btn__shine" aria-hidden="true"></span>
			</a>
			<a class="btn btn--ghost btn--magnetic" href="<?php echo esc_url( home_url( '/products/' ) ); ?>" data-magnetic>
				<span class="btn__label"><?php esc_html_e( 'Explore Products', 'corten-steel' ); ?></span>
				<span class="btn__shine" aria-hidden="true"></span>
			</a>
		</div>
		<div class="hero__scroll-hint reveal reveal--delay-5" aria-hidden="true">
			<span class="hero__scroll-line"></span>
			<span class="hero__scroll-text"><?php esc_html_e( 'Scroll', 'corten-steel' ); ?></span>
		</div>
	</div>
	<div class="hero__frame" aria-hidden="true">
		<span class="hero__rivet hero__rivet--tl"></span>
		<span class="hero__rivet hero__rivet--tr"></span>
		<span class="hero__rivet hero__rivet--bl"></span>
		<span class="hero__rivet hero__rivet--br"></span>
	</div>
</section>
