<?php
/**
 * Plugin Name: Dinosaur World ACF Custom Blocks
 * Plugin URI:  https://dinosaurworld.com.au
 * Description: Custom Gutenberg blocks powered by ACF (Advanced Custom Fields / Secure Custom Fields) for Dinosaur World.
 * Version:     1.0.0
 * Author:      Dinosaur World Development Team
 * Author URI:  https://dinosaurworld.com.au
 * Text Domain: dino-world-blocks
 * Domain Path: /languages
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'DINO_BLOCKS_VERSION', '1.0.0' );
define( 'DINO_BLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'DINO_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin textdomain on init
 */
function dino_load_blocks_textdomain() {
	load_plugin_textdomain( 'dino-world-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'dino_load_blocks_textdomain' );

/**
 * Register custom Gutenberg block category for Dinosaur World
 *
 * @param array                   $categories Array of block categories.
 * @param WP_Block_Editor_Context $context    The current block editor context.
 * @return array
 */
function dino_register_block_category( $categories, $context ) {
	return array_merge(
		array(
			array(
				'slug'  => 'dino-world-blocks',
				'title' => __( '🦖 Dinosaur World Blocks', 'dino-world-blocks' ),
				'icon'  => 'palmtree',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'dino_register_block_category', 10, 2 );

/**
 * Check if ACF / Secure Custom Fields is active.
 * If not, show an admin notice.
 */
function dino_blocks_admin_notice() {
	if ( ! function_exists( 'acf_register_block_type' ) && ! function_exists( 'register_block_type' ) ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Dinosaur World Blocks:', 'dino-world-blocks' ); ?></strong>
				<?php esc_html_e( 'ACF Pro or Secure Custom Fields plugin is required to use custom ACF Gutenberg blocks.', 'dino-world-blocks' ); ?>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'dino_blocks_admin_notice' );

/**
 * Automatically register all ACF blocks found inside the /blocks directory.
 * Each block contains a block.json metadata file.
 */
function dino_register_acf_blocks() {
	static $registered = false;
	if ( $registered ) {
		return;
	}

	$blocks_dir = DINO_BLOCKS_DIR . 'blocks';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$block_folders = scandir( $blocks_dir );

	foreach ( $block_folders as $folder ) {
		if ( '.' === $folder || '..' === $folder ) {
			continue;
		}

		$block_path = $blocks_dir . '/' . $folder;

		if ( is_dir( $block_path ) ) {
			if ( file_exists( $block_path . '/block.json' ) && function_exists( 'register_block_type' ) ) {
				register_block_type( $block_path . '/block.json' );
			} elseif ( file_exists( $block_path . '/init.php' ) ) {
				require_once $block_path . '/init.php';
			}
		}
	}

	$registered = true;
}
add_action( 'init', 'dino_register_acf_blocks', 10 );


