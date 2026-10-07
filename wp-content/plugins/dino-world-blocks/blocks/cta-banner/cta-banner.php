<?php
/**
 * Block Name: CTA Banner (Come Face to Face)
 *
 * ACF Field Architecture:
 * - heading (Text)
 * - description (Textarea / WYSIWYG)
 * - button (Link)
 * - background_image (Image)
 * - show_background_image (True / False or Checkbox)
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'face-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'face-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// Helper to check truthy values for checkboxes/booleans
$is_truthy = function( $val ) {
	if ( empty( $val ) ) {
		return false;
	}
	if ( is_array( $val ) ) {
		$filtered = array_filter( $val );
		return ! empty( $filtered );
	}
	if ( is_string( $val ) && ( '0' === $val || 'false' === strtolower( $val ) ) ) {
		return false;
	}
	return (bool) $val;
};

// 1. Heading
$heading = '';
if ( function_exists( 'get_field' ) ) {
	$heading = get_field( 'heading' ) ?: ( get_field( 'cta_heading' ) ?: get_field( 'title' ) );
}
if ( empty( $heading ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) {
			continue;
		}
		if ( preg_match( '/(cta_heading|heading|title)$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

// 2. Description
$description = '';
if ( function_exists( 'get_field' ) ) {
	$description = get_field( 'description' ) ?: ( get_field( 'cta_description' ) ?: ( get_field( 'content' ) ?: get_field( 'text' ) ) );
}
if ( empty( $description ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) {
			continue;
		}
		if ( preg_match( '/(cta_description|description|content|text)$/', $k ) ) {
			$description = $v;
			break;
		}
	}
}

// 3. Button
$button_data = null;
if ( function_exists( 'get_field' ) ) {
	$button_data = get_field( 'button' ) ?: ( get_field( 'cta_button' ) ?: get_field( 'link' ) );
}
if ( empty( $button_data ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) {
			continue;
		}
		if ( preg_match( '/(cta_button|button|link)$/', $k ) ) {
			$button_data = $v;
			break;
		}
	}
}

$btn_url    = '';
$btn_title  = '';
$btn_target = '_self';
if ( ! empty( $button_data ) ) {
	if ( is_array( $button_data ) ) {
		$btn_url    = ! empty( $button_data['url'] ) ? $button_data['url'] : '';
		$btn_title  = ! empty( $button_data['title'] ) ? $button_data['title'] : '';
		$btn_target = ! empty( $button_data['target'] ) ? $button_data['target'] : '_self';
	} elseif ( is_string( $button_data ) ) {
		$btn_url   = $button_data;
		$btn_title = $button_data;
	}
}

// 4. Background Image & Visibility
$show_bg_img = true;
if ( function_exists( 'get_field' ) ) {
	$bg_show_val = get_field( 'show_background_image' );
	if ( null === $bg_show_val || '' === $bg_show_val ) {
		$bg_show_val = get_field( 'show_bg_image' );
	}
	if ( null !== $bg_show_val && '' !== $bg_show_val ) {
		$show_bg_img = $is_truthy( $bg_show_val );
	} else {
		$bg_hide_val = get_field( 'hide_background_image' ) ?: get_field( 'hide_bg_image' );
		if ( ! empty( $bg_hide_val ) && $is_truthy( $bg_hide_val ) ) {
			$show_bg_img = false;
		}
	}
}
if ( ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) {
			continue;
		}
		if ( preg_match( '/(show_background_image|show_bg_image)$/', $k ) ) {
			$show_bg_img = $is_truthy( $v );
		}
		if ( preg_match( '/(hide_background_image|hide_bg_image)$/', $k ) ) {
			if ( $is_truthy( $v ) ) {
				$show_bg_img = false;
			}
		}
	}
}

$bg_img = null;
if ( function_exists( 'get_field' ) ) {
	$bg_img = get_field( 'background_image' ) ?: ( get_field( 'cta_background_image' ) ?: ( get_field( 'bg_image' ) ?: get_field( 'image' ) ) );
}
if ( empty( $bg_img ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) {
			continue;
		}
		if ( preg_match( '/(background_image|cta_background_image|bg_image|image)$/', $k ) ) {
			$bg_img = $v;
			break;
		}
	}
}

$bg_img_url = '';
if ( ! empty( $bg_img ) ) {
	if ( is_array( $bg_img ) && ! empty( $bg_img['url'] ) ) {
		$bg_img_url = $bg_img['url'];
	} elseif ( is_numeric( $bg_img ) ) {
		$bg_img_url = wp_get_attachment_image_url( (int) $bg_img, 'full' );
	} elseif ( is_string( $bg_img ) && filter_var( $bg_img, FILTER_VALIDATE_URL ) ) {
		$bg_img_url = $bg_img;
	}
}

// Fallback to default theme template image if no custom image is uploaded
if ( empty( $bg_img_url ) && $show_bg_img ) {
	$bg_img_url = get_template_directory_uri() . '/assets/image/come-face.png';
}

$style_attr = '';
if ( ! $show_bg_img ) {
	$style_attr = ' style="background-image: none;"';
} elseif ( ! empty( $bg_img_url ) ) {
	$style_attr = ' style="background-image: url(\'' . esc_url( $bg_img_url ) . '\');"';
}

// Disable AOS in Gutenberg editor preview so it is never hidden by opacity:0
$is_admin_preview = ! empty( $is_preview ) || is_admin();
$aos_attr         = ! $is_admin_preview ? ' data-aos="fade-up"' : '';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>"<?php echo $style_attr; ?><?php echo $aos_attr; ?>>
	<div class="container">
		<div class="face-cntn">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>
				<div class="face-para">
					<?php if ( strpos( $description, '<p>' ) !== false ) : ?>
						<?php echo wp_kses_post( $description ); ?>
					<?php else : ?>
						<p><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $btn_title ) ) : ?>
				<div class="face-btn">
					<a class="secondary-btn" href="<?php echo ! empty( $btn_url ) ? esc_url( $btn_url ) : '#'; ?>" target="<?php echo esc_attr( $btn_target ); ?>">
						<?php echo esc_html( $btn_title ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
