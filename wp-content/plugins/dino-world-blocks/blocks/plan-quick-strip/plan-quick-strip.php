<?php
/**
 * Block Name: Plan – Quick Highlights Strip
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'plan-quick-strip-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'plan-quick-strip';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// Retrieve highlights
$highlights = array();
if ( function_exists( 'have_rows' ) && have_rows( 'highlights' ) ) {
	while ( have_rows( 'highlights' ) ) {
		the_row();
		$highlights[] = array(
			'icon'     => get_sub_field( 'icon' ),
			'title'    => get_sub_field( 'title' ),
			'subtitle' => get_sub_field( 'subtitle' ),
		);
	}
} elseif ( ! empty( $block_data['highlights'] ) ) {
	$highlights = is_array( $block_data['highlights'] ) ? $block_data['highlights'] : array();
}

// Fallback items if none are configured
if ( empty( $highlights ) ) {
	$highlights = array(
		array(
			'icon'     => '🕒',
			'title'    => 'Opening Hours',
			'subtitle' => 'Fri, Sat, Sun & School Hols',
		),
		array(
			'icon'     => '🎟️',
			'title'    => 'Gate Tickets Only',
			'subtitle' => 'No Pre-booking Required',
		),
		array(
			'icon'     => '📍',
			'title'    => 'Somerville, VIC',
			'subtitle' => '5 Mins from Peninsula Link',
		),
		array(
			'icon'     => '🚗',
			'title'    => 'Free Parking',
			'subtitle' => 'Plenty of On-Site Spaces',
		),
	);
}
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<?php foreach ( $highlights as $item ) : ?>
		<?php
		$icon     = ! empty( $item['icon'] ) ? $item['icon'] : '🕒';
		$title    = ! empty( $item['title'] ) ? $item['title'] : '';
		$subtitle = ! empty( $item['subtitle'] ) ? $item['subtitle'] : '';
		?>
		<div class="plan-quick-item">
			<div class="plan-quick-icon"><?php echo esc_html( $icon ); ?></div>
			<div class="plan-quick-text">
				<?php echo esc_html( $title ); ?>
				<?php if ( ! empty( $subtitle ) ) : ?>
					<small><?php echo esc_html( $subtitle ); ?></small>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
