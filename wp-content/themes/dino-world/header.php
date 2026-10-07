<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <main>
 * Powered purely by ACF Theme Options and WordPress Menus
 *
 * @package Dino_World
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Logo from ACF Theme Options or Customizer
$logo_url = '';
$logo_alt = get_bloginfo( 'name' );

if ( function_exists( 'get_field' ) ) {
	$acf_logo = get_field( 'header_logo', 'option' );
	if ( ! empty( $acf_logo ) ) {
		if ( is_array( $acf_logo ) ) {
			$logo_url = ! empty( $acf_logo['url'] ) ? $acf_logo['url'] : '';
			if ( ! empty( $acf_logo['alt'] ) ) {
				$logo_alt = $acf_logo['alt'];
			}
		} elseif ( is_numeric( $acf_logo ) ) {
			$logo_src = wp_get_attachment_image_url( (int) $acf_logo, 'full' );
			if ( $logo_src ) {
				$logo_url = $logo_src;
			}
		} elseif ( is_string( $acf_logo ) ) {
			$logo_url = $acf_logo;
		}
	}
}

if ( empty( $logo_url ) && has_custom_logo() ) {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_data      = wp_get_attachment_image_src( $custom_logo_id, 'full' );
	if ( $logo_data ) {
		$logo_url = $logo_data[0];
	}
}

// 2. CTA Button from ACF Theme Options (only if configured)
$cta_text   = '';
$cta_url    = '';
$cta_target = '_self';

if ( function_exists( 'get_field' ) ) {
	$custom_cta_text = get_field( 'header_cta_text', 'option' );
	if ( ! empty( $custom_cta_text ) && is_string( $custom_cta_text ) ) {
		$cta_text = $custom_cta_text;
	}

	$custom_cta_link = get_field( 'header_cta_link', 'option' );
	if ( ! empty( $custom_cta_link ) ) {
		if ( is_array( $custom_cta_link ) && ! empty( $custom_cta_link['url'] ) ) {
			$cta_url = $custom_cta_link['url'];
			if ( ! empty( $custom_cta_link['title'] ) && empty( $cta_text ) ) {
				$cta_text = $custom_cta_link['title'];
			}
			if ( ! empty( $custom_cta_link['target'] ) ) {
				$cta_target = $custom_cta_link['target'];
			}
		} elseif ( is_string( $custom_cta_link ) ) {
			$cta_url = $custom_cta_link;
		}
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header>
	<nav class="navbar navbar-expand-xxl">
		<div class="container">
			<div class="nav-logo">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( ! empty( $logo_url ) ) : ?>
						<img class="h-100 w-100 object-fit-cover" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" />
					<?php else : ?>
						<span class="navbar-brand fw-bold text-dark"><?php bloginfo( 'name' ); ?></span>
					<?php endif; ?>
				</a>
			</div>
			<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarSupportedContent"
				aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'dino-world' ); ?>">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="offcanvas offcanvas-end" id="navbarSupportedContent">
				<div class="offcanvas-header d-xxl-none">
					<div class="nav-logo" style="max-width: 140px;">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php if ( ! empty( $logo_url ) ) : ?>
								<img class="h-100 w-100 object-fit-cover" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" />
							<?php else : ?>
								<span class="navbar-brand fw-bold text-dark"><?php bloginfo( 'name' ); ?></span>
							<?php endif; ?>
						</a>
					</div>
					<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="<?php esc_attr_e( 'Close', 'dino-world' ); ?>"></button>
				</div>
				<?php
				$cta_button_html = '';
				if ( ! empty( $cta_text ) && ! empty( $cta_url ) ) {
					$cta_button_html = '<div class="nav-btn"><a class="primary-btn" href="' . esc_url( $cta_url ) . '" target="' . esc_attr( $cta_target ) . '">' . esc_html( $cta_text ) . ' <img src="' . esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ) . '" alt="" /></a></div>';
				}

				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'navbar-nav',
							'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s' . $cta_button_html . '</ul>',
							'fallback_cb'    => '__return_false',
							'walker'         => new Dino_Bootstrap_Navwalker(),
						)
					);
				} elseif ( ! empty( $cta_button_html ) ) {
					echo '<ul class="navbar-nav">' . $cta_button_html . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
	</nav>
</header>
<main>
