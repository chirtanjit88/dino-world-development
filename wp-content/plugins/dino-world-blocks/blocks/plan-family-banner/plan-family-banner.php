<?php
/**
 * Block Name: Plan – Family Adventure Banner
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'family-banner-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'plan-family-banner';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

$default_img_url = get_template_directory_uri() . '/assets/image/plan.png';
$img_url = $default_img_url;
$img_alt = 'Kids playing at Dinosaur World';

$image_raw = function_exists( 'get_field' ) ? get_field( 'image' ) : null;
if ( ! empty( $image_raw ) ) {
	if ( is_array( $image_raw ) && ! empty( $image_raw['url'] ) ) {
		$img_url = $image_raw['url'];
		$img_alt = ! empty( $image_raw['alt'] ) ? $image_raw['alt'] : $img_alt;
	} elseif ( is_numeric( $image_raw ) ) {
		$img_url = wp_get_attachment_image_url( (int) $image_raw, 'full' ) ?: $default_img_url;
		$img_alt = get_post_meta( (int) $image_raw, '_wp_attachment_image_alt', true ) ?: $img_alt;
	} elseif ( is_string( $image_raw ) ) {
		$img_url = $image_raw;
	}
}

$short_heading = function_exists( 'get_field' ) ? get_field( 'short_heading' ) : '';
if ( empty( $short_heading ) && ! empty( $block_data['short_heading'] ) ) {
	$short_heading = $block_data['short_heading'];
}
if ( empty( $short_heading ) ) {
	$short_heading = 'Where Imaginations Roar';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Unforgettable Prehistoric Fun for All Generations';
}

$description = function_exists( 'get_field' ) ? get_field( 'description' ) : '';
if ( empty( $description ) && ! empty( $block_data['description'] ) ) {
	$description = $block_data['description'];
}
if ( empty( $description ) ) {
	$description = 'From toddler smiles meeting their very first life-size Brachiosaurus to grandparents relaxing in shady picnic pavilions, Dinosaur World Somerville offers a gentle, delightful day out that the whole family will cherish.';
}

$btn_url    = 'https://www.google.com/maps/search/1385+Frankston-Flinders+Road,+Somerville';
$btn_title  = 'Plan Your Adventure';
$btn_target = '_blank';

$btn = function_exists( 'get_field' ) ? get_field( 'button' ) : null;
if ( ! empty( $btn ) && is_array( $btn ) ) {
	$btn_url    = ! empty( $btn['url'] ) ? $btn['url'] : $btn_url;
	$btn_title  = ! empty( $btn['title'] ) ? $btn['title'] : $btn_title;
	$btn_target = ! empty( $btn['target'] ) ? $btn['target'] : '_blank';
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<img class="plan-family-banner-img" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
	<div class="plan-family-banner-content">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>
		<?php if ( ! empty( $heading ) ) : ?>
			<h3><?php echo esc_html( $heading ); ?></h3>
		<?php endif; ?>
		<?php if ( ! empty( $description ) ) : ?>
			<p><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $btn_url ) ) : ?>
			<a href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>" rel="noopener noreferrer" class="orange-btn">
				<?php echo esc_html( $btn_title ); ?> <img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
			</a>
		<?php endif; ?>
	</div>
</div>
