<?php
/**
 * Single Post Template
 *
 * Implements the 2-column article + sidebar structure based on the reference layout:
 * - Centered Hero Header with category eyebrow, large title, and meta info bar
 * - Left Column (col-lg-8):
 *   - Featured Image (rounded with shadow)
 *   - Entry Body Typography
 *   - Tags & Social Share Bar
 *   - Author Bio Card
 *   - Previous / Next Post Interactive Navigation Cards with divider
 *   - Related Articles (2-column cards)
 *   - Comments Section
 * - Right Column (col-lg-4 Sticky Sidebar):
 *   - Categories Widget with post counts & active category indicator
 *   - Search Widget
 *   - Recent Articles Widget with thumbnails
 *   - Dinosaur World "Plan Your Visit" CTA Card
 * - Dino Explorers Club Newsletter CTA Card
 *
 * @package Dino_World
 */

get_header();

$arrow_icon_url    = get_template_directory_uri() . '/assets/image/right-arrow.png';
$default_thumb_url = get_template_directory_uri() . '/assets/image/Life-size-Dinosaurs.png';
?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php
	$post_id          = get_the_ID();
	$categories       = get_the_category( $post_id );
	$primary_cat      = ! empty( $categories ) ? $categories[0] : null;
	$primary_cat_name = $primary_cat ? $primary_cat->name : __( 'Prehistoric Dispatch', 'dino-world' );
	$primary_cat_slug = $primary_cat ? $primary_cat->slug : '';
	$primary_cat_url  = $primary_cat ? get_category_link( $primary_cat->term_id ) : home_url( '/index.php/blogs/' );
	
	$word_count       = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$read_time        = max( 1, ceil( $word_count / 200 ) ) . ' Min Read';
	$pub_date         = get_the_date( 'd M Y' );
	
	$author_id        = get_the_author_meta( 'ID' );
	$author_name      = get_the_author_meta( 'display_name' );
	if ( 'admin' === strtolower( $author_name ) ) {
		$author_name = __( 'Dinosaur World Team', 'dino-world' );
	}
	$author_desc      = get_the_author_meta( 'description' );
	if ( empty( $author_desc ) ) {
		$author_desc = __( 'Passionate paleontology and wildlife adventure specialists bringing the wonder of Jurassic exploration to life at Dinosaur World.', 'dino-world' );
	}
	
	$post_permalink   = get_permalink( $post_id );
	$post_title_raw   = get_the_title();
	?>

	<!-- 1. Blog Details Hero Header Banner (Centered, matching reference) -->
	<section class="single-blog-hero py-5">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-9 col-lg-10 text-center">
					
					<!-- Eyebrow with theme styling & category name -->
					<div class="section-subtitle justify-content-center mb-3">
						<span class="eyebrow-divider left"></span>
						<a href="<?php echo esc_url( $primary_cat_url ); ?>" class="eyebrow-cat-text">
							<?php echo esc_html( strtoupper( $primary_cat_name ) ); ?>
						</a>
						<span class="eyebrow-divider right"></span>
					</div>

					<!-- Post Title -->
					<h1 class="single-post-title mb-4"><?php the_title(); ?></h1>

					<!-- Post Meta Bar with icons -->
					<div class="single-post-meta d-flex flex-wrap align-items-center justify-content-center gap-4">
						<div class="meta-item author-meta d-flex align-items-center gap-2">
							<?php echo get_avatar( $author_id, 32, '', esc_attr( $author_name ), array( 'class' => 'author-thumb rounded-circle' ) ); ?>
							<span><?php esc_html_e( 'By', 'dino-world' ); ?> <strong><?php echo esc_html( $author_name ); ?></strong></span>
						</div>
						<div class="meta-item d-flex align-items-center gap-2">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gold"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
							<span><?php echo esc_html( $pub_date ); ?></span>
						</div>
						<div class="meta-item d-flex align-items-center gap-2">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gold"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
							<span><?php echo esc_html( $read_time ); ?></span>
						</div>
						<div class="meta-item d-flex align-items-center gap-2">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gold"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
							<a href="<?php echo esc_url( $primary_cat_url ); ?>" class="text-category"><?php echo esc_html( $primary_cat_name ); ?></a>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>

	<!-- 2. Main 2-Column Content + Sidebar Section -->
	<section class="single-blog-main-section py-5">
		<div class="container">
			<div class="row g-4 g-lg-5">

				<!-- LEFT COLUMN: MAIN ARTICLE CONTENT (col-lg-8) -->
				<div class="col-lg-8">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-article' ); ?>>

						<!-- Featured Image (Edge-to-Edge rounded) -->
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="single-post-featured-image mb-4" data-aos="fade-up">
								<?php the_post_thumbnail( 'full', array( 'class' => 'img-fluid rounded-4 shadow-sm w-100' ) ); ?>
								<?php
								$thumb_caption = get_the_post_thumbnail_caption();
								if ( ! empty( $thumb_caption ) ) :
									?>
									<figcaption class="single-media-caption"><?php echo esc_html( $thumb_caption ); ?></figcaption>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<!-- Post Entry Content Body -->
						<div class="entry-content blog-details-body mb-5" data-aos="fade-up">
							<?php
							the_content();

							wp_link_pages(
								array(
									'before'      => '<div class="page-links mt-4"><span class="page-links-title">' . esc_html__( 'Pages:', 'dino-world' ) . '</span>',
									'after'       => '</div>',
									'link_before' => '<span class="page-number">',
									'link_after'  => '</span>',
								)
							);
							?>
						</div>

						<!-- Tags & Social Share Bar -->
						<div class="post-tags-share-bar d-flex flex-wrap align-items-center justify-content-between gap-3 p-4 mb-5 rounded-4" data-aos="fade-up">
							<div class="post-tags-wrapper d-flex align-items-center flex-wrap gap-2">
								<span class="tags-label">
									<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
									<?php esc_html_e( 'Tags:', 'dino-world' ); ?>
								</span>
								<?php
								$post_tags = get_the_tags();
								if ( ! empty( $post_tags ) ) :
									foreach ( $post_tags as $tag_item ) :
										?>
										<a href="<?php echo esc_url( get_tag_link( $tag_item->term_id ) ); ?>" class="tag-badge">
											#<?php echo esc_html( $tag_item->name ); ?>
										</a>
										<?php
									endforeach;
								else :
									?>
									<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="tag-badge">#DinosaurWorld</a>
									<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="tag-badge">#Expedition</a>
									<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="tag-badge">#Prehistoric</a>
								<?php endif; ?>
							</div>

							<div class="post-share-wrapper d-flex align-items-center gap-2">
								<span class="share-label me-1"><?php esc_html_e( 'Share:', 'dino-world' ); ?></span>
								<!-- Facebook -->
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $post_permalink ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn fb" aria-label="Facebook">
									<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
								</a>
								<!-- Twitter / X -->
								<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( $post_permalink ); ?>&text=<?php echo rawurlencode( $post_title_raw ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn tw" aria-label="Twitter">
									<svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
								</a>
								<!-- WhatsApp -->
								<a href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( $post_title_raw . ' ' . $post_permalink ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn wa" aria-label="WhatsApp">
									<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.971.53 1.77.813 2.796.814 3.183 0 5.769-2.587 5.77-5.767 0-3.181-2.586-5.769-5.77-5.769zm3.387 8.219c-.14.394-.717.735-1.02.774-.298.038-.686.069-2.029-.488-1.579-.652-2.584-2.262-2.664-2.368-.079-.105-.639-.851-.639-1.624 0-.773.404-1.154.548-1.309.144-.155.314-.194.419-.194.105 0 .21.002.302.006.098.005.228-.037.357.273.132.318.45 1.096.489 1.176.04.08.066.173.013.279-.053.106-.08.172-.158.265-.079.092-.167.206-.239.277-.079.079-.161.164-.069.322.092.158.409.676.877 1.092.602.535 1.109.701 1.267.78.158.079.25.069.342-.037.092-.106.394-.459.5-.617.105-.158.21-.132.355-.079.145.053.918.433 1.076.512.158.079.263.119.302.185.039.066.039.382-.101.776z"/></svg>
								</a>
								<!-- Copy Link -->
								<button type="button" class="share-btn cp" id="copyExpeditionLink" data-link="<?php echo esc_url( $post_permalink ); ?>" title="<?php esc_attr_e( 'Copy Link', 'dino-world' ); ?>">
									<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
								</button>
							</div>
						</div>

						<!-- Author Bio Box -->
						<div class="author-bio-card p-4 mb-5 rounded-4 d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4" data-aos="fade-up">
							<?php echo get_avatar( $author_id, 90, '', esc_attr( $author_name ), array( 'class' => 'author-bio-img rounded-circle' ) ); ?>
							<div class="author-bio-info text-center text-sm-start">
								<span class="author-label text-gold text-uppercase"><?php esc_html_e( 'Article Author', 'dino-world' ); ?></span>
								<h4 class="author-name mb-2"><?php echo esc_html( $author_name ); ?></h4>
								<p class="author-desc mb-3"><?php echo esc_html( $author_desc ); ?></p>
								<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="author-posts-link">
									<?php esc_html_e( 'View All Posts by', 'dino-world' ); ?> <?php echo esc_html( $author_name ); ?>
									<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" width="12" class="ms-1" />
								</a>
							</div>
						</div>

						<!-- PREVIOUS POST & NEXT POST NAVIGATION (Matching Reference Design) -->
						<?php
						$prev_post = get_previous_post();
						$next_post = get_next_post();
						?>
						<div class="post-navigation-wrapper mb-5" data-aos="fade-up">
							<div class="post-nav-row d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
								
								<!-- PREVIOUS POST CARD -->
								<div class="post-nav-col prev-post-col w-100">
									<?php if ( $prev_post ) : ?>
										<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="post-nav-card">
											<div class="nav-bg-glow"></div>
											<div class="nav-arrow-btn">
												<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
											</div>
											<div class="nav-card-content">
												<span class="nav-card-label"><?php esc_html_e( 'PREVIOUS POST', 'dino-world' ); ?></span>
												<h5 class="nav-card-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></h5>
											</div>
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="post-nav-card">
											<div class="nav-bg-glow"></div>
											<div class="nav-arrow-btn">
												<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
											</div>
											<div class="nav-card-content">
												<span class="nav-card-label"><?php esc_html_e( 'PREVIOUS POST', 'dino-world' ); ?></span>
												<h5 class="nav-card-title"><?php esc_html_e( 'Explore All Blog Articles', 'dino-world' ); ?></h5>
											</div>
										</a>
									<?php endif; ?>
								</div>

								<!-- VERTICAL DOTTED DIVIDER -->
								<div class="post-nav-divider d-none d-md-flex flex-column align-items-center">
									<span></span>
									<span></span>
									<span></span>
									<span></span>
									<span></span>
								</div>

								<!-- NEXT POST CARD -->
								<div class="post-nav-col next-post-col w-100">
									<?php if ( $next_post ) : ?>
										<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="post-nav-card">
											<div class="nav-bg-glow"></div>
											<div class="nav-card-content text-start text-md-end">
												<span class="nav-card-label"><?php esc_html_e( 'NEXT POST', 'dino-world' ); ?></span>
												<h5 class="nav-card-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></h5>
											</div>
											<div class="nav-arrow-btn me-0 ms-md-3">
												<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
											</div>
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( home_url( '/index.php/blogs/' ) ); ?>" class="post-nav-card">
											<div class="nav-bg-glow"></div>
											<div class="nav-card-content text-start text-md-end">
												<span class="nav-card-label"><?php esc_html_e( 'NEXT POST', 'dino-world' ); ?></span>
												<h5 class="nav-card-title"><?php esc_html_e( 'Discover More Prehistoric Guides', 'dino-world' ); ?></h5>
											</div>
											<div class="nav-arrow-btn me-0 ms-md-3">
												<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
											</div>
										</a>
									<?php endif; ?>
								</div>

							</div>
						</div>

						<!-- Related Articles Section (Matching 2-column reference grid) -->
						<?php
						$cat_ids = $primary_cat ? array( $primary_cat->term_id ) : array();
						$related_query = new WP_Query(
							array(
								'post_type'      => 'post',
								'post_status'    => 'publish',
								'posts_per_page' => 2,
								'post__not_in'   => array( $post_id ),
								'category__in'   => $cat_ids,
								'orderby'        => 'rand',
							)
						);

						if ( ! $related_query->have_posts() ) {
							$related_query = new WP_Query(
								array(
									'post_type'      => 'post',
									'post_status'    => 'publish',
									'posts_per_page' => 2,
									'post__not_in'   => array( $post_id ),
									'orderby'        => 'date',
									'order'          => 'DESC',
								)
							);
						}

						if ( $related_query->have_posts() ) :
							?>
							<div class="related-posts-wrapper mt-5 pt-4 border-top" data-aos="fade-up">
								<div class="related-header mb-4">
									<h3 class="related-section-title"><?php esc_html_e( 'Related Articles', 'dino-world' ); ?></h3>
									<span class="title-divider"></span>
								</div>
								<div class="row g-4">
									<?php
									while ( $related_query->have_posts() ) :
										$related_query->the_post();
										$rel_id       = get_the_ID();
										$rel_title    = get_the_title();
										$rel_url      = get_permalink();
										$rel_date     = get_the_date( 'd M Y' );
										$rel_cats     = get_the_category();
										$rel_cat_name = ! empty( $rel_cats ) ? $rel_cats[0]->name : 'Prehistoric';
										$rel_thumb    = has_post_thumbnail() ? get_the_post_thumbnail_url( $rel_id, 'medium_large' ) : $default_thumb_url;
										?>
										<div class="col-md-6">
											<article class="related-card rounded-4 border p-3 bg-white h-100 d-flex flex-column">
												<div class="related-thumb mb-3 overflow-hidden rounded-3">
													<a href="<?php echo esc_url( $rel_url ); ?>">
														<img src="<?php echo esc_url( $rel_thumb ); ?>" alt="<?php echo esc_attr( $rel_title ); ?>" class="img-fluid w-100 object-fit-cover" style="height: 200px;">
													</a>
												</div>
												<div class="related-content d-flex flex-column flex-grow-1">
													<span class="related-cat text-gold small fw-bold text-uppercase mb-1"><?php echo esc_html( $rel_cat_name ); ?></span>
													<h5 class="related-title mb-2">
														<a href="<?php echo esc_url( $rel_url ); ?>"><?php echo esc_html( $rel_title ); ?></a>
													</h5>
													<div class="related-meta mt-auto text-muted small pt-2 d-flex align-items-center justify-content-between border-top">
														<span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> <?php echo esc_html( $rel_date ); ?></span>
														<a href="<?php echo esc_url( $rel_url ); ?>" class="read-more-link text-gold font-weight-bold">
															<?php esc_html_e( 'Read', 'dino-world' ); ?>
															<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" width="11" class="ms-1" />
														</a>
													</div>
												</div>
											</article>
										</div>
									<?php endwhile; ?>
								</div>
							</div>
							<?php
							wp_reset_postdata();
						endif;
						?>

						<!-- Comments Area -->
						<?php
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						?>

					</article>
				</div>

				<!-- RIGHT COLUMN: SIDEBAR WITH CATEGORIES ON RIGHT SIDE (col-lg-4) -->
				<div class="col-lg-4">
					<aside class="blog-sidebar sticky-top" style="top: 120px; z-index: 10;">

						<!-- 1. CATEGORIES WIDGET -->
						<?php
						$all_cats = get_categories(
							array(
								'taxonomy'   => 'category',
								'hide_empty' => true,
								'exclude'    => array( (int) get_cat_ID( 'Uncategorized' ) ),
								'orderby'    => 'count',
								'order'      => 'DESC',
							)
						);
						?>
						<div class="sidebar-widget widget-categories mb-4 p-4 rounded-4 bg-white border shadow-sm" data-aos="fade-up">
							<div class="widget-header mb-3 pb-2 border-bottom">
								<h4 class="widget-title mb-0"><?php esc_html_e( 'Categories', 'dino-world' ); ?></h4>
								<span class="widget-line"></span>
							</div>
							<div class="widget-content">
								<ul class="categories-list p-0 m-0">
									<?php if ( ! empty( $all_cats ) ) : ?>
										<?php foreach ( $all_cats as $cat_item ) : ?>
											<?php
											$is_active = ( $primary_cat && $primary_cat->term_id === $cat_item->term_id ) ? 'active' : '';
											?>
											<li class="<?php echo esc_attr( $is_active ); ?> mb-2">
												<a href="<?php echo esc_url( get_category_link( $cat_item->term_id ) ); ?>" class="d-flex align-items-center justify-content-between p-2 rounded-3">
													<span class="cat-name">
														<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-2 cat-arrow"><polyline points="9 18 15 12 9 6"></polyline></svg>
														<?php echo esc_html( $cat_item->name ); ?>
													</span>
													<span class="cat-count badge rounded-pill"><?php echo esc_html( $cat_item->count ); ?></span>
												</a>
											</li>
										<?php endforeach; ?>
									<?php else : ?>
										<li class="text-muted small"><?php esc_html_e( 'No categories found.', 'dino-world' ); ?></li>
									<?php endif; ?>
								</ul>
							</div>
						</div>

						<!-- 2. SEARCH WIDGET -->
						<div class="sidebar-widget widget-search mb-4 p-4 rounded-4 bg-white border shadow-sm" data-aos="fade-up" data-aos-delay="100">
							<div class="widget-header mb-3 pb-2 border-bottom">
								<h4 class="widget-title mb-0"><?php esc_html_e( 'Search Articles', 'dino-world' ); ?></h4>
								<span class="widget-line"></span>
							</div>
							<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
								<div class="search-input-group position-relative">
									<input type="search" class="form-control rounded-3 pe-5" placeholder="<?php esc_attr_e( 'Search articles...', 'dino-world' ); ?>" value="<?php echo get_search_query(); ?>" name="s" required />
									<button type="submit" class="search-submit-btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-3 text-gold" aria-label="<?php esc_attr_e( 'Search', 'dino-world' ); ?>">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
									</button>
								</div>
							</form>
						</div>

						<!-- 3. RECENT POSTS WIDGET -->
						<?php
						$recent_query = new WP_Query(
							array(
								'post_type'      => 'post',
								'post_status'    => 'publish',
								'posts_per_page' => 4,
								'post__not_in'   => array( $post_id ),
								'orderby'        => 'date',
								'order'          => 'DESC',
							)
						);
						if ( $recent_query->have_posts() ) :
							?>
							<div class="sidebar-widget widget-recent-posts mb-4 p-4 rounded-4 bg-white border shadow-sm" data-aos="fade-up" data-aos-delay="150">
								<div class="widget-header mb-3 pb-2 border-bottom">
									<h4 class="widget-title mb-0"><?php esc_html_e( 'Recent Articles', 'dino-world' ); ?></h4>
									<span class="widget-line"></span>
								</div>
								<div class="recent-posts-list">
									<?php
									while ( $recent_query->have_posts() ) :
										$recent_query->the_post();
										$rec_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) : $default_thumb_url;
										?>
										<div class="recent-post-item d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
											<a href="<?php the_permalink(); ?>" class="recent-thumb-link flex-shrink-0">
												<img src="<?php echo esc_url( $rec_thumb ); ?>" alt="<?php the_title_attribute(); ?>" class="recent-thumb rounded-3 object-fit-cover" width="68" height="68">
											</a>
											<div class="recent-info">
												<h6 class="recent-title mb-1">
													<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
												</h6>
												<span class="recent-date text-muted small">
													<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
													<?php echo get_the_date( 'd M Y' ); ?>
												</span>
											</div>
										</div>
									<?php endwhile; ?>
								</div>
							</div>
							<?php
							wp_reset_postdata();
						endif;
						?>

						<!-- 4. DINOSAUR WORLD PLAN YOUR VISIT CTA WIDGET -->
						<div class="sidebar-widget widget-cta-card p-4 rounded-4 text-center position-relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
							<div class="cta-bg-shape"></div>
							<div class="cta-icon-box mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-white"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
							</div>
							<h4 class="cta-title mb-2 text-white"><?php esc_html_e( 'Plan Your Prehistoric Visit', 'dino-world' ); ?></h4>
							<p class="cta-desc small mb-4 text-white-50"><?php esc_html_e( 'Book online tickets, birthday party packages, and school excursions to experience life-size animatronic dinosaurs.', 'dino-world' ); ?></p>
							<a href="<?php echo esc_url( home_url( '/#plan-visit' ) ); ?>" class="orange-btn w-100 justify-content-center">
								<?php esc_html_e( 'Plan Your Visit', 'dino-world' ); ?>
								<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
							</a>
						</div>

					</aside>
				</div>

			</div>
		</div>
	</section>

	<!-- 3. Dino Explorers Club Newsletter CTA Card -->
	<section class="single-newsletter-sec sec-padding1 pb-5">
		<div class="container">
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

<?php endwhile; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var copyBtn = document.getElementById('copyExpeditionLink');
	if (copyBtn) {
		copyBtn.addEventListener('click', function () {
			var link = this.getAttribute('data-link');
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(link).then(function () {
					showCopyFeedback(copyBtn);
				});
			} else {
				var tempInput = document.createElement('input');
				tempInput.value = link;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
				showCopyFeedback(copyBtn);
			}
		});
	}

	function showCopyFeedback(btn) {
		var origHtml = btn.innerHTML;
		btn.innerHTML = '<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>';
		btn.style.backgroundColor = 'var(--primary-color)';
		btn.style.color = '#ffffff';
		setTimeout(function () {
			btn.innerHTML = origHtml;
			btn.style.backgroundColor = '';
			btn.style.color = '';
		}, 2000);
	}
});
</script>

<?php
get_footer();
