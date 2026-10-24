<?php
/**
 * Block Name: Plan – Opening Hours
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'opening-hours';
$class_name = 'plan-section';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

$short_heading  = function_exists( 'get_field' ) ? get_field( 'short_heading' ) : '';
if ( empty( $short_heading ) && ! empty( $block_data['short_heading'] ) ) {
	$short_heading = $block_data['short_heading'];
}
if ( empty( $short_heading ) ) {
	$short_heading = 'Plan Your Timing';
}

$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
if ( empty( $heading ) && ! empty( $block_data['heading'] ) ) {
	$heading = $block_data['heading'];
}
if ( empty( $heading ) ) {
	$heading = 'Park Opening Hours';
}

$schedule_title = function_exists( 'get_field' ) ? get_field( 'schedule_title' ) : '';
if ( empty( $schedule_title ) && ! empty( $block_data['schedule_title'] ) ) {
	$schedule_title = $block_data['schedule_title'];
}
if ( empty( $schedule_title ) ) {
	$schedule_title = 'Weekly Operating Schedule';
}

// Operating rows
$schedule_rows = array();
if ( function_exists( 'have_rows' ) && have_rows( 'schedule_rows' ) ) {
	while ( have_rows( 'schedule_rows' ) ) {
		the_row();
		$schedule_rows[] = array(
			'day'       => get_sub_field( 'day' ),
			'time'      => get_sub_field( 'time' ),
			'tag_text'  => get_sub_field( 'tag_text' ),
			'tag_style' => get_sub_field( 'tag_style' ),
		);
	}
} elseif ( ! empty( $block_data['schedule_rows'] ) && is_array( $block_data['schedule_rows'] ) ) {
	$schedule_rows = $block_data['schedule_rows'];
}

if ( empty( $schedule_rows ) ) {
	$schedule_rows = array(
		array(
			'day'       => 'Friday',
			'time'      => '10:00am – 3:00pm',
			'tag_text'  => 'Story Time at 11am',
			'tag_style' => 'tag-story',
		),
		array(
			'day'       => 'Saturday',
			'time'      => '10:00am – 4:00pm',
			'tag_text'  => 'Weekend Fun',
			'tag_style' => 'tag-open',
		),
		array(
			'day'       => 'Sunday',
			'time'      => '10:00am – 4:00pm',
			'tag_text'  => 'Weekend Fun',
			'tag_style' => 'tag-open',
		),
		array(
			'day'       => 'Victorian School Holidays',
			'time'      => 'Open Every Single Day',
			'tag_text'  => '10am – 4pm',
			'tag_style' => 'tag-holiday',
		),
		array(
			'day'       => 'Public Holidays',
			'time'      => 'Open as Usual',
			'tag_text'  => 'Closed Xmas, Boxing Day & NYD',
			'tag_style' => 'tag-open',
		),
	);
}

// Notes
$notes = array();
if ( function_exists( 'have_rows' ) && have_rows( 'notes' ) ) {
	while ( have_rows( 'notes' ) ) {
		the_row();
		$notes[] = array(
			'title'     => get_sub_field( 'title' ),
			'content'   => get_sub_field( 'content' ),
			'note_type' => get_sub_field( 'note_type' ),
		);
	}
} elseif ( ! empty( $block_data['notes'] ) && is_array( $block_data['notes'] ) ) {
	$notes = $block_data['notes'];
}

if ( empty( $notes ) ) {
	$notes = array(
		array(
			'title'     => '📢 School Holiday Story Time Note',
			'content'   => "No Friday story time runs during school holidays. Bedrock Indoor Mini Golf doesn't operate on Fridays during standard school terms.",
			'note_type' => 'default',
		),
		array(
			'title'     => '☀️ Summer Extreme Heat Policy',
			'content'   => 'On days forecast 34°C or more, please come early! To ensure animal and visitor safety, the park may close early at 2:00pm.',
			'note_type' => 'heat-policy',
		),
		array(
			'title'     => '💡 Explorer Tip',
			'content'   => 'We recommend arriving between 10am and 11am to make the most of your day, catch the train rides with minimal queues, and enjoy a relaxing lunch in our picnic grounds.',
			'note_type' => 'pro-tip',
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

	<div class="plan-hours-grid">
		<!-- Schedule Card -->
		<div class="plan-hours-card">
			<div class="plan-hours-head">
				<h4><?php echo esc_html( $schedule_title ); ?></h4>
			</div>
			<div class="plan-hours-list">
				<?php foreach ( $schedule_rows as $row ) : ?>
					<?php
					$day       = ! empty( $row['day'] ) ? $row['day'] : '';
					$time      = ! empty( $row['time'] ) ? $row['time'] : '';
					$tag_text  = ! empty( $row['tag_text'] ) ? $row['tag_text'] : '';
					$tag_style = ! empty( $row['tag_style'] ) ? $row['tag_style'] : 'tag-open';
					?>
					<div class="plan-hours-row">
						<div class="plan-hours-day"><?php echo esc_html( $day ); ?></div>
						<div class="plan-hours-time">
							<span><?php echo esc_html( $time ); ?></span>
							<?php if ( ! empty( $tag_text ) ) : ?>
								<span class="plan-tag <?php echo esc_attr( $tag_style ); ?>"><?php echo esc_html( $tag_text ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Notes & Advisories Card -->
		<div class="plan-notes-card">
			<?php foreach ( $notes as $note ) : ?>
				<?php
				$note_title = ! empty( $note['title'] ) ? $note['title'] : '';
				$content    = ! empty( $note['content'] ) ? $note['content'] : '';
				$note_type  = ! empty( $note['note_type'] ) && 'default' !== $note['note_type'] ? ' ' . $note['note_type'] : '';
				?>
				<div class="plan-note-box<?php echo esc_attr( $note_type ); ?>">
					<?php if ( ! empty( $note_title ) ) : ?>
						<h5><?php echo esc_html( $note_title ); ?></h5>
					<?php endif; ?>
					<?php if ( ! empty( $content ) ) : ?>
						<p><?php echo wp_kses_post( $content ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
