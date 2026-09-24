<?php
/**
 * Site footer. Skipped when Elementor Theme Builder provides a Footer location.
 *
 * @package Corten_Steel
 */

if ( ! corten_elementor_location_done( 'footer' ) ) :
	?>
	<footer class="site-footer">
		<div class="wrap site-footer__grid">
			<div class="site-footer__brand">
				<p class="site-logo__text">Heshan Power</p>
				<p><?php esc_html_e( 'Weathering-steel planters, equipment enclosures, and custom sheet metal for landscape architects, studios, and OEM buyers in Europe and North America.', 'corten-steel' ); ?></p>
				<?php
				$socials = corten_get_social_profiles();
				if ( $socials ) :
					?>
					<p class="footer-label"><?php esc_html_e( 'Follow', 'corten-steel' ); ?></p>
					<ul class="social-links">
						<?php foreach ( $socials as $profile ) : ?>
							<li>
								<a class="social-links__item" href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $profile['label'] ); ?>">
									<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
										<path fill="currentColor" d="<?php echo esc_attr( $profile['path'] ); ?>"/>
									</svg>
									<span><?php echo esc_html( $profile['label'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div>
				<p class="footer-label"><?php esc_html_e( 'Catalog', 'corten-steel' ); ?></p>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'All products', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/products/?application=landscape' ) ); ?>"><?php esc_html_e( 'Planters & boxes', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/products/?application=industrial' ) ); ?>"><?php esc_html_e( 'Enclosures', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/products/?application=oem' ) ); ?>"><?php esc_html_e( 'Custom fabrication', 'corten-steel' ); ?></a></li>
				</ul>
			</div>
			<div>
				<p class="footer-label"><?php esc_html_e( 'Company', 'corten-steel' ); ?></p>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/capabilities/' ) ); ?>"><?php esc_html_e( 'Capabilities', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'corten-steel' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/resources/' ) ); ?>"><?php esc_html_e( 'Resources', 'corten-steel' ); ?></a></li>
				</ul>
			</div>
			<div>
				<p class="footer-label"><?php esc_html_e( 'Contact', 'corten-steel' ); ?></p>
				<ul>
					<li><a href="mailto:sales@heshanpower.com">sales@heshanpower.com</a></li>
					<li><?php esc_html_e( 'EU dispatch + North American staging', 'corten-steel' ); ?></li>
					<li><a class="btn btn--primary btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic><span class="btn__label"><?php esc_html_e( 'Start an RFQ', 'corten-steel' ); ?></span><span class="btn__shine" aria-hidden="true"></span></a></li>
				</ul>
			</div>
		</div>
		<div class="wrap site-footer__base">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Heshan Power. <?php esc_html_e( 'All rights reserved.', 'corten-steel' ); ?></p>
			<p><?php esc_html_e( 'English default. German and French prepared for WPML directory URLs.', 'corten-steel' ); ?></p>
		</div>
	</footer>
	<?php
endif;
wp_footer();
?>
</body>
</html>
