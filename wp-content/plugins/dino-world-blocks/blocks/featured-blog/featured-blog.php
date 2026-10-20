<?php
/**
 * Block Name: Featured Blog
 *
 * Configured ACF / SCF Fields:
 * - select_specific_post (Post Object) - Select a specific post
 * - featured_tag (Text) - Badge label (defaults to "Featured Expedition")
 * - custom_title (Text) - Optional custom title override
 * - custom_excerpt (WYSIWYG/Text) - Optional custom excerpt override
 * - custom_image (Image) - Optional custom image override
 * - button_text (Link/Text) - Custom button text / link
 * - show_meta_info (True/False) - Toggle meta info visibility
 *
 * Fallback & Dynamic behaviors:
 * 1. Post Checkbox field: Uses post with ACF 'featured' = 'yes'
 * 2. Block dropdown: Uses post selected in 'select_specific_post'
 * 3. Fallback: Uses latest published post
 * 4. Category filter: Dynamically switches featured hero to the latest (last) post of the selected category
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'featured-blog-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'featured-sec sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// Helper to parse image URL & alt
$parse_image = function( $val ) {
	if ( empty( $val ) ) {
		return array( 'url' => '', 'alt' => '' );
	}
	if ( is_array( $val ) ) {
		return array(
			'url' => ! empty( $val['url'] ) ? $val['url'] : '',
			'alt' => ! empty( $val['alt'] ) ? $val['alt'] : '',
		);
	}
	if ( is_numeric( $val ) ) {
		return array(
			'url' => wp_get_attachment_image_url( (int) $val, 'full' ) ?: '',
			'alt' => get_post_meta( (int) $val, '_wp_attachment_image_alt', true ) ?: '',
		);
	}
	if ( is_string( $val ) ) {
		return array( 'url' => $val, 'alt' => '' );
	}
	return array( 'url' => '', 'alt' => '' );
};

// 1. Determine Target Post for "All Articles"
$target_post = null;

// Priority A: Check if a post is marked with ACF 'featured' checkbox = 'yes'
$featured_posts = get_posts(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'featured',
				'value'   => '"yes"',
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'featured',
				'value'   => 'yes',
				'compare' => '=',
			),
			array(
				'key'     => 'featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
if ( ! empty( $featured_posts ) ) {
	$target_post = $featured_posts[0];
}

// Priority B: Check if post is selected in the block dropdown
if ( ! $target_post && function_exists( 'get_field' ) ) {
	$selected = get_field( 'select_specific_post' )
		?: ( get_field( 'select_post' )
		?: ( get_field( 'post' )
		?: get_field( 'featured_post' ) ) );

	if ( empty( $selected ) && ! empty( $block_data['select_specific_post'] ) ) {
		$selected = $block_data['select_specific_post'];
	}

	if ( ! empty( $selected ) ) {
		if ( is_object( $selected ) && isset( $selected->ID ) ) {
			$target_post = $selected;
		} elseif ( is_numeric( $selected ) ) {
			$target_post = get_post( (int) $selected );
		} elseif ( is_array( $selected ) && ! empty( $selected[0] ) ) {
			$first = $selected[0];
			$target_post = is_object( $first ) ? $first : get_post( (int) $first );
		}
	}
}

// Priority C: Fallback to latest published post
if ( ! $target_post ) {
	$latest_posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	if ( ! empty( $latest_posts ) ) {
		$target_post = $latest_posts[0];
	}
}

// 2. Custom Overrides & Options for Main Featured Card
$featured_tag   = 'Featured Expedition';
$custom_title   = '';
$custom_excerpt = '';
$custom_img     = array( 'url' => '', 'alt' => '' );
$button_text    = 'Read Full Story';
$button_url     = '';
$show_meta      = true;

if ( function_exists( 'get_field' ) ) {
	$tag_val = get_field( 'featured_tag' ) ?: ( get_field( 'badge' ) ?: get_field( 'tag' ) );
	if ( ! empty( $tag_val ) ) {
		$featured_tag = $tag_val;
	}
	$custom_title = get_field( 'custom_title' ) ?: '';

	$raw_excerpt = get_field( 'custom_excerpt' );
	if ( ! empty( $raw_excerpt ) && trim( wp_strip_all_tags( $raw_excerpt ) ) !== '' ) {
		$custom_excerpt = $raw_excerpt;
	}

	$custom_img = $parse_image( get_field( 'custom_image' ) ?: get_field( 'image' ) );

	$btn_val = get_field( 'button_text' ) ?: ( ! empty( $block_data['button_text'] ) ? $block_data['button_text'] : null );
	if ( is_array( $btn_val ) ) {
		if ( ! empty( $btn_val['title'] ) ) {
			$button_text = $btn_val['title'];
		}
		if ( ! empty( $btn_val['url'] ) ) {
			$button_url = $btn_val['url'];
		}
	} elseif ( is_string( $btn_val ) && trim( $btn_val ) !== '' ) {
		$button_text = trim( $btn_val );
	}
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';

// Helper to build standardized card data array
$build_card_data = function( $post_obj, $overrides = array() ) use ( $parse_image ) {
	if ( ! $post_obj ) {
		return null;
	}
	$p_id = is_object( $post_obj ) ? $post_obj->ID : (int) $post_obj;
	$post = get_post( $p_id );
	if ( ! $post ) {
		return null;
	}

	$title = ! empty( $overrides['title'] ) ? $overrides['title'] : get_the_title( $p_id );
	$url   = get_permalink( $p_id );
	$date  = get_the_date( 'M j, Y', $p_id );

	$categories = get_the_category( $p_id );
	$category   = ! empty( $categories ) ? $categories[0]->name : 'Dino Spotlight';
	$cat_slugs  = ! empty( $categories ) ? implode( ' ', wp_list_pluck( $categories, 'slug' ) ) : 'all';

	$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
	$read_time  = max( 1, ceil( $word_count / 200 ) ) . ' min read';

	if ( ! empty( $overrides['excerpt'] ) && trim( wp_strip_all_tags( $overrides['excerpt'] ) ) !== '' ) {
		$excerpt = $overrides['excerpt'];
	} elseif ( has_excerpt( $p_id ) ) {
		$excerpt = get_the_excerpt( $p_id );
	} else {
		$excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 35, '...' );
	}

	if ( ! empty( $overrides['image']['url'] ) ) {
		$img_url = $overrides['image']['url'];
		$img_alt = ! empty( $overrides['image']['alt'] ) ? $overrides['image']['alt'] : $title;
	} elseif ( has_post_thumbnail( $p_id ) ) {
		$img_url = get_the_post_thumbnail_url( $p_id, 'full' );
		$img_alt = get_post_meta( get_post_thumbnail_id( $p_id ), '_wp_attachment_image_alt', true ) ?: $title;
	} else {
		$img_url = get_template_directory_uri() . '/assets/image/Life-size-Dinosaurs.png';
		$img_alt = $title;
	}

	$tag         = ! empty( $overrides['tag'] ) ? $overrides['tag'] : 'Featured Expedition';
	$button_text = ! empty( $overrides['button_text'] ) ? $overrides['button_text'] : 'Read Full Story';
	$button_url  = ! empty( $overrides['button_url'] ) ? $overrides['button_url'] : $url;

	return array(
		'id'          => $p_id,
		'title'       => $title,
		'url'         => $url,
		'date'        => $date,
		'category'    => $category,
		'cat_slugs'   => $cat_slugs,
		'read_time'   => $read_time,
		'excerpt'     => $excerpt,
		'img_url'     => $img_url,
		'img_alt'     => $img_alt,
		'tag'         => $tag,
		'button_text' => $button_text,
		'button_url'  => $button_url,
	);
};

// Build Main Featured Card Data
$main_card_overrides = array(
	'title'       => $custom_title,
	'excerpt'     => $custom_excerpt,
	'image'       => $custom_img,
	'tag'         => $featured_tag,
	'button_text' => $button_text,
	'button_url'  => $button_url,
);
$main_card = $build_card_data( $target_post, $main_card_overrides );

// Fetch all active categories
$all_categories = get_categories(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
		'exclude'    => array( (int) get_cat_ID( 'Uncategorized' ) ),
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);

// Prepare category featured cards (each category shows its latest / last blog)
$category_cards = array();
if ( ! empty( $all_categories ) ) {
	foreach ( $all_categories as $cat_item ) {
		$cat_posts = get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $cat_item->term_id,
					),
				),
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		if ( ! empty( $cat_posts ) ) {
			$cat_card_data = $build_card_data(
				$cat_posts[0],
				array(
					'tag'         => $cat_item->name,
					'button_text' => 'Read Full Story',
				)
			);
			if ( $cat_card_data ) {
				$category_cards[ $cat_item->slug ] = $cat_card_data;
			}
		}
	}
}

// Card Renderer Closure
$render_featured_card = function( $card, $hero_class, $extra_attrs = '' ) use ( $arrow_icon_url, $show_meta ) {
	if ( ! $card ) {
		return;
	}
	?>
	<section class="featured-sec sec-padding1 featured-blog-hero <?php echo esc_attr( $hero_class ); ?>" data-title="<?php echo esc_attr( strtolower( $card['title'] ) ); ?>" <?php echo $extra_attrs; ?>>
		<div class="container">
			<div class="featured-card">
				<div class="row g-0 align-items-stretch">
					<div class="col-lg-6">
						<div class="featured-img-col">
							<a href="<?php echo esc_url( $card['url'] ); ?>">
								<img src="<?php echo esc_url( $card['img_url'] ); ?>" alt="<?php echo esc_attr( $card['img_alt'] ); ?>" />
							</a>
							<?php if ( ! empty( $card['tag'] ) ) : ?>
								<span class="featured-tag"><?php echo esc_html( $card['tag'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="featured-content-col">
							<?php if ( $show_meta ) : ?>
								<div class="meta-info">
									<?php if ( ! empty( $card['category'] ) ) : ?>
										<span class="meta-category"><?php echo esc_html( $card['category'] ); ?></span>
										<span>•</span>
									<?php endif; ?>
									<span><?php echo esc_html( $card['date'] ); ?></span>
									<span>•</span>
									<span><?php echo esc_html( $card['read_time'] ); ?></span>
								</div>
							<?php endif; ?>

							<div class="featured-hd">
								<a href="<?php echo esc_url( $card['url'] ); ?>">
									<h3><?php echo esc_html( $card['title'] ); ?></h3>
								</a>
							</div>

							<?php if ( ! empty( $card['excerpt'] ) ) : ?>
								<div class="featured-desc">
									<p><?php echo wp_kses_post( $card['excerpt'] ); ?></p>
								</div>
							<?php endif; ?>

							<div>
								<a class="orange-btn" href="<?php echo esc_url( $card['button_url'] ); ?>">
									<?php echo esc_html( $card['button_text'] ); ?>
									<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
};
?>

<div id="featured-block-wrapper-<?php echo esc_attr( $block_id ); ?>">
	<!-- 1. Filter & Search Controls -->
	<section class="filter-search-sec sec-padding1">
		<div class="container">
			<div class="row align-items-center g-3">
				<div class="col-lg-8">
					<div class="filter-wrap" id="filterBtnGroup-<?php echo esc_attr( $block_id ); ?>">
						<button type="button" class="filter-btn active" data-filter="all"><?php esc_html_e( 'All Articles', 'dino-world' ); ?></button>
						<?php if ( ! empty( $all_categories ) ) : ?>
							<?php foreach ( $all_categories as $cat_item ) : ?>
								<button type="button" class="filter-btn" data-filter="<?php echo esc_attr( $cat_item->slug ); ?>">
									<?php echo esc_html( $cat_item->name ); ?>
								</button>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="search-input-wrap">
						<svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
						<input type="text" class="blog-search-input" id="searchInput-<?php echo esc_attr( $block_id ); ?>" placeholder="<?php esc_attr_e( 'Search articles...', 'dino-world' ); ?>">
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 2. Main Featured Post Card (Displayed on "All Articles") -->
	<?php if ( $main_card ) : ?>
		<?php
		$render_featured_card(
			$main_card,
			'main-featured-hero ' . $class_name,
			'id="' . esc_attr( $block_id ) . '" data-featured-category="all" data-cat="all ' . esc_attr( $main_card['cat_slugs'] ) . '" data-aos="fade-up"'
		);
		?>
	<?php endif; ?>

	<!-- 3. Category Featured Cards (Dynamically shown when a Category filter is clicked to display that category's last blog) -->
	<?php if ( ! empty( $category_cards ) ) : ?>
		<?php foreach ( $category_cards as $cat_slug => $c_card ) : ?>
			<?php
			$render_featured_card(
				$c_card,
				'category-featured-hero ' . $class_name,
				'data-featured-category="' . esc_attr( $cat_slug ) . '" data-cat="' . esc_attr( $cat_slug ) . '" style="display: none;"'
			);
			?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
