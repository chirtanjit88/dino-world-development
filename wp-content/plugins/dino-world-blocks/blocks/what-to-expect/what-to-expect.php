<?php
/**
 * Block Name: What to Expect
 *
 * Powered by ACF Block Fields:
 * - heading (Text)
 * - sub_heading (Text)
 * - content (WYSIWYG Editor)
 * - highlight_text (Text)
 * - buttons (Repeater: button [Link])
 * - image_1 (Image)
 * - image_2 (Image)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'what-to-expect-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'about-section sec-padding';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// 1. Text Fields
$heading        = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
$sub_heading    = function_exists( 'get_field' ) ? get_field( 'sub_heading' ) : '';
$main_content   = function_exists( 'get_field' ) ? get_field( 'content' ) : '';
$highlight_text = function_exists( 'get_field' ) ? get_field( 'highlight_text' ) : '';

// 2. Images
$image_1_url = '';
$image_1_alt = '';
$image_2_url = '';
$image_2_alt = '';

if ( function_exists( 'get_field' ) ) {
	// Image 1
	$img1 = get_field( 'image_1' );
	if ( ! empty( $img1 ) ) {
		if ( is_array( $img1 ) ) {
			$image_1_url = ! empty( $img1['url'] ) ? $img1['url'] : '';
			$image_1_alt = ! empty( $img1['alt'] ) ? $img1['alt'] : '';
		} elseif ( is_numeric( $img1 ) ) {
			$image_1_url = wp_get_attachment_image_url( (int) $img1, 'full' );
			$image_1_alt = get_post_meta( (int) $img1, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $img1 ) ) {
			$image_1_url = $img1;
		}
	}

	// Image 2
	$img2 = get_field( 'image_2' );
	if ( ! empty( $img2 ) ) {
		if ( is_array( $img2 ) ) {
			$image_2_url = ! empty( $img2['url'] ) ? $img2['url'] : '';
			$image_2_alt = ! empty( $img2['alt'] ) ? $img2['alt'] : '';
		} elseif ( is_numeric( $img2 ) ) {
			$image_2_url = wp_get_attachment_image_url( (int) $img2, 'full' );
			$image_2_alt = get_post_meta( (int) $img2, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $img2 ) ) {
			$image_2_url = $img2;
		}
	}
}

// 3. Buttons Repeater
$has_buttons = function_exists( 'have_rows' ) && have_rows( 'buttons' );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-down-left" data-aos-duration="500">
	<div class="leaf-element"><img class="leaf-img" src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/leaf.png' ); ?>" alt="" /></div>
	<div class="container">
		<div class="row m-row">
			<div class="col-lg-6">
				<div class="about-cntn">
					<?php if ( ! empty( $heading ) ) : ?>
						<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $sub_heading ) ) : ?>
						<div class="abt-sub">
							<p><?php echo esc_html( $sub_heading ); ?></p>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $main_content ) || ! empty( $highlight_text ) ) : ?>
						<div class="abt-txt-cntn">
							<?php if ( ! empty( $main_content ) ) : ?>
								<div class="para">
									<?php echo wp_kses_post( $main_content ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $highlight_text ) ) : ?>
								<div class="abt-highlit">
									<p><?php echo esc_html( $highlight_text ); ?></p>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $has_buttons ) : ?>
						<div class="abt-btn">
							<?php
							$btn_index = 0;
							while ( have_rows( 'buttons' ) ) :
								the_row();
								$btn_index++;
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

								if ( 1 === $btn_index ) :
									?>
									<div class="abt-btn1">
										<a class="orange-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
											<?php echo esc_html( $btn_title ); ?> <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
										</a>
									</div>
								<?php else : ?>
									<div class="abt-btn2">
										<a class="primary-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
											<?php echo esc_html( $btn_title ); ?> <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
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

			<div class="col-lg-6">
				<?php if ( ! empty( $image_1_url ) || ! empty( $image_2_url ) ) : ?>
					<div class="abt-img-cntn">
						<?php if ( ! empty( $image_1_url ) ) : ?>
							<div class="abt-img1">
								<img src="<?php echo esc_url( $image_1_url ); ?>" alt="<?php echo esc_attr( $image_1_alt ); ?>">
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $image_2_url ) ) : ?>
							<div class="abt-img2 position-absolute">
								<img src="<?php echo esc_url( $image_2_url ); ?>" alt="<?php echo esc_attr( $image_2_alt ); ?>">
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
