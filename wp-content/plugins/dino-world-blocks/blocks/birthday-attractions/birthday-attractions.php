<?php
/**
 * Block Name: Birthday Party & Attractions
 *
 * ACF Field Architecture:
 * 1. Birthday & Activity Promo Cards:
 *    - birthday_&_activity_promo_cards (Group)
 *      - promo_cards (Repeater)
 *        - card_title (Text)
 *        - card_description (Textarea / WYSIWYG)
 *        - features (Repeater: text [Text])
 *        - button (Link)
 *        - image (Image)
 *
 * 2. Attraction Grid:
 *    - attraction_grid / attractions_grid (Group)
 *      - attractions_tagline (Text)
 *      - attractions_heading (Text)
 *      - attractions (Repeater)
 *        - attractions_selection (Post Object: Attraction CPT)
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'common-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'common-sec sec-padding';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// ==========================================
// 1. EXTRACT PROMO CARDS (Group -> Repeater)
// ==========================================
$promo_cards_list = array();

if ( function_exists( 'have_rows' ) ) {
	$group_keys = array( 'birthday_&_activity_promo_cards', 'birthday_activity_promo_cards', 'promo_cards_group' );
	foreach ( $group_keys as $g_key ) {
		if ( have_rows( $g_key ) ) {
			while ( have_rows( $g_key ) ) {
				the_row();
				if ( have_rows( 'promo_cards' ) ) {
					while ( have_rows( 'promo_cards' ) ) {
						the_row();
						$feats = array();
						if ( have_rows( 'features' ) ) {
							while ( have_rows( 'features' ) ) {
								the_row();
								$f_text = get_sub_field( 'text' );
								if ( ! empty( $f_text ) ) {
									$feats[] = $f_text;
								}
							}
						}
						$promo_cards_list[] = array(
							'title'       => get_sub_field( 'card_title' ) ?: get_sub_field( 'title' ),
							'description' => get_sub_field( 'card_description' ) ?: get_sub_field( 'description' ),
							'features'    => $feats,
							'button'      => get_sub_field( 'button' ) ?: get_sub_field( 'link' ),
							'image'       => get_sub_field( 'image' ) ?: get_sub_field( 'card_image' ),
						);
					}
				}
			}
			break;
		}
	}

	// Flat repeater fallback
	if ( empty( $promo_cards_list ) ) {
		$rep_keys = array( 'promo_cards', 'promo_card', 'cards' );
		foreach ( $rep_keys as $r_key ) {
			if ( have_rows( $r_key ) ) {
				while ( have_rows( $r_key ) ) {
					the_row();
					$feats = array();
					if ( have_rows( 'features' ) ) {
						while ( have_rows( 'features' ) ) {
							the_row();
							$f_text = get_sub_field( 'text' );
							if ( ! empty( $f_text ) ) {
								$feats[] = $f_text;
							}
						}
					}
					$promo_cards_list[] = array(
						'title'       => get_sub_field( 'card_title' ) ?: get_sub_field( 'title' ),
						'description' => get_sub_field( 'card_description' ) ?: get_sub_field( 'description' ),
						'features'    => $feats,
						'button'      => get_sub_field( 'button' ) ?: get_sub_field( 'link' ),
						'image'       => get_sub_field( 'image' ) ?: get_sub_field( 'card_image' ),
					);
				}
				break;
			}
		}
	}
}

// Fallback from raw block data
if ( empty( $promo_cards_list ) && ! empty( $block_data ) ) {
	$card_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/promo_cards$/', $k ) && is_numeric( $v ) ) {
			$card_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $card_count; $i++ ) {
		$c_title = '';
		$c_desc  = '';
		$c_btn   = null;
		$c_img   = null;
		$c_feats = array();

		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) {
				continue;
			}
			if ( preg_match( "/promo_cards_{$i}_card_title$/", $k ) || preg_match( "/promo_cards_{$i}_title$/", $k ) ) {
				$c_title = $v;
			}
			if ( preg_match( "/promo_cards_{$i}_card_description$/", $k ) || preg_match( "/promo_cards_{$i}_description$/", $k ) ) {
				$c_desc = $v;
			}
			if ( preg_match( "/promo_cards_{$i}_button$/", $k ) || preg_match( "/promo_cards_{$i}_link$/", $k ) ) {
				$c_btn = $v;
			}
			if ( preg_match( "/promo_cards_{$i}_image$/", $k ) ) {
				$c_img = $v;
			}
		}

		$feat_count = 0;
		foreach ( $block_data as $k => $v ) {
			if ( ! empty( $k ) && $k[0] !== '_' && preg_match( "/promo_cards_{$i}_features$/", $k ) && is_numeric( $v ) ) {
				$feat_count = (int) $v;
				break;
			}
		}
		for ( $j = 0; $j < $feat_count; $j++ ) {
			foreach ( $block_data as $k => $v ) {
				if ( empty( $k ) || $k[0] === '_' ) {
					continue;
				}
				if ( preg_match( "/promo_cards_{$i}_features_{$j}_text$/", $k ) ) {
					$c_feats[] = $v;
				}
			}
		}

		if ( ! empty( $c_title ) || ! empty( $c_desc ) || ! empty( $c_img ) ) {
			$promo_cards_list[] = array(
				'title'       => $c_title,
				'description' => $c_desc,
				'features'    => $c_feats,
				'button'      => $c_btn,
				'image'       => $c_img,
			);
		}
	}
}

// ==========================================
// 2. EXTRACT ATTRACTION GRID (Group -> Repeater)
// ==========================================
$attractions_tagline   = '';
$attractions_heading   = '';
$show_all_attractions  = false;
$attractions_count     = -1;
$selected_post_ids     = array();

// Helper to check if a checkbox/boolean field is active
$is_truthy_acf = function( $val ) {
	if ( empty( $val ) ) {
		return false;
	}
	if ( is_array( $val ) ) {
		$filtered = array_filter( $val );
		return ! empty( $filtered );
	}
	if ( is_string( $val ) && ( '0' === $val || 'false' === strtolower( $val ) ) ) {
		return false;
	}
	return (bool) $val;
};

// Method A: ACF have_rows on attraction_grid / attractions_grid
if ( function_exists( 'have_rows' ) ) {
	$grid_group_keys = array( 'attractions_grid', 'attraction_grid' );
	foreach ( $grid_group_keys as $gg_key ) {
		if ( have_rows( $gg_key ) ) {
			while ( have_rows( $gg_key ) ) {
				the_row();
				$g_tag = get_sub_field( 'attractions_tagline' ) ?: get_sub_field( 'tagline' );
				$g_hd  = get_sub_field( 'attractions_heading' ) ?: get_sub_field( 'heading' );
				$g_all = get_sub_field( 'show_all_attractions' );
				$g_cnt = get_sub_field( 'attractions_count' );

				if ( ! empty( $g_tag ) ) {
					$attractions_tagline = $g_tag;
				}
				if ( ! empty( $g_hd ) ) {
					$attractions_heading = $g_hd;
				}
				if ( $is_truthy_acf( $g_all ) ) {
					$show_all_attractions = true;
				}
				if ( ! empty( $g_cnt ) && (int) $g_cnt > 0 ) {
					$attractions_count = (int) $g_cnt;
				}

				// Check 'attractions' repeater inside attraction_grid
				if ( have_rows( 'attractions' ) ) {
					while ( have_rows( 'attractions' ) ) {
						the_row();
						$sel = get_sub_field( 'attractions_selection' ) ?: ( get_sub_field( 'select_attractions' ) ?: get_sub_field( 'attraction' ) );
						if ( ! empty( $sel ) ) {
							if ( is_array( $sel ) ) {
								foreach ( $sel as $item ) {
									$p_id = is_object( $item ) ? $item->ID : (int) $item;
									if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
										$selected_post_ids[] = $p_id;
									}
								}
							} else {
								$p_id = is_object( $sel ) ? $sel->ID : (int) $sel;
								if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
									$selected_post_ids[] = $p_id;
								}
							}
						}
					}
				} else {
					// Check flat field inside group
					$flat_sel = get_sub_field( 'attractions_selection' ) ?: get_sub_field( 'select_attractions' );
					if ( ! empty( $flat_sel ) ) {
						if ( is_array( $flat_sel ) ) {
							foreach ( $flat_sel as $item ) {
								$p_id = is_object( $item ) ? $item->ID : (int) $item;
								if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
									$selected_post_ids[] = $p_id;
								}
							}
						} else {
							$p_id = is_object( $flat_sel ) ? $flat_sel->ID : (int) $flat_sel;
							if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
								$selected_post_ids[] = $p_id;
							}
						}
					}
				}
			}
			break;
		}
	}
}

// Method B: Direct get_field fallback if group exists as array
if ( function_exists( 'get_field' ) && ! $show_all_attractions && empty( $selected_post_ids ) ) {
	$grid_group_keys = array( 'attractions_grid', 'attraction_grid' );
	foreach ( $grid_group_keys as $gg_key ) {
		$group_val = get_field( $gg_key );
		if ( ! empty( $group_val ) && is_array( $group_val ) ) {
			if ( empty( $attractions_tagline ) && ! empty( $group_val['attractions_tagline'] ) ) {
				$attractions_tagline = $group_val['attractions_tagline'];
			}
			if ( empty( $attractions_heading ) && ! empty( $group_val['attractions_heading'] ) ) {
				$attractions_heading = $group_val['attractions_heading'];
			}
			if ( ! empty( $group_val['show_all_attractions'] ) && $is_truthy_acf( $group_val['show_all_attractions'] ) ) {
				$show_all_attractions = true;
			}
			if ( ! empty( $group_val['attractions_count'] ) && (int) $group_val['attractions_count'] > 0 ) {
				$attractions_count = (int) $group_val['attractions_count'];
			}
			if ( ! empty( $group_val['attractions'] ) && is_array( $group_val['attractions'] ) ) {
				foreach ( $group_val['attractions'] as $row ) {
					$sel = ! empty( $row['attractions_selection'] ) ? $row['attractions_selection'] : ( ! empty( $row['select_attractions'] ) ? $row['select_attractions'] : ( ! empty( $row['attraction'] ) ? $row['attraction'] : null ) );
					if ( ! empty( $sel ) ) {
						if ( is_array( $sel ) ) {
							foreach ( $sel as $item ) {
								$p_id = is_object( $item ) ? $item->ID : (int) $item;
								if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
									$selected_post_ids[] = $p_id;
								}
							}
						} else {
							$p_id = is_object( $sel ) ? $sel->ID : (int) $sel;
							if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
								$selected_post_ids[] = $p_id;
							}
						}
					}
				}
			}
			break;
		}
	}
}

// Method C: Fallback from raw block data
if ( empty( $attractions_tagline ) || empty( $attractions_heading ) || ( empty( $selected_post_ids ) && ! $show_all_attractions ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' || empty( $v ) ) {
			continue;
		}
		// Tagline
		if ( empty( $attractions_tagline ) && ( preg_match( '/attractions_tagline$/', $k ) || preg_match( '/tagline$/', $k ) ) ) {
			$attractions_tagline = $v;
		}
		// Heading
		if ( empty( $attractions_heading ) && ( preg_match( '/attractions_heading$/', $k ) || preg_match( '/heading$/', $k ) ) ) {
			$attractions_heading = $v;
		}
		// Show all checkbox
		if ( preg_match( '/show_all_attractions$/', $k ) ) {
			if ( $is_truthy_acf( $v ) ) {
				$show_all_attractions = true;
			}
		}
		// Count
		if ( preg_match( '/attractions_count$/', $k ) && (int) $v > 0 ) {
			$attractions_count = (int) $v;
		}
		// Selection inside repeater (e.g. attraction_grid_attractions_0_attractions_selection) or flat
		if ( preg_match( '/attractions_selection$/', $k ) || preg_match( '/select_attractions$/', $k ) ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $item ) {
					$p_id = is_object( $item ) ? $item->ID : (int) $item;
					if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
						$selected_post_ids[] = $p_id;
					}
				}
			} else {
				$p_id = is_object( $v ) ? $v->ID : (int) $v;
				if ( $p_id && ! in_array( $p_id, $selected_post_ids, true ) ) {
					$selected_post_ids[] = $p_id;
				}
			}
		}
	}
}

// If Show All Attractions is checked, query all published attractions
if ( $show_all_attractions ) {
	$selected_post_ids = array();
	$cpt_types = array();
	if ( post_type_exists( 'attraction' ) ) {
		$cpt_types[] = 'attraction';
	}
	if ( post_type_exists( 'attractions' ) ) {
		$cpt_types[] = 'attractions';
	}
	if ( empty( $cpt_types ) ) {
		$cpt_types = array( 'attraction', 'attractions' );
	}

	$all_attractions_query = new WP_Query(
		array(
			'post_type'      => $cpt_types,
			'posts_per_page' => $attractions_count > 0 ? $attractions_count : -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $all_attractions_query->posts ) ) {
		$selected_post_ids = $all_attractions_query->posts;
	}
}

// Build list of valid published attraction items
$attraction_items = array();
if ( ! empty( $selected_post_ids ) ) {
	foreach ( $selected_post_ids as $p_id ) {
		$post_obj = get_post( $p_id );
		if ( ! empty( $post_obj ) && 'publish' === $post_obj->post_status ) {
			$thumb_url = get_the_post_thumbnail_url( $p_id, 'full' );
			$attraction_items[] = array(
				'id'        => $p_id,
				'title'     => get_the_title( $p_id ),
				'permalink' => get_permalink( $p_id ),
				'thumbnail' => $thumb_url ? $thumb_url : '',
			);
		}
	}
}

// ==========================================
// 3. BACKGROUND IMAGE VISIBILITY / CUSTOM BG
// ==========================================
$show_bg_img   = true; // Default to true if not specified
$custom_bg_url = '';

// Check top-level ACF fields or group fields
if ( function_exists( 'get_field' ) ) {
	$bg_show_val = get_field( 'show_background_image' );
	if ( null === $bg_show_val || '' === $bg_show_val ) {
		$bg_show_val = get_field( 'show_bg_image' );
	}
	if ( null === $bg_show_val || '' === $bg_show_val ) {
		$bg_show_val = get_field( 'show_background' );
	}
	if ( null === $bg_show_val || '' === $bg_show_val ) {
		$bg_show_val = get_field( 'enable_background_image' );
	}

	if ( null !== $bg_show_val && '' !== $bg_show_val ) {
		$show_bg_img = $is_truthy_acf( $bg_show_val );
	} else {
		// Check inside groups if placed within a group
		$group_keys = array( 'birthday_&_activity_promo_cards', 'birthday_activity_promo_cards', 'attractions_grid', 'attraction_grid' );
		foreach ( $group_keys as $gk ) {
			$g_data = get_field( $gk );
			if ( is_array( $g_data ) ) {
				if ( isset( $g_data['show_background_image'] ) ) {
					$bg_show_val = $g_data['show_background_image'];
					$show_bg_img = $is_truthy_acf( $bg_show_val );
					break;
				} elseif ( isset( $g_data['show_bg_image'] ) ) {
					$bg_show_val = $g_data['show_bg_image'];
					$show_bg_img = $is_truthy_acf( $bg_show_val );
					break;
				}
			}
		}

		if ( null === $bg_show_val || '' === $bg_show_val ) {
			// Check hide_background_image field
			$bg_hide_val = get_field( 'hide_background_image' ) ?: ( get_field( 'hide_bg_image' ) ?: get_field( 'hide_background' ) );
			if ( ! empty( $bg_hide_val ) && $is_truthy_acf( $bg_hide_val ) ) {
				$show_bg_img = false;
			}
		}
	}

	// Custom background image field (if user provides one)
	$custom_bg = get_field( 'background_image' ) ?: ( get_field( 'bg_image' ) ?: get_field( 'section_background_image' ) );
	if ( ! empty( $custom_bg ) ) {
		if ( is_array( $custom_bg ) && ! empty( $custom_bg['url'] ) ) {
			$custom_bg_url = $custom_bg['url'];
		} elseif ( is_numeric( $custom_bg ) ) {
			$custom_bg_url = wp_get_attachment_image_url( (int) $custom_bg, 'full' );
		} elseif ( is_string( $custom_bg ) && filter_var( $custom_bg, FILTER_VALIDATE_URL ) ) {
			$custom_bg_url = $custom_bg;
		}
	}
}

// Fallback from raw block data
if ( ! empty( $block_data ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) {
			continue;
		}
		if ( preg_match( '/(show_background_image|show_bg_image|show_background|enable_background_image)$/', $k ) ) {
			$show_bg_img = $is_truthy_acf( $v );
		}
		if ( preg_match( '/(hide_background_image|hide_bg_image|hide_background)$/', $k ) ) {
			if ( $is_truthy_acf( $v ) ) {
				$show_bg_img = false;
			}
		}
		if ( preg_match( '/(section_background_image|background_image|bg_image)$/', $k ) ) {
			if ( is_numeric( $v ) ) {
				$c_url = wp_get_attachment_image_url( (int) $v, 'full' );
				if ( $c_url ) {
					$custom_bg_url = $c_url;
				}
			} elseif ( is_string( $v ) && filter_var( $v, FILTER_VALIDATE_URL ) ) {
				$custom_bg_url = $v;
			}
		}
	}
}

if ( ! $show_bg_img ) {
	$class_name .= ' no-bg-img';
}

$section_styles = array();
if ( ! $show_bg_img ) {
	$section_styles[] = 'background-image: none;';
} elseif ( ! empty( $custom_bg_url ) ) {
	$section_styles[] = 'background-image: url(' . esc_url( $custom_bg_url ) . ');';
}
$section_style_attr = ! empty( $section_styles ) ? ' style="' . esc_attr( implode( ' ', $section_styles ) ) . '"' : '';

$total_attractions = count( $attraction_items );
$has_promo_cards   = ! empty( $promo_cards_list );
$has_attractions   = ! empty( $attraction_items );
?>

<?php if ( $has_promo_cards && $has_attractions ) : ?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>"<?php echo $section_style_attr; ?>>
		<div class="container">
			<div class="row">
				<?php
				foreach ( $promo_cards_list as $card_idx => $card ) :
					$card_num   = $card_idx + 1;
					$card_class = 1 === $card_num ? 'party-card card-1' : 'party-card';
					$col_text   = 1 === $card_num ? 'col-lg-7' : 'col-lg-8';
					$col_img    = 1 === $card_num ? 'col-lg-5' : 'col-lg-4';
					$img_div    = 1 === $card_num ? 'party-img h-100' : 'party-img1 h-100';

					// Image URL & Alt
					$img_url = '';
					$img_alt = '';
					$img_val = $card['image'];
					if ( ! empty( $img_val ) ) {
						if ( is_array( $img_val ) ) {
							$img_url = ! empty( $img_val['url'] ) ? $img_val['url'] : '';
							$img_alt = ! empty( $img_val['alt'] ) ? $img_val['alt'] : '';
						} elseif ( is_numeric( $img_val ) ) {
							$img_url = wp_get_attachment_image_url( (int) $img_val, 'full' );
							$img_alt = get_post_meta( (int) $img_val, '_wp_attachment_image_alt', true );
						} elseif ( is_string( $img_val ) ) {
							$img_url = $img_val;
						}
					}

					// Button
					$btn_url    = '';
					$btn_title  = '';
					$btn_target = '_self';
					$btn_val    = $card['button'];
					if ( ! empty( $btn_val ) ) {
						if ( is_array( $btn_val ) ) {
							$btn_url    = ! empty( $btn_val['url'] ) ? $btn_val['url'] : '';
							$btn_title  = ! empty( $btn_val['title'] ) ? $btn_val['title'] : '';
							$btn_target = ! empty( $btn_val['target'] ) ? $btn_val['target'] : '_self';
						} elseif ( is_string( $btn_val ) ) {
							$btn_url   = $btn_val;
							$btn_title = $btn_val;
						}
					}
					$card_aos   = 0 === $card_idx ? ' data-aos="fade-down-left" data-aos-duration="600"' : ' data-aos="fade-down-right" data-aos-duration="600"';
					?>
					<div class="col-md-6"<?php echo $card_aos; ?>>
						<div class="<?php echo esc_attr( $card_class ); ?>">
							<div class="row">
								<div class="<?php echo esc_attr( $col_text ); ?>">
									<div class="pc-txt">
										<?php if ( ! empty( $card['title'] ) ) : ?>
											<div class="pc-hd">
												<p><?php echo esc_html( $card['title'] ); ?></p>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $card['description'] ) ) : ?>
											<div class="party-para">
												<?php if ( strpos( $card['description'], '<p>' ) !== false ) : ?>
													<?php echo wp_kses_post( $card['description'] ); ?>
												<?php else : ?>
													<p><?php echo esc_html( $card['description'] ); ?></p>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $card['features'] ) ) : ?>
											<ul class="pc-list">
												<?php foreach ( $card['features'] as $feat_text ) : ?>
													<?php if ( ! empty( $feat_text ) ) : ?>
														<li><?php echo esc_html( $feat_text ); ?></li>
													<?php endif; ?>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>

										<?php if ( ! empty( $btn_url ) && ! empty( $btn_title ) ) : ?>
											<div class="pc-btn">
												<a class="orange-btn gap-2" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
													<?php echo esc_html( $btn_title ); ?> <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>

								<?php if ( ! empty( $img_url ) ) : ?>
									<div class="<?php echo esc_attr( $col_img ); ?>">
										<div class="<?php echo esc_attr( $img_div ); ?>">
											<img class="h-100 w-100 object-fit-cover" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="activity sec-padding">
			<div class="container">
				<?php if ( ! empty( $attractions_tagline ) ) : ?>
					<h6 class="short-hd"><?php echo esc_html( $attractions_tagline ); ?></h6>
				<?php endif; ?>

				<?php if ( ! empty( $attractions_heading ) ) : ?>
					<h2 class="title-2"><?php echo esc_html( $attractions_heading ); ?></h2>
				<?php endif; ?>

				<div class="active-card">
					<div class="row">
						<?php
						foreach ( $attraction_items as $index => $item ) :
							$col_class = 'col-sm-4';
							if ( 1 === $total_attractions ) {
								$col_class = 'col-md-6 mx-auto';
							} elseif ( 2 === $total_attractions ) {
								$col_class = 0 === $index ? 'col-sm-7' : 'col-sm-5';
							} elseif ( 3 === $total_attractions ) {
								$col_class = 'col-sm-4';
							} elseif ( 4 === $total_attractions ) {
								if ( 0 === $index ) {
									$col_class = 'col-sm-7';
								} elseif ( 1 === $index ) {
									$col_class = 'col-sm-5';
								} else {
									$col_class = 'col-sm-6';
								}
							} else {
								if ( 0 === $index ) {
									$col_class = 'col-sm-7';
								} elseif ( 1 === $index ) {
									$col_class = 'col-sm-5';
								} else {
									$col_class = 'col-sm-4';
								}
							}
							$attr_aos = $index < 2 ? ' data-aos="fade-down"' : ' data-aos="fade-up"';
							?>
							<div class="<?php echo esc_attr( $col_class ); ?>"<?php echo $attr_aos; ?>>
								<div class="a-card">
									<?php if ( ! empty( $item['thumbnail'] ) ) : ?>
										<div class="a-card-img">
											<img src="<?php echo esc_url( $item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
										</div>
									<?php endif; ?>
									<div class="acrd-cntn">
										<h5 class="a-card-txt"><?php echo esc_html( $item['title'] ); ?></h5>
										<div class="a-card-btn">
											<a class="glass-btn" href="<?php echo esc_url( $item['permalink'] ); ?>">
												<?php esc_html_e( 'Learn More', 'dino-world' ); ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php elseif ( $has_promo_cards ) : ?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>"<?php echo $section_style_attr; ?>>
		<div class="container">
			<div class="row">
				<?php
				foreach ( $promo_cards_list as $card_idx => $card ) :
					$card_num   = $card_idx + 1;
					$card_class = 1 === $card_num ? 'party-card card-1' : 'party-card';
					$col_text   = 1 === $card_num ? 'col-lg-7' : 'col-lg-8';
					$col_img    = 1 === $card_num ? 'col-lg-5' : 'col-lg-4';
					$img_div    = 1 === $card_num ? 'party-img h-100' : 'party-img1 h-100';

					// Image URL & Alt
					$img_url = '';
					$img_alt = '';
					$img_val = $card['image'];
					if ( ! empty( $img_val ) ) {
						if ( is_array( $img_val ) ) {
							$img_url = ! empty( $img_val['url'] ) ? $img_val['url'] : '';
							$img_alt = ! empty( $img_val['alt'] ) ? $img_val['alt'] : '';
						} elseif ( is_numeric( $img_val ) ) {
							$img_url = wp_get_attachment_image_url( (int) $img_val, 'full' );
							$img_alt = get_post_meta( (int) $img_val, '_wp_attachment_image_alt', true );
						} elseif ( is_string( $img_val ) ) {
							$img_url = $img_val;
						}
					}

					// Button
					$btn_url    = '';
					$btn_title  = '';
					$btn_target = '_self';
					$btn_val    = $card['button'];
					if ( ! empty( $btn_val ) ) {
						if ( is_array( $btn_val ) ) {
							$btn_url    = ! empty( $btn_val['url'] ) ? $btn_val['url'] : '';
							$btn_title  = ! empty( $btn_val['title'] ) ? $btn_val['title'] : '';
							$btn_target = ! empty( $btn_val['target'] ) ? $btn_val['target'] : '_self';
						} elseif ( is_string( $btn_val ) ) {
							$btn_url   = $btn_val;
							$btn_title = $btn_val;
						}
					}
					$card_aos   = 0 === $card_idx ? ' data-aos="fade-down-left" data-aos-duration="600"' : ' data-aos="fade-down-right" data-aos-duration="600"';
					?>
					<div class="col-md-6"<?php echo $card_aos; ?>>
						<div class="<?php echo esc_attr( $card_class ); ?>">
							<div class="row">
								<div class="<?php echo esc_attr( $col_text ); ?>">
									<div class="pc-txt">
										<?php if ( ! empty( $card['title'] ) ) : ?>
											<div class="pc-hd">
												<p><?php echo esc_html( $card['title'] ); ?></p>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $card['description'] ) ) : ?>
											<div class="party-para">
												<?php if ( strpos( $card['description'], '<p>' ) !== false ) : ?>
													<?php echo wp_kses_post( $card['description'] ); ?>
												<?php else : ?>
													<p><?php echo esc_html( $card['description'] ); ?></p>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $card['features'] ) ) : ?>
											<ul class="pc-list">
												<?php foreach ( $card['features'] as $feat_text ) : ?>
													<?php if ( ! empty( $feat_text ) ) : ?>
														<li><?php echo esc_html( $feat_text ); ?></li>
													<?php endif; ?>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>

										<?php if ( ! empty( $btn_url ) && ! empty( $btn_title ) ) : ?>
											<div class="pc-btn">
												<a class="orange-btn gap-2" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
													<?php echo esc_html( $btn_title ); ?> <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/right-arrow.png' ); ?>" alt="" />
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>

								<?php if ( ! empty( $img_url ) ) : ?>
									<div class="<?php echo esc_attr( $col_img ); ?>">
										<div class="<?php echo esc_attr( $img_div ); ?>">
											<img class="h-100 w-100 object-fit-cover" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php elseif ( $has_attractions ) : ?>
	<section id="<?php echo esc_attr( $block_id ); ?>" class="top-attraction sec-padding1<?php echo ! empty( $block['className'] ) ? ' ' . esc_attr( $block['className'] ) : ''; ?>"<?php echo $section_style_attr; ?>>
		<div class="container">
			<?php if ( ! empty( $attractions_tagline ) ) : ?>
				<h6 class="short-hd"><?php echo esc_html( $attractions_tagline ); ?></h6>
			<?php endif; ?>

			<?php if ( ! empty( $attractions_heading ) ) : ?>
				<h2 class="title-2"><?php echo esc_html( $attractions_heading ); ?></h2>
			<?php endif; ?>

			<div class="active-card">
				<div class="row">
					<?php
					foreach ( $attraction_items as $index => $item ) :
						$col_class = 'col-sm-4';
						if ( 1 === $total_attractions ) {
							$col_class = 'col-md-6 mx-auto';
						} elseif ( 2 === $total_attractions ) {
							$col_class = 0 === $index ? 'col-sm-7' : 'col-sm-5';
						} elseif ( 3 === $total_attractions ) {
							$col_class = 'col-sm-4';
						} elseif ( 4 === $total_attractions ) {
							if ( 0 === $index ) {
								$col_class = 'col-sm-7';
							} elseif ( 1 === $index ) {
								$col_class = 'col-sm-5';
							} else {
								$col_class = 'col-sm-6';
							}
						} else {
							if ( 0 === $index ) {
								$col_class = 'col-sm-7';
							} elseif ( 1 === $index ) {
								$col_class = 'col-sm-5';
							} else {
								$col_class = 'col-sm-4';
							}
						}
						$attr_aos = $index < 2 ? ' data-aos="fade-down"' : ' data-aos="fade-up"';
						?>
						<div class="<?php echo esc_attr( $col_class ); ?>"<?php echo $attr_aos; ?>>
							<div class="a-card">
								<?php if ( ! empty( $item['thumbnail'] ) ) : ?>
									<div class="a-card-img">
										<img src="<?php echo esc_url( $item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
									</div>
								<?php endif; ?>
								<div class="acrd-cntn">
									<h5 class="a-card-txt"><?php echo esc_html( $item['title'] ); ?></h5>
									<div class="a-card-btn">
										<a class="glass-btn" href="<?php echo esc_url( $item['permalink'] ); ?>">
											<?php esc_html_e( 'Learn More', 'dino-world' ); ?>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
