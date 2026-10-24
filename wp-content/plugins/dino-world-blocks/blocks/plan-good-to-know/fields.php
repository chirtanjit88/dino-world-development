<?php
/**
 * ACF Field Group: Plan – Good to Know Visitor Guide
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_good_to_know',
			'title'                 => __( 'Plan – Good to Know Visitor Guide', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_gk_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Visitor Guide',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_gk_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Good to Know Before You Arrive',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_gk_tips',
					'label'        => __( 'Visitor Tips Grid', 'dino-world-blocks' ),
					'name'         => 'tips',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Tip Card', 'dino-world-blocks' ),
					'wrapper'      => array( 'width' => '100' ),
					'sub_fields'   => array(
						array(
							'key'     => 'field_plan_gk_tip_icon',
							'label'   => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'    => 'icon',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_gk_tip_title',
							'label'   => __( 'Tip Title', 'dino-world-blocks' ),
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_gk_tip_desc',
							'label'   => __( 'Tip Description', 'dino-world-blocks' ),
							'name'    => 'description',
							'type'    => 'textarea',
							'rows'    => 2,
							'wrapper' => array( 'width' => '100' ),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-good-to-know',
					),
				),
			),
			'menu_order'            => 6,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
