<?php
/**
 * Block Name: Plan – Food, Drinks & Picnic Facilities
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'food-drinks';
$class_name = 'plan-section';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

$short_heading = function_exists( 'get_field' ) ? get_field( 'short_heading' ) : '';
if ( empty( $short_heading ) && ! empty( $block_data['short_heading'] ) ) {
	$short_heading = $block_data['short_heading'];
}
if ( empty( $short_heading ) ) {
	$short_heading = 'Refuel & Relax';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Food, Drinks & Picnic Facilities';
}

// Facility cards
$cards = array();
if ( function_exists( 'have_rows' ) && have_rows( 'cards' ) ) {
	while ( have_rows( 'cards' ) ) {
		the_row();
		$tags = array();
		if ( have_rows( 'tags' ) ) {
			while ( have_rows( 'tags' ) ) {
				the_row();
				$tags[] = get_sub_field( 'tag_text' );
			}
		}
		$cards[] = array(
			'icon'    => get_sub_field( 'icon' ),
			'title'   => get_sub_field( 'title' ),
			'content' => get_sub_field( 'content' ),
			'tags'    => $tags,
		);
	}
} elseif ( ! empty( $block_data['cards'] ) && is_array( $block_data['cards'] ) ) {
	$cards = $block_data['cards'];
}

if ( empty( $cards ) ) {
	$cards = array(
		array(
			'icon'    => '🌭',
			'title'   => 'Park Kiosk & Refreshments',
			'content' => "Hungry explorers can fuel up with our sizzling sausage sizzle, hot chips by the bucket, crispy potato cakes, and fresh hot jam donuts! We also offer an array of cold soft drinks, juices, hot coffee & tea, and ice creams. Don't leave without trying our popular artisan <strong>Bedrock Gelato</strong>!",
			'tags'    => array(
				'Sausage Sizzle',
				'Hot Jam Donuts',
				'Buckets of Chips',
				'Bedrock Gelato',
				'Hot & Cold Drinks',
			),
		),
		array(
			'icon'    => '🧺',
			'title'   => 'Complimentary BBQs & Picnic Tables',
			'content' => 'Prefer to bring your own spread? Family picnics are warmly welcomed! Feel free to pack a basket of your favourite lunch treats and make use of our complimentary gas BBQs and shaded outdoor picnic tables scattered throughout the lush park gardens at no charge.',
			'tags'    => array(
				'Free Gas BBQs',
				'BYO Picnic Friendly',
				'Shaded Pergolas',
				'Grass Lawn Areas',
				'Outdoor Dining',
			),
		),
	);
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<div class="plan-sec-header">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="plan-food-grid">
		<?php foreach ( $cards as $card ) : ?>
			<?php
			$icon    = ! empty( $card['icon'] ) ? $card['icon'] : '🍽️';
			$title   = ! empty( $card['title'] ) ? $card['title'] : '';
			$content = ! empty( $card['content'] ) ? $card['content'] : '';
			$tags    = ! empty( $card['tags'] ) && is_array( $card['tags'] ) ? $card['tags'] : array();
			?>
			<div class="plan-food-card">
				<div class="plan-food-header">
					<div class="plan-food-icon"><?php echo esc_html( $icon ); ?></div>
					<?php if ( ! empty( $title ) ) : ?>
						<h4><?php echo esc_html( $title ); ?></h4>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $content ) ) : ?>
					<p><?php echo wp_kses_post( $content ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $tags ) ) : ?>
					<div class="plan-food-tags">
						<?php foreach ( $tags as $tag_item ) : ?>
							<?php
							$tag_label = is_array( $tag_item ) && isset( $tag_item['tag_text'] ) ? $tag_item['tag_text'] : ( is_string( $tag_item ) ? $tag_item : '' );
							if ( empty( $tag_label ) ) continue;
							?>
							<span class="plan-food-pill"><?php echo esc_html( $tag_label ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
