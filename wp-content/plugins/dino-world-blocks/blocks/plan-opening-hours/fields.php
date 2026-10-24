<?php
/**
 * ACF Field Group: Plan – Opening Hours
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_opening_hours',
			'title'                 => __( 'Plan – Opening Hours', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_oh_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Plan Your Timing',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_oh_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Park Opening Hours',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_oh_schedule_title',
					'label'        => __( 'Schedule Card Title', 'dino-world-blocks' ),
					'name'         => 'schedule_title',
					'type'         => 'text',
					'default_value'=> 'Weekly Operating Schedule',
				),
				array(
					'key'          => 'field_plan_oh_schedule_rows',
					'label'        => __( 'Schedule Operating Rows', 'dino-world-blocks' ),
					'name'         => 'schedule_rows',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add Operating Row', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_plan_oh_day',
							'label' => __( 'Day / Period', 'dino-world-blocks' ),
							'name'  => 'day',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_plan_oh_time',
							'label' => __( 'Opening Times', 'dino-world-blocks' ),
							'name'  => 'time',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_plan_oh_tag_text',
							'label' => __( 'Badge / Note Tag', 'dino-world-blocks' ),
							'name'  => 'tag_text',
							'type'  => 'text',
						),
						array(
							'key'           => 'field_plan_oh_tag_style',
							'label'         => __( 'Badge Style', 'dino-world-blocks' ),
							'name'          => 'tag_style',
							'type'          => 'select',
							'choices'       => array(
								'tag-open'    => __( 'Green (Open)', 'dino-world-blocks' ),
								'tag-story'   => __( 'Purple (Story Time)', 'dino-world-blocks' ),
								'tag-holiday' => __( 'Orange (Holiday)', 'dino-world-blocks' ),
							),
							'default_value' => 'tag-open',
						),
					),
				),
				array(
					'key'          => 'field_plan_oh_notes',
					'label'        => __( 'Notes & Advisories (Right Column)', 'dino-world-blocks' ),
					'name'         => 'notes',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Advisory Box', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_plan_oh_note_title',
							'label' => __( 'Note Title', 'dino-world-blocks' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_plan_oh_note_content',
							'label' => __( 'Note Description', 'dino-world-blocks' ),
							'name'  => 'content',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'           => 'field_plan_oh_note_type',
							'label'         => __( 'Advisory Type / Box Style', 'dino-world-blocks' ),
							'name'          => 'note_type',
							'type'          => 'select',
							'choices'       => array(
								'default'     => __( 'Standard Card', 'dino-world-blocks' ),
								'heat-policy' => __( 'Heat Policy (Amber Border)', 'dino-world-blocks' ),
								'pro-tip'     => __( 'Explorer Tip (Golden Banner)', 'dino-world-blocks' ),
							),
							'default_value' => 'default',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-opening-hours',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
