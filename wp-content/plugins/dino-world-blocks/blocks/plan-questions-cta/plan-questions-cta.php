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

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}

$description = function_exists( 'get_field' ) ? get_field( 'description' ) : '';
if ( empty( $description ) && ! empty( $block_data['description'] ) ) {
	$description = $block_data['description'];
}

// Contact Pills
$contact_pills = array();
if ( function_exists( 'have_rows' ) && have_rows( 'contact_pills' ) ) {
	while ( have_rows( 'contact_pills' ) ) {
		the_row();
		$icon    = get_sub_field( 'icon' );
		$text    = get_sub_field( 'text' );
		$link    = get_sub_field( 'link' );
		$new_tab = get_sub_field( 'new_tab' );

		if ( ! empty( $icon ) || ! empty( $text ) ) {
			$contact_pills[] = array(
				'icon'    => $icon,
				'text'    => $text,
				'link'    => $link,
				'new_tab' => $new_tab,
			);
		}
	}
} elseif ( ! empty( $block_data['contact_pills'] ) && is_array( $block_data['contact_pills'] ) ) {
	foreach ( $block_data['contact_pills'] as $pill ) {
		if ( is_array( $pill ) && ( ! empty( $pill['icon'] ) || ! empty( $pill['text'] ) ) ) {
			$contact_pills[] = array(
				'icon'    => ! empty( $pill['icon'] ) ? $pill['icon'] : '',
				'text'    => ! empty( $pill['text'] ) ? $pill['text'] : '',
				'link'    => ! empty( $pill['link'] ) ? $pill['link'] : '',
				'new_tab' => ! empty( $pill['new_tab'] ),
			);
		}
	}
}

// Primary Button
$primary_btn_url    = '';
$primary_btn_title  = '';
$primary_btn_target = '_self';

$p_btn = function_exists( 'get_field' ) ? get_field( 'primary_button' ) : null;
if ( empty( $p_btn ) && ! empty( $block_data['primary_button'] ) ) {
	$p_btn = $block_data['primary_button'];
}
if ( ! empty( $p_btn ) && is_array( $p_btn ) ) {
	$primary_btn_url    = ! empty( $p_btn['url'] ) ? $p_btn['url'] : '';
	$primary_btn_title  = ! empty( $p_btn['title'] ) ? $p_btn['title'] : '';
	$primary_btn_target = ! empty( $p_btn['target'] ) ? $p_btn['target'] : '_self';
} elseif ( is_string( $p_btn ) && ! empty( $p_btn ) ) {
	$primary_btn_url   = $p_btn;
	$primary_btn_title = $p_btn;
}

// Secondary Button
$secondary_btn_url    = '';
$secondary_btn_title  = '';
$secondary_btn_target = '_self';

$s_btn = function_exists( 'get_field' ) ? get_field( 'secondary_button' ) : null;
if ( empty( $s_btn ) && ! empty( $block_data['secondary_button'] ) ) {
	$s_btn = $block_data['secondary_button'];
}
if ( ! empty( $s_btn ) && is_array( $s_btn ) ) {
	$secondary_btn_url    = ! empty( $s_btn['url'] ) ? $s_btn['url'] : '';
	$secondary_btn_title  = ! empty( $s_btn['title'] ) ? $s_btn['title'] : '';
	$secondary_btn_target = ! empty( $s_btn['target'] ) ? $s_btn['target'] : '_self';
} elseif ( is_string( $s_btn ) && ! empty( $s_btn ) ) {
	$secondary_btn_url   = $s_btn;
	$secondary_btn_title = $s_btn;
}

$has_content = ! empty( $short_heading ) || ! empty( $heading ) || ! empty( $description ) || ! empty( $contact_pills ) || ! empty( $primary_btn_url ) || ! empty( $secondary_btn_url );

if ( ! $has_content ) {
	if ( ! empty( $is_preview ) ) {
		echo '<div class="plan-section"><div class="plan-cta-card text-center p-4"><p style="color:#F4E8D1;margin:0;">' . esc_html__( 'Plan – Have Questions CTA: Add content in block settings.', 'dino-world-blocks' ) . '</p></div></div>';
	}
	return;
}

$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="fade-up">
	<div class="plan-cta-card">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd"><?php echo wp_kses_post( htmlspecialchars_decode( $short_heading, ENT_QUOTES ) ); ?></h6>
		<?php endif; ?>

		<?php if ( ! empty( $heading ) ) : ?>
			<h2><?php echo wp_kses_post( htmlspecialchars_decode( $heading, ENT_QUOTES ) ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $description ) ) : ?>
			<p class="plan-cta-sub"><?php echo nl2br( wp_kses_post( htmlspecialchars_decode( $description, ENT_QUOTES ) ) ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $contact_pills ) ) : ?>
			<div class="plan-cta-contact-strip">
				<?php foreach ( $contact_pills as $pill ) : ?>
					<?php
					$pill_icon   = ! empty( $pill['icon'] ) ? trim( $pill['icon'] ) : '';
					$pill_text   = ! empty( $pill['text'] ) ? trim( $pill['text'] ) : '';
					$pill_link   = ! empty( $pill['link'] ) ? trim( $pill['link'] ) : '';
					$pill_target = ! empty( $pill['new_tab'] ) ? '_blank' : '_self';
					$rel_attr    = ! empty( $pill['new_tab'] ) ? ' rel="noopener noreferrer"' : '';

					if ( empty( $pill_icon ) && empty( $pill_text ) ) {
						continue;
					}
					?>
					<?php if ( ! empty( $pill_link ) ) : ?>
						<a href="<?php echo esc_url( $pill_link ); ?>" target="<?php echo esc_attr( $pill_target ); ?>" class="plan-contact-pill"<?php echo $rel_attr; ?>>
							<?php if ( ! empty( $pill_icon ) ) : ?>
								<span class="pill-icon"><?php echo esc_html( $pill_icon ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $pill_text ) ) : ?>
								<span><?php echo esc_html( $pill_text ); ?></span>
							<?php endif; ?>
						</a>
					<?php else : ?>
						<div class="plan-contact-pill">
							<?php if ( ! empty( $pill_icon ) ) : ?>
								<span class="pill-icon"><?php echo esc_html( $pill_icon ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $pill_text ) ) : ?>
								<span><?php echo esc_html( $pill_text ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $primary_btn_url ) || ! empty( $secondary_btn_url ) ) : ?>
			<div class="plan-cta-actions">
				<?php if ( ! empty( $primary_btn_url ) ) : ?>
					<a href="<?php echo esc_url( $primary_btn_url ); ?>" target="<?php echo esc_attr( $primary_btn_target ); ?>" class="orange-btn">
						<?php echo esc_html( $primary_btn_title ? $primary_btn_title : $primary_btn_url ); ?>
						<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $secondary_btn_url ) ) : ?>
					<a href="<?php echo esc_url( $secondary_btn_url ); ?>" target="<?php echo esc_attr( $secondary_btn_target ); ?>" class="primary-btn">
						<?php echo esc_html( $secondary_btn_title ? $secondary_btn_title : $secondary_btn_url ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
