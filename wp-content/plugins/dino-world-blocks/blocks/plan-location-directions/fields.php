<?php
/**
 * ACF Field Group: Plan – Location & Directions
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_location_directions',
			'title'                 => __( 'Plan – Location & Directions', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_ld_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Find Us',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'How to Get to Dinosaur World',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_items',
					'label'        => __( 'Directions & Travel Points (Left Column)', 'dino-world-blocks' ),
					'name'         => 'directions_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Travel Point', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'     => 'field_plan_ld_item_icon',
							'label'   => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'    => 'icon',
							'type'    => 'text',
							'wrapper' => array( 'width' => '20' ),
						),
						array(
							'key'     => 'field_plan_ld_item_title',
							'label'   => __( 'Point Title', 'dino-world-blocks' ),
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '80' ),
						),
						array(
							'key'     => 'field_plan_ld_item_desc',
							'label'   => __( 'Point Description', 'dino-world-blocks' ),
							'name'    => 'description',
							'type'    => 'wysiwyg',
							'tabs'    => 'visual',
							'toolbar' => 'basic',
							'media_upload' => 0,
						),
					),
				),
				array(
					'key'          => 'field_plan_ld_dir_btn',
					'label'        => __( 'Directions Button', 'dino-world-blocks' ),
					'name'         => 'directions_button',
					'type'         => 'link',
					'return_format'=> 'array',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_map_bg',
					'label'        => __( 'Map Card Background Image', 'dino-world-blocks' ),
					'name'         => 'map_bg_image',
					'type'         => 'image',
					'return_format'=> 'array',
					'preview_size' => 'medium',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_pin_title',
					'label'        => __( 'Map Pin Badge Title', 'dino-world-blocks' ),
					'name'         => 'map_pin_title',
					'type'         => 'text',
					'default_value'=> '📍 Dinosaur World Somerville',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_pin_address',
					'label'        => __( 'Map Pin Badge Address / Details', 'dino-world-blocks' ),
					'name'         => 'map_pin_address',
					'type'         => 'wysiwyg',
					'tabs'         => 'visual',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'default_value'=> "1385 Frankston-Flinders Road, Somerville VIC 3912<br>Corner Frankston-Flinders Rd & O'Neills Rd<br><strong>Look for the Giant Dinosaur Gate!</strong>",
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_map_dino',
					'label'        => __( 'Map Overlay Dino Sketch Graphic', 'dino-world-blocks' ),
					'name'         => 'map_dino_image',
					'type'         => 'image',
					'return_format'=> 'array',
					'preview_size' => 'thumbnail',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_ld_map_btn',
					'label'        => __( 'Map Card Bottom Button', 'dino-world-blocks' ),
					'name'         => 'map_button',
					'type'         => 'link',
					'return_format'=> 'array',
					'wrapper'      => array( 'width' => '50' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-location-directions',
					),
				),
			),
			'menu_order'            => 3,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
