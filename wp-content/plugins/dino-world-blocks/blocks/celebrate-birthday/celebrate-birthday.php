<?php
/**
 * Block Name: Celebrate Birthday
 *
 * Configured ACF Fields:
 * - heading (Text)
 * - description (WYSIWYG Editor)
 * - showcase_images (Gallery)
 * - pricing_cards (Repeater)
 *   - card_title (Text)
 *   - card_subtitle (Text)
 *   - items (WYSIWYG Editor)
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'celebrate-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'celebrate-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading & Description
$heading     = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
$description = function_exists( 'get_field' ) ? get_field( 'description' ) : '';

// Fallbacks from raw block_data if get_field is empty
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $description ) && ! empty( $block_data['description'] ) ) {
	$description = $block_data['description'];
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

// 2. Showcase Images (Gallery)
$images = array();
if ( function_exists( 'get_field' ) ) {
	$raw_gallery = get_field( 'showcase_images' ) ?: ( get_field( 'gallery_images' ) ?: get_field( 'images' ) );
	if ( ! empty( $raw_gallery ) && is_array( $raw_gallery ) ) {
		foreach ( $raw_gallery as $item ) {
			if ( is_array( $item ) && isset( $item['image'] ) ) {
				$img_data = $parse_image( $item['image'] );
			} else {
				$img_data = $parse_image( $item );
			}
			if ( ! empty( $img_data['url'] ) ) {
				$images[] = $img_data;
			}
		}
	}
}

// Fallback block_data for showcase images
if ( empty( $images ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/^(showcase_images|gallery_images|images)/', $k ) && ! empty( $v ) ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $sub_v ) {
					$img_data = $parse_image( $sub_v );
					if ( ! empty( $img_data['url'] ) ) {
						$images[] = $img_data;
					}
				}
			} else {
				$img_data = $parse_image( $v );
				if ( ! empty( $img_data['url'] ) ) {
					$images[] = $img_data;
				}
			}
		}
	}
}

// 3. Pricing Cards (Repeater: pricing_cards)
$pricing_cards = array();

if ( function_exists( 'have_rows' ) && have_rows( 'pricing_cards' ) ) {
	while ( have_rows( 'pricing_cards' ) ) {
		the_row();
		$card_title    = get_sub_field( 'card_title' ) ?: ( get_sub_field( 'title' ) ?: '' );
		$card_subtitle = get_sub_field( 'card_subtitle' ) ?: ( get_sub_field( 'subtitle' ) ?: '' );
		$items_content = get_sub_field( 'items' );

		if ( ! empty( $card_title ) || ! empty( $card_subtitle ) || ! empty( $items_content ) ) {
			$pricing_cards[] = array(
				'title'    => $card_title,
				'subtitle' => $card_subtitle,
				'items'    => $items_content,
			);
		}
	}
} elseif ( function_exists( 'get_field' ) ) {
	$raw_cards = get_field( 'pricing_cards' ) ?: ( get_field( 'cards' ) ?: '' );
	if ( ! empty( $raw_cards ) && is_array( $raw_cards ) ) {
		foreach ( $raw_cards as $c ) {
			if ( is_array( $c ) ) {
				$card_title    = ! empty( $c['card_title'] ) ? $c['card_title'] : ( ! empty( $c['title'] ) ? $c['title'] : '' );
				$card_subtitle = ! empty( $c['card_subtitle'] ) ? $c['card_subtitle'] : ( ! empty( $c['subtitle'] ) ? $c['subtitle'] : '' );
				$items_content = ! empty( $c['items'] ) ? $c['items'] : '';

				if ( ! empty( $card_title ) || ! empty( $card_subtitle ) || ! empty( $items_content ) ) {
					$pricing_cards[] = array(
						'title'    => $card_title,
						'subtitle' => $card_subtitle,
						'items'    => $items_content,
					);
				}
			}
		}
	}
}

// Fallback block_data for pricing_cards
if ( empty( $pricing_cards ) && ! empty( $block_data ) ) {
	$count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/^(pricing_cards|cards)$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$card_title    = '';
		$card_subtitle = '';
		$items_content = '';

		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/^pricing_cards_{$i}_card_title$/", $k ) || preg_match( "/^pricing_cards_{$i}_title$/", $k ) ) {
				$card_title = $v;
			}
			if ( preg_match( "/^pricing_cards_{$i}_card_subtitle$/", $k ) || preg_match( "/^pricing_cards_{$i}_subtitle$/", $k ) ) {
				$card_subtitle = $v;
			}
			if ( preg_match( "/^pricing_cards_{$i}_items$/", $k ) ) {
				$items_content = $v;
			}
		}

		if ( ! empty( $card_title ) || ! empty( $card_subtitle ) || ! empty( $items_content ) ) {
			$pricing_cards[] = array(
				'title'    => $card_title,
				'subtitle' => $card_subtitle,
				'items'    => $items_content,
			);
		}
	}
}

$tick_icon_url = get_template_directory_uri() . '/assets/image/tick.png';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="zoom-in">
	<div class="container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $description ) ) : ?>
			<div class="celeb-para">
				<?php if ( strpos( $description, '<p>' ) !== false ) : ?>
					<?php echo wp_kses_post( $description ); ?>
				<?php else : ?>
					<p><?php echo wp_kses_post( nl2br( $description ) ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $images ) ) : ?>
			<div class="celeb-img">
				<div class="row">
					<?php
					$total_imgs = count( $images );
					$img_col_class = ( $total_imgs <= 3 ) ? 'col-md-4' : ( ( $total_imgs === 4 ) ? 'col-md-3 col-sm-6' : 'col-md-4 col-sm-6' );
					foreach ( $images as $idx => $img ) :
						?>
						<div class="<?php echo esc_attr( $img_col_class ); ?>">
							<div class="cel-img">
								<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $img['alt'] ) ? $img['alt'] : 'Celebrate Image ' . ( $idx + 1 ) ); ?>" />
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $pricing_cards ) ) : ?>
			<div class="celeb-card">
				<?php
				// Group cards in rows of 2 to match HTML design
				$card_rows = array_chunk( $pricing_cards, 2 );
				foreach ( $card_rows as $row_cards ) :
					$col_size = ( count( $row_cards ) === 1 ) ? 'col-lg-12' : 'col-lg-6';
					?>
					<div class="row">
						<?php
						foreach ( $row_cards as $card_idx => $c ) :
							$items_raw = ! empty( $c['items'] ) ? $c['items'] : '';

							// Check if items is an unordered/ordered list <li>
							$has_li = false;
							$li_items = array();
							if ( ! empty( $items_raw ) && is_string( $items_raw ) && preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $items_raw, $matches ) ) {
								if ( ! empty( $matches[1] ) ) {
									$has_li = true;
									$li_items = $matches[1];
								}
							}

							$card_extra = ( $card_idx > 0 || ! $has_li || count( $li_items ) > 4 ) ? ' cel-card-cntn1' : '';
							?>
							<div class="<?php echo esc_attr( $col_size ); ?>">
								<div class="cel-card-cntn<?php echo esc_attr( $card_extra ); ?>">
									<?php if ( ! empty( $c['title'] ) ) : ?>
										<div class="cel-hd"><p><?php echo esc_html( $c['title'] ); ?></p></div>
									<?php endif; ?>

									<?php if ( ! empty( $c['subtitle'] ) ) : ?>
										<div class="cel-para el-para-ph"><p><?php echo esc_html( $c['subtitle'] ); ?></p></div>
									<?php endif; ?>

									<?php if ( ! empty( $items_raw ) ) : ?>
										<?php if ( $has_li ) : ?>
											<?php
											$total_li = count( $li_items );
											if ( $total_li > 4 ) :
												$half = (int) ceil( $total_li / 2 );
												$col1 = array_slice( $li_items, 0, $half );
												$col2 = array_slice( $li_items, $half );
												?>
												<div class="row">
													<div class="col-sm-6">
														<?php foreach ( $col1 as $li_text ) : ?>
															<div class="card-info">
																<img src="<?php echo esc_url( $tick_icon_url ); ?>" alt="" />
																<p><?php echo wp_kses_post( trim( $li_text ) ); ?></p>
															</div>
														<?php endforeach; ?>
													</div>
													<div class="col-sm-6">
														<?php foreach ( $col2 as $li_text ) : ?>
															<div class="card-info">
																<img src="<?php echo esc_url( $tick_icon_url ); ?>" alt="" />
																<p><?php echo wp_kses_post( trim( $li_text ) ); ?></p>
															</div>
														<?php endforeach; ?>
													</div>
												</div>
											<?php else : ?>
												<?php foreach ( $li_items as $li_text ) : ?>
													<div class="card-info">
														<img src="<?php echo esc_url( $tick_icon_url ); ?>" alt="" />
														<p><?php echo wp_kses_post( trim( $li_text ) ); ?></p>
													</div>
												<?php endforeach; ?>
											<?php endif; ?>
										<?php else : ?>
											<div class="cel-card-body">
												<?php echo wp_kses_post( $items_raw ); ?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
