<?php
/**
 * Block Name: Plan Your Visit
 *
 * Powered by ACF Block Fields:
 * - short_heading (Text)
 * - heading (Text)
 * - features (Repeater: text [Text])
 * - button (Link)
 * - dino_image (Image)
 * - tickets (Repeater: ticket_title [Text], ticket_subtitle [Text], ticket_price [Text], price_label [Text], card_image [Image])
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'plan-visit-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'plan-visit sec-padding';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// 1. Text & General Fields
$short_heading = function_exists( 'get_field' ) ? get_field( 'short_heading' ) : '';
$heading       = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';

// 2. Dino Image
$dino_img_url = '';
$dino_img_alt = '';
if ( function_exists( 'get_field' ) ) {
	$dino_img = get_field( 'dino_image' );
	if ( ! empty( $dino_img ) ) {
		if ( is_array( $dino_img ) ) {
			$dino_img_url = ! empty( $dino_img['url'] ) ? $dino_img['url'] : '';
			$dino_img_alt = ! empty( $dino_img['alt'] ) ? $dino_img['alt'] : '';
		} elseif ( is_numeric( $dino_img ) ) {
			$dino_img_url = wp_get_attachment_image_url( (int) $dino_img, 'full' );
			$dino_img_alt = get_post_meta( (int) $dino_img, '_wp_attachment_image_alt', true );
		} elseif ( is_string( $dino_img ) ) {
			$dino_img_url = $dino_img;
		}
	} else {
		$dino_img_url = get_template_directory_uri() . '/assets/image/price-sec-dino.png';
	}
}

// 3. CTA Button
$button_url    = '';
$button_title  = '';
$button_target = '_self';
if ( function_exists( 'get_field' ) ) {
	$btn = get_field( 'button' );
	if ( ! empty( $btn ) ) {
		if ( is_array( $btn ) ) {
			$button_url    = ! empty( $btn['url'] ) ? $btn['url'] : '';
			$button_title  = ! empty( $btn['title'] ) ? $btn['title'] : '';
			$button_target = ! empty( $btn['target'] ) ? $btn['target'] : '_self';
		} elseif ( is_string( $btn ) ) {
			$button_url   = $btn;
			$button_title = $btn;
		}
	}
}

// 4. Repeaters check
$has_features = function_exists( 'have_rows' ) && have_rows( 'features' );
$has_tickets  = function_exists( 'have_rows' ) && have_rows( 'tickets' );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="flip-left" data-aos-duration="600">
	<div class="container">
		<?php if ( ! empty( $dino_img_url ) ) : ?>
			<div class="price-dino">
				<img src="<?php echo esc_url( $dino_img_url ); ?>" alt="<?php echo esc_attr( $dino_img_alt ); ?>" />
			</div>
		<?php endif; ?>

		<div class="row">
			<div class="col-xl-5">
				<?php if ( ! empty( $short_heading ) ) : ?>
					<h6 class="short-hd"><?php echo esc_html( $short_heading ); ?></h6>
				<?php endif; ?>

				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( $has_features ) : ?>
					<ul class="visit-cntn">
						<?php
						while ( have_rows( 'features' ) ) :
							the_row();
							$feature_text = get_sub_field( 'text' );
							if ( empty( $feature_text ) ) {
								continue;
							}
							?>
							<li><?php echo esc_html( $feature_text ); ?></li>
						<?php endwhile; ?>
					</ul>
				<?php endif; ?>

				<?php if ( ! empty( $button_url ) && ! empty( $button_title ) ) : ?>
					<div class="visit-btn">
						<a class="orange-btn" href="<?php echo esc_url( $button_url ); ?>" target="<?php echo esc_attr( $button_target ); ?>">
							<?php echo esc_html( $button_title ); ?> <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $has_tickets ) : ?>
				<div class="col-xl-7 ms-auto">
					<div class="price-card" id="tickets">
						<div class="row">
							<?php
							while ( have_rows( 'tickets' ) ) :
								the_row();
								$ticket_title    = get_sub_field( 'ticket_title' );
								$ticket_subtitle = get_sub_field( 'ticket_subtitle' );
								$ticket_price    = get_sub_field( 'ticket_price' );
								$price_label     = get_sub_field( 'price_label' );
								$card_img        = get_sub_field( 'card_image' );

								$card_img_url = '';
								$card_img_alt = '';
								if ( ! empty( $card_img ) ) {
									if ( is_array( $card_img ) ) {
										$card_img_url = ! empty( $card_img['url'] ) ? $card_img['url'] : '';
										$card_img_alt = ! empty( $card_img['alt'] ) ? $card_img['alt'] : '';
									} elseif ( is_numeric( $card_img ) ) {
										$card_img_url = wp_get_attachment_image_url( (int) $card_img, 'full' );
										$card_img_alt = get_post_meta( (int) $card_img, '_wp_attachment_image_alt', true );
									} elseif ( is_string( $card_img ) ) {
										$card_img_url = $card_img;
									}
								} else {
									$card_img_url = get_template_directory_uri() . '/assets/image/paw.png';
								}
								?>
								<div class="col-sm-4">
									<div class="pcard">
										<div class="man-count">
											<div class="p-card-cntn">
												<?php if ( ! empty( $ticket_title ) ) : ?>
													<div class="p-hd">
														<p><?php echo esc_html( $ticket_title ); ?></p>
													</div>
												<?php endif; ?>

												<?php if ( ! empty( $ticket_subtitle ) ) : ?>
													<div class="p-child">
														<p><?php echo esc_html( $ticket_subtitle ); ?></p>
													</div>
												<?php endif; ?>
											</div>
										</div>

										<?php if ( ! empty( $ticket_price ) || ! empty( $price_label ) ) : ?>
											<div class="price">
												<div class="card-price">
													<?php if ( ! empty( $ticket_price ) ) : ?>
														<div class="p-price">
															<p><?php echo esc_html( $ticket_price ); ?></p>
														</div>
													<?php endif; ?>

													<?php if ( ! empty( $price_label ) ) : ?>
														<div class="price-c">
															<p><?php echo esc_html( $price_label ); ?></p>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $card_img_url ) ) : ?>
											<div class="p-card-img mx-auto">
												<img src="<?php echo esc_url( $card_img_url ); ?>" alt="<?php echo esc_attr( $card_img_alt ); ?>" />
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
