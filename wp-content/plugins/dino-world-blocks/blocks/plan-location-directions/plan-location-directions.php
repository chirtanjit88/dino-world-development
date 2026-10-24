<?php
/**
 * Block Name: Plan – Location & Directions
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'location';
$class_name = 'plan-section';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

$short_heading = function_exists( 'get_field' ) ? get_field( 'short_heading' ) : '';
if ( empty( $short_heading ) && ! empty( $block_data['short_heading'] ) ) {
	$short_heading = $block_data['short_heading'];
}
if ( empty( $short_heading ) ) {
	$short_heading = 'Find Us';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'How to Get to Dinosaur World';
}

// Directions items
$directions_items = array();
if ( function_exists( 'have_rows' ) && have_rows( 'directions_items' ) ) {
	while ( have_rows( 'directions_items' ) ) {
		the_row();
		$directions_items[] = array(
			'icon'        => get_sub_field( 'icon' ),
			'title'       => get_sub_field( 'title' ),
			'description' => get_sub_field( 'description' ),
		);
	}
} elseif ( ! empty( $block_data['directions_items'] ) && is_array( $block_data['directions_items'] ) ) {
	$directions_items = $block_data['directions_items'];
}

if ( empty( $directions_items ) ) {
	$directions_items = array(
		array(
			'icon'        => '📍',
			'title'       => 'Park Address',
			'description' => 'Corner of Frankston-Flinders Road & Oneills Road, Somerville VIC 3912<br><em>(Located inside Somerville Christmas Tree Farm)</em>',
		),
		array(
			'icon'        => '🚗',
			'title'       => 'Easy Driving Access',
			'description' => "We're just <strong>5 minutes from Peninsula Link</strong>! Take the Somerville exit and head down Frankston-Flinders Road.",
		),
		array(
			'icon'        => '🧭',
			'title'       => 'GPS Navigation Tip',
			'description' => 'Pop <strong>1385 Frankston-Flinders Road, Somerville</strong> into your GPS; we are just a little further up the road.',
		),
		array(
			'icon'        => '🦕',
			'title'       => 'Look for the Giant Dinosaur',
			'description' => "You can't miss our entrance! Look for our colossal dinosaur standing proudly at the main gates.",
		),
		array(
			'icon'        => '🅿️',
			'title'       => 'Parking is On-Site & 100% Free',
			'description' => 'Generous parking space available right outside the entrance with easy access for prams and families.',
		),
	);
}

// Directions Button
$dir_btn_url    = 'https://www.google.com/maps/search/1385+Frankston-Flinders+Road,+Somerville';
$dir_btn_title  = 'Get Directions in Google Maps';
$dir_btn_target = '_blank';

$dir_btn = function_exists( 'get_field' ) ? get_field( 'directions_button' ) : null;
if ( ! empty( $dir_btn ) && is_array( $dir_btn ) ) {
	$dir_btn_url    = ! empty( $dir_btn['url'] ) ? $dir_btn['url'] : $dir_btn_url;
	$dir_btn_title  = ! empty( $dir_btn['title'] ) ? $dir_btn['title'] : $dir_btn_title;
	$dir_btn_target = ! empty( $dir_btn['target'] ) ? $dir_btn['target'] : '_blank';
}

// Map Card BG image
$default_map_bg = get_template_directory_uri() . '/assets/image/loaction.png';
$map_bg_url = $default_map_bg;
$map_bg = function_exists( 'get_field' ) ? get_field( 'map_bg_image' ) : null;
if ( ! empty( $map_bg ) ) {
	if ( is_array( $map_bg ) && ! empty( $map_bg['url'] ) ) {
		$map_bg_url = $map_bg['url'];
	} elseif ( is_numeric( $map_bg ) ) {
		$map_bg_url = wp_get_attachment_image_url( (int) $map_bg, 'full' ) ?: $default_map_bg;
	} elseif ( is_string( $map_bg ) ) {
		$map_bg_url = $map_bg;
	}
}

// Map Pin Badge
$map_pin_title = function_exists( 'get_field' ) ? get_field( 'map_pin_title' ) : '';
if ( empty( $map_pin_title ) && ! empty( $block_data['map_pin_title'] ) ) {
	$map_pin_title = $block_data['map_pin_title'];
}
if ( empty( $map_pin_title ) ) {
	$map_pin_title = '📍 Dinosaur World Somerville';
}

$map_pin_address = function_exists( 'get_field' ) ? get_field( 'map_pin_address' ) : '';
if ( empty( $map_pin_address ) && ! empty( $block_data['map_pin_address'] ) ) {
	$map_pin_address = $block_data['map_pin_address'];
}
if ( empty( $map_pin_address ) ) {
	$map_pin_address = "1385 Frankston-Flinders Road, Somerville VIC 3912<br>Corner Frankston-Flinders Rd & O'Neills Rd<br><strong>Look for the Giant Dinosaur Gate!</strong>";
}

// Dino Sketch
$default_dino_sketch = get_template_directory_uri() . '/assets/image/dino-sketch.png';
$map_dino_url = $default_dino_sketch;
$map_dino = function_exists( 'get_field' ) ? get_field( 'map_dino_image' ) : null;
if ( ! empty( $map_dino ) ) {
	if ( is_array( $map_dino ) && ! empty( $map_dino['url'] ) ) {
		$map_dino_url = $map_dino['url'];
	} elseif ( is_numeric( $map_dino ) ) {
		$map_dino_url = wp_get_attachment_image_url( (int) $map_dino, 'full' ) ?: $default_dino_sketch;
	} elseif ( is_string( $map_dino ) ) {
		$map_dino_url = $map_dino;
	}
}

// Map Button
$map_btn_url    = 'https://www.google.com/maps/search/1385+Frankston-Flinders+Road,+Somerville';
$map_btn_title  = 'Open in Google Maps';
$map_btn_target = '_blank';

$map_btn = function_exists( 'get_field' ) ? get_field( 'map_button' ) : null;
if ( ! empty( $map_btn ) && is_array( $map_btn ) ) {
	$map_btn_url    = ! empty( $map_btn['url'] ) ? $map_btn['url'] : $map_btn_url;
	$map_btn_title  = ! empty( $map_btn['title'] ) ? $map_btn['title'] : $map_btn_title;
	$map_btn_target = ! empty( $map_btn['target'] ) ? $map_btn['target'] : '_blank';
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<div class="plan-sec-header">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="plan-location-grid">
		<!-- Directions Info Card -->
		<div class="plan-directions-card">
			<div>
				<?php foreach ( $directions_items as $item ) : ?>
					<?php
					$item_icon  = ! empty( $item['icon'] ) ? $item['icon'] : '📍';
					$item_title = ! empty( $item['title'] ) ? $item['title'] : '';
					$item_desc  = ! empty( $item['description'] ) ? $item['description'] : '';
					?>
					<div class="plan-dir-item">
						<div class="plan-dir-icon"><?php echo esc_html( $item_icon ); ?></div>
						<div class="plan-dir-info">
							<?php if ( ! empty( $item_title ) ) : ?>
								<h5><?php echo esc_html( $item_title ); ?></h5>
							<?php endif; ?>
							<?php if ( ! empty( $item_desc ) ) : ?>
								<p><?php echo wp_kses_post( $item_desc ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( ! empty( $dir_btn_url ) ) : ?>
				<div class="pt-3">
					<a class="orange-btn" href="<?php echo esc_url( $dir_btn_url ); ?>" target="<?php echo esc_attr( $dir_btn_target ); ?>" rel="noopener noreferrer">
						<?php echo esc_html( $dir_btn_title ); ?> <img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
					</a>
				</div>
			<?php endif; ?>
		</div>

		<!-- Stylized Themed Map Card -->
		<div class="plan-map-card" style="background-image: url('<?php echo esc_url( $map_bg_url ); ?>')">
			<div class="plan-map-overlay"></div>
			<div class="plan-map-pin-badge">
				<?php if ( ! empty( $map_pin_title ) ) : ?>
					<h4><?php echo esc_html( $map_pin_title ); ?></h4>
				<?php endif; ?>
				<?php if ( ! empty( $map_pin_address ) ) : ?>
					<p><?php echo wp_kses_post( $map_pin_address ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $map_dino_url ) ) : ?>
				<img class="plan-map-dino" src="<?php echo esc_url( $map_dino_url ); ?>" alt="Dino Sketch" />
			<?php endif; ?>
			<?php if ( ! empty( $map_btn_url ) ) : ?>
				<div class="plan-map-bottom-btn">
					<a class="orange-btn" href="<?php echo esc_url( $map_btn_url ); ?>" target="<?php echo esc_attr( $map_btn_target ); ?>" rel="noopener noreferrer">
						<?php echo esc_html( $map_btn_title ); ?> <img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
