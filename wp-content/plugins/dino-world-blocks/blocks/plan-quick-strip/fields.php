<?php
/**
 * ACF Field Group: Plan – Quick Highlights Strip
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_quick_strip',
			'title'                 => __( 'Plan – Quick Highlights Strip', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_qs_highlights',
					'label'        => __( 'Highlights Items', 'dino-world-blocks' ),
					'name'         => 'highlights',
					'type'         => 'repeater',
					'instructions' => __( 'Add quick highlight items for the strip (recommended: 4 items).', 'dino-world-blocks' ),
					'layout'       => 'block',
					'button_label' => __( 'Add Highlight Item', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_plan_qs_icon',
							'label' => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'  => 'icon',
							'type'  => 'text',
							'wrapper' => array( 'width' => '25' ),
						),
						array(
							'key'   => 'field_plan_qs_title',
							'label' => __( 'Title', 'dino-world-blocks' ),
							'name'  => 'title',
							'type'  => 'text',
							'wrapper' => array( 'width' => '35' ),
						),
						array(
							'key'   => 'field_plan_qs_subtitle',
							'label' => __( 'Subtitle / Note', 'dino-world-blocks' ),
							'name'  => 'subtitle',
							'type'  => 'text',
							'wrapper' => array( 'width' => '40' ),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-quick-strip',
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
