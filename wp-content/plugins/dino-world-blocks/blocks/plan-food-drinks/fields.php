<?php
/**
 * ACF Field Group: Plan – Food, Drinks & Picnic Facilities
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_food_drinks',
			'title'                 => __( 'Plan – Food, Drinks & Picnic Facilities', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_fd_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Refuel & Relax',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fd_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Food, Drinks & Picnic Facilities',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_fd_cards',
					'label'        => __( 'Facility & Dining Cards', 'dino-world-blocks' ),
					'name'         => 'cards',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Dining / Facility Card', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'     => 'field_plan_fd_card_icon',
							'label'   => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'    => 'icon',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_fd_card_title',
							'label'   => __( 'Card Title', 'dino-world-blocks' ),
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_fd_card_content',
							'label'   => __( 'Card Description / Paragraph', 'dino-world-blocks' ),
							'name'    => 'content',
							'type'    => 'wysiwyg',
							'tabs'    => 'visual',
							'toolbar' => 'basic',
							'media_upload' => 0,
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'          => 'field_plan_fd_card_tags',
							'label'        => __( 'Highlight Tags / Pills', 'dino-world-blocks' ),
							'name'         => 'tags',
							'type'         => 'repeater',
							'layout'       => 'block',
							'button_label' => __( 'Add Tag Pill', 'dino-world-blocks' ),
							'sub_fields'   => array(
								array(
									'key'     => 'field_plan_fd_tag_text',
									'label'   => __( 'Pill Label', 'dino-world-blocks' ),
									'name'    => 'tag_text',
									'type'    => 'text',
									'wrapper' => array( 'width' => '100' ),
								),
							),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-food-drinks',
					),
				),
			),
			'menu_order'            => 4,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
