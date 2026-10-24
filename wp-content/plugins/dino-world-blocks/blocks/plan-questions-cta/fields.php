<?php
/**
 * ACF Field Group: Plan – Have Questions CTA
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_questions_cta',
			'title'                 => __( 'Plan – Have Questions CTA', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_qc_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> "We're Here to Help",
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_qc_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Have Questions Before You Visit?',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_qc_desc',
					'label'        => __( 'Description / Subtitle', 'dino-world-blocks' ),
					'name'         => 'description',
					'type'         => 'textarea',
					'rows'         => 2,
					'default_value'=> 'Our friendly park crew is ready to answer any questions about accessibility, party bookings, holiday hours, or group visits.',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_qc_contact_pills',
					'label'        => __( 'Contact Pills Strip', 'dino-world-blocks' ),
					'name'         => 'contact_pills',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Contact Pill', 'dino-world-blocks' ),
					'wrapper'      => array( 'width' => '100' ),
					'sub_fields'   => array(
						array(
							'key'     => 'field_plan_qc_pill_icon',
							'label'   => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'    => 'icon',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_qc_pill_text',
							'label'   => __( 'Label Text', 'dino-world-blocks' ),
							'name'    => 'text',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_qc_pill_link',
							'label'   => __( 'Link / URL / Tel / Mailto', 'dino-world-blocks' ),
							'name'    => 'link',
							'type'    => 'text',
							'wrapper' => array( 'width' => '100' ),
						),
						array(
							'key'     => 'field_plan_qc_pill_target',
							'label'   => __( 'Open in New Tab?', 'dino-world-blocks' ),
							'name'    => 'new_tab',
							'type'    => 'true_false',
							'wrapper' => array( 'width' => '100' ),
						),
					),
				),
				array(
					'key'          => 'field_plan_qc_primary_btn',
					'label'        => __( 'Primary Button (Orange / Phone)', 'dino-world-blocks' ),
					'name'         => 'primary_button',
					'type'         => 'link',
					'return_format'=> 'array',
					'wrapper'      => array( 'width' => '100' ),
				),
				array(
					'key'          => 'field_plan_qc_sec_btn',
					'label'        => __( 'Secondary Button (Olive / Email)', 'dino-world-blocks' ),
					'name'         => 'secondary_button',
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
						'value'    => 'dino-world/plan-questions-cta',
					),
				),
			),
			'menu_order'            => 7,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
