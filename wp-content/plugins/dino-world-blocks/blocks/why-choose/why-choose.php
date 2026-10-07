<?php
/**
 * Block Name: Why Choose
 *
 * Powered by ACF Block Fields:
 * - heading (Text)
 * - cards (Repeater: icon [Image], title [Text], description [Textarea])
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'why-choose-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'why-choose sec-padding1';
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

// 2. Cards / Features List
$cards = array();

$parse_icon_url = function( $val ) {
	if ( empty( $val ) ) {
		return '';
	}
	if ( is_array( $val ) && ! empty( $val['url'] ) ) {
		return $val['url'];
	}
	if ( is_numeric( $val ) ) {
		$src = wp_get_attachment_image_url( (int) $val, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	if ( is_string( $val ) ) {
		return $val;
	}
	return '';
};

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'cards', 'features', 'items', 'why_choose_cards', 'feature_cards' );
	foreach ( $rep_keys as $rk ) {
		if ( have_rows( $rk ) ) {
			while ( have_rows( $rk ) ) {
				the_row();
				$icon_val = get_sub_field( 'icon' ) ?: ( get_sub_field( 'image' ) ?: get_sub_field( 'svg_icon' ) );
				$title    = get_sub_field( 'title' ) ?: ( get_sub_field( 'heading' ) ?: get_sub_field( 'name' ) );
				$desc     = get_sub_field( 'description' ) ?: ( get_sub_field( 'text' ) ?: get_sub_field( 'content' ) );

				if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $icon_val ) ) {
					$cards[] = array(
						'icon'        => $parse_icon_url( $icon_val ),
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
	$rep_keys = array( 'cards', 'features', 'items', 'why_choose_cards', 'feature_cards' );
	foreach ( $rep_keys as $rk ) {
		$g_cards = get_field( $rk );
		if ( ! empty( $g_cards ) && is_array( $g_cards ) ) {
			foreach ( $g_cards as $item ) {
				if ( is_array( $item ) ) {
					$icon_val = ! empty( $item['icon'] ) ? $item['icon'] : ( ! empty( $item['image'] ) ? $item['image'] : '' );
					$title    = ! empty( $item['title'] ) ? $item['title'] : ( ! empty( $item['heading'] ) ? $item['heading'] : '' );
					$desc     = ! empty( $item['description'] ) ? $item['description'] : ( ! empty( $item['text'] ) ? $item['text'] : '' );

					if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $icon_val ) ) {
						$cards[] = array(
							'icon'        => $parse_icon_url( $icon_val ),
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
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(cards|features|items)$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$icon_val = '';
		$title    = '';
		$desc     = '';
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(cards|features|items)_{$i}_(icon|image|svg_icon)$/", $k ) ) {
				$icon_val = $v;
			}
			if ( preg_match( "/(cards|features|items)_{$i}_(title|heading|name)$/", $k ) ) {
				$title = $v;
			}
			if ( preg_match( "/(cards|features|items)_{$i}_(description|text|content)$/", $k ) ) {
				$desc = $v;
			}
		}
		if ( ! empty( $title ) || ! empty( $desc ) || ! empty( $icon_val ) ) {
			$cards[] = array(
				'icon'        => $parse_icon_url( $icon_val ),
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
				if ( 5 === $total_cards ) {
					$col_class = 'col-20';
				} elseif ( 4 === $total_cards ) {
					$col_class = 'col-lg-3 col-md-6 col-sm-6';
				} elseif ( 3 === $total_cards ) {
					$col_class = 'col-lg-4 col-md-4 col-sm-6';
				} elseif ( 2 === $total_cards ) {
					$col_class = 'col-lg-6 col-md-6';
				} else {
					$col_class = 'col-20';
				}

				foreach ( $cards as $index => $item ) :
					?>
					<div class="<?php echo esc_attr( $col_class ); ?>" data-aos="fade-left" data-aos-delay="<?php echo esc_attr( $index * 100 ); ?>">
						<div class="why-card">
							<?php if ( ! empty( $item['icon'] ) ) : ?>
								<div class="wh-img">
									<img src="<?php echo esc_url( $item['icon'] ); ?>" alt="<?php echo esc_attr( ! empty( $item['title'] ) ? $item['title'] : 'Why Choose Feature' ); ?>" />
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['title'] ) ) : ?>
								<div class="wh-hd">
									<p><?php echo esc_html( $item['title'] ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['description'] ) ) : ?>
								<div class="wh-para">
									<?php if ( strpos( $item['description'], '<p>' ) !== false ) : ?>
										<?php echo wp_kses_post( $item['description'] ); ?>
									<?php else : ?>
										<p><?php echo wp_kses_post( nl2br( $item['description'] ) ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
