<?php
/**
 * Dinosaur World Theme Functions
 *
 * @package Dino_World
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DINO_WORLD_VERSION', '1.0.0' );
define( 'DINO_WORLD_URI', get_template_directory_uri() );
define( 'DINO_WORLD_DIR', get_template_directory() );

/**
 * Theme Setup
 */
function dino_world_theme_setup() {
	load_theme_textdomain( 'dino-world', DINO_WORLD_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	register_nav_menus(
		array(
			'primary'  => esc_html__( 'Primary Navigation Menu', 'dino-world' ),
			'footer_1' => esc_html__( 'Footer Quick Links 1', 'dino-world' ),
			'footer_2' => esc_html__( 'Footer Quick Links 2', 'dino-world' ),
		)
	);
}
add_action( 'after_setup_theme', 'dino_world_theme_setup' );

/**
 * Enqueue Styles and Scripts
 */
function dino_world_enqueue_assets() {
	// Google Fonts
	wp_enqueue_style(
		'dino-google-fonts',
		'https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
		array(),
		null
	);

	// Bootstrap 5 CSS
	wp_enqueue_style(
		'bootstrap-css',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
		array(),
		'5.3.8'
	);

	// AOS CSS
	wp_enqueue_style(
		'aos-css',
		'https://unpkg.com/aos@2.3.1/dist/aos.css',
		array(),
		'2.3.1'
	);

	// Splide CSS
	wp_enqueue_style(
		'splide-css',
		'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css',
		array(),
		'4.1.4'
	);

	// Main Custom CSS
	$style_ver = file_exists( DINO_WORLD_DIR . '/assets/css/style.css' ) ? filemtime( DINO_WORLD_DIR . '/assets/css/style.css' ) : DINO_WORLD_VERSION;
	wp_enqueue_style(
		'dino-style',
		DINO_WORLD_URI . '/assets/css/style.css',
		array( 'bootstrap-css', 'aos-css', 'splide-css' ),
		$style_ver
	);

	// Responsive Media CSS
	$media_ver = file_exists( DINO_WORLD_DIR . '/assets/css/media.css' ) ? filemtime( DINO_WORLD_DIR . '/assets/css/media.css' ) : DINO_WORLD_VERSION;
	wp_enqueue_style(
		'dino-media',
		DINO_WORLD_URI . '/assets/css/media.css',
		array( 'dino-style' ),
		$media_ver
	);

	// Theme style.css
	wp_enqueue_style(
		'dino-root-style',
		get_stylesheet_uri(),
		array( 'dino-media' ),
		DINO_WORLD_VERSION
	);

	// jQuery
	wp_enqueue_script( 'jquery' );

	// Bootstrap 5 Bundle JS
	wp_enqueue_script(
		'bootstrap-js',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
		array( 'jquery' ),
		'5.3.8',
		true
	);

	// AOS JS
	wp_enqueue_script(
		'aos-js',
		'https://unpkg.com/aos@2.3.1/dist/aos.js',
		array(),
		'2.3.1',
		true
	);

	// Splide JS
	wp_enqueue_script(
		'splide-js',
		'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js',
		array(),
		'4.1.4',
		true
	);

	// Theme custom script.js
	$script_ver = file_exists( DINO_WORLD_DIR . '/assets/js/script.js' ) ? filemtime( DINO_WORLD_DIR . '/assets/js/script.js' ) : DINO_WORLD_VERSION;
	wp_enqueue_script(
		'dino-script',
		DINO_WORLD_URI . '/assets/js/script.js',
		array( 'jquery', 'bootstrap-js', 'aos-js', 'splide-js' ),
		$script_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'dino_world_enqueue_assets' );

/**
 * Custom Nav Walker for Bootstrap 5
 */
class Dino_Bootstrap_Navwalker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'nav-item';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= '<li' . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		if ( '_blank' === $item->target && empty( $item->xfn ) ) {
			$atts['rel'] = 'noopener noreferrer';
		} else {
			$atts['rel'] = $item->xfn;
		}
		$atts['href'] = ! empty( $item->url ) ? $item->url : '';

		$link_classes = array( 'nav-link' );
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
			$link_classes[] = 'active';
			$atts['aria-current'] = 'page';
		}

		$atts['class'] = join( ' ', $link_classes );
		$atts          = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/**
 * Register ACF / SCF Theme Options Page
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => __( 'Theme Options', 'dino-world' ),
			'menu_title' => __( 'Theme Options', 'dino-world' ),
			'menu_slug'  => 'theme-options',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'icon_url'   => 'dashicons-admin-generic',
			'position'   => 59,
		)
	);
}

/**
 * Helpers
 */
if ( ! function_exists( 'dino_get_option' ) ) {
	function dino_get_option( $field_name, $default = '' ) {
		if ( function_exists( 'get_field' ) ) {
			$val = get_field( $field_name, 'option' );
			if ( ! empty( $val ) ) {
				return $val;
			}
		}
		return $default;
	}
}

if ( ! function_exists( 'dino_get_image_option' ) ) {
	function dino_get_image_option( $field_name, $default = '' ) {
		if ( function_exists( 'get_field' ) ) {
			$img = get_field( $field_name, 'option' );
			if ( ! empty( $img ) ) {
				if ( is_array( $img ) && ! empty( $img['url'] ) ) {
					return $img['url'];
				} elseif ( is_numeric( $img ) ) {
					$src = wp_get_attachment_image_url( (int) $img, 'full' );
					if ( $src ) {
						return $src;
					}
				} elseif ( is_string( $img ) ) {
					return $img;
				}
			}
		}
		return $default;
	}
}

if ( ! function_exists( 'dino_clean_phone' ) ) {
	function dino_clean_phone( $phone ) {
		return preg_replace( '/[^0-9+]/', '', $phone );
	}
}
/**
 * SVG Upload Support
 */
function allow_svg_img( $svg_mime ) {
	$svg_mime['svg'] = 'image/svg+xml';
	return $svg_mime;
}
add_filter( 'upload_mimes', 'allow_svg_img' );

/**
 * Register Custom Block Styles
 */
function dino_world_register_block_styles() {
	if ( function_exists( 'register_block_style' ) ) {
		// Green Heading for core/heading -> adds 'is-style-green-heading' & 'title-2'
		register_block_style(
			'core/heading',
			array(
				'name'         => 'green-heading',
				'label'        => __( 'Green Heading', 'dino-world' ),
				'inline_style' => '.is-style-green-heading { font-family: "Afacad Flux", sans-serif; font-size: 40px; font-weight: 900; text-transform: uppercase; margin-bottom: 30px; color: #233400; }',
			)
		);

		// Paragraph Large Style for core/paragraph -> adds 'is-style-paragraph-large' & 'w-para'
		register_block_style(
			'core/paragraph',
			array(
				'name'         => 'paragraph-large',
				'label'        => __( 'Paragraph Large', 'dino-world' ),
				'inline_style' => '.is-style-paragraph-large, .is-style-w-para { font-size: 24px; color: #000; font-family: "Afacad Flux", sans-serif; text-align: center; font-weight: 500; margin: 22px auto 0; text-transform: capitalize; }',
			)
		);

		// Paragraph Medium Style for core/paragraph -> adds 'is-style-paragraph-medium' & 'place-para'
		register_block_style(
			'core/paragraph',
			array(
				'name'         => 'paragraph-medium',
				'label'        => __( 'Paragraph Medium', 'dino-world' ),
				'inline_style' => '.is-style-paragraph-medium, .is-style-place-para { font-size: 16px; color: #000; font-family: "Poppins", sans-serif; }',
			)
		);
	}
}
add_action( 'init', 'dino_world_register_block_styles' );

/**
 * Add custom classes to core blocks when specific styles are selected
 */
function dino_world_render_custom_block_styles( $block_content, $block ) {
	if ( ! empty( $block['blockName'] ) ) {
		// Heading: add 'title-2' when 'green-heading' style is selected
		if ( 'core/heading' === $block['blockName'] ) {
			$class_name = ! empty( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
			if ( strpos( $class_name, 'is-style-green-heading' ) !== false || strpos( $block_content, 'is-style-green-heading' ) !== false ) {
				if ( strpos( $block_content, 'title-2' ) === false ) {
					$block_content = preg_replace( '/class="([^"]*is-style-green-heading[^"]*)"/', 'class="$1 title-2"', $block_content, 1 );
				}
			}
		}

		// Paragraph: add 'w-para' when 'paragraph-large' style is selected
		if ( 'core/paragraph' === $block['blockName'] ) {
			$class_name = ! empty( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
			
			// Large Paragraph -> add 'w-para'
			if ( strpos( $class_name, 'is-style-paragraph-large' ) !== false || strpos( $class_name, 'is-style-w-para' ) !== false || strpos( $block_content, 'is-style-paragraph-large' ) !== false || strpos( $block_content, 'is-style-w-para' ) !== false ) {
				if ( strpos( $block_content, 'w-para' ) === false ) {
					$block_content = preg_replace( '/class="([^"]*is-style-(?:paragraph-large|w-para)[^"]*)"/', 'class="$1 w-para"', $block_content, 1 );
				}
			}

			// Medium Paragraph -> add 'place-para'
			if ( strpos( $class_name, 'is-style-paragraph-medium' ) !== false || strpos( $class_name, 'is-style-place-para' ) !== false || strpos( $block_content, 'is-style-paragraph-medium' ) !== false || strpos( $block_content, 'is-style-place-para' ) !== false ) {
				if ( strpos( $block_content, 'place-para' ) === false ) {
					$block_content = preg_replace( '/class="([^"]*is-style-(?:paragraph-medium|place-para)[^"]*)"/', 'class="$1 place-para"', $block_content, 1 );
				}
			}
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'dino_world_render_custom_block_styles', 10, 2 );

/**
 * Enqueue Block Editor Assets for Backend Gutenberg
 */
function dino_world_block_editor_assets() {
	wp_enqueue_style(
		'dino-google-fonts',
		'https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'dino-editor-custom-style',
		DINO_WORLD_URI . '/assets/css/style.css',
		array(),
		DINO_WORLD_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'dino_world_block_editor_assets' );

/**
 * Fallback: Ensure Dino World Blocks plugin is loaded and registered
 * even if not yet explicitly activated via wp-admin.
 */
add_action(
	'after_setup_theme',
	function() {
		if ( ! function_exists( 'dino_register_acf_blocks' ) ) {
			$plugin_file = WP_PLUGIN_DIR . '/dino-world-blocks/dino-world-blocks.php';
			if ( file_exists( $plugin_file ) ) {
				require_once $plugin_file;
			}
		}
	},
	5
);