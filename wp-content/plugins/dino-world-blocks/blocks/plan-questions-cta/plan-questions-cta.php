<?php
/**
 * Block Name: Plan – Have Questions CTA
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'questions';
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
	$short_heading = "We're Here to Help";
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Have Questions Before You Visit?';
}

$description = function_exists( 'get_field' ) ? get_field( 'description' ) : '';
if ( empty( $description ) && ! empty( $block_data['description'] ) ) {
	$description = $block_data['description'];
}
if ( empty( $description ) ) {
	$description = 'Our friendly park crew is ready to answer any questions about accessibility, party bookings, holiday hours, or group visits.';
}

// Contact Pills
$contact_pills = array();
if ( function_exists( 'have_rows' ) && have_rows( 'contact_pills' ) ) {
	while ( have_rows( 'contact_pills' ) ) {
		the_row();
		$contact_pills[] = array(
			'icon'    => get_sub_field( 'icon' ),
			'text'    => get_sub_field( 'text' ),
			'link'    => get_sub_field( 'link' ),
			'new_tab' => get_sub_field( 'new_tab' ),
		);
	}
} elseif ( ! empty( $block_data['contact_pills'] ) && is_array( $block_data['contact_pills'] ) ) {
	$contact_pills = $block_data['contact_pills'];
}

if ( empty( $contact_pills ) ) {
	$contact_pills = array(
		array(
			'icon'    => '📞',
			'text'    => '(03) 5977 3018',
			'link'    => 'tel:0359773018',
			'new_tab' => false,
		),
		array(
			'icon'    => '✉️',
			'text'    => 'info@dinosaurworld.net.au',
			'link'    => 'mailto:info@dinosaurworld.net.au',
			'new_tab' => false,
		),
		array(
			'icon'    => '📘',
			'text'    => 'Follow Us on Facebook',
			'link'    => 'https://www.facebook.com',
			'new_tab' => true,
		),
	);
}

// Primary Button
$primary_btn_url    = 'tel:0359773018';
$primary_btn_title  = 'Call (03) 5977 3018';
$primary_btn_target = '_self';

$p_btn = function_exists( 'get_field' ) ? get_field( 'primary_button' ) : null;
if ( ! empty( $p_btn ) && is_array( $p_btn ) ) {
	$primary_btn_url    = ! empty( $p_btn['url'] ) ? $p_btn['url'] : $primary_btn_url;
	$primary_btn_title  = ! empty( $p_btn['title'] ) ? $p_btn['title'] : $primary_btn_title;
	$primary_btn_target = ! empty( $p_btn['target'] ) ? $p_btn['target'] : '_self';
}

// Secondary Button
$secondary_btn_url    = 'mailto:info@dinosaurworld.net.au';
$secondary_btn_title  = 'Email Our Team';
$secondary_btn_target = '_self';

$s_btn = function_exists( 'get_field' ) ? get_field( 'secondary_button' ) : null;
if ( ! empty( $s_btn ) && is_array( $s_btn ) ) {
	$secondary_btn_url    = ! empty( $s_btn['url'] ) ? $s_btn['url'] : $secondary_btn_url;
	$secondary_btn_title  = ! empty( $s_btn['title'] ) ? $s_btn['title'] : $secondary_btn_title;
	$secondary_btn_target = ! empty( $s_btn['target'] ) ? $s_btn['target'] : '_self';
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<div class="plan-cta-card">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>
		<?php if ( ! empty( $heading ) ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $description ) ) : ?>
			<p class="plan-cta-sub"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $contact_pills ) ) : ?>
			<div class="plan-cta-contact-strip">
				<?php foreach ( $contact_pills as $pill ) : ?>
					<?php
					$pill_icon   = ! empty( $pill['icon'] ) ? $pill['icon'] : '📍';
					$pill_text   = ! empty( $pill['text'] ) ? $pill['text'] : '';
					$pill_link   = ! empty( $pill['link'] ) ? $pill['link'] : '#';
					$pill_target = ! empty( $pill['new_tab'] ) ? '_blank' : '_self';
					$rel_attr    = ! empty( $pill['new_tab'] ) ? ' rel="noopener noreferrer"' : '';
					?>
					<a href="<?php echo esc_url( $pill_link ); ?>" target="<?php echo esc_attr( $pill_target ); ?>" class="plan-contact-pill"<?php echo $rel_attr; ?>>
						<span class="pill-icon"><?php echo esc_html( $pill_icon ); ?></span>
						<span><?php echo esc_html( $pill_text ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="plan-cta-actions">
			<?php if ( ! empty( $primary_btn_url ) ) : ?>
				<a href="<?php echo esc_url( $primary_btn_url ); ?>" target="<?php echo esc_attr( $primary_btn_target ); ?>" class="orange-btn">
					<?php echo esc_html( $primary_btn_title ); ?> <img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $secondary_btn_url ) ) : ?>
				<a href="<?php echo esc_url( $secondary_btn_url ); ?>" target="<?php echo esc_attr( $secondary_btn_target ); ?>" class="primary-btn">
					<?php echo esc_html( $secondary_btn_title ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
