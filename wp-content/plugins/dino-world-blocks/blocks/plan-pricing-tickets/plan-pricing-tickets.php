<?php
/**
 * Block Name: Plan – Admission & Ticket Prices
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'ticket-prices';
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
	$short_heading = 'Simple & Transparent';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Gate Admission & Ticket Prices';
}

$alert_icon = function_exists( 'get_field' ) ? get_field( 'alert_icon' ) : '';
if ( empty( $alert_icon ) && ! empty( $block_data['alert_icon'] ) ) {
	$alert_icon = $block_data['alert_icon'];
}
if ( empty( $alert_icon ) ) {
	$alert_icon = '🎟️';
}

$alert_text = function_exists( 'get_field' ) ? get_field( 'alert_text' ) : '';
if ( empty( $alert_text ) && ! empty( $block_data['alert_text'] ) ) {
	$alert_text = $block_data['alert_text'];
}
if ( empty( $alert_text ) ) {
	$alert_text = '<strong>Tickets are sold at the gate only — no online bookings needed!</strong> Just turn up, grab your passes, and roar!';
}

// Pricing Cards
$pricing_cards = array();
if ( function_exists( 'have_rows' ) && have_rows( 'pricing_cards' ) ) {
	while ( have_rows( 'pricing_cards' ) ) {
		the_row();
		$features = array();
		if ( have_rows( 'features' ) ) {
			while ( have_rows( 'features' ) ) {
				the_row();
				$features[] = get_sub_field( 'text' );
			}
		}
		$pricing_cards[] = array(
			'card_title'     => get_sub_field( 'card_title' ),
			'subtitle'       => get_sub_field( 'subtitle' ),
			'price'          => get_sub_field( 'price' ),
			'price_label'    => get_sub_field( 'price_label' ),
			'is_featured'    => get_sub_field( 'is_featured' ),
			'featured_badge' => get_sub_field( 'featured_badge' ),
			'paw_image'      => get_sub_field( 'paw_image' ),
			'features'       => $features,
		);
	}
} elseif ( ! empty( $block_data['pricing_cards'] ) && is_array( $block_data['pricing_cards'] ) ) {
	$pricing_cards = $block_data['pricing_cards'];
}

if ( empty( $pricing_cards ) ) {
	$pricing_cards = array(
		array(
			'card_title'     => 'Standard Entry',
			'subtitle'       => 'Ages 16 – 64 years',
			'price'          => '$22',
			'price_label'    => 'Per Person',
			'is_featured'    => false,
			'featured_badge' => '',
			'features'       => array(
				'Full park & dinosaur exhibits access',
				'Trainasaurus miniature train ride included',
				'Picnic areas & playground access',
			),
		),
		array(
			'card_title'     => 'Child Entry',
			'subtitle'       => 'Ages 2 – 15 years',
			'price'          => '$20',
			'price_label'    => 'Per Child',
			'is_featured'    => false,
			'featured_badge' => '',
			'features'       => array(
				'Meet life-size moving & roaring dinos',
				'Trainasaurus train ride included free',
				'Prehistoric adventure playground',
			),
		),
		array(
			'card_title'     => 'Family Pass',
			'subtitle'       => '2 Adults + 2 Children',
			'price'          => '$78',
			'price_label'    => 'Complete Family Admission',
			'is_featured'    => true,
			'featured_badge' => '⭐ Best Value Family Deal',
			'features'       => array(
				'All-inclusive admission for family of 4',
				'Save $6 compared to individual tickets',
				'Unlimited Trainasaurus train rides',
				'Full day access to all park exhibits',
			),
		),
		array(
			'card_title'     => 'Children Under 2',
			'subtitle'       => 'Ages 0 – 23 months',
			'price'          => 'FREE',
			'price_label'    => 'Free Admission',
			'is_featured'    => false,
			'featured_badge' => '',
			'features'       => array(
				'Free with paying adult or family ticket',
				'Gentle, pram-friendly forest trails',
				'Safe sensory toddler play areas',
			),
		),
	);
}

// Addons
$addons = array();
if ( function_exists( 'have_rows' ) && have_rows( 'addons' ) ) {
	while ( have_rows( 'addons' ) ) {
		the_row();
		$addons[] = array(
			'icon'        => get_sub_field( 'icon' ),
			'badge_text'  => get_sub_field( 'badge_text' ),
			'badge_type'  => get_sub_field( 'badge_type' ),
			'title'       => get_sub_field( 'title' ),
			'description' => get_sub_field( 'description' ),
		);
	}
} elseif ( ! empty( $block_data['addons'] ) && is_array( $block_data['addons'] ) ) {
	$addons = $block_data['addons'];
}

if ( empty( $addons ) ) {
	$addons = array(
		array(
			'icon'        => '🚂',
			'badge_text'  => 'Included Free With Entry',
			'badge_type'  => 'badge-free',
			'title'       => 'Trainasaurus Train Ride',
			'description' => 'Hop aboard our famous open-air miniature railway for a delightful scenic ride circling right through the dinosaur trails.',
		),
		array(
			'icon'        => '⛏️',
			'badge_text'  => '$8.00 Per Child',
			'badge_type'  => 'badge-price',
			'title'       => 'Dig & Discover Fossil Dig',
			'description' => 'Grab your brushes and chisels in our palaeontologist sand pit, unearth real genuine fossils and take your prehistoric find home!',
		),
		array(
			'icon'        => '⛳',
			'badge_text'  => '$8.00 Per Player',
			'badge_type'  => 'badge-price',
			'title'       => 'Bedrock Indoor Mini Golf',
			'description' => 'Challenge friends and family to 9 holes of indoor dino mini-golf nestled in our Bedrock themed cave. Perfect in any weather!',
		),
	);
}

$default_paw_url = get_template_directory_uri() . '/assets/image/paw.png';
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

	<?php if ( ! empty( $alert_text ) ) : ?>
		<div class="plan-gate-alert">
			<span class="alert-icon"><?php echo esc_html( $alert_icon ); ?></span>
			<div>
				<?php echo wp_kses_post( $alert_text ); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="plan-price-grid">
		<?php foreach ( $pricing_cards as $card ) : ?>
			<?php
			$card_title  = ! empty( $card['card_title'] ) ? $card['card_title'] : '';
			$subtitle    = ! empty( $card['subtitle'] ) ? $card['subtitle'] : '';
			$price       = ! empty( $card['price'] ) ? $card['price'] : '';
			$price_label = ! empty( $card['price_label'] ) ? $card['price_label'] : '';
			$is_featured = ! empty( $card['is_featured'] );
			$badge_text  = ! empty( $card['featured_badge'] ) ? $card['featured_badge'] : '';

			$paw_url = $default_paw_url;
			if ( ! empty( $card['paw_image'] ) ) {
				if ( is_array( $card['paw_image'] ) && ! empty( $card['paw_image']['url'] ) ) {
					$paw_url = $card['paw_image']['url'];
				} elseif ( is_numeric( $card['paw_image'] ) ) {
					$paw_url = wp_get_attachment_image_url( (int) $card['paw_image'], 'full' ) ?: $default_paw_url;
				} elseif ( is_string( $card['paw_image'] ) ) {
					$paw_url = $card['paw_image'];
				}
			}

			$is_free = ( strtoupper( trim( $price ) ) === 'FREE' );
			?>
			<div class="plan-price-card<?php echo $is_featured ? ' featured' : ''; ?>">
				<?php if ( $is_featured && ! empty( $badge_text ) ) : ?>
					<div class="plan-badge-featured"><?php echo esc_html( $badge_text ); ?></div>
				<?php endif; ?>

				<div class="plan-card-top">
					<h3><?php echo esc_html( $card_title ); ?></h3>
					<?php if ( ! empty( $subtitle ) ) : ?>
						<div class="plan-card-sub"><?php echo esc_html( $subtitle ); ?></div>
					<?php endif; ?>
					<div class="plan-card-val<?php echo $is_free ? ' free' : ''; ?>"><?php echo esc_html( $price ); ?></div>
					<?php if ( ! empty( $price_label ) ) : ?>
						<div class="plan-card-label"><?php echo esc_html( $price_label ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $card['features'] ) && is_array( $card['features'] ) ) : ?>
					<ul class="plan-card-features">
						<?php foreach ( $card['features'] as $feat ) : ?>
							<?php
							$feat_text = is_array( $feat ) && isset( $feat['text'] ) ? $feat['text'] : ( is_string( $feat ) ? $feat : '' );
							if ( empty( $feat_text ) ) continue;
							?>
							<li>
								<span><?php echo esc_html( $feat_text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="plan-card-bottom">
					<img src="<?php echo esc_url( $paw_url ); ?>" alt="<?php echo esc_attr( $card_title ); ?> Paw" />
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( ! empty( $addons ) ) : ?>
		<div class="plan-addons-grid">
			<?php foreach ( $addons as $addon ) : ?>
				<?php
				$ad_icon  = ! empty( $addon['icon'] ) ? $addon['icon'] : '🦕';
				$ad_badge = ! empty( $addon['badge_text'] ) ? $addon['badge_text'] : '';
				$ad_type  = ! empty( $addon['badge_type'] ) ? $addon['badge_type'] : 'badge-price';
				$ad_title = ! empty( $addon['title'] ) ? $addon['title'] : '';
				$ad_desc  = ! empty( $addon['description'] ) ? $addon['description'] : '';
				?>
				<div class="plan-addon-card">
					<div class="plan-addon-icon"><?php echo esc_html( $ad_icon ); ?></div>
					<div class="plan-addon-content">
						<?php if ( ! empty( $ad_badge ) ) : ?>
							<span class="plan-addon-badge <?php echo esc_attr( $ad_type ); ?>"><?php echo esc_html( $ad_badge ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $ad_title ) ) : ?>
							<h4><?php echo esc_html( $ad_title ); ?></h4>
						<?php endif; ?>
						<?php if ( ! empty( $ad_desc ) ) : ?>
							<p><?php echo esc_html( $ad_desc ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
