<?php
/**
 * Single blog post — full article with hero, breadcrumb, location, related posts.
 *
 * @package Corten_Steel
 */

get_header();

if ( corten_elementor_location_done( 'single' ) ) {
	get_footer();
	return;
}

$post_id    = get_the_ID();
$location   = get_post_meta( $post_id, '_corten_post_location', true );
$date       = get_the_date();
$date_iso   = get_the_date( 'c' );
$categories = get_the_category( $post_id );
$tags       = get_the_tags( $post_id );
?>
<main id="content" class="site-main blog-single">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-article' ); ?>>
			<header class="blog-article__hero">
				<div class="wrap">
					<nav class="blog-breadcrumb reveal" aria-label="<?php esc_attr_e( 'Breadcrumb', 'corten-steel' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'corten-steel' ); ?></a>
						<span aria-hidden="true">/</span>
						<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Journal', 'corten-steel' ); ?></a>
						<?php if ( $categories ) : ?>
							<span aria-hidden="true">/</span>
							<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
						<?php endif; ?>
						<span aria-hidden="true">/</span>
						<span><?php echo esc_html( get_the_title() ); ?></span>
					</nav>

					<div class="blog-article__intro reveal reveal--delay-1">
						<?php if ( $categories ) : ?>
							<p class="eyebrow"><?php echo esc_html( $categories[0]->name ); ?></p>
						<?php else : ?>
							<p class="eyebrow"><?php esc_html_e( 'Journal', 'corten-steel' ); ?></p>
						<?php endif; ?>
						<h1><?php the_title(); ?></h1>
						<div class="blog-article__byline">
							<time datetime="<?php echo esc_attr( $date_iso ); ?>"><?php echo esc_html( $date ); ?></time>
							<?php if ( $location ) : ?>
								<span class="blog-article__dot" aria-hidden="true">&middot;</span>
								<span class="blog-article__location"><?php echo esc_html( $location ); ?></span>
							<?php endif; ?>
							<?php if ( $tags ) : ?>
								<span class="blog-article__dot" aria-hidden="true">&middot;</span>
								<span class="blog-article__tags">
									<?php foreach ( $tags as $t ) : ?>
										<a href="<?php echo esc_url( get_tag_link( $t->term_id ) ); ?>">#<?php echo esc_html( $t->name ); ?></a>
									<?php endforeach; ?>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="blog-article__media reveal reveal--delay-2">
							<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			</header>

			<section class="section blog-article__body">
				<div class="wrap blog-article__content reveal reveal--delay-3">
					<?php
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'corten-steel' ),
						'after'  => '</div>',
					) );
					?>
				</div>
			</section>
		</article>

		<?php
		// Related posts (up to 3).
		$related = get_posts( array(
			'category__in' => wp_get_post_categories( $post_id ),
			'numberposts'  => 3,
			'post__not_in' => array( $post_id ),
			'orderby'      => 'date',
		) );
		?>
		<?php if ( $related ) : ?>
			<section class="section blog-article__related">
				<div class="wrap">
					<p class="eyebrow reveal"><?php esc_html_e( 'Also in this category', 'corten-steel' ); ?></p>
					<h2 class="reveal reveal--delay-1"><?php esc_html_e( 'Read more from our journal', 'corten-steel' ); ?></h2>
					<div class="blog-card-grid">
						<?php foreach ( $related as $idx => $rel ) : ?>
							<?php setup_postdata( $rel ); ?>
							<?php
							$delay = 'reveal--delay-' . min( $idx + 2, 5 );
							get_template_part( 'template-parts/blog-card', null, array( 'delay' => $delay ) );
							?>
						<?php endforeach; wp_reset_postdata(); ?>
					</div>
					<a class="btn btn--outline btn--magnetic" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" data-magnetic>
						<span class="btn__label"><?php esc_html_e( 'View all articles', 'corten-steel' ); ?></span>
						<span class="btn__shine" aria-hidden="true"></span>
					</a>
				</div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
