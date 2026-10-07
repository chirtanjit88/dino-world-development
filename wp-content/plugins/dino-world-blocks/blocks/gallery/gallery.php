<?php
/**
 * Block Name: Gallery Preview / Masonry Gallery
 *
 * Powered by ACF Block Fields:
 * - short_heading / tagline (Text)
 * - heading (Text)
 * - gallery_images (Gallery or Repeater: image [Image])
 * - button (Link)
 *
 * Automatically balances and auto-arranges images across 4 masonry columns
 * based on image aspect ratio and height for optimal aesthetic balance.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id   = ! empty( $block['anchor'] ) ? $block['anchor'] : 'gallery-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Text Fields
$short_heading = '';
$heading       = '';

if ( function_exists( 'get_field' ) ) {
	$short_heading = get_field( 'short_heading' ) ?: ( get_field( 'tagline' ) ?: get_field( 'sub_heading' ) );
	$heading       = get_field( 'heading' ) ?: get_field( 'title' );
}

// Fallback for texts from block_data
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

// 2. Extract Gallery Images (Gallery field or Repeater) with dimensions & aspect ratios
$gallery_list = array();

$extract_img_item = function( $img_val ) {
	$url    = '';
	$alt    = '';
	$width  = 300.0;
	$height = 300.0;
	$aspect = 1.0;

	if ( is_array( $img_val ) ) {
		if ( ! empty( $img_val['url'] ) ) {
			$url = $img_val['url'];
			$alt = ! empty( $img_val['alt'] ) ? $img_val['alt'] : '';
			if ( ! empty( $img_val['width'] ) && ! empty( $img_val['height'] ) ) {
				$width  = (float) $img_val['width'];
				$height = (float) $img_val['height'];
				$aspect = $height / max( 1.0, $width );
			} elseif ( ! empty( $img_val['ID'] ) ) {
				$meta = wp_get_attachment_metadata( (int) $img_val['ID'] );
				if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$width  = (float) $meta['width'];
					$height = (float) $meta['height'];
					$aspect = $height / max( 1.0, $width );
				}
			}
		}
	} elseif ( is_numeric( $img_val ) ) {
		$attach_id = (int) $img_val;
		$url = wp_get_attachment_image_url( $attach_id, 'full' );
		$alt = get_post_meta( $attach_id, '_wp_attachment_image_alt', true );
		$meta = wp_get_attachment_metadata( $attach_id );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$width  = (float) $meta['width'];
			$height = (float) $meta['height'];
			$aspect = $height / max( 1.0, $width );
		}
	} elseif ( is_string( $img_val ) ) {
		$url = $img_val;
	}

	if ( empty( $url ) ) {
		return null;
	}

	return array(
		'url'    => $url,
		'alt'    => $alt,
		'width'  => $width,
		'height' => $height,
		'aspect' => $aspect,
	);
};

if ( function_exists( 'get_field' ) ) {
	$g_field = get_field( 'gallery_images' ) ?: ( get_field( 'gallery' ) ?: get_field( 'images' ) );
	if ( ! empty( $g_field ) && is_array( $g_field ) ) {
		foreach ( $g_field as $item ) {
			if ( is_array( $item ) && isset( $item['image'] ) ) {
				$parsed = $extract_img_item( $item['image'] );
			} else {
				$parsed = $extract_img_item( $item );
			}
			if ( $parsed ) {
				$gallery_list[] = $parsed;
			}
		}
	}
}

// Check have_rows for repeater format
if ( empty( $gallery_list ) && function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'gallery_images', 'gallery', 'images', 'photos' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$img_obj = get_sub_field( 'image' ) ?: get_sub_field( 'photo' );
				$parsed  = $extract_img_item( $img_obj );
				if ( $parsed ) {
					$gallery_list[] = $parsed;
				}
			}
			break;
		}
	}
}

// Fallback from raw block data
if ( empty( $gallery_list ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) continue;
		if ( preg_match( '/(gallery_images|gallery|images)$/', $k ) && is_array( $v ) ) {
			foreach ( $v as $item ) {
				$parsed = $extract_img_item( $item );
				if ( $parsed ) {
					$gallery_list[] = $parsed;
				}
			}
			break;
		}
	}
}

// 3. CTA Button
$btn_url    = '';
$btn_title  = '';
$btn_target = '_self';

if ( function_exists( 'get_field' ) ) {
	$btn = get_field( 'button' ) ?: get_field( 'gallery_button' );
	if ( ! empty( $btn ) ) {
		if ( is_array( $btn ) ) {
			$btn_url    = ! empty( $btn['url'] ) ? $btn['url'] : '';
			$btn_title  = ! empty( $btn['title'] ) ? $btn['title'] : '';
			$btn_target = ! empty( $btn['target'] ) ? $btn['target'] : '_self';
		} elseif ( is_string( $btn ) ) {
			$btn_url   = $btn;
			$btn_title = __( 'View Gallery', 'dino-world' );
		}
	}
}

if ( empty( $btn_url ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(button|gallery_button)$/', $k ) && is_array( $v ) ) {
			$btn_url    = ! empty( $v['url'] ) ? $v['url'] : '';
			$btn_title  = ! empty( $v['title'] ) ? $v['title'] : '';
			$btn_target = ! empty( $v['target'] ) ? $v['target'] : '_self';
			break;
		}
	}
}

$total_images = count( $gallery_list );

// Determine layout: Homepage 7-image 2-column layout vs 4-Column Masonry Gallery
$is_homepage_7_layout = ( $total_images === 7 && is_front_page() );

// Class names
if ( $is_homepage_7_layout ) {
	$class_name = 'gallery sec-padding';
} else {
	$class_name = 'memories-sec sec-padding1';
}

if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-right">
	<div class="container">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<div class="short-hd text-center"><?php echo esc_html( $short_heading ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $gallery_list ) ) : ?>
			<?php if ( $is_homepage_7_layout ) :
				// 7-Image Asymmetric 2-Column Layout (index.html reference)
				$left_images  = array_slice( $gallery_list, 0, 4 );
				$right_images = array_slice( $gallery_list, 4 );
				?>
				<div class="row">
					<div class="col-xl-6" data-aos="fade-right">
						<div class="row">
							<?php foreach ( $left_images as $img ) : ?>
								<div class="col-md-6">
									<div class="g-img">
										<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" />
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="col-xl-6" data-aos="fade-left">
						<div class="row">
							<div class="col-md-6">
								<div class="g-img">
									<img src="<?php echo esc_url( $right_images[0]['url'] ); ?>" alt="<?php echo esc_attr( $right_images[0]['alt'] ); ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="g-img">
									<img src="<?php echo esc_url( $right_images[1]['url'] ); ?>" alt="<?php echo esc_attr( $right_images[1]['alt'] ); ?>" />
								</div>
								<div class="g-img">
									<img src="<?php echo esc_url( $right_images[2]['url'] ); ?>" alt="<?php echo esc_attr( $right_images[2]['alt'] ); ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php else :
				// 4-Column Auto-Arranged Masonry Gallery Layout (atraction.html & gallery.html reference)
				$num_cols       = 4;
				$columns        = array_fill( 0, $num_cols, array() );
				$column_heights = array_fill( 0, $num_cols, 0.0 );

				// Sort descending by aspect ratio (tallest/portrait images first)
				$sorted_images = $gallery_list;
				usort( $sorted_images, function( $a, $b ) {
					$aspect_a = isset( $a['aspect'] ) ? (float) $a['aspect'] : 1.0;
					$aspect_b = isset( $b['aspect'] ) ? (float) $b['aspect'] : 1.0;
					if ( abs( $aspect_a - $aspect_b ) < 0.0001 ) {
						return 0;
					}
					return ( $aspect_a > $aspect_b ) ? -1 : 1;
				} );

				// Greedily assign each image to the column with the minimum accumulated height
				foreach ( $sorted_images as $img ) {
					$min_col    = 0;
					$min_height = $column_heights[0];
					for ( $c = 1; $c < $num_cols; $c++ ) {
						if ( $column_heights[ $c ] < $min_height ) {
							$min_height = $column_heights[ $c ];
							$min_col    = $c;
						}
					}
					$columns[ $min_col ][]     = $img;
					$aspect                    = isset( $img['aspect'] ) ? (float) $img['aspect'] : 1.0;
					$column_heights[ $min_col ] += $aspect + 0.08; // gap ratio offset
				}
				?>
				<div class="gallery-grid">
					<?php foreach ( $columns as $col_images ) : ?>
						<?php if ( ! empty( $col_images ) ) : ?>
							<div class="gallery-column">
								<?php foreach ( $col_images as $img ) : ?>
									<div class="gallery-item">
										<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" />
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $btn_url ) ) : ?>
				<div class="g-btn">
					<a class="orange-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
						<?php echo esc_html( ! empty( $btn_title ) ? $btn_title : __( 'View Gallery', 'dino-world' ) ); ?>
						<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15.7063 6.70859C16.0969 6.31797 16.0969 5.68359 15.7063 5.29297L10.7063 0.292969C10.3156 -0.0976562 9.68125 -0.0976562 9.29062 0.292969C8.9 0.683594 8.9 1.31797 9.29062 1.70859L12.5844 5.00234H1C0.446875 5.00234 0 5.44922 0 6.00234C0 6.55547 0.446875 7.00234 1 7.00234H12.5844L9.29062 10.2961C8.9 10.6867 8.9 11.3211 9.29062 11.7117C9.68125 12.1023 10.3156 12.1023 10.7063 11.7117L15.7063 6.71172V6.70859Z" fill="white" />
						</svg>
					</a>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
