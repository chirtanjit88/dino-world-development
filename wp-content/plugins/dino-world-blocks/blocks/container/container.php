<?php
/**
 * Block Name: Custom Container
 *
 * Simple container block that automatically wraps any nested inner blocks
 * inside the theme container width (1440px max-width).
 *
 * No ACF fields required.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'custom-container-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );

$class_name = 'custom-container-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container">
		<InnerBlocks />
	</div>
</section>
