<?php
/**
 * ACF Field Group: Excursion Packages
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
		'key'                   => 'group_6ac3971e2bd12',
		'title'                 => __( 'Excursion Packages', 'dino-world-blocks' ),
		'fields'                => array(
			array(
				'key'          => 'field_6ac3971eff67b',
				'label'        => __( 'Heading', 'dino-world-blocks' ),
				'name'         => 'heading',
				'type'         => 'text',
				'instructions' => __( 'Section title above packages list.', 'dino-world-blocks' ),
				'wrapper'      => array( 'width' => '100' ),
			),
			array(
				'key'          => 'field_6ac3972eff67c',
				'label'        => __( 'Packages', 'dino-world-blocks' ),
				'name'         => 'packages',
				'type'         => 'repeater',
				'instructions' => __( 'Add excursion package cards.', 'dino-world-blocks' ),
				'layout'       => 'block',
				'button_label' => __( 'Add Package', 'dino-world-blocks' ),
				'wrapper'      => array( 'width' => '100' ),
				'sub_fields'   => array(
					array(
						'key'     => 'field_6ac39747ff67d',
						'label'   => __( 'Package Title', 'dino-world-blocks' ),
						'name'    => 'package_title',
						'type'    => 'text',
						'wrapper' => array( 'width' => '100' ),
					),
					array(
						'key'     => 'field_6ac39754ff67e',
						'label'   => __( 'Subtitle', 'dino-world-blocks' ),
						'name'    => 'subtitle',
						'type'    => 'text',
						'wrapper' => array( 'width' => '100' ),
					),
					array(
						'key'          => 'field_6ac39771ff67f',
						'label'        => __( 'Features', 'dino-world-blocks' ),
						'name'         => 'features',
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Add Feature', 'dino-world-blocks' ),
						'wrapper'      => array( 'width' => '100' ),
						'sub_fields'   => array(
							array(
								'key'     => 'field_6ac3978bff680',
								'label'   => __( 'Feature Text', 'dino-world-blocks' ),
								'name'    => 'feature_text',
								'type'    => 'text',
								'wrapper' => array( 'width' => '100' ),
							),
						),
					),
					array(
						'key'          => 'field_6ac397a0ff681',
						'label'        => __( 'Price Text', 'dino-world-blocks' ),
						'name'         => 'price_text',
						'type'         => 'text',
						'instructions' => __( 'e.g. $25 + GST per student ($27.50 inc. GST)', 'dino-world-blocks' ),
						'wrapper'      => array( 'width' => '100' ),
					),
					array(
						'key'     => 'field_6ac397b0ff682',
						'label'   => __( 'Is Featured', 'dino-world-blocks' ),
						'name'    => 'is_featured',
						'type'    => 'true_false',
						'ui'      => 1,
						'wrapper' => array( 'width' => '100' ),
					),
					array(
						'key'          => 'field_6ad1ceef44172',
						'label'        => __( 'Information', 'dino-world-blocks' ),
						'name'         => 'information',
						'type'         => 'wysiwyg',
						'tabs'         => 'all',
						'toolbar'      => 'full',
						'media_upload' => 0,
						'wrapper'      => array( 'width' => '100' ),
					),
					array(
						'key'     => 'field_6ad1cf0044173',
						'label'   => __( 'Button', 'dino-world-blocks' ),
						'name'    => 'button',
						'type'    => 'link',
						'wrapper' => array( 'width' => '100' ),
					),
					array(
						'key'          => 'field_6add12ef33e4d',
						'label'        => __( 'Enquire Form Shortcode', 'dino-world-blocks' ),
						'name'         => 'enquire_form_shortcode',
						'type'         => 'text',
						'instructions' => __( 'Enter contact/booking form shortcode (e.g. [forminator_form id="310"]) to open in a popup on click.', 'dino-world-blocks' ),
						'placeholder'  => '[forminator_form id="310"]',
						'wrapper'      => array( 'width' => '100' ),
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'dino-world/excursion-packages',
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
