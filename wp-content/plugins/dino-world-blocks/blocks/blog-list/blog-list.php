<?php
/**
 * Block Name: Blog List
 *
 * Displays the main blog grid (excluding the featured post), pagination, and newsletter card from html/blog.html:
 * 1. Main 3-Column Blog Grid (filtered dynamically by category & search)
 * 2. Pagination
 * 3. Newsletter CTA Card
 *
 * No ACF fields required.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'blog-list-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'blog-list-block';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// 1. Pagination calculation
$paged = 1;
if ( get_query_var( 'paged' ) ) {
	$paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
	$paged = get_query_var( 'page' );
} elseif ( isset( $_GET['paged'] ) ) {
	$paged = max( 1, (int) $_GET['paged'] );
}

$posts_per_page = 6; // 6 cards per page for standard 3-column grid

// 2. Identify latest featured post to avoid duplicate rendering in the grid
$latest_featured = get_posts(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'fields'         => 'ids',
	)
);
$exclude_ids = ! empty( $latest_featured ) ? $latest_featured : array();

// 3. Query published blog posts for the grid
$blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => $exclude_ids,
	)
);

$arrow_icon_url  = get_template_directory_uri() . '/assets/image/right-arrow.png';
$default_img_url = get_template_directory_uri() . '/assets/image/Fossil-Dig-Experience.png';
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<?php if ( $blog_query->have_posts() ) : ?>
		<!-- Main Blog Grid (Latest From Dinosaur World) -->
		<section class="blog-grid-sec sec-padding1">
			<div class="container">
				<h2 class="title-2"><?php esc_html_e( 'Latest From Dinosaur World', 'dino-world' ); ?></h2>

				<div class="row g-4" id="articlesGrid-<?php echo esc_attr( $block_id ); ?>">
					<?php
					$grid_posts = $blog_query->posts;
					foreach ( $grid_posts as $idx => $g_post ) :
						$g_id          = $g_post->ID;
						$g_title       = get_the_title( $g_id );
						$g_url         = get_permalink( $g_id );
						$g_date        = get_the_date( 'M j, Y', $g_id );
						$g_cats        = get_the_category( $g_id );
						$g_cat_slugs   = ! empty( $g_cats ) ? wp_list_pluck( $g_cats, 'slug' ) : array( 'all' );
						$g_cat_slug_str= implode( ' ', $g_cat_slugs );
						$g_cat_name    = ! empty( $g_cats ) ? $g_cats[0]->name : 'Article';
						$g_thumb       = has_post_thumbnail( $g_id ) ? get_the_post_thumbnail_url( $g_id, 'large' ) : $default_img_url;
						$g_word_count  = str_word_count( wp_strip_all_tags( $g_post->post_content ) );
						$g_read_time   = max( 1, ceil( $g_word_count / 200 ) ) . ' min read';
						$g_excerpt     = has_excerpt( $g_id ) ? get_the_excerpt( $g_id ) : wp_trim_words( strip_shortcodes( $g_post->post_content ), 20, '...' );
						
						$author_data   = get_userdata( $g_post->post_author );
						$author_name   = ( $author_data && ! empty( $author_data->display_name ) && 'admin' !== strtolower( $author_data->display_name ) ) ? $author_data->display_name : 'Dinosaur World Team';

						$delay_ms      = ( $idx % 3 ) * 100;
						?>
						<div class="col-lg-4 col-md-6 blog-col" data-cat="<?php echo esc_attr( $g_cat_slug_str ); ?>" data-title="<?php echo esc_attr( strtolower( $g_title ) ); ?>">
							<div class="b-card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $delay_ms ); ?>">
								<div class="b-card-img">
									<a href="<?php echo esc_url( $g_url ); ?>">
										<img src="<?php echo esc_url( $g_thumb ); ?>" alt="<?php echo esc_attr( $g_title ); ?>" />
									</a>
									<span class="b-card-badge"><?php echo esc_html( $g_cat_name ); ?></span>
								</div>
								<div class="b-card-body">
									<div class="meta-info">
										<span><?php echo esc_html( $g_date ); ?></span>
										<span>•</span>
										<span><?php echo esc_html( $g_read_time ); ?></span>
									</div>
									<div class="b-card-hd">
										<a href="<?php echo esc_url( $g_url ); ?>">
											<h4><?php echo esc_html( $g_title ); ?></h4>
										</a>
									</div>
									<div class="b-card-desc">
										<p><?php echo wp_kses_post( $g_excerpt ); ?></p>
									</div>
									<div class="b-card-footer">
										<span class="text-muted small">By <?php echo esc_html( $author_name ); ?></span>
										<a class="read-link" href="<?php echo esc_url( $g_url ); ?>">
											<?php esc_html_e( 'Read More', 'dino-world' ); ?>
											<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" width="12" />
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Pagination -->
				<?php
				$total_pages = $blog_query->max_num_pages;
				if ( $total_pages > 1 ) :
					$page_links = paginate_links(
						array(
							'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
							'format'    => '?paged=%#%',
							'current'   => max( 1, $paged ),
							'total'     => $total_pages,
							'type'      => 'array',
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
						)
					);

					if ( ! empty( $page_links ) ) :
						?>
						<div class="pagination-wrap">
							<?php foreach ( $page_links as $link_item ) : ?>
								<?php
								$link_styled = str_replace( 'page-numbers', 'page-num', $link_item );
								$link_styled = str_replace( 'current', 'active', $link_styled );
								echo wp_kses_post( $link_styled );
								?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<!-- Newsletter CTA Card -->
				<div class="newsletter-card" data-aos="fade-up">
					<h3><?php esc_html_e( 'JOIN THE DINO EXPLORERS CLUB', 'dino-world' ); ?></h3>
					<p><?php esc_html_e( 'Get exclusive ticket discounts, invitations to special twilight tours, and prehistoric fun facts delivered straight to your inbox.', 'dino-world' ); ?></p>
					<form class="newsletter-form" onsubmit="event.preventDefault(); alert('Welcome to the Dino Explorers Club!');">
						<input type="email" placeholder="<?php esc_attr_e( 'Enter your email address...', 'dino-world' ); ?>" required />
						<button type="submit" class="orange-btn">
							<?php esc_html_e( 'Subscribe Now', 'dino-world' ); ?>
							<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
						</button>
					</form>
				</div>

			</div>
		</section>

	<?php else : ?>
		<div class="container py-5">
			<div class="alert alert-info text-center">
				<p><?php esc_html_e( 'No blog articles found.', 'dino-world' ); ?></p>
			</div>
		</div>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
</div>
