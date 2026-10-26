<?php
/**
 * Block Name: Testimonials Carousel
 *
 * Displays visitor testimonials and Google reviews in a responsive Splide carousel.
 * Supports Google Places API (New & Legacy), real-time rating summary pill, verified Google badges,
 * and direct Google review link.
 *
 * Powered by ACF Block Fields:
 * - background_color (Select: green / white)
 * - short_heading (Text)
 * - heading (Text)
 * - dino_image (Image)
 * - review_source (Select: google / both / manual)
 * - google_place_id (Text)
 * - google_api_key (Text)
 * - min_rating (Select: 1, 2, 3, 4, 5)
 * - max_reviews (Number)
 * - show_summary_badge (Boolean)
 * - show_google_badge (Boolean)
 * - show_review_button (Boolean)
 * - testimonials (Repeater: rating [Number], content [Textarea], author [Text], relative_time [Text])
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

require_once __DIR__ . '/google-reviews.php';

$block_id   = ! empty( $block['anchor'] ) ? $block['anchor'] : 'testimonial-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Dynamic Background Color (green vs white)
$bg_color = 'green';
if ( function_exists( 'get_field' ) ) {
	$bg_color_val = get_field( 'background_color' );
	if ( ! empty( $bg_color_val ) ) {
		$bg_color = is_array( $bg_color_val ) ? ( $bg_color_val['value'] ?? reset( $bg_color_val ) ) : $bg_color_val;
	}
}

if ( empty( $bg_color_val ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/background_color$/', $k ) ) {
			$bg_color = $v;
			break;
		}
	}
}

$is_white_bg = ( strtolower( trim( (string) $bg_color ) ) === 'white' );

// Design classes based on background color
$base_sec_class = $is_white_bg ? 'testimoniala-sec sec-padding1' : 'testimonial-sec sec-padding';
$class_name     = $base_sec_class;
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$title_class = $is_white_bg ? 'title-2 mb-2 text-center' : 'title-2 mb-2 text-center text-white';
$card_class  = $is_white_bg ? 'testa-card' : 'test-card';

// 2. Text Headings (with friendly defaults)
$short_heading = '';
$heading       = '';

if ( function_exists( 'get_field' ) ) {
	$short_heading = get_field( 'short_heading' ) ?: ( get_field( 'tagline' ) ?: get_field( 'sub_heading' ) );
	$heading       = get_field( 'heading' ) ?: get_field( 'title' );
}

if ( empty( $short_heading ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(short_heading|tagline|sub_heading)$/', $k ) ) {
			$short_heading = $v;
			break;
		}
	}
}

if ( empty( $heading ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(^|_)heading$/', $k ) && ! preg_match( '/short_heading/', $k ) ) {
			$heading = $v;
			break;
		}
		if ( preg_match( '/(^|_)title$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

// Default labels if unpopulated
$short_heading = ! empty( $short_heading ) ? $short_heading : __( 'Visitor Reviews', 'dino-world-blocks' );
$heading       = ! empty( $heading ) ? $heading : __( 'What Families Say About Dinosaur World', 'dino-world-blocks' );

// 3. Decorative Dinosaur Image (displayed on green background)
$dino_img_url = '';
$dino_img_alt = 'Dinosaur World mascot';
if ( function_exists( 'get_field' ) ) {
	$dino_img = get_field( 'dino_image' ) ?: get_field( 'testimonial_dino' );
	if ( ! empty( $dino_img ) ) {
		if ( is_array( $dino_img ) ) {
			$dino_img_url = ! empty( $dino_img['url'] ) ? $dino_img['url'] : '';
			$dino_img_alt = ! empty( $dino_img['alt'] ) ? $dino_img['alt'] : $dino_img_alt;
		} elseif ( is_numeric( $dino_img ) ) {
			$dino_img_url = wp_get_attachment_image_url( (int) $dino_img, 'full' );
			$dino_img_alt = get_post_meta( (int) $dino_img, '_wp_attachment_image_alt', true ) ?: $dino_img_alt;
		} elseif ( is_string( $dino_img ) ) {
			$dino_img_url = $dino_img;
		}
	}
}
if ( empty( $dino_img_url ) ) {
	$dino_img_url = get_template_directory_uri() . '/assets/image/testimonial-dino.png';
}

// 4. Google Reviews Configuration
// Default to 'google' so the block directly displays Google reviews
$review_source = 'google';
if ( function_exists( 'get_field' ) ) {
	$rs = get_field( 'review_source' );
	if ( ! empty( $rs ) ) {
		$review_source = is_array( $rs ) ? ( $rs['value'] ?? reset( $rs ) ) : $rs;
	}
}
if ( empty( $rs ) && ! empty( $block_data['review_source'] ) ) {
	$review_source = $block_data['review_source'];
}

$google_place_id = function_exists( 'get_field' ) ? get_field( 'google_place_id' ) : ( $block_data['google_place_id'] ?? '' );
$google_place_id = dino_get_google_place_id( $google_place_id );

$google_api_key = function_exists( 'get_field' ) ? get_field( 'google_api_key' ) : ( $block_data['google_api_key'] ?? '' );
$google_api_key = dino_get_google_places_api_key( $google_api_key );

$min_rating  = function_exists( 'get_field' ) ? ( get_field( 'min_rating' ) ?: 4 ) : ( $block_data['min_rating'] ?? 4 );
$max_reviews = function_exists( 'get_field' ) ? ( get_field( 'max_reviews' ) ?: 6 ) : ( $block_data['max_reviews'] ?? 6 );
$max_reviews = max( 1, min( 20, (int) $max_reviews ) );

$show_google_badge  = true;
$show_summary_badge = true;
$show_review_button = true;

if ( function_exists( 'get_field' ) ) {
	$sgb = get_field( 'show_google_badge' );
	if ( null !== $sgb && '' !== $sgb ) {
		$show_google_badge = (bool) $sgb;
	}
	$ssb = get_field( 'show_summary_badge' );
	if ( null !== $ssb && '' !== $ssb ) {
		$show_summary_badge = (bool) $ssb;
	}
	$srb = get_field( 'show_review_button' );
	if ( null !== $srb && '' !== $srb ) {
		$show_review_button = (bool) $srb;
	}
}

// 5. Extract Manual Testimonials Repeater
$manual_testimonials = array();

if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'testimonials', 'testimonial_list', 'reviews', 'quotes' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$rating    = get_sub_field( 'rating' );
				$content   = get_sub_field( 'content' ) ?: ( get_sub_field( 'review' ) ?: get_sub_field( 'testimonial' ) );
				$author    = get_sub_field( 'author' ) ?: ( get_sub_field( 'author_name' ) ?: get_sub_field( 'name' ) );
				$rel_time  = get_sub_field( 'relative_time' ) ?: '';

				if ( ! empty( $content ) || ! empty( $author ) ) {
					$manual_testimonials[] = array(
						'rating'        => ! empty( $rating ) ? (int) $rating : 5,
						'content'       => $content,
						'author'        => $author,
						'source'        => 'manual',
						'relative_time' => $rel_time,
						'profile_photo' => '',
						'author_url'    => '',
					);
				}
			}
			break;
		}
	}
}

// Raw block_data fallback for manual testimonials
if ( empty( $manual_testimonials ) && ! empty( $block_data ) ) {
	$test_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(testimonials|testimonial_list|reviews)$/', $k ) && is_numeric( $v ) ) {
			$test_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $test_count; $i++ ) {
		$rating   = 5;
		$content  = '';
		$author   = '';
		$rel_time = '';
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(testimonials|testimonial_list|reviews)_{$i}_rating$/", $k ) ) {
				$rating = (int) $v;
			}
			if ( preg_match( "/(testimonials|testimonial_list|reviews)_{$i}_(content|review|testimonial)$/", $k ) ) {
				$content = $v;
			}
			if ( preg_match( "/(testimonials|testimonial_list|reviews)_{$i}_(author|author_name|name)$/", $k ) ) {
				$author = $v;
			}
			if ( preg_match( "/(testimonials|testimonial_list|reviews)_{$i}_relative_time$/", $k ) ) {
				$rel_time = $v;
			}
		}
		if ( ! empty( $content ) || ! empty( $author ) ) {
			$manual_testimonials[] = array(
				'rating'        => $rating > 0 ? $rating : 5,
				'content'       => $content,
				'author'        => $author,
				'source'        => 'manual',
				'relative_time' => $rel_time,
				'profile_photo' => '',
				'author_url'    => '',
			);
		}
	}
}

// 6. Fetch Google Reviews
$google_data    = array();
$google_reviews = array();

if ( 'manual' !== $review_source ) {
	$cache_hours = function_exists( 'get_field' ) ? ( (int) get_field( 'google_reviews_cache_hours', 'option' ) ?: 24 ) : 24;
	$google_data = dino_fetch_google_reviews(
		array(
			'place_id'    => $google_place_id,
			'api_key'     => $google_api_key,
			'min_rating'  => $min_rating,
			'max_reviews' => $max_reviews,
			'cache_hours' => $cache_hours,
		)
	);

	if ( ! empty( $google_data['reviews'] ) ) {
		$google_reviews = $google_data['reviews'];
	}
}

// 7. Assemble Unified Testimonials List
$testimonials_list = array();
if ( 'google' === $review_source ) {
	$testimonials_list = ! empty( $google_reviews ) ? $google_reviews : $manual_testimonials;
} elseif ( 'manual' === $review_source ) {
	$testimonials_list = $manual_testimonials;
} else {
	// 'both': Merge and interleave Google reviews and manual testimonials
	if ( ! empty( $google_reviews ) && ! empty( $manual_testimonials ) ) {
		$g_idx = 0;
		$m_idx = 0;
		$g_cnt = count( $google_reviews );
		$m_cnt = count( $manual_testimonials );

		while ( count( $testimonials_list ) < $max_reviews && ( $g_idx < $g_cnt || $m_idx < $m_cnt ) ) {
			if ( $g_idx < $g_cnt ) {
				$testimonials_list[] = $google_reviews[ $g_idx++ ];
			}
			if ( count( $testimonials_list ) >= $max_reviews ) {
				break;
			}
			if ( $m_idx < $m_cnt ) {
				$testimonials_list[] = $manual_testimonials[ $m_idx++ ];
			}
		}
	} elseif ( ! empty( $google_reviews ) ) {
		$testimonials_list = $google_reviews;
	} else {
		$testimonials_list = $manual_testimonials;
	}
}

// Trim to max_reviews limit
if ( count( $testimonials_list ) > $max_reviews ) {
	$testimonials_list = array_slice( $testimonials_list, 0, $max_reviews );
}

$carousel_id = 'carousel-' . preg_replace( '/[^a-zA-Z0-9_-]/', '', $block_id );
$has_google  = false;
foreach ( $testimonials_list as $t ) {
	if ( ! empty( $t['source'] ) && 'google' === $t['source'] ) {
		$has_google = true;
		break;
	}
}

$overall_rating = ! empty( $google_data['rating'] ) ? number_format( (float) $google_data['rating'], 1 ) : '4.1';
$total_ratings  = ! empty( $google_data['user_ratings_total'] ) ? (int) $google_data['user_ratings_total'] : 1306;
$place_url      = ! empty( $google_data['place_url'] ) ? $google_data['place_url'] : dino_get_google_place_url( $google_place_id );
$write_rev_url  = dino_get_google_write_review_url( $google_place_id );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-down" data-aos-duration="800">
	<div class="container">
		<?php if ( ! $is_white_bg && ! empty( $dino_img_url ) ) : ?>
			<div class="test-dino">
				<img src="<?php echo esc_url( $dino_img_url ); ?>" alt="<?php echo esc_attr( $dino_img_alt ); ?>" loading="lazy" />
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd mb-2 text-center text-uppercase"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>

		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $show_summary_badge && $has_google ) : ?>
			<div class="d-flex justify-content-center mb-4">
				<a href="<?php echo esc_url( $place_url ); ?>" target="_blank" rel="noopener noreferrer" class="google-summary-pill d-inline-flex align-items-center gap-2" title="<?php esc_attr_e( 'View Dinosaur World on Google Maps', 'dino-world-blocks' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" class="google-logo-svg" aria-hidden="true">
						<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
						<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
						<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
						<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
					</svg>
					<span class="rating-val"><strong><?php echo esc_html( $overall_rating ); ?></strong></span>
					<span class="rating-stars" aria-label="<?php echo esc_attr( $overall_rating ); ?> stars">★★★★★</span>
					<span class="rating-count">on Google (<?php echo esc_html( number_format_i18n( $total_ratings ) ); ?>+ reviews)</span>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $testimonials_list ) ) : ?>
			<div id="<?php echo esc_attr( $carousel_id ); ?>" class="splide dino-testimonials-splide" aria-label="<?php echo esc_attr( $heading ?: 'Testimonials' ); ?>">
				<div class="splide__track">
					<ul class="splide__list">
						<?php foreach ( $testimonials_list as $item ) : ?>
							<?php
							$is_google_card = ! empty( $item['source'] ) && 'google' === $item['source'];
							?>
							<li class="splide__slide">
								<div class="<?php echo esc_attr( $card_class ); ?> d-flex flex-column h-100">
									<div class="card-header-row d-flex align-items-center justify-content-between mb-2">
										<div class="card-star d-flex align-items-center gap-1">
											<?php
											$star_count = ( ! empty( $item['rating'] ) && (int) $item['rating'] > 0 ) ? (int) $item['rating'] : 5;
											for ( $s = 0; $s < $star_count; $s++ ) :
											?>
												<div class="star-svg">
													<svg width="18" height="17" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M3.825 19L5.45 11.975L0 7.25L7.2 6.625L10 0L12.8 6.625L20 7.25L14.55 11.975L16.175 19L10 15.275L3.825 19Z" fill="#CD8143" />
													</svg>
												</div>
											<?php endfor; ?>
										</div>

										<?php if ( $is_google_card && $show_google_badge ) : ?>
											<span class="google-card-badge d-inline-flex align-items-center gap-1" title="<?php esc_attr_e( 'Verified Google Review', 'dino-world-blocks' ); ?>">
												<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
													<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
													<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
													<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
													<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
												</svg>
												<span class="badge-label">Google</span>
											</span>
										<?php endif; ?>
									</div>

									<?php if ( ! empty( $item['content'] ) ) : ?>
										<div class="card-cntn flex-grow-1">
											<p><?php echo wp_kses_post( $item['content'] ); ?></p>
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $item['author'] ) ) : ?>
										<div class="test-name mt-auto pt-2 d-flex align-items-center justify-content-between">
											<div class="author-meta d-flex align-items-center gap-2">
												<?php if ( ! empty( $item['profile_photo'] ) ) : ?>
													<img src="<?php echo esc_url( $item['profile_photo'] ); ?>" alt="<?php echo esc_attr( $item['author'] ); ?>" class="reviewer-avatar rounded-circle" width="36" height="36" loading="lazy" />
												<?php else : ?>
													<div class="reviewer-avatar-fallback rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#EAF3DC;color:#233400;font-weight:700;font-size:14px;border:1.5px solid #d5e5bf;" aria-hidden="true">
														<?php echo esc_html( mb_substr( $item['author'] ?? 'D', 0, 1 ) ); ?>
													</div>
												<?php endif; ?>
												<div>
													<?php if ( ! empty( $item['author_url'] ) ) : ?>
														<a href="<?php echo esc_url( $item['author_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="author-link" title="<?php echo esc_attr( sprintf( __( 'View %s on Google Maps', 'dino-world-blocks' ), $item['author'] ) ); ?>">
															<p class="mb-0 author-text"><?php echo esc_html( $item['author'] ); ?></p>
														</a>
													<?php else : ?>
														<p class="mb-0 author-text"><?php echo esc_html( $item['author'] ); ?></p>
													<?php endif; ?>

													<?php if ( ! empty( $item['relative_time'] ) ) : ?>
														<span class="review-date d-block"><?php echo esc_html( $item['relative_time'] ); ?></span>
													<?php endif; ?>
												</div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

			<?php if ( $has_google && $show_review_button ) : ?>
				<div class="d-flex justify-content-center mt-4 pt-2">
					<a href="<?php echo esc_url( $write_rev_url ); ?>" target="_blank" rel="noopener noreferrer" class="primary-btn d-inline-flex align-items-center gap-2">
						<span><?php esc_html_e( 'Review Us on Google', 'dino-world-blocks' ); ?></span>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
							<polyline points="15 3 21 3 21 9"></polyline>
							<line x1="10" y1="14" x2="21" y2="3"></line>
						</svg>
					</a>
				</div>
			<?php endif; ?>

			<script>
				(function() {
					function initDinoTestimonialsSlider_<?php echo esc_js( str_replace( '-', '_', $carousel_id ) ); ?>() {
						var el = document.getElementById('<?php echo esc_js( $carousel_id ); ?>');
						if (!el || typeof Splide === 'undefined') return;
						if (el.classList.contains('is-active')) return;

						new Splide(el, {
							type: 'loop',
							perPage: 3,
							gap: '1.25rem',
							pagination: false,
							arrows: true,
							autoplay: true,
							interval: 5000,
							pauseOnHover: true,
							pauseOnFocus: true,
							breakpoints: {
								992: { perPage: 2, gap: '1rem' },
								768: { perPage: 1, gap: '.75rem' }
							}
						}).mount();
					}

					if (document.readyState === 'loading') {
						document.addEventListener('DOMContentLoaded', initDinoTestimonialsSlider_<?php echo esc_js( str_replace( '-', '_', $carousel_id ) ); ?>);
					} else {
						initDinoTestimonialsSlider_<?php echo esc_js( str_replace( '-', '_', $carousel_id ) ); ?>();
					}

					// Support ACF Block Preview inside the Gutenberg editor
					if (window.acf) {
						window.acf.addAction('render_block_preview/type=dino-world/testimonials', function() {
							setTimeout(initDinoTestimonialsSlider_<?php echo esc_js( str_replace( '-', '_', $carousel_id ) ); ?>, 150);
						});
					}
				})();
			</script>
		<?php endif; ?>
	</div>
</section>
