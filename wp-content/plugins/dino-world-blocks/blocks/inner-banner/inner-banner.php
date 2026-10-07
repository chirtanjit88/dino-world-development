<?php
/**
 * Block Name: Inner Banner
 *
 * Powered by ACF Block Fields:
 * - title / banner_title (Text)
 * - description / banner_description (Textarea / WYSIWYG)
 * - background_image (Image)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'at-banner-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'at-banner';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Title & Description
$title       = '';
$description = '';

if ( function_exists( 'get_field' ) ) {
	$title       = get_field( 'title' ) ?: ( get_field( 'banner_title' ) ?: get_field( 'heading' ) );
	$description = get_field( 'description' ) ?: ( get_field( 'banner_description' ) ?: ( get_field( 'content' ) ?: get_field( 'sub_heading' ) ) );
}

// Fallback from raw block data
if ( empty( $title ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(banner_title|title|heading)$/', $k ) ) {
			$title = $v;
			break;
		}
	}
}

if ( empty( $description ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(banner_description|description|content|sub_heading)$/', $k ) ) {
			$description = $v;
			break;
		}
	}
}

// Fallback title to current page title if not set
if ( empty( $title ) && ! empty( $post_id ) ) {
	$title = get_the_title( $post_id );
}

// 2. Background Image
$bg_image_url = '';
if ( function_exists( 'get_field' ) ) {
	$bg_img = get_field( 'background_image' ) ?: ( get_field( 'banner_image' ) ?: get_field( 'image' ) );
	if ( ! empty( $bg_img ) ) {
		if ( is_array( $bg_img ) ) {
			$bg_image_url = ! empty( $bg_img['url'] ) ? $bg_img['url'] : '';
		} elseif ( is_numeric( $bg_img ) ) {
			$bg_image_url = wp_get_attachment_image_url( (int) $bg_img, 'full' );
		} elseif ( is_string( $bg_img ) ) {
			$bg_image_url = $bg_img;
		}
	}
}

// Fallback bg from block data
if ( empty( $bg_image_url ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) continue;
		if ( preg_match( '/(background_image|banner_image|image)$/', $k ) ) {
			if ( is_numeric( $v ) ) {
				$bg_image_url = wp_get_attachment_image_url( (int) $v, 'full' );
			} elseif ( is_array( $v ) && ! empty( $v['url'] ) ) {
				$bg_image_url = $v['url'];
			} elseif ( is_string( $v ) ) {
				$bg_image_url = $v;
			}
			break;
		}
	}
}

if ( empty( $bg_image_url ) ) {
	$bg_image_url = get_template_directory_uri() . '/assets/image/attractions-bg.png';
}

$bg_style = ! empty( $bg_image_url ) ? 'style="background: url(' . esc_url( $bg_image_url ) . ') center/cover no-repeat;"' : '';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" <?php echo $bg_style; ?> data-aos="fade-right">
	<div class="at-banner-overlay">
		<div class="container">
			<div class="at-cntn">
				<?php if ( ! empty( $title ) ) : ?>
					<h1 class="at-title"><?php echo esc_html( $title ); ?></h1>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<div class="at-para">
						<?php if ( strpos( $description, '<p>' ) !== false ) : ?>
							<?php echo wp_kses_post( $description ); ?>
						<?php else : ?>
							<p><?php echo wp_kses_post( nl2br( $description ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
