<?php
/**
 * Block Name: Testimonials Carousel
 *
 * Powered by ACF Block Fields:
 * - background_color (Select: green / white)
 * - short_heading / tagline (Text)
 * - heading (Text)
 * - dino_image (Image)
 * - testimonials (Repeater: rating [Number], content [Textarea], author [Text])
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

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

$title_class = $is_white_bg ? 'title-2 mb-4 text-center' : 'title-2 mb-4 text-center text-white';
$card_class  = $is_white_bg ? 'testa-card' : 'test-card';

// 2. Text Fields
$short_heading = '';
$heading       = '';

if ( function_exists( 'get_field' ) ) {
	$short_heading = get_field( 'short_heading' ) ?: ( get_field( 'tagline' ) ?: get_field( 'sub_heading' ) );
	$heading       = get_field( 'heading' ) ?: get_field( 'title' );
}

// Fallback for text fields
if ( empty( $short_heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(short_heading|tagline|sub_heading)$/', $k ) ) {
			$short_heading = $v;
			break;
		}
	}
}
if ( empty( $heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title)$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

// 3. Decorative Dino Image (used on green design)
$dino_img_url = '';
$dino_img_alt = '';
if ( function_exists( 'get_field' ) ) {
	$dino_img = get_field( 'dino_image' ) ?: get_field( 'testimonial_dino' );
	if ( ! empty( $dino_img ) ) {
		if ( is_array( $dino_img ) ) {
			$dino_img_url = ! empty( $dino_img['url'] ) ? $dino_img['url'] : '';
			$dino_img_alt = ! empty( $dino_img['alt'] ) ? $dino_img['alt'] : '';
		} elseif ( is_numeric( $dino_img ) ) {
			$dino_img_url = wp_get_attachment_image_url( (int) $dino_img, 'full' );
			$dino_img_alt = get_post_meta( (int) $dino_img, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $dino_img ) ) {
			$dino_img_url = $dino_img;
		}
	}
}
if ( empty( $dino_img_url ) ) {
	$dino_img_url = get_template_directory_uri() . '/assets/image/testimonial-dino.png';
}

// 4. Extract Testimonials Repeater
$testimonials_list = array();

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'testimonials', 'testimonial_list', 'reviews', 'quotes' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$rating  = get_sub_field( 'rating' );
				$content = get_sub_field( 'content' ) ?: ( get_sub_field( 'review' ) ?: get_sub_field( 'testimonial' ) );
				$author  = get_sub_field( 'author' ) ?: ( get_sub_field( 'author_name' ) ?: get_sub_field( 'name' ) );

				if ( ! empty( $content ) || ! empty( $author ) ) {
					$testimonials_list[] = array(
						'rating'  => ! empty( $rating ) ? (int) $rating : 5,
						'content' => $content,
						'author'  => $author,
					);
				}
			}
			break;
		}
	}
}

// Method B: Raw block_data fallback
if ( empty( $testimonials_list ) && ! empty( $block_data ) ) {
	$test_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(testimonials|testimonial_list|reviews)$/', $k ) && is_numeric( $v ) ) {
			$test_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $test_count; $i++ ) {
		$rating  = 5;
		$content = '';
		$author  = '';
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
		}
		if ( ! empty( $content ) || ! empty( $author ) ) {
			$testimonials_list[] = array(
				'rating'  => $rating > 0 ? $rating : 5,
				'content' => $content,
				'author'  => $author,
			);
		}
	}
}

$carousel_id = 'carousel-' . preg_replace( '/[^a-zA-Z0-9_-]/', '', $block_id );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-down" data-aos-duration="800">
	<div class="container">
		<?php if ( ! $is_white_bg && ! empty( $dino_img_url ) ) : ?>
			<div class="test-dino">
				<img src="<?php echo esc_url( $dino_img_url ); ?>" alt="<?php echo esc_attr( $dino_img_alt ); ?>" />
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd mb-2 text-center text-uppercase"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>

		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $testimonials_list ) ) : ?>
			<div id="<?php echo esc_attr( $carousel_id ); ?>" class="splide" aria-label="<?php echo esc_attr( $heading ?: 'Testimonials' ); ?>">
				<div class="splide__track">
					<ul class="splide__list">
						<?php foreach ( $testimonials_list as $item ) : ?>
							<li class="splide__slide">
								<div class="<?php echo esc_attr( $card_class ); ?>">
									<div class="card-star d-flex align-items-center gap-1">
										<?php
										$star_count = ( ! empty( $item['rating'] ) && (int) $item['rating'] > 0 ) ? (int) $item['rating'] : 5;
										for ( $s = 0; $s < $star_count; $s++ ) :
										?>
											<div class="star-svg">
												<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M3.825 19L5.45 11.975L0 7.25L7.2 6.625L10 0L12.8 6.625L20 7.25L14.55 11.975L16.175 19L10 15.275L3.825 19Z" fill="#CD8143" />
												</svg>
											</div>
										<?php endfor; ?>
									</div>

									<?php if ( ! empty( $item['content'] ) ) : ?>
										<div class="card-cntn">
											<p><?php echo wp_kses_post( $item['content'] ); ?></p>
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $item['author'] ) ) : ?>
										<div class="test-name">
											<p><?php echo esc_html( $item['author'] ); ?></p>
										</div>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function() {
					var el = document.getElementById('<?php echo esc_js( $carousel_id ); ?>');
					if (typeof Splide !== 'undefined' && el && !el.classList.contains('is-active')) {
						new Splide(el, {
							perPage: 3,
							gap: '1rem',
							pagination: true,
							arrows: true,
							breakpoints: {
								992: { perPage: 2, gap: '.7rem' },
								768: { perPage: 1, gap: '.7rem' }
							}
						}).mount();
					}
				});
			</script>
		<?php endif; ?>
	</div>
</section>
