<?php
/**
 * Block Name: Featured Blog
 *
 * Configured ACF / SCF Fields:
 * - select_post (Post Object) - Optional: Select a specific post (defaults to latest published post)
 * - featured_tag (Text) - Badge label (e.g. "Featured Expedition", defaults to "Featured Expedition")
 * - custom_title (Text) - Optional custom title override
 * - custom_excerpt (Textarea) - Optional custom excerpt override
 * - custom_image (Image) - Optional custom image override
 * - button_text (Text) - Custom button text (defaults to "Read Full Story")
 * - show_meta (True/False) - Toggle meta info visibility (defaults to true)
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

// 1. Get Selected Post or Query Latest Post
$target_post = null;
if ( function_exists( 'get_field' ) ) {
	$selected = get_field( 'select_post' ) ?: ( get_field( 'post' ) ?: get_field( 'featured_post' ) );
	if ( ! empty( $selected ) ) {
		if ( is_object( $selected ) ) {
			$target_post = $selected;
		} elseif ( is_numeric( $selected ) ) {
			$target_post = get_post( (int) $selected );
		}
	}
}

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

// 2. Custom Overrides & Options
$featured_tag   = 'Featured Expedition';
$custom_title   = '';
$custom_excerpt = '';
$custom_img     = array( 'url' => '', 'alt' => '' );
$button_text    = 'Read Full Story';
$show_meta      = true;

if ( function_exists( 'get_field' ) ) {
	$tag_val = get_field( 'featured_tag' ) ?: ( get_field( 'badge' ) ?: get_field( 'tag' ) );
	if ( ! empty( $tag_val ) ) {
		$featured_tag = $tag_val;
	}
	$custom_title   = get_field( 'custom_title' ) ?: '';
	$custom_excerpt = get_field( 'custom_excerpt' ) ?: '';
	$custom_img     = $parse_image( get_field( 'custom_image' ) ?: get_field( 'image' ) );
	$btn_val        = get_field( 'button_text' ) ?: get_field( 'btn_text' );
	if ( ! empty( $btn_val ) ) {
		$button_text = $btn_val;
	}
	$meta_val = get_field( 'show_meta' );
	if ( null !== $meta_val && '' !== $meta_val ) {
		$show_meta = (bool) $meta_val;
	}
}

// Fallback values from target post or default demo content
if ( $target_post ) {
	$p_id        = $target_post->ID;
	$p_title     = ! empty( $custom_title ) ? $custom_title : get_the_title( $p_id );
	$p_url       = get_permalink( $p_id );
	$p_date      = get_the_date( 'M j, Y', $p_id );
	
	$categories  = get_the_category( $p_id );
	$p_category  = ! empty( $categories ) ? $categories[0]->name : 'Dino Spotlight';
	$p_cat_slugs = ! empty( $categories ) ? implode( ' ', wp_list_pluck( $categories, 'slug' ) ) : 'all';
	
	// Estimated Read Time based on word count
	$word_count  = str_word_count( wp_strip_all_tags( $target_post->post_content ) );
	$read_time   = max( 1, ceil( $word_count / 200 ) ) . ' min read';

	// Excerpt
	if ( ! empty( $custom_excerpt ) ) {
		$p_excerpt = $custom_excerpt;
	} elseif ( has_excerpt( $p_id ) ) {
		$p_excerpt = get_the_excerpt( $p_id );
	} else {
		$p_excerpt = wp_trim_words( strip_shortcodes( $target_post->post_content ), 35, '...' );
	}

	// Image
	if ( ! empty( $custom_img['url'] ) ) {
		$img_url = $custom_img['url'];
		$img_alt = ! empty( $custom_img['alt'] ) ? $custom_img['alt'] : $p_title;
	} elseif ( has_post_thumbnail( $p_id ) ) {
		$img_url = get_the_post_thumbnail_url( $p_id, 'full' );
		$img_alt = get_post_meta( get_post_thumbnail_id( $p_id ), '_wp_attachment_image_alt', true ) ?: $p_title;
	} else {
		$img_url = get_template_directory_uri() . '/assets/image/Life-size-Dinosaurs.png';
		$img_alt = $p_title;
	}
} else {
	// Fallback when no posts exist yet
	$p_title    = ! empty( $custom_title ) ? $custom_title : 'Meet the Giants: The Science Behind Our Life-Size Animatronic Dinosaurs';
	$p_url      = '#';
	$p_date     = 'Oct 24, 2026';
	$p_category = 'Dino Spotlight';
	$p_cat_slugs= 'spotlight dino-spotlight';
	$read_time  = '6 min read';
	$p_excerpt  = ! empty( $custom_excerpt ) ? $custom_excerpt : 'Step behind the roaring animatronics and uncover the scientific craftsmanship that brings Cretaceous titans to life. From authentic fossil-based skeletal proportions to sensory roaring soundscapes, discover how prehistoric giants roam again.';
	$img_url    = ! empty( $custom_img['url'] ) ? $custom_img['url'] : get_template_directory_uri() . '/assets/image/Life-size-Dinosaurs.png';
	$img_alt    = $p_title;
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';

// Active Categories for filter tabs placed above featured post
$all_categories = get_categories(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
		'exclude'    => array( (int) get_cat_ID( 'Uncategorized' ) ),
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
?>

<div id="featured-block-wrapper-<?php echo esc_attr( $block_id ); ?>">
	<!-- 1. Filter & Search Controls (Placed Above Featured Post as in HTML Design) -->
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

	<!-- 2. Featured Post Card (Filterable) -->
	<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?> blog-col" data-cat="<?php echo esc_attr( $p_cat_slugs ); ?>" data-title="<?php echo esc_attr( strtolower( $p_title ) ); ?>" data-aos="fade-up">
		<div class="container">
			<div class="featured-card">
				<div class="row g-0 align-items-stretch">
					<div class="col-lg-6">
						<div class="featured-img-col">
							<a href="<?php echo esc_url( $p_url ); ?>">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
							</a>
							<?php if ( ! empty( $featured_tag ) ) : ?>
								<span class="featured-tag"><?php echo esc_html( $featured_tag ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="featured-content-col">
							<?php if ( $show_meta ) : ?>
								<div class="meta-info">
									<?php if ( ! empty( $p_category ) ) : ?>
										<span class="meta-category"><?php echo esc_html( $p_category ); ?></span>
										<span>•</span>
									<?php endif; ?>
									<span><?php echo esc_html( $p_date ); ?></span>
									<span>•</span>
									<span><?php echo esc_html( $read_time ); ?></span>
								</div>
							<?php endif; ?>

							<div class="featured-hd">
								<a href="<?php echo esc_url( $p_url ); ?>">
									<h3><?php echo esc_html( $p_title ); ?></h3>
								</a>
							</div>

							<?php if ( ! empty( $p_excerpt ) ) : ?>
								<div class="featured-desc">
									<p><?php echo wp_kses_post( $p_excerpt ); ?></p>
								</div>
							<?php endif; ?>

							<div>
								<a class="orange-btn" href="<?php echo esc_url( $p_url ); ?>">
									<?php echo esc_html( $button_text ); ?>
									<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
