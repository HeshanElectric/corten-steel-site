<?php
/**
 * Closing RFQ banner.
 *
 * @package Corten_Steel
 */
?>
<section class="cta-banner">
	<div class="wrap cta-banner__inner reveal">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Next drawing', 'corten-steel' ); ?></p>
			<h2><?php esc_html_e( 'Ready to start your project?', 'corten-steel' ); ?></h2>
			<p><?php esc_html_e( 'Send a sketch or a STEP file. We return feasibility, MOQ, and a packing plan for EU or US delivery.', 'corten-steel' ); ?></p>
		</div>
		<a class="btn btn--primary btn--magnetic" href="<?php echo esc_url( corten_quote_url() ); ?>" data-magnetic>
			<span class="btn__label"><?php esc_html_e( 'Start an RFQ', 'corten-steel' ); ?></span>
			<span class="btn__shine" aria-hidden="true"></span>
		</a>
	</div>
</section>
