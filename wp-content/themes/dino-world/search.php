<?php
/**
 * Search Results Template
 *
 * @package Dino_World
 */

get_header();
?>

<div class="search-results-sec sec-py py-5">
	<div class="container">
		<h1 class="mb-4"><?php printf( esc_html__( 'Search Results for: %s', 'dino-world' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		<?php if ( have_posts() ) : ?>
			<div class="row g-4">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="col-lg-4 col-md-6" data-aos="fade-up">
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'card blog-card h-100 border-0 shadow-sm overflow-hidden' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" class="blog-thumb-link overflow-hidden d-block">
									<?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid w-100 object-fit-cover' ) ); ?>
								</a>
							<?php endif; ?>
							<div class="card-body p-4 d-flex flex-column">
								<h3 class="card-title h5 font-secondary mb-3">
									<a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a>
								</h3>
								<div class="card-text text-muted mb-4 flex-grow-1">
									<?php the_excerpt(); ?>
								</div>
								<a href="<?php the_permalink(); ?>" class="btn custom-theme-btn align-self-start"><?php esc_html_e( 'Read More', 'dino-world' ); ?></a>
							</div>
						</article>
					</div>
				<?php endwhile; ?>

				<div class="col-12 mt-5">
					<div class="pagination-wrapper d-flex justify-content-center">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => __( '&laquo; Prev', 'dino-world' ),
								'next_text' => __( 'Next &raquo;', 'dino-world' ),
							)
						);
						?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="col-12 text-center py-5">
				<h3><?php esc_html_e( 'Nothing Found', 'dino-world' ); ?></h3>
				<div class="search-box-wrapper mx-auto mt-4" style="max-width: 500px;">
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
