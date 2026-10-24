<?php
/**
 * ACF Field Group: Inner Banner
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
		'key'                   => 'group_6ac2a4eeed29e',
		'title'                 => __( 'Inner Banner', 'dino-world-blocks' ),
		'fields'                => array(
			array(
				'key'          => 'field_6ac2a4ef2fcaf',
				'label'        => __( 'Title', 'dino-world-blocks' ),
				'name'         => 'title',
				'type'         => 'text',
				'instructions' => __( 'Page banner main heading.', 'dino-world-blocks' ),
				'wrapper'      => array( 'width' => '100' ),
			),
			array(
				'key'          => 'field_6ac2a5022fcb0',
				'label'        => __( 'Description', 'dino-world-blocks' ),
				'name'         => 'description',
				'type'         => 'wysiwyg',
				'instructions' => __( 'Subtitle or brief narrative beneath the banner title.', 'dino-world-blocks' ),
				'tabs'         => 'all',
				'toolbar'      => 'full',
				'media_upload' => 1,
				'wrapper'      => array( 'width' => '100' ),
			),
			array(
				'key'           => 'field_6ac2a50e2fcb1',
				'label'         => __( 'Background Image', 'dino-world-blocks' ),
				'name'          => 'background_image',
				'type'          => 'image',
				'instructions'  => __( 'Hero background image.', 'dino-world-blocks' ),
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'wrapper'       => array( 'width' => '100' ),
			),
			array(
				'key'           => 'field_6ac2a51a2fcb2',
				'label'         => __( 'Overlay Opacity', 'dino-world-blocks' ),
				'name'          => 'overlay_opacity',
				'type'          => 'text',
				'instructions'  => __( 'Dark overlay opacity between 0.0 (fully transparent) and 1.0 (fully dark). Default is 0.35.', 'dino-world-blocks' ),
				'default_value' => '0.35',
				'placeholder'   => '0.35',
				'wrapper'       => array( 'width' => '100' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'dino-world/inner-banner',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
	) );
}
