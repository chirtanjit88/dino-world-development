<?php
/**
 * Block Name: Opening Hours & Location
 *
 * Powered by ACF Block Fields:
 * - hours_title (Text)
 * - schedule (Repeater: days [Text], hours [Text], is_closed [Boolean])
 * - holiday_note (Text)
 * - location_text (Textarea / WYSIWYG)
 * - location_bg_image (Image)
 * - directions_button (Link)
 * - dino_sketch_image (Image)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'time-loc-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'time-loc sec-padding';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Opening Hours Card Details
$hours_title  = '';
$holiday_note = '';
$schedule     = array();

if ( function_exists( 'get_field' ) ) {
	$hours_title  = get_field( 'hours_title' ) ?: get_field( 'opening_hours_title' );
	$holiday_note = get_field( 'holiday_note' ) ?: ( get_field( 'school_holidays_note' ) ?: get_field( 'note' ) );
}

// Fallback for title/note
if ( empty( $hours_title ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(hours_title|opening_hours_title)$/', $k ) ) {
			$hours_title = $v;
			break;
		}
	}
}
if ( empty( $hours_title ) ) {
	$hours_title = __( 'Opening Hours', 'dino-world' );
}

if ( empty( $holiday_note ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(holiday_note|school_holidays_note|note)$/', $k ) ) {
			$holiday_note = $v;
			break;
		}
	}
}

// Extract Schedule Repeater
if ( function_exists( 'have_rows' ) ) {
	$sched_keys = array( 'schedule', 'opening_hours', 'hours_list', 'timing' );
	foreach ( $sched_keys as $sk ) {
		if ( have_rows( $sk ) ) {
			while ( have_rows( $sk ) ) {
				the_row();
				$d = get_sub_field( 'days' ) ?: ( get_sub_field( 'day' ) ?: get_sub_field( 'title' ) );
				$h = get_sub_field( 'hours' ) ?: ( get_sub_field( 'time' ) ?: get_sub_field( 'timing' ) );
				$c = get_sub_field( 'is_closed' );

				if ( ! empty( $d ) || ! empty( $h ) ) {
					$schedule[] = array(
						'days'      => $d,
						'hours'     => $h,
						'is_closed' => ! empty( $c ) || stripos( (string) $h, 'closed' ) !== false,
					);
				}
			}
			break;
		}
	}
}

// Raw block_data fallback for schedule
if ( empty( $schedule ) && ! empty( $block_data ) ) {
	$sched_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(schedule|opening_hours|hours_list|timing)$/', $k ) && is_numeric( $v ) ) {
			$sched_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $sched_count; $i++ ) {
		$d = '';
		$h = '';
		$c = false;
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(schedule|opening_hours|hours_list)_{$i}_(days|day|title)$/", $k ) ) {
				$d = $v;
			}
			if ( preg_match( "/(schedule|opening_hours|hours_list)_{$i}_(hours|time|timing)$/", $k ) ) {
				$h = $v;
			}
			if ( preg_match( "/(schedule|opening_hours|hours_list)_{$i}_is_closed$/", $k ) ) {
				$c = (bool) $v;
			}
		}
		if ( ! empty( $d ) || ! empty( $h ) ) {
			$schedule[] = array(
				'days'      => $d,
				'hours'     => $h,
				'is_closed' => $c || stripos( (string) $h, 'closed' ) !== false,
			);
		}
	}
}

// 2. Location & Directions Card Details
$location_text   = '';
$location_bg_url = '';
$btn_url         = '';
$btn_title       = '';
$btn_target      = '_self';

if ( function_exists( 'get_field' ) ) {
	$location_text = get_field( 'location_text' ) ?: ( get_field( 'address' ) ?: get_field( 'location' ) );

	$bg_img = get_field( 'location_bg_image' );
	if ( ! empty( $bg_img ) ) {
		if ( is_array( $bg_img ) ) {
			$location_bg_url = ! empty( $bg_img['url'] ) ? $bg_img['url'] : '';
		} elseif ( is_numeric( $bg_img ) ) {
			$location_bg_url = wp_get_attachment_image_url( (int) $bg_img, 'full' );
		} elseif ( is_string( $bg_img ) ) {
			$location_bg_url = $bg_img;
		}
	}

	$btn = get_field( 'directions_button' ) ?: ( get_field( 'button' ) ?: get_field( 'link' ) );
	if ( ! empty( $btn ) ) {
		if ( is_array( $btn ) ) {
			$btn_url    = ! empty( $btn['url'] ) ? $btn['url'] : '';
			$btn_title  = ! empty( $btn['title'] ) ? $btn['title'] : '';
			$btn_target = ! empty( $btn['target'] ) ? $btn['target'] : '_self';
		} elseif ( is_string( $btn ) ) {
			$btn_url   = $btn;
			$btn_title = __( 'Get Direction', 'dino-world' );
		}
	}
}

// Fallbacks from block_data
if ( empty( $location_text ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(location_text|address|location)$/', $k ) ) {
			$location_text = $v;
			break;
		}
	}
}
if ( empty( $btn_url ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(directions_button|button|link)$/', $k ) && is_array( $v ) ) {
			$btn_url    = ! empty( $v['url'] ) ? $v['url'] : '';
			$btn_title  = ! empty( $v['title'] ) ? $v['title'] : '';
			$btn_target = ! empty( $v['target'] ) ? $v['target'] : '_self';
			break;
		}
	}
}

// 3. Dino Sketch Illustration
$dino_sketch_url = '';
$dino_sketch_alt = '';
if ( function_exists( 'get_field' ) ) {
	$sketch_img = get_field( 'dino_sketch_image' );
	if ( ! empty( $sketch_img ) ) {
		if ( is_array( $sketch_img ) ) {
			$dino_sketch_url = ! empty( $sketch_img['url'] ) ? $sketch_img['url'] : '';
			$dino_sketch_alt = ! empty( $sketch_img['alt'] ) ? $sketch_img['alt'] : '';
		} elseif ( is_numeric( $sketch_img ) ) {
			$dino_sketch_url = wp_get_attachment_image_url( (int) $sketch_img, 'full' );
			$dino_sketch_alt = get_post_meta( (int) $sketch_img, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $sketch_img ) ) {
			$dino_sketch_url = $sketch_img;
		}
	}
}
if ( empty( $dino_sketch_url ) ) {
	$dino_sketch_url = get_template_directory_uri() . '/assets/image/dino-sketch.png';
}

$loc_card_style = ! empty( $location_bg_url ) ? 'style="background: url(' . esc_url( $location_bg_url ) . ') center/cover no-repeat;"' : '';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<?php if ( ! empty( $dino_sketch_url ) ) : ?>
		<div class="dino-sketch">
			<img src="<?php echo esc_url( $dino_sketch_url ); ?>" alt="<?php echo esc_attr( $dino_sketch_alt ); ?>" />
		</div>
	<?php endif; ?>

	<div class="container">
		<div class="row">
			<div class="col-xl-5" data-aos="fade-down">
				<div class="time-card">
					<div class="time-hd">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.3 14.7L14.7 13.3L11 9.6V5H9V10.4L13.3 14.7ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2167 18 14.1042 17.2208 15.6625 15.6625C17.2208 14.1042 18 12.2167 18 10C18 7.78333 17.2208 5.89583 15.6625 4.3375C14.1042 2.77917 12.2167 2 10 2C7.78333 2 5.89583 2.77917 4.3375 4.3375C2.77917 5.89583 2 7.78333 2 10C2 12.2167 2.77917 14.1042 4.3375 15.6625C5.89583 17.2208 7.78333 18 10 18Z" fill="#56642B" />
						</svg>
						<p><?php echo esc_html( $hours_title ); ?></p>
					</div>

					<?php if ( ! empty( $schedule ) ) : ?>
						<?php foreach ( $schedule as $item ) :
							$is_closed = ! empty( $item['is_closed'] );
							$time_class = $is_closed ? 'o-time p-time' : 'o-time';
							?>
							<div class="time-data">
								<div class="time-info">
									<p><?php echo esc_html( $item['days'] ); ?></p>
								</div>
								<div class="<?php echo esc_attr( $time_class ); ?>">
									<p><?php echo esc_html( $item['hours'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( ! empty( $holiday_note ) ) : ?>
						<div class="time-h">
							<p><?php echo esc_html( $holiday_note ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-xl-5" data-aos="fade-down">
				<div class="location-card" <?php echo $loc_card_style; ?>>
					<?php if ( ! empty( $location_text ) ) : ?>
						<div class="location-txt">
							<?php if ( strpos( $location_text, '<p>' ) !== false ) : ?>
								<?php echo wp_kses_post( $location_text ); ?>
							<?php else : ?>
								<p><?php echo wp_kses_post( nl2br( $location_text ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $btn_url ) ) : ?>
						<div class="location-btn">
							<a class="orange-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>" rel="noopener noreferrer">
								<?php echo esc_html( ! empty( $btn_title ) ? $btn_title : __( 'Get Direction', 'dino-world' ) ); ?>
								<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
