<?php
/**
 * The template for displaying the footer
 *
 * Powered by ACF Theme Options (Footer Field Group):
 * - footer_logo (Image)
 * - footer_content (WYSIWYG)
 * - email (Link)
 * - phone (Link)
 * - social_icons (Repeater: media_icon, media_label, media_url)
 * - Navigation Menus: footer_1, footer_2
 *
 * @package Dino_World
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Footer Logo
$footer_logo_url = '';
$footer_logo_alt = get_bloginfo( 'name' );

if ( function_exists( 'get_field' ) ) {
	$acf_flogo = get_field( 'footer_logo', 'option' );
	if ( empty( $acf_flogo ) ) {
		$acf_flogo = get_field( 'header_logo', 'option' );
	}

	if ( ! empty( $acf_flogo ) ) {
		if ( is_array( $acf_flogo ) ) {
			$footer_logo_url = ! empty( $acf_flogo['url'] ) ? $acf_flogo['url'] : '';
			if ( ! empty( $acf_flogo['alt'] ) ) {
				$footer_logo_alt = $acf_flogo['alt'];
			}
		} elseif ( is_numeric( $acf_flogo ) ) {
			$src = wp_get_attachment_image_url( (int) $acf_flogo, 'full' );
			if ( $src ) {
				$footer_logo_url = $src;
			}
		} elseif ( is_string( $acf_flogo ) ) {
			$footer_logo_url = $acf_flogo;
		}
	}
}

if ( empty( $footer_logo_url ) && has_custom_logo() ) {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_data      = wp_get_attachment_image_src( $custom_logo_id, 'full' );
	if ( $logo_data ) {
		$footer_logo_url = $logo_data[0];
	}
}

if ( empty( $footer_logo_url ) ) {
	$footer_logo_url = get_template_directory_uri() . '/assets/image/logo.png';
}

// 2. Footer Content (WYSIWYG)
$footer_content = '';
if ( function_exists( 'get_field' ) ) {
	$footer_content = get_field( 'footer_content', 'option' );
}

// 3. Phone Link Field
$phone_url    = 'tel:0359773018';
$phone_title  = '(03) 5977 3018';
$phone_target = '_self';

if ( function_exists( 'get_field' ) ) {
	$phone_field = get_field( 'phone', 'option' );
	if ( ! empty( $phone_field ) ) {
		if ( is_array( $phone_field ) ) {
			$phone_url    = ! empty( $phone_field['url'] ) ? $phone_field['url'] : '';
			$phone_title  = ! empty( $phone_field['title'] ) ? $phone_field['title'] : $phone_url;
			$phone_target = ! empty( $phone_field['target'] ) ? $phone_field['target'] : '_self';
		} elseif ( is_string( $phone_field ) ) {
			$phone_title = $phone_field;
			$phone_url   = ( strpos( $phone_field, 'tel:' ) === 0 ) ? $phone_field : 'tel:' . preg_replace( '/[^0-9+]/', '', $phone_field );
		}
	}
}

// 4. Email Link Field
$email_url    = 'mailto:mail@Info.com';
$email_title  = 'mail@Info.com';
$email_target = '_self';

if ( function_exists( 'get_field' ) ) {
	$email_field = get_field( 'email', 'option' );
	if ( ! empty( $email_field ) ) {
		if ( is_array( $email_field ) ) {
			$email_url    = ! empty( $email_field['url'] ) ? $email_field['url'] : '';
			$email_title  = ! empty( $email_field['title'] ) ? $email_field['title'] : $email_url;
			$email_target = ! empty( $email_field['target'] ) ? $email_field['target'] : '_self';
		} elseif ( is_string( $email_field ) ) {
			$email_title = $email_field;
			$email_url   = ( strpos( $email_field, 'mailto:' ) === 0 ) ? $email_field : 'mailto:' . sanitize_email( $email_field );
		}
	}
}

$has_social_repeater = function_exists( 'have_rows' ) && have_rows( 'social_icons', 'option' );
?>
</main><!-- #primary -->

<footer class="footer-sec sec-padding" data-aos="fade-up" data-aos-duration="800">
	<div class="container">
		<div class="row">
			<!-- Column 1: Logo, Content & Social Links -->
			<div class="col-lg-3">
				<?php if ( ! empty( $footer_logo_url ) ) : ?>
					<div class="footer-logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<img src="<?php echo esc_url( $footer_logo_url ); ?>" alt="<?php echo esc_attr( $footer_logo_alt ); ?>">
						</a>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $footer_content ) ) : ?>
					<div class="footer-txt">
						<?php echo wp_kses_post( $footer_content ); ?>
					</div>
				<?php else : ?>
					<div class="footer-txt">
						<p>A premium prehistoric adventure<br>for families .</p>
					</div>
				<?php endif; ?>

				<div class="social-link">
					<ul class="s-link">
						<?php if ( $has_social_repeater ) : ?>
							<?php
							while ( have_rows( 'social_icons', 'option' ) ) :
								the_row();
								$media_icon  = get_sub_field( 'media_icon' );
								$media_label = get_sub_field( 'media_label' );
								$media_url   = get_sub_field( 'media_url' );

								$icon_src = '';
								$icon_alt = ! empty( $media_label ) ? $media_label : 'Social Media';

								if ( is_array( $media_icon ) && ! empty( $media_icon['url'] ) ) {
									$icon_src = $media_icon['url'];
									if ( ! empty( $media_icon['alt'] ) ) {
										$icon_alt = $media_icon['alt'];
									}
								} elseif ( is_numeric( $media_icon ) ) {
									$src = wp_get_attachment_image_url( (int) $media_icon, 'full' );
									if ( $src ) {
										$icon_src = $src;
									}
								} elseif ( is_string( $media_icon ) ) {
									$icon_src = $media_icon;
								}

								if ( ! empty( $media_url ) && ! empty( $icon_src ) ) :
									?>
									<li>
										<a href="<?php echo esc_url( $media_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $icon_alt ); ?>">
											<img src="<?php echo esc_url( $icon_src ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>">
										</a>
									</li>
									<?php
								endif;
							endwhile;
							?>
						<?php else : ?>
							<li><a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/face-book-logo.png' ); ?>" alt="Facebook"></a></li>
							<li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/instagram-logo.png' ); ?>" alt="Instagram"></a></li>
							<li><a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/you-tube-logo.png' ); ?>" alt="YouTube"></a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>

			<!-- Column 2: Quick Links 1 Menu -->
			<div class="col-sm-4 col-lg-3">
				<div class="footer-link-hd">
					<p><?php echo esc_html( wp_get_nav_menu_name( 'footer_1' ) ? wp_get_nav_menu_name( 'footer_1' ) : __( 'Quick Links', 'dino-world' ) ); ?></p>
				</div>
				<div class="footer-link">
					<?php
					if ( has_nav_menu( 'footer_1' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer_1',
								'container'      => false,
								'menu_class'     => 'f-menu',
								'fallback_cb'    => '__return_false',
							)
						);
					} else {
						?>
						<ul class="f-menu">
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/schoolex' ) ); ?>">School & kindergarden excursions</a></li>
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/atraction' ) ); ?>">Upcoming Events</a></li>
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/#plan-visit' ) ); ?>">Gift Vouchers</a></li>
						</ul>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Column 3: Quick Links 2 Menu -->
			<div class="col-sm-4 col-lg-3">
				<div class="footer-link-hd">
					<p><?php echo esc_html( wp_get_nav_menu_name( 'footer_2' ) ? wp_get_nav_menu_name( 'footer_2' ) : __( 'Quick Links', 'dino-world' ) ); ?></p>
				</div>
				<div class="footer-link">
					<?php
					if ( has_nav_menu( 'footer_2' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer_2',
								'container'      => false,
								'menu_class'     => 'f-menu',
								'fallback_cb'    => '__return_false',
							)
						);
					} else {
						?>
						<ul class="f-menu">
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/birthday' ) ); ?>">Birthday Parties</a></li>
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/gallery' ) ); ?>">Gallery</a></li>
							<li><a class="f-link" href="<?php echo esc_url( home_url( '/about' ) ); ?>">About Us</a></li>
						</ul>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Column 4: Phone & Email Contact Links -->
			<div class="col-sm-4 col-lg-3">
				<div class="f-info">
					<?php if ( ! empty( $phone_url ) ) : ?>
						<div class="number mb-5">
							<a class="gap-2" href="<?php echo esc_url( $phone_url ); ?>" target="<?php echo esc_attr( $phone_target ); ?>">
								<svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect x="2" y="2" width="46" height="46" rx="23" fill="#F59E0B" />
									<rect x="2" y="2" width="46" height="46" rx="23" stroke="white" stroke-width="4" />
									<path d="M26.125 13C32.125 13 37 17.875 37 23.875C37 24.5312 36.4844 25 35.875 25C35.2188 25 34.75 24.5312 34.75 23.875C34.75 19.1406 30.8594 15.25 26.125 15.25C25.4688 15.25 25 14.7812 25 14.125C25 13.5156 25.4688 13 26.125 13ZM26.5 22C27.2969 22 28 22.7031 28 23.5C28 24.3438 27.2969 25 26.5 25C25.6562 25 25 24.3438 25 23.5C25 22.7031 25.6562 22 26.5 22ZM25 18.625C25 18.0156 25.4688 17.5 26.125 17.5C29.6406 17.5 32.5 20.3594 32.5 23.875C32.5 24.5312 31.9844 25 31.375 25C30.7188 25 30.25 24.5312 30.25 23.875C30.25 21.625 28.375 19.75 26.125 19.75C25.4688 19.75 25 19.2812 25 18.625ZM28.4219 26.4531C28.9375 25.7969 29.8281 25.6094 30.5781 25.9375L35.8281 28.1875C36.6719 28.5156 37.1406 29.4062 36.9531 30.2969L35.8281 35.5469C35.6406 36.3906 34.8438 37 34 37C33.6719 37 33.3906 37 33.1094 37C32.6406 37 32.1719 36.9531 31.75 36.9062C21.2031 35.7812 13 26.875 13 16C13 15.1562 13.6094 14.3594 14.4531 14.1719L19.7031 13.0469C20.5938 12.8594 21.4844 13.3281 21.8125 14.1719L24.0625 19.4219C24.3906 20.1719 24.2031 21.0625 23.5469 21.5781L21.625 23.1719C22.8906 25.3281 24.6719 27.1094 26.8281 28.375L28.4219 26.4531ZM34.6562 30.1094L29.9688 28.0938L28.6094 29.7812C27.9062 30.625 26.6875 30.8594 25.7031 30.2969C23.2188 28.8438 21.1562 26.7812 19.7031 24.2969C19.1406 23.3125 19.375 22.0938 20.2188 21.3906L21.9062 20.0312L19.8906 15.3438L15.25 16.3281C15.3906 26.4531 23.5469 34.6094 33.6719 34.75L34.6562 30.1094Z" fill="white" />
								</svg>
								<?php echo esc_html( $phone_title ); ?>
							</a>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $email_url ) ) : ?>
						<div class="mail">
							<a class="gap-2" href="<?php echo esc_url( $email_url ); ?>" target="<?php echo esc_attr( $email_target ); ?>">
								<svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect x="2" y="2" width="46" height="46" rx="23" fill="#F59E0B" />
									<rect x="2" y="2" width="46" height="46" rx="23" stroke="white" stroke-width="4" />
									<path d="M16 18.25C15.5781 18.25 15.25 18.625 15.25 19V20.0781L23.3125 26.6875C24.2969 27.4844 25.6562 27.4844 26.6406 26.6875L34.75 20.0781V19C34.75 18.625 34.375 18.25 34 18.25H16ZM15.25 22.9844V31C15.25 31.4219 15.5781 31.75 16 31.75H34C34.375 31.75 34.75 31.4219 34.75 31V22.9844L28.0938 28.4219C26.2656 29.9219 23.6875 29.9219 21.9062 28.4219L15.25 22.9844ZM13 19C13 17.3594 14.3125 16 16 16H34C35.6406 16 37 17.3594 37 19V31C37 32.6875 35.6406 34 34 34H16C14.3125 34 13 32.6875 13 31V19Z" fill="white" />
								</svg>
								<?php echo esc_html( $email_title ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
