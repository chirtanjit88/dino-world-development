<?php
/**
 * ACF Field Group: Plan – Family Adventure Banner
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_family_banner',
			'title'                 => __( 'Plan – Family Adventure Banner', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_fb_image',
					'label'        => __( 'Banner Image', 'dino-world-blocks' ),
					'name'         => 'image',
					'type'         => 'image',
					'return_format'=> 'array',
					'preview_size' => 'medium',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fb_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Where Imaginations Roar',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fb_heading',
					'label'        => __( 'Banner Title', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Unforgettable Prehistoric Fun for All Generations',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fb_desc',
					'label'        => __( 'Description Paragraph', 'dino-world-blocks' ),
					'name'         => 'description',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value'=> 'From toddler smiles meeting their very first life-size Brachiosaurus to grandparents relaxing in shady picnic pavilions, Dinosaur World Somerville offers a gentle, delightful day out that the whole family will cherish.',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fb_btn',
					'label'        => __( 'Action Button', 'dino-world-blocks' ),
					'name'         => 'button',
					'type'         => 'link',
					'return_format'=> 'array',
					'wrapper'      => array( 'width' => '100' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-family-banner',
					),
				),
			),
			'menu_order'            => 5,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
