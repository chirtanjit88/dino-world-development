<?php
/**
 * Block Name: Adventure Features
 *
 * Powered by ACF Block Fields:
 * - heading (Text)
 * - description (Textarea / WYSIWYG)
 * - features (Repeater: icon [Image], title [Text])
 * - image (Image)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'adventure-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'adventure-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading & Description
$heading     = '';
$description = '';

if ( function_exists( 'get_field' ) ) {
	$heading     = get_field( 'heading' ) ?: get_field( 'title' );
	$description = get_field( 'description' ) ?: ( get_field( 'content' ) ?: get_field( 'para' ) );
}

// Fallbacks from raw block data
if ( empty( $heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title)$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

if ( empty( $description ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(description|content|para)$/', $k ) ) {
			$description = $v;
			break;
		}
	}
}

// 2. Extract Features Repeater
$features = array();

if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'features', 'highlights', 'cards', 'feature_list' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$f_title = get_sub_field( 'title' ) ?: ( get_sub_field( 'text' ) ?: get_sub_field( 'heading' ) );
				$f_icon  = get_sub_field( 'icon' ) ?: ( get_sub_field( 'image' ) ?: get_sub_field( 'svg' ) );

				$f_icon_url = '';
				if ( ! empty( $f_icon ) ) {
					if ( is_array( $f_icon ) ) {
						$f_icon_url = ! empty( $f_icon['url'] ) ? $f_icon['url'] : '';
					} elseif ( is_numeric( $f_icon ) ) {
						$f_icon_url = wp_get_attachment_image_url( (int) $f_icon, 'full' );
					} elseif ( is_string( $f_icon ) ) {
						$f_icon_url = $f_icon;
					}
				}

				if ( ! empty( $f_title ) || ! empty( $f_icon_url ) ) {
					$features[] = array(
						'title' => $f_title,
						'icon'  => $f_icon_url,
					);
				}
			}
			break;
		}
	}
}

// Fallback for features from block_data
if ( empty( $features ) && ! empty( $block_data ) ) {
	$feat_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(features|highlights|cards)$/', $k ) && is_numeric( $v ) ) {
			$feat_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $feat_count; $i++ ) {
		$f_title = '';
		$f_icon  = null;
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(features|highlights|cards)_{$i}_(title|text|heading)$/", $k ) ) {
				$f_title = $v;
			}
			if ( preg_match( "/(features|highlights|cards)_{$i}_(icon|image|svg)$/", $k ) ) {
				$f_icon = $v;
			}
		}

		$f_icon_url = '';
		if ( ! empty( $f_icon ) ) {
			if ( is_numeric( $f_icon ) ) {
				$f_icon_url = wp_get_attachment_image_url( (int) $f_icon, 'full' );
			} elseif ( is_array( $f_icon ) && ! empty( $f_icon['url'] ) ) {
				$f_icon_url = $f_icon['url'];
			} elseif ( is_string( $f_icon ) ) {
				$f_icon_url = $f_icon;
			}
		}

		if ( ! empty( $f_title ) || ! empty( $f_icon_url ) ) {
			$features[] = array(
				'title' => $f_title,
				'icon'  => $f_icon_url,
			);
		}
	}
}

// 3. Right Side Main Image
$image_url = '';
$image_alt = '';

if ( function_exists( 'get_field' ) ) {
	$main_img = get_field( 'image' ) ?: ( get_field( 'main_image' ) ?: get_field( 'showcase_image' ) );
	if ( ! empty( $main_img ) ) {
		if ( is_array( $main_img ) ) {
			$image_url = ! empty( $main_img['url'] ) ? $main_img['url'] : '';
			$image_alt = ! empty( $main_img['alt'] ) ? $main_img['alt'] : '';
		} elseif ( is_numeric( $main_img ) ) {
			$image_url = wp_get_attachment_image_url( (int) $main_img, 'full' );
			$image_alt = get_post_meta( (int) $main_img, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $main_img ) ) {
			$image_url = $main_img;
		}
	}
}

// Fallback for image from block_data
if ( empty( $image_url ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) continue;
		if ( preg_match( '/(main_image|showcase_image|image)$/', $k ) ) {
			if ( is_numeric( $v ) ) {
				$image_url = wp_get_attachment_image_url( (int) $v, 'full' );
				$image_alt = get_post_meta( (int) $v, '_wp_attachment_image_alt', true );
			} elseif ( is_array( $v ) && ! empty( $v['url'] ) ) {
				$image_url = $v['url'];
				$image_alt = ! empty( $v['alt'] ) ? $v['alt'] : '';
			} elseif ( is_string( $v ) ) {
				$image_url = $v;
			}
			break;
		}
	}
}

if ( empty( $image_url ) ) {
	$image_url = get_template_directory_uri() . '/assets/image/adventure.png';
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-right">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-5">
				<div class="adv-cntn">
					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<div class="adv-para">
							<?php if ( strpos( $description, '<p>' ) !== false ) : ?>
								<?php echo wp_kses_post( $description ); ?>
							<?php else : ?>
								<p><?php echo wp_kses_post( nl2br( $description ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $features ) ) : ?>
						<div class="row">
							<?php foreach ( $features as $feat ) : ?>
								<div class="col-sm-4 col-4">
									<div class="our-card">
										<?php if ( ! empty( $feat['icon'] ) ) : ?>
											<div class="card-img">
												<img src="<?php echo esc_url( $feat['icon'] ); ?>" alt="<?php echo esc_attr( $feat['title'] ); ?>" />
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $feat['title'] ) ) : ?>
											<div class="oura-card-txt">
												<p><?php echo esc_html( $feat['title'] ); ?></p>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-lg-7">
				<?php if ( ! empty( $image_url ) ) : ?>
					<div class="ad-img">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
