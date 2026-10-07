<?php
/**
 * Block Name: Our Story
 *
 * Configured ACF / SCF Fields:
 * - heading (Text) - Section heading (e.g., "OUR STORY")
 * - content (WYSIWYG Editor / Textarea) - Main narrative text
 * - image (Image) - Main section image
 * - image_position (Radio Button / Select) - "left" or "right" (Default: "left")
 * - cards (Repeater) - Highlight badge cards
 *   - icon (Image) - Card icon badge
 *   - text (Text) - Card text / title (e.g., "A FAMILY OWNED AUSTRALIAN BUSINESS")
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'our-story-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'our-story sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading & Content
$heading = '';
$story_content = '';
if ( function_exists( 'get_field' ) ) {
	$heading       = get_field( 'heading' ) ?: ( get_field( 'title' ) ?: get_field( 'section_title' ) );
	$story_content = get_field( 'content' ) ?: ( get_field( 'description' ) ?: ( get_field( 'story_text' ) ?: get_field( 'text' ) ) );
}

if ( empty( $heading ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title|section_title)$/', $k ) && is_string( $v ) ) {
			$heading = $v;
			break;
		}
	}
}

if ( empty( $story_content ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(content|description|story_text|text)$/', $k ) && is_string( $v ) ) {
			$story_content = $v;
			break;
		}
	}
}

// Helper to parse image URL & alt
$parse_image = function( $val ) {
	if ( empty( $val ) ) {
		return array( 'url' => '', 'alt' => '' );
	}
	if ( is_array( $val ) ) {
		return array(
			'url' => ! empty( $val['url'] ) ? $val['url'] : '',
			'alt' => ! empty( $val['alt'] ) ? $val['alt'] : '',
		);
	}
	if ( is_numeric( $val ) ) {
		return array(
			'url' => wp_get_attachment_image_url( (int) $val, 'full' ) ?: '',
			'alt' => get_post_meta( (int) $val, '_wp_attachment_image_alt', true ) ?: '',
		);
	}
	if ( is_string( $val ) ) {
		return array( 'url' => $val, 'alt' => '' );
	}
	return array( 'url' => '', 'alt' => '' );
};

// 2. Main Image
$main_image = array( 'url' => '', 'alt' => '' );
if ( function_exists( 'get_field' ) ) {
	$raw_img = get_field( 'image' ) ?: ( get_field( 'story_image' ) ?: get_field( 'main_image' ) );
	$main_image = $parse_image( $raw_img );
}

if ( empty( $main_image['url'] ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/^(image|story_image|main_image)$/', $k ) ) {
			$main_image = $parse_image( $v );
			if ( ! empty( $main_image['url'] ) ) break;
		}
	}
}

// 3. Image Position (Left or Right)
$image_position = 'left';
if ( function_exists( 'get_field' ) ) {
	$pos_val = get_field( 'image_position' ) ?: ( get_field( 'position' ) ?: get_field( 'layout' ) );
	if ( ! empty( $pos_val ) ) {
		$image_position = strtolower( trim( $pos_val ) );
	}
}

if ( empty( $pos_val ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(image_position|position|layout)$/', $k ) && is_string( $v ) ) {
			$image_position = strtolower( trim( $v ) );
			break;
		}
	}
}

$is_right = ( 'right' === $image_position );
$img_col_class = $is_right ? 'col-xl-6 order-xl-2' : 'col-xl-6 order-xl-1';
$txt_col_class = $is_right ? 'col-xl-6 order-xl-1' : 'col-xl-6 order-xl-2';

// 4. Cards Repeater
$cards = array();

// Method A: have_rows
if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'cards', 'features', 'badges', 'items', 'highlight_cards' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$c_icon = $parse_image( get_sub_field( 'icon' ) ?: ( get_sub_field( 'image' ) ?: get_sub_field( 'badge' ) ) );
				$c_text = get_sub_field( 'text' ) ?: ( get_sub_field( 'title' ) ?: ( get_sub_field( 'card_text' ) ?: get_sub_field( 'heading' ) ) );

				if ( ! empty( $c_icon['url'] ) || ! empty( $c_text ) ) {
					$cards[] = array(
						'icon' => $c_icon,
						'text' => $c_text,
					);
				}
			}
			break;
		}
	}
}

// Method B: get_field array
if ( empty( $cards ) && function_exists( 'get_field' ) ) {
	$rep_keys = array( 'cards', 'features', 'badges', 'items', 'highlight_cards' );
	foreach ( $rep_keys as $rk ) {
		$raw_cards = get_field( $rk );
		if ( ! empty( $raw_cards ) && is_array( $raw_cards ) ) {
			foreach ( $raw_cards as $c ) {
				if ( is_array( $c ) ) {
					$c_icon = $parse_image( ! empty( $c['icon'] ) ? $c['icon'] : ( ! empty( $c['image'] ) ? $c['image'] : '' ) );
					$c_text = ! empty( $c['text'] ) ? $c['text'] : ( ! empty( $c['title'] ) ? $c['title'] : ( ! empty( $c['card_text'] ) ? $c['card_text'] : '' ) );

					if ( ! empty( $c_icon['url'] ) || ! empty( $c_text ) ) {
						$cards[] = array(
							'icon' => $c_icon,
							'text' => $c_text,
						);
					}
				}
			}
			if ( ! empty( $cards ) ) break;
		}
	}
}

// Method C: Raw block_data fallback
if ( empty( $cards ) && ! empty( $block_data ) ) {
	$count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/^(cards|features|badges|items)$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$c_icon = array( 'url' => '', 'alt' => '' );
		$c_text = '';

		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/^(cards|features|badges|items)_{$i}_(icon|image|badge)$/", $k ) ) {
				$c_icon = $parse_image( $v );
			}
			if ( preg_match( "/^(cards|features|badges|items)_{$i}_(text|title|card_text|heading)$/", $k ) && is_string( $v ) ) {
				$c_text = $v;
			}
		}

		if ( ! empty( $c_icon['url'] ) || ! empty( $c_text ) ) {
			$cards[] = array(
				'icon' => $c_icon,
				'text' => $c_text,
			);
		}
	}
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="<?php echo esc_attr( $is_right ? 'fade-right' : 'fade-left' ); ?>">
	<div class="container">
		<div class="row align-items-center">
			<?php if ( ! empty( $main_image['url'] ) ) : ?>
				<div class="<?php echo esc_attr( $img_col_class ); ?>">
					<div class="our-img">
						<img src="<?php echo esc_url( $main_image['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $main_image['alt'] ) ? $main_image['alt'] : ( ! empty( $heading ) ? $heading : 'Our Story' ) ); ?>" />
					</div>
				</div>
			<?php endif; ?>

			<div class="<?php echo esc_attr( empty( $main_image['url'] ) ? 'col-xl-12' : $txt_col_class ); ?>">
				<div class="our-txt-cntn">
					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $story_content ) ) : ?>
						<div class="our-para">
							<?php if ( strpos( $story_content, '<p>' ) !== false ) : ?>
								<?php echo wp_kses_post( $story_content ); ?>
							<?php else : ?>
								<p><?php echo wp_kses_post( nl2br( $story_content ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $cards ) ) : ?>
					<?php
					$total_cards = count( $cards );
					if ( 3 === $total_cards ) {
						$col_class = 'col-sm-4';
					} elseif ( 2 === $total_cards ) {
						$col_class = 'col-sm-6';
					} elseif ( 4 === $total_cards ) {
						$col_class = 'col-sm-3 col-6';
					} else {
						$col_class = 'col-sm-4';
					}
					?>
					<div class="row">
						<?php foreach ( $cards as $card_item ) : ?>
							<div class="<?php echo esc_attr( $col_class ); ?>">
								<div class="our-card">
									<?php if ( ! empty( $card_item['icon']['url'] ) ) : ?>
										<div class="card-img">
											<img src="<?php echo esc_url( $card_item['icon']['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $card_item['icon']['alt'] ) ? $card_item['icon']['alt'] : ( ! empty( $card_item['text'] ) ? $card_item['text'] : '' ) ); ?>" />
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $card_item['text'] ) ) : ?>
										<div class="our-card-txt">
											<p><?php echo wp_kses_post( nl2br( $card_item['text'] ) ); ?></p>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
