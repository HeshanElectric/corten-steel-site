<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#1A1A1A">
	<?php
	$preload_hero = corten_get_background_url( 'corten_hero_bg', 'hero-scene.jpg' );
	if ( $preload_hero ) :
		?>
		<link rel="preload" as="image" href="<?php echo esc_url( $preload_hero ); ?>" fetchpriority="high">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="scroll-progress" data-scroll-progress aria-hidden="true"><span></span></div>
<div class="site-noise" data-site-noise aria-hidden="true"></div>
<div class="rust-bloom" aria-hidden="true"></div>
<canvas class="ember-canvas" data-ember-canvas aria-hidden="true"></canvas>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'corten-steel' ); ?></a>
<div class="cursor-follower" data-cursor hidden></div>
<?php if ( ! corten_elementor_location_done( 'header' ) ) : ?>
<header class="site-header" data-site-header>
	<div class="site-header__bar">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="site-logo__mark" aria-hidden="true"></span>
				<span class="site-logo__text">Oxiron</span>
			<?php endif; ?>
		</a>
		<button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="site-navigation">
			<span class="nav-toggle__lines" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'corten-steel' ); ?></span>
		</button>
		<nav id="site-navigation" class="site-nav" data-site-nav aria-label="<?php esc_attr_e( 'Primary', 'corten-steel' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu nav-menu',
					'fallback_cb'    => 'corten_fallback_menu',
					'depth'          => 2,
				)
			);
			?>
		</nav>
		<div class="site-header__tools">
			<div class="lang-switch" aria-label="<?php esc_attr_e( 'Language', 'corten-steel' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" hreflang="en" lang="en" aria-current="true">EN</a>
				<a href="<?php echo esc_url( home_url( '/de/' ) ); ?>" hreflang="de" lang="de">DE</a>
				<a href="<?php echo esc_url( home_url( '/fr/' ) ); ?>" hreflang="fr" lang="fr">FR</a>
			</div>
			<button class="theme-toggle" type="button" data-theme-toggle aria-pressed="true">
				<span data-theme-label><?php esc_html_e( 'Dark', 'corten-steel' ); ?></span>
			</button>
			<a class="btn btn--primary header-cta btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic>
				<span class="btn__label"><?php esc_html_e( 'Get a Quote', 'corten-steel' ); ?></span>
				<span class="btn__shine" aria-hidden="true"></span>
			</a>
		</div>
	</div>
</header>
<?php endif; ?>
