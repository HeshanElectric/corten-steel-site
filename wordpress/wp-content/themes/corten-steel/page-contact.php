<?php
/**
 * Template Name: Contact
 * RFQ form with Direct Contacts and Working Hours beside it.
 *
 * @package Corten_Steel
 */

get_header();

$page_id      = get_queried_object_id();
$direct_title = corten_meta_or_default( $page_id, 'corten_contact_direct_title', __( 'Direct Contacts', 'corten-steel' ) );
$hours_title  = corten_meta_or_default( $page_id, 'corten_contact_hours_title', __( 'Working Hours (Your Timezone)', 'corten-steel' ) );
$direct_rows  = corten_get_contact_direct_rows( $page_id );
$hours_rows   = corten_get_contact_hours_rows( $page_id );
?>
<main id="content" class="site-main">
	<section class="section contact-page">
		<div class="wrap">
			<header class="page-header reveal">
				<p class="eyebrow"><?php esc_html_e( 'RFQ', 'corten-steel' ); ?></p>
				<h1><?php esc_html_e( 'Contact', 'corten-steel' ); ?></h1>
			</header>

			<div class="contact-grid">
				<aside class="contact-directory contact-directory--aside reveal" aria-label="<?php esc_attr_e( 'Contact directory', 'corten-steel' ); ?>">
					<?php if ( $direct_rows ) : ?>
						<div class="contact-block">
							<header class="contact-block__head">
								<p class="eyebrow"><?php esc_html_e( 'Reach us', 'corten-steel' ); ?></p>
								<h2><?php echo esc_html( $direct_title ); ?></h2>
							</header>
							<table class="contact-table contact-table--direct">
								<tbody>
									<?php foreach ( $direct_rows as $row ) : ?>
										<tr>
											<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
											<td><?php echo corten_format_contact_value( $row['label'], $row['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>

					<?php if ( $hours_rows ) : ?>
						<div class="contact-block">
							<header class="contact-block__head">
								<p class="eyebrow"><?php esc_html_e( 'Timezone', 'corten-steel' ); ?></p>
								<h2><?php echo esc_html( $hours_title ); ?></h2>
							</header>
							<table class="contact-table contact-table--hours">
								<thead>
									<tr>
										<th scope="col"><?php esc_html_e( 'Region', 'corten-steel' ); ?></th>
										<th scope="col"><?php esc_html_e( 'Working Hours', 'corten-steel' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $hours_rows as $row ) : ?>
										<tr>
											<th scope="row"><?php echo esc_html( $row['region'] ); ?></th>
											<td><?php echo esc_html( $row['hours'] ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>
				</aside>

				<div class="rfq-panel reveal">
					<header class="contact-block__head">
						<p class="eyebrow"><?php esc_html_e( 'Inquiry', 'corten-steel' ); ?></p>
						<h2><?php esc_html_e( 'Send an RFQ', 'corten-steel' ); ?></h2>
					</header>
					<?php if ( shortcode_exists( 'fluentform' ) ) : ?>
						<?php echo do_shortcode( '[fluentform id="1"]' ); ?>
					<?php else : ?>
						<form class="rfq-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
							<input type="hidden" name="action" value="corten_rfq">
							<?php wp_nonce_field( 'corten_rfq', 'corten_rfq_nonce' ); ?>
							<label>
								<span><?php esc_html_e( 'Name', 'corten-steel' ); ?></span>
								<input type="text" name="name" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Company', 'corten-steel' ); ?></span>
								<input type="text" name="company" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Email', 'corten-steel' ); ?></span>
								<input type="email" name="email" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Country', 'corten-steel' ); ?></span>
								<input type="text" name="country" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Product interest', 'corten-steel' ); ?></span>
								<select name="product_interest">
									<option><?php esc_html_e( 'Corten planters', 'corten-steel' ); ?></option>
									<option><?php esc_html_e( 'Sheet-metal enclosures', 'corten-steel' ); ?></option>
									<option><?php esc_html_e( 'Custom fabrication', 'corten-steel' ); ?></option>
								</select>
							</label>
							<label>
								<span><?php esc_html_e( 'Quantity', 'corten-steel' ); ?></span>
								<input type="number" name="quantity" min="1" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Target date', 'corten-steel' ); ?></span>
								<input type="date" name="target_date">
							</label>
							<label class="full">
								<span><?php esc_html_e( 'Message', 'corten-steel' ); ?></span>
								<textarea name="message" rows="5" required></textarea>
							</label>
							<div class="full">
								<span class="rfq-form__field-label"><?php esc_html_e( 'File upload', 'corten-steel' ); ?></span>
								<div class="file-field" data-file-field>
									<input class="file-field__input" type="file" id="corten_rfq_drawing" name="drawing" accept=".pdf,.dwg,.step,.stp,.zip,.jpg,.png">
									<label class="file-field__trigger" for="corten_rfq_drawing"><?php esc_html_e( 'Choose file', 'corten-steel' ); ?></label>
									<span class="file-field__name" data-file-name data-file-placeholder="<?php esc_attr_e( 'No file chosen', 'corten-steel' ); ?>"><?php esc_html_e( 'No file chosen', 'corten-steel' ); ?></span>
								</div>
							</div>
							<button class="btn btn--primary btn--magnetic" type="submit" data-magnetic>
								<span class="btn__label"><?php esc_html_e( 'Send RFQ', 'corten-steel' ); ?></span>
								<span class="btn__shine" aria-hidden="true"></span>
							</button>
							<p class="form-note"><?php esc_html_e( 'This native form is a fallback. Fluent Forms will replace it in production and send notification plus confirmation email.', 'corten-steel' ); ?></p>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
