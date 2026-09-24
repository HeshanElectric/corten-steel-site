<?php
/**
 * 404 template.
 *
 * @package Corten_Steel
 */

get_header();
?>
<main id="content" class="site-main section">
	<div class="wrap narrow error-404">
		<p class="eyebrow"><?php esc_html_e( '404', 'corten-steel' ); ?></p>
		<h1><?php esc_html_e( 'This plate was never nested.', 'corten-steel' ); ?></h1>
		<p><?php esc_html_e( 'The page is missing. Browse the catalog or send drawings for a quote.', 'corten-steel' ); ?></p>
		<div class="hero-actions">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Explore Products', 'corten-steel' ); ?></a>
			<a class="btn btn--ghost" href="<?php echo esc_url( corten_quote_url() ); ?>"><?php esc_html_e( 'Get a Quote', 'corten-steel' ); ?></a>
		</div>
	</div>
</main>
<?php
get_footer();
