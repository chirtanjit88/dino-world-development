<?php
/**
 * Block Name: Excursion Highlights
 *
 * Powered by ACF Block Fields:
 * - heading (Text)
 * - cards (Repeater: image [Image], title [Text], description [Textarea])
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'excursion-high-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'excursion-high sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading
$heading = '';
if ( function_exists( 'get_field' ) ) {
	$heading = get_field( 'heading' ) ?: ( get_field( 'title' ) ?: get_field( 'section_title' ) );
}

if ( empty( $heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title|section_title)$/', $k ) ) {
			$heading = $v;
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

// 2. Cards / Highlights List
$cards = array();

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'cards', 'highlights', 'attractions', 'items', 'excursion_cards' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$img_val = get_sub_field( 'image' ) ?: ( get_sub_field( 'img' ) ?: get_sub_field( 'photo' ) );
				$title   = get_sub_field( 'title' ) ?: ( get_sub_field( 'heading' ) ?: get_sub_field( 'name' ) );
				$desc    = get_sub_field( 'description' ) ?: ( get_sub_field( 'text' ) ?: get_sub_field( 'content' ) );

				$img_data = $parse_image( $img_val );

				if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $img_data['url'] ) ) {
					$cards[] = array(
						'image'       => $img_data['url'],
						'alt'         => $img_data['alt'],
						'title'       => $title,
						'description' => $desc,
					);
				}
			}
			break;
		}
	}
}

// Method B: ACF get_field array
if ( empty( $cards ) && function_exists( 'get_field' ) ) {
	$rep_keys = array( 'cards', 'highlights', 'attractions', 'items', 'excursion_cards' );
	foreach ( $rep_keys as $rk ) {
		$g_cards = get_field( $rk );
		if ( ! empty( $g_cards ) && is_array( $g_cards ) ) {
			foreach ( $g_cards as $item ) {
				if ( is_array( $item ) ) {
					$img_val = ! empty( $item['image'] ) ? $item['image'] : ( ! empty( $item['img'] ) ? $item['img'] : '' );
					$title   = ! empty( $item['title'] ) ? $item['title'] : ( ! empty( $item['heading'] ) ? $item['heading'] : '' );
					$desc    = ! empty( $item['description'] ) ? $item['description'] : ( ! empty( $item['text'] ) ? $item['text'] : '' );

					$img_data = $parse_image( $img_val );

					if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $img_data['url'] ) ) {
						$cards[] = array(
							'image'       => $img_data['url'],
							'alt'         => $img_data['alt'],
							'title'       => $title,
							'description' => $desc,
						);
					}
				}
			}
			if ( ! empty( $cards ) ) {
				break;
			}
		}
	}
}

// Method C: Raw block_data fallback
if ( empty( $cards ) && ! empty( $block_data ) ) {
	$count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(cards|highlights|attractions|items)$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$img_val = '';
		$title   = '';
		$desc    = '';
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(cards|highlights|attractions|items)_{$i}_(image|img|photo)$/", $k ) ) {
				$img_val = $v;
			}
			if ( preg_match( "/(cards|highlights|attractions|items)_{$i}_(title|heading|name)$/", $k ) ) {
				$title = $v;
			}
			if ( preg_match( "/(cards|highlights|attractions|items)_{$i}_(description|text|content)$/", $k ) ) {
				$desc = $v;
			}
		}

		$img_data = $parse_image( $img_val );

		if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $img_data['url'] ) ) {
			$cards[] = array(
				'image'       => $img_data['url'],
				'alt'         => $img_data['alt'],
				'title'       => $title,
				'description' => $desc,
			);
		}
	}
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="row">
				<?php
				$total_cards = count( $cards );
				if ( 4 === $total_cards ) {
					$col_class = 'col-lg-3 col-sm-6';
				} elseif ( 3 === $total_cards ) {
					$col_class = 'col-lg-4 col-sm-6';
				} elseif ( 2 === $total_cards ) {
					$col_class = 'col-lg-6 col-sm-6';
				} elseif ( 5 === $total_cards ) {
					$col_class = 'col-20';
				} else {
					$col_class = 'col-lg-3 col-sm-6';
				}

				foreach ( $cards as $index => $item ) :
					$aos_anim = ( $index < ceil( $total_cards / 2 ) ) ? 'zoom-in-right' : 'zoom-in-left';
					?>
					<div class="<?php echo esc_attr( $col_class ); ?>" data-aos="<?php echo esc_attr( $aos_anim ); ?>" data-aos-delay="<?php echo esc_attr( $index * 100 ); ?>">
						<div class="xh-card">
							<?php if ( ! empty( $item['image'] ) ) : ?>
								<div class="xh-img">
									<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( ! empty( $item['alt'] ) ? $item['alt'] : ( ! empty( $item['title'] ) ? $item['title'] : 'Excursion Highlight' ) ); ?>" />
								</div>
							<?php endif; ?>

							<div class="xh-txt">
								<?php if ( ! empty( $item['title'] ) ) : ?>
									<div class="xh-hd">
										<p><?php echo esc_html( $item['title'] ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $item['description'] ) ) : ?>
									<div class="xh-para">
										<?php if ( strpos( $item['description'], '<p>' ) !== false ) : ?>
											<?php echo wp_kses_post( $item['description'] ); ?>
										<?php else : ?>
											<p><?php echo wp_kses_post( nl2br( $item['description'] ) ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
