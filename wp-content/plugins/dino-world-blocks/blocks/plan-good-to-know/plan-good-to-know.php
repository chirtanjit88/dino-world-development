<?php
/**
 * Block Name: Plan – Good to Know Visitor Guide
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'good-to-know';
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
	$short_heading = 'Visitor Guide';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Good to Know Before You Arrive';
}

// Tips grid
$tips = array();
if ( function_exists( 'have_rows' ) && have_rows( 'tips' ) ) {
	while ( have_rows( 'tips' ) ) {
		the_row();
		$tips[] = array(
			'icon'        => get_sub_field( 'icon' ),
			'title'       => get_sub_field( 'title' ),
			'description' => get_sub_field( 'description' ),
		);
	}
} elseif ( ! empty( $block_data['tips'] ) && is_array( $block_data['tips'] ) ) {
	$tips = $block_data['tips'];
}

if ( empty( $tips ) ) {
	$tips = array(
		array(
			'icon'        => '👶',
			'title'       => 'Best for Ages 2–8',
			'description' => 'Tailored specifically for toddlers, preschoolers, and young children — and grandparents love every minute of it too!',
		),
		array(
			'icon'        => '🚶',
			'title'       => 'Easy Walking & Prams',
			'description' => 'Compact, gentle layout with short walking distances — ideal for prams, strollers, and little legs that tire quickly.',
		),
		array(
			'icon'        => '♿',
			'title'       => 'Wheelchair Friendly',
			'description' => 'Most trails and main areas across the park feature flat, even terrain accessible for wheelchairs and mobility aids.',
		),
		array(
			'icon'        => '🚗',
			'title'       => 'Free On-Site Parking',
			'description' => 'Generous, hassle-free parking right by the entrance gates. No parking fees, meters, or long walks to your car.',
		),
		array(
			'icon'        => '🌳',
			'title'       => 'Shade & Rain Shelter',
			'description' => 'Abundant mature pine tree shade along paths, plus undercover pavilions for comfort on hot or rainy days.',
		),
		array(
			'icon'        => '🚫',
			'title'       => 'No Pets Allowed',
			'description' => 'For health, safety, and animal welfare reasons, pets are not permitted inside the park (certified assistance dogs exempt).',
		),
		array(
			'icon'        => '🧸',
			'title'       => 'Big Red Barn Dino Shop',
			'description' => 'Visit our charming shop stocked with quality dinosaur toys, fossil kits, plushies, and educational books for every budget.',
		),
		array(
			'icon'        => '🎁',
			'title'       => 'Gift Vouchers',
			'description' => 'The ultimate present for any little dino lover! Available for purchase at the entrance gate or online via our website.',
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

	<div class="plan-tips-grid">
		<?php foreach ( $tips as $tip ) : ?>
			<?php
			$tip_icon  = ! empty( $tip['icon'] ) ? $tip['icon'] : '💡';
			$tip_title = ! empty( $tip['title'] ) ? $tip['title'] : '';
			$tip_desc  = ! empty( $tip['description'] ) ? $tip['description'] : '';
			?>
			<div class="plan-tip-card">
				<div class="plan-tip-icon-box"><?php echo esc_html( $tip_icon ); ?></div>
				<?php if ( ! empty( $tip_title ) ) : ?>
					<h4><?php echo esc_html( $tip_title ); ?></h4>
				<?php endif; ?>
				<?php if ( ! empty( $tip_desc ) ) : ?>
					<p><?php echo esc_html( $tip_desc ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
