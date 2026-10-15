<?php
/**
 * Block Name: Custom Container
 *
 * Container block that matches the project's fixed container width (1440px).
 * Automatically wraps any nested inner blocks (InnerBlocks) with optional
 * container width, section padding, background color/image, and overlay settings.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'custom-container-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Container Width: Default is standard project container (1440px)
$container_type = 'container'; // Default theme container (1440px max-width)
if ( function_exists( 'get_field' ) ) {
	$c_type = get_field( 'container_width' ) ?: get_field( 'container_type' );
	if ( ! empty( $c_type ) ) {
		$container_type = $c_type;
	}
}
if ( empty( $container_type ) || 'container' === $container_type ) {
	if ( ! empty( $block_data['container_width'] ) ) {
		$container_type = $block_data['container_width'];
	}
}

// 2. Padding Size: Default is sec-padding1 (30px) or sec-padding (60px)
$padding_class = 'sec-padding1';
if ( function_exists( 'get_field' ) ) {
	$p_val = get_field( 'padding' ) ?: ( get_field( 'padding_size' ) ?: get_field( 'section_padding' ) );
	if ( ! empty( $p_val ) ) {
		$padding_class = $p_val;
	}
}
if ( ! empty( $block_data['padding'] ) ) {
	$padding_class = $block_data['padding'];
}

// 3. Background Settings
$bg_color        = '';
$bg_image_url    = '';
$has_overlay     = false;
$overlay_opacity = '0.5';

if ( function_exists( 'get_field' ) ) {
	$bg_color    = get_field( 'background_color' ) ?: get_field( 'bg_color' );
	$bg_img_raw  = get_field( 'background_image' ) ?: get_field( 'bg_image' );
	$has_overlay = (bool) get_field( 'enable_overlay' );
	$ov_op       = get_field( 'overlay_opacity' );
	if ( ! empty( $ov_op ) ) {
		$overlay_opacity = $ov_op;
	}

	if ( ! empty( $bg_img_raw ) ) {
		if ( is_array( $bg_img_raw ) && ! empty( $bg_img_raw['url'] ) ) {
			$bg_image_url = $bg_img_raw['url'];
		} elseif ( is_numeric( $bg_img_raw ) ) {
			$bg_image_url = wp_get_attachment_image_url( (int) $bg_img_raw, 'full' ) ?: '';
		} elseif ( is_string( $bg_img_raw ) ) {
			$bg_image_url = $bg_img_raw;
		}
	}
}

// 4. HTML Tag
$html_tag = 'section';
if ( function_exists( 'get_field' ) ) {
	$tag = get_field( 'html_tag' );
	if ( in_array( $tag, array( 'section', 'div', 'article', 'main', 'header', 'footer' ), true ) ) {
		$html_tag = $tag;
	}
}

// 5. Build Classes & Inline Styles
$classes = array( 'custom-container-sec', $padding_class );
if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$classes[] = 'align' . $block['align'];
} else {
	$classes[] = 'alignfull';
}
if ( ! empty( $bg_image_url ) ) {
	$classes[] = 'has-bg-image';
}

$styles = array();
if ( ! empty( $bg_color ) ) {
	$styles[] = 'background-color: ' . esc_attr( $bg_color ) . ';';
}
if ( ! empty( $bg_image_url ) ) {
	$styles[] = 'background-image: url(' . esc_url( $bg_image_url ) . '); background-size: cover; background-position: center;';
}
$style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( ' ', $styles ) ) . '"' : '';

// Inner container class (defaults to .container for standard 1440px project width)
$container_class = ( 'narrow' === $container_type || 'container-narrow' === $container_type )
	? 'container-narrow'
	: ( ( 'fluid' === $container_type || 'container-fluid' === $container_type ) ? 'container-fluid' : 'container' );
?>

<<?php echo esc_attr( $html_tag ); ?> id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr; ?>>
	<?php if ( ! empty( $bg_image_url ) && $has_overlay ) : ?>
		<div class="custom-container-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,<?php echo esc_attr( $overlay_opacity ); ?>); z-index: 1;"></div>
	<?php endif; ?>
	<div class="<?php echo esc_attr( $container_class ); ?>"<?php echo ( ! empty( $bg_image_url ) && $has_overlay ) ? ' style="position: relative; z-index: 2;"' : ''; ?>>
		<InnerBlocks />
	</div>
</<?php echo esc_attr( $html_tag ); ?>>
