<?php
/**
 * Block Name: Home Banner
 *
 * Powered by ACF Block Fields:
 * - banner_video (File)
 * - banner_image (Image)
 * - sub_heading (Text)
 * - main_heading (Text)
 * - tagline (Text)
 * - description (WYSIWYG Editor)
 * - banner_buttons (Repeater: button [Link])
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'home-banner-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'banner-sectionv';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// 1. Media (Video & Image)
$video_url = '';
$image_url = '';
$image_alt = '';

if ( function_exists( 'get_field' ) ) {
	// Video File
	$video_field = get_field( 'banner_video' );
	if ( ! empty( $video_field ) ) {
		if ( is_array( $video_field ) && ! empty( $video_field['url'] ) ) {
			$video_url = $video_field['url'];
		} elseif ( is_numeric( $video_field ) ) {
			$video_url = wp_get_attachment_url( (int) $video_field );
		} elseif ( is_string( $video_field ) ) {
			$video_url = $video_field;
		}
	}

	// Image
	$image_field = get_field( 'banner_image' );
	if ( ! empty( $image_field ) ) {
		if ( is_array( $image_field ) ) {
			$image_url = ! empty( $image_field['url'] ) ? $image_field['url'] : '';
			$image_alt = ! empty( $image_field['alt'] ) ? $image_field['alt'] : '';
		} elseif ( is_numeric( $image_field ) ) {
			$image_url = wp_get_attachment_image_url( (int) $image_field, 'full' );
			$image_alt = get_post_meta( (int) $image_field, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $image_field ) ) {
			$image_url = $image_field;
		}
	}
}

// 2. Content
$sub_heading  = function_exists( 'get_field' ) ? get_field( 'sub_heading' ) : '';
$main_heading = function_exists( 'get_field' ) ? get_field( 'main_heading' ) : '';
$tagline      = function_exists( 'get_field' ) ? get_field( 'tagline' ) : '';
$description  = function_exists( 'get_field' ) ? get_field( 'description' ) : '';

$has_buttons = function_exists( 'have_rows' ) && have_rows( 'banner_buttons' );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<div class="bannerv-media">
		<?php if ( ! empty( $video_url ) ) : ?>
			<video id="hero-video" class="hero-video" autoplay muted loop playsinline preload="auto" <?php echo ! empty( $image_url ) ? 'poster="' . esc_url( $image_url ) . '"' : ''; ?>>
				<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
			</video>
		<?php elseif ( ! empty( $image_url ) ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ? $image_alt : ( $main_heading ? $main_heading : get_bloginfo( 'name' ) ) ); ?>" class="h-100 w-100 object-fit-cover">
		<?php endif; ?>
	</div>

	<div class="bannerv-overlay">
		<div class="container">
			<div class="banner-cntn">
				<?php if ( ! empty( $sub_heading ) ) : ?>
					<h4 class="banner-sub-hd text-center text-uppercase"><?php echo esc_html( $sub_heading ); ?></h4>
				<?php endif; ?>

				<?php if ( ! empty( $main_heading ) ) : ?>
					<h1 class="title1"><?php echo esc_html( $main_heading ); ?></h1>
				<?php endif; ?>

				<?php if ( ! empty( $tagline ) ) : ?>
					<div class="banner-sub">
						<p><?php echo esc_html( $tagline ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
					<div class="bannerv-para">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $has_buttons ) : ?>
					<div class="bannerv-btn">
						<?php
						$index = 0;
						while ( have_rows( 'banner_buttons' ) ) :
							the_row();
							$index++;
							$btn = get_sub_field( 'button' );

							if ( empty( $btn ) ) {
								continue;
							}

							$btn_url    = '';
							$btn_title  = '';
							$btn_target = '_self';

							if ( is_array( $btn ) ) {
								$btn_url    = ! empty( $btn['url'] ) ? $btn['url'] : '';
								$btn_title  = ! empty( $btn['title'] ) ? $btn['title'] : '';
								$btn_target = ! empty( $btn['target'] ) ? $btn['target'] : '_self';
							} elseif ( is_string( $btn ) ) {
								$btn_url   = $btn;
								$btn_title = $btn;
							}

							if ( empty( $btn_url ) || empty( $btn_title ) ) {
								continue;
							}

							if ( 1 === $index ) :
								?>
								<div class="banner-btn1">
									<a class="secondary-btn text-uppercase" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
										<?php echo esc_html( $btn_title ); ?>
									</a>
								</div>
							<?php else : ?>
								<div class="banner-btn2">
									<a class="primary-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
										<?php echo esc_html( $btn_title ); ?>
										<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
									</a>
								</div>
							<?php
							endif;
						endwhile;
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
