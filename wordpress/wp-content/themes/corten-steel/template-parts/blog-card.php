<?php
/**
 * Single blog card — used on archive page and homepage featured section.
 *
 * Accepts $args with 'delay' (reveal delay class) and 'show_excerpt' (bool).
 *
 * @package Corten_Steel
 */

$args = wp_parse_args( $args ?? array(), array(
	'delay'        => '',
	'show_excerpt' => true,
) );

$post_id   = get_the_ID();
$location  = get_post_meta( $post_id, '_corten_post_location', true );
$date      = get_the_date();
$date_iso  = get_the_date( 'c' );
$categories = get_the_category( $post_id );
$cat_name  = $categories ? $categories[0]->name : '';
$cat_link  = $categories ? get_category_link( $categories[0]->term_id ) : '';
$excerpt   = $args['show_excerpt'] ? get_the_excerpt() : '';
?>
<article <?php post_class( 'blog-card reveal ' . esc_attr( $args['delay'] ) ); ?>>
	<a class="blog-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="blog-card__media">
				<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?>
				<span class="blog-card__media-bloom" aria-hidden="true"></span>
			</div>
		<?php else : ?>
			<div class="blog-card__media blog-card__media--placeholder" aria-hidden="true">
				<span class="blog-card__placeholder-mark">CRTN</span>
			</div>
		<?php endif; ?>

		<div class="blog-card__body">
			<div class="blog-card__meta">
				<?php if ( $cat_name ) : ?>
					<span class="blog-card__cat"><?php echo esc_html( $cat_name ); ?></span>
				<?php endif; ?>
				<time datetime="<?php echo esc_attr( $date_iso ); ?>"><?php echo esc_html( $date ); ?></time>
				<?php if ( $location ) : ?>
					<span class="blog-card__location"><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="blog-card__title"><?php the_title(); ?></h3>
			<?php if ( $excerpt ) : ?>
				<p class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 22 ) ); ?></p>
			<?php endif; ?>
			<span class="blog-card__arrow" aria-hidden="true">&rarr;</span>
		</div>
	</a>
</article>
