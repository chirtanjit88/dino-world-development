<?php
/**
 * Block Name: Excursion Packages
 *
 * Powered by ACF / SCF Block Fields:
 * - heading (Text)
 * - packages (Repeater)
 *   - package_title (Text)
 *   - subtitle (Text)
 *   - features (Repeater)
 *     - feature_text (Text)
 *   - price_text (Text)
 *   - is_featured (True/False)
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'ex-packeg-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'ex-packeg sec-padding1';
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

// Helper to parse features list from array / strings
$parse_features = function( $val ) {
	$result = array();
	if ( empty( $val ) ) {
		return $result;
	}
	if ( is_array( $val ) ) {
		foreach ( $val as $row ) {
			if ( is_array( $row ) ) {
				$t = ! empty( $row['feature_text'] ) ? $row['feature_text'] : ( ! empty( $row['text'] ) ? $row['text'] : ( ! empty( $row['item'] ) ? $row['item'] : ( ! empty( $row['title'] ) ? $row['title'] : '' ) ) );
				if ( ! empty( $t ) ) {
					$result[] = $t;
				}
			} elseif ( is_string( $row ) && ! empty( $row ) ) {
				$result[] = $row;
			}
		}
	} elseif ( is_string( $val ) ) {
		if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $val, $matches ) && ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $li ) {
				$li = trim( wp_strip_all_tags( $li ) );
				if ( ! empty( $li ) ) $result[] = $li;
			}
		} else {
			$lines = explode( "\n", str_replace( "\r", "", $val ) );
			foreach ( $lines as $line ) {
				$line = trim( wp_strip_all_tags( $line ) );
				if ( ! empty( $line ) ) $result[] = $line;
			}
		}
	}
	return $result;
};

// 2. Packages List
$packages = array();

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) && have_rows( 'packages' ) ) {
	while ( have_rows( 'packages' ) ) {
		the_row();
		$title       = get_sub_field( 'package_title' ) ?: ( get_sub_field( 'title' ) ?: get_sub_field( 'heading' ) );
		$subtitle    = get_sub_field( 'subtitle' ) ?: ( get_sub_field( 'package_subtitle' ) ?: get_sub_field( 'sub_heading' ) );
		$price       = get_sub_field( 'price_text' ) ?: ( get_sub_field( 'price' ) ?: get_sub_field( 'pricing' ) );
		$is_featured = (bool) ( get_sub_field( 'is_featured' ) ?: ( get_sub_field( 'featured' ) ?: get_sub_field( 'highlighted' ) ) );

		$feats = array();
		if ( have_rows( 'features' ) ) {
			while ( have_rows( 'features' ) ) {
				the_row();
				$f_txt = get_sub_field( 'feature_text' ) ?: ( get_sub_field( 'text' ) ?: ( get_sub_field( 'item' ) ?: get_sub_field( 'title' ) ) );
				if ( ! empty( $f_txt ) ) {
					$feats[] = $f_txt;
				}
			}
		} else {
			$raw_f = get_sub_field( 'features' ) ?: ( get_sub_field( 'items' ) ?: get_sub_field( 'list' ) );
			$feats = $parse_features( $raw_f );
		}

		if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) ) {
			$packages[] = array(
				'title'       => $title,
				'subtitle'    => $subtitle,
				'features'    => $feats,
				'price'       => $price,
				'is_featured' => $is_featured,
			);
		}
	}
}

// Method B: ACF get_field array
if ( empty( $packages ) && function_exists( 'get_field' ) ) {
	$rep_keys = array( 'packages', 'package_cards', 'cards', 'items', 'excursion_packages' );
	foreach ( $rep_keys as $rk ) {
		$g_pkgs = get_field( $rk );
		if ( ! empty( $g_pkgs ) && is_array( $g_pkgs ) ) {
			foreach ( $g_pkgs as $item ) {
				if ( is_array( $item ) ) {
					$title       = ! empty( $item['package_title'] ) ? $item['package_title'] : ( ! empty( $item['title'] ) ? $item['title'] : ( ! empty( $item['heading'] ) ? $item['heading'] : '' ) );
					$subtitle    = ! empty( $item['subtitle'] ) ? $item['subtitle'] : ( ! empty( $item['package_subtitle'] ) ? $item['package_subtitle'] : ( ! empty( $item['sub_heading'] ) ? $item['sub_heading'] : '' ) );
					$price       = ! empty( $item['price_text'] ) ? $item['price_text'] : ( ! empty( $item['price'] ) ? $item['price'] : ( ! empty( $item['pricing'] ) ? $item['pricing'] : '' ) );
					$is_featured = ! empty( $item['is_featured'] ) || ! empty( $item['featured'] ) || ! empty( $item['highlighted'] );
					$raw_f       = ! empty( $item['features'] ) ? $item['features'] : ( ! empty( $item['items'] ) ? $item['items'] : '' );
					$feats       = $parse_features( $raw_f );

					if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) ) {
						$packages[] = array(
							'title'       => $title,
							'subtitle'    => $subtitle,
							'features'    => $feats,
							'price'       => $price,
							'is_featured' => $is_featured,
						);
					}
				}
			}
			if ( ! empty( $packages ) ) {
				break;
			}
		}
	}
}

// Method C: Raw block_data fallback (handles nested repeaters in block preview)
if ( empty( $packages ) && ! empty( $block_data ) ) {
	$count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/^packages$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}
	if ( ! $count ) {
		foreach ( $block_data as $k => $v ) {
			if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(packages|package_cards|cards)$/', $k ) && is_numeric( $v ) ) {
				$count = (int) $v;
				break;
			}
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$title       = '';
		$subtitle    = '';
		$price       = '';
		$is_featured = false;
		$feats       = array();

		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/packages_{$i}_(package_title|title|heading)$/", $k ) ) {
				$title = $v;
			}
			if ( preg_match( "/packages_{$i}_(subtitle|package_subtitle|sub_heading)$/", $k ) ) {
				$subtitle = $v;
			}
			if ( preg_match( "/packages_{$i}_(price_text|price|pricing)$/", $k ) ) {
				$price = $v;
			}
			if ( preg_match( "/packages_{$i}_(is_featured|featured|highlighted)$/", $k ) ) {
				$is_featured = (bool) $v;
			}
			if ( preg_match( "/packages_{$i}_features_\d+_(feature_text|text|item|title)$/", $k ) && ! empty( $v ) ) {
				$feats[] = $v;
			}
		}

		if ( empty( $feats ) ) {
			foreach ( $block_data as $k => $v ) {
				if ( empty( $k ) || $k[0] === '_' ) continue;
				if ( preg_match( "/packages_{$i}_(features|items)$/", $k ) ) {
					$feats = $parse_features( $v );
				}
			}
		}

		if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) ) {
			$packages[] = array(
				'title'       => $title,
				'subtitle'    => $subtitle,
				'features'    => $feats,
				'price'       => $price,
				'is_featured' => $is_featured,
			);
		}
	}
}

$green_tick_url = get_template_directory_uri() . '/assets/image/green-tick.png';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $packages ) ) : ?>
			<div class="row">
				<?php
				$total_pkgs = count( $packages );
				if ( 3 === $total_pkgs ) {
					$col_class = 'col-lg-4 col-md-6';
				} elseif ( 4 === $total_pkgs ) {
					$col_class = 'col-lg-3 col-md-6';
				} elseif ( 2 === $total_pkgs ) {
					$col_class = 'col-lg-6 col-md-6';
				} else {
					$col_class = 'col-lg-4 col-md-6';
				}

				foreach ( $packages as $index => $item ) :
					$card_class = 'xp-card';
					if ( ! empty( $item['is_featured'] ) ) {
						$card_class .= ' xp-card1';
					}
					?>
					<div class="<?php echo esc_attr( $col_class ); ?>" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $index * 100 ); ?>">
						<div class="<?php echo esc_attr( $card_class ); ?>">
							<?php if ( ! empty( $item['title'] ) ) : ?>
								<div class="xp-hd">
									<p><?php echo esc_html( $item['title'] ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['subtitle'] ) ) : ?>
								<div class="xp-sub-hd">
									<p><?php echo esc_html( $item['subtitle'] ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['features'] ) ) : ?>
								<?php foreach ( $item['features'] as $f_text ) : ?>
									<div class="xp-txt">
										<div class="xp-img">
											<img src="<?php echo esc_url( $green_tick_url ); ?>" alt="" />
										</div>
										<div class="xp-inf">
											<p><?php echo esc_html( $f_text ); ?></p>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>

							<?php if ( ! empty( $item['price'] ) ) : ?>
								<?php
								$price_output = $item['price'];
								// If price contains numbers like $22 and does not already have <span>, wrap the price number with <span>
								if ( strpos( $price_output, '<span>' ) === false ) {
									$price_output = preg_replace( '/(\$\d+)/', '<span>$1</span>', $price_output );
								}
								?>
								<div class="xp-price">
									<p><?php echo wp_kses_post( $price_output ); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
