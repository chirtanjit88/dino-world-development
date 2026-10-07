<?php
/**
 * Block Name: Birthday Party Booking Enquiry
 *
 * Powered by ACF Block Fields:
 * - heading (Text)
 * - description (Textarea / WYSIWYG)
 * - image / plan_image (Image)
 * - form_shortcode / shortcode (Text / Textarea)
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'planb-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'planb-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading & Description
$heading     = function_exists( 'get_field' ) ? ( get_field( 'heading' ) ?: ( get_field( 'title' ) ?: get_field( 'section_title' ) ) ) : '';
$description = function_exists( 'get_field' ) ? ( get_field( 'description' ) ?: ( get_field( 'sub_heading' ) ?: get_field( 'intro_text' ) ) ) : '';

if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $description ) && ! empty( $block_data['description'] ) ) {
	$description = $block_data['description'];
}

// 2. Image (Plan / Dino illustration)
$image_url = '';
$image_alt = '';

if ( function_exists( 'get_field' ) ) {
	$raw_img = get_field( 'image' ) ?: ( get_field( 'plan_image' ) ?: ( get_field( 'dino_image' ) ?: get_field( 'illustration' ) ) );
	if ( ! empty( $raw_img ) ) {
		if ( is_array( $raw_img ) ) {
			$image_url = ! empty( $raw_img['url'] ) ? $raw_img['url'] : '';
			$image_alt = ! empty( $raw_img['alt'] ) ? $raw_img['alt'] : '';
		} elseif ( is_numeric( $raw_img ) ) {
			$image_url = wp_get_attachment_image_url( (int) $raw_img, 'full' );
			$image_alt = get_post_meta( (int) $raw_img, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $raw_img ) ) {
			$image_url = $raw_img;
		}
	}
}

// Fallback block_data for image
if ( empty( $image_url ) && ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(image|plan_image|dino_image)$/', $k ) && ! empty( $v ) ) {
			if ( is_numeric( $v ) ) {
				$image_url = wp_get_attachment_image_url( (int) $v, 'full' );
				$image_alt = get_post_meta( (int) $v, '_wp_attachment_image_alt', true );
			} elseif ( is_string( $v ) ) {
				$image_url = $v;
			}
			break;
		}
	}
}

// 3. Form Shortcode
$form_shortcode = function_exists( 'get_field' ) ? ( get_field( 'form_shortcode' ) ?: ( get_field( 'shortcode' ) ?: ( get_field( 'contact_form_shortcode' ) ?: get_field( 'contact_form' ) ) ) ) : '';

if ( empty( $form_shortcode ) && ! empty( $block_data['form_shortcode'] ) ) {
	$form_shortcode = $block_data['form_shortcode'];
}
if ( empty( $form_shortcode ) && ! empty( $block_data['shortcode'] ) ) {
	$form_shortcode = $block_data['shortcode'];
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-5">
				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<div class="lets-para">
						<?php if ( strpos( $description, '<p>' ) !== false ) : ?>
							<?php echo wp_kses_post( $description ); ?>
						<?php else : ?>
							<p><?php echo wp_kses_post( nl2br( $description ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $image_url ) ) : ?>
					<div class="planb-img">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( ! empty( $image_alt ) ? $image_alt : ( ! empty( $heading ) ? $heading : 'Birthday Enquiry' ) ); ?>" />
					</div>
				<?php endif; ?>
			</div>

			<div class="col-lg-7">
				<div class="enquiry-section">
					<div class="enquiry-form">
						<?php if ( ! empty( $form_shortcode ) ) : ?>
							<?php echo do_shortcode( $form_shortcode ); ?>
						<?php elseif ( $is_preview ) : ?>
							<div class="enquiry-form-placeholder text-center p-4" style="background: rgba(255,255,255,0.7); border: 2px dashed #999; border-radius: 12px; padding: 25px; text-align: center;">
								<p style="margin-bottom: 8px; font-weight: 700; color: #233400; font-size: 18px;">📋 Contact Form Area</p>
								<p style="margin: 0; font-size: 14px; color: #555;">Please enter a Contact Form shortcode (e.g. <code>[contact-form-7 id="..." title="..."]</code> or <code>[wpforms id="..."]</code>) in the block inspector to display the form here.</p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
