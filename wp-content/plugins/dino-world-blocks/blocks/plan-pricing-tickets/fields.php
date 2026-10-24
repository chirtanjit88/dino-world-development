<?php
/**
 * ACF Field Group: Plan – Admission & Ticket Prices
 *
 * @package Dino_World_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( array(
			'key'                   => 'group_dino_plan_pricing_tickets',
			'title'                 => __( 'Plan – Admission & Ticket Prices', 'dino-world-blocks' ),
			'fields'                => array(
				array(
					'key'          => 'field_plan_pt_short_heading',
					'label'        => __( 'Short Heading / Eyebrow', 'dino-world-blocks' ),
					'name'         => 'short_heading',
					'type'         => 'text',
					'default_value'=> 'Simple & Transparent',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_pt_heading',
					'label'        => __( 'Main Heading', 'dino-world-blocks' ),
					'name'         => 'heading',
					'type'         => 'text',
					'default_value'=> 'Gate Admission & Ticket Prices',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_plan_pt_alert_icon',
					'label'        => __( 'Alert Box Icon / Emoji', 'dino-world-blocks' ),
					'name'         => 'alert_icon',
					'type'         => 'text',
					'default_value'=> '🎟️',
					'wrapper'      => array( 'width' => '20' ),
				),
				array(
					'key'          => 'field_plan_pt_alert_text',
					'label'        => __( 'Gate Notice / Alert Text', 'dino-world-blocks' ),
					'name'         => 'alert_text',
					'type'         => 'wysiwyg',
					'tabs'         => 'visual',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'default_value'=> '<strong>Tickets are sold at the gate only — no online bookings needed!</strong> Just turn up, grab your passes, and roar!',
					'wrapper'      => array( 'width' => '80' ),
				),
				array(
					'key'          => 'field_plan_pt_pricing_cards',
					'label'        => __( 'Pricing Cards', 'dino-world-blocks' ),
					'name'         => 'pricing_cards',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Pricing Card', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_plan_pt_card_title',
							'label'        => __( 'Card Title', 'dino-world-blocks' ),
							'name'         => 'card_title',
							'type'         => 'text',
							'wrapper'      => array( 'width' => '30' ),
						),
						array(
							'key'          => 'field_plan_pt_card_sub',
							'label'        => __( 'Age Range / Subtitle', 'dino-world-blocks' ),
							'name'         => 'subtitle',
							'type'         => 'text',
							'wrapper'      => array( 'width' => '30' ),
						),
						array(
							'key'          => 'field_plan_pt_card_price',
							'label'        => __( 'Price Value', 'dino-world-blocks' ),
							'name'         => 'price',
							'type'         => 'text',
							'instructions' => __( 'e.g. $22, $20, $78, or FREE', 'dino-world-blocks' ),
							'wrapper'      => array( 'width' => '20' ),
						),
						array(
							'key'          => 'field_plan_pt_card_label',
							'label'        => __( 'Price Label', 'dino-world-blocks' ),
							'name'         => 'price_label',
							'type'         => 'text',
							'instructions' => __( 'e.g. Per Person, Complete Family Admission', 'dino-world-blocks' ),
							'wrapper'      => array( 'width' => '20' ),
						),
						array(
							'key'          => 'field_plan_pt_is_featured',
							'label'        => __( 'Featured Card? (Highlight)', 'dino-world-blocks' ),
							'name'         => 'is_featured',
							'type'         => 'true_false',
							'ui'           => 1,
							'wrapper'      => array( 'width' => '30' ),
						),
						array(
							'key'               => 'field_plan_pt_featured_badge',
							'label'             => __( 'Featured Badge Text', 'dino-world-blocks' ),
							'name'              => 'featured_badge',
							'type'              => 'text',
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_plan_pt_is_featured',
										'operator' => '==',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array( 'width' => '40' ),
						),
						array(
							'key'          => 'field_plan_pt_paw_image',
							'label'        => __( 'Card Footer Paw Icon / Image', 'dino-world-blocks' ),
							'name'         => 'paw_image',
							'type'         => 'image',
							'return_format'=> 'array',
							'preview_size' => 'thumbnail',
							'wrapper'      => array( 'width' => '30' ),
						),
						array(
							'key'          => 'field_plan_pt_features',
							'label'        => __( 'Included Features / Bullet Points', 'dino-world-blocks' ),
							'name'         => 'features',
							'type'         => 'repeater',
							'layout'       => 'table',
							'button_label' => __( 'Add Feature Line', 'dino-world-blocks' ),
							'sub_fields'   => array(
								array(
									'key'   => 'field_plan_pt_feature_text',
									'label' => __( 'Feature Description', 'dino-world-blocks' ),
									'name'  => 'text',
									'type'  => 'text',
								),
							),
						),
					),
				),
				array(
					'key'          => 'field_plan_pt_addons',
					'label'        => __( 'Add-on Attractions & Inclusions', 'dino-world-blocks' ),
					'name'         => 'addons',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add Attraction Item', 'dino-world-blocks' ),
					'sub_fields'   => array(
						array(
							'key'     => 'field_plan_pt_addon_icon',
							'label'   => __( 'Icon / Emoji', 'dino-world-blocks' ),
							'name'    => 'icon',
							'type'    => 'text',
							'wrapper' => array( 'width' => '20' ),
						),
						array(
							'key'     => 'field_plan_pt_addon_badge',
							'label'   => __( 'Badge Text', 'dino-world-blocks' ),
							'name'    => 'badge_text',
							'type'    => 'text',
							'wrapper' => array( 'width' => '40' ),
						),
						array(
							'key'           => 'field_plan_pt_addon_badge_type',
							'label'         => __( 'Badge Type', 'dino-world-blocks' ),
							'name'          => 'badge_type',
							'type'          => 'select',
							'choices'       => array(
								'badge-free'  => __( 'Free (Green)', 'dino-world-blocks' ),
								'badge-price' => __( 'Price (Amber)', 'dino-world-blocks' ),
							),
							'default_value' => 'badge-price',
							'wrapper'       => array( 'width' => '40' ),
						),
						array(
							'key'     => 'field_plan_pt_addon_title',
							'label'   => __( 'Attraction Title', 'dino-world-blocks' ),
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '50' ),
						),
						array(
							'key'     => 'field_plan_pt_addon_desc',
							'label'   => __( 'Attraction Description', 'dino-world-blocks' ),
							'name'    => 'description',
							'type'    => 'textarea',
							'rows'    => 2,
							'wrapper' => array( 'width' => '50' ),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'dino-world/plan-pricing-tickets',
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
	) );
}
