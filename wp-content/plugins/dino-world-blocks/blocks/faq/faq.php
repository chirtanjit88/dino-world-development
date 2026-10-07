<?php
/**
 * Block Name: FAQ Section
 *
 * Powered by ACF Block Fields:
 * - short_heading / tagline (Text)
 * - heading (Text)
 * - faqs (Repeater: question [Text], answer [Textarea/WYSIWYG])
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'faq-sec-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'faq-sec sec-padding';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Text Fields
$short_heading = '';
$heading       = '';

if ( function_exists( 'get_field' ) ) {
	$short_heading = get_field( 'short_heading' ) ?: ( get_field( 'tagline' ) ?: get_field( 'sub_heading' ) );
	$heading       = get_field( 'heading' ) ?: get_field( 'title' );
}

// Fallback from block_data
if ( empty( $short_heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(short_heading|tagline|sub_heading)$/', $k ) ) {
			$short_heading = $v;
			break;
		}
	}
}
if ( empty( $heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title)$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

// 2. Extract FAQ Items
$faq_items = array();

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) ) {
	$rep_keys = array( 'faqs', 'faq_list', 'faq_items', 'questions', 'accordion' );
	foreach ( $rep_keys as $r_key ) {
		if ( have_rows( $r_key ) ) {
			while ( have_rows( $r_key ) ) {
				the_row();
				$q = get_sub_field( 'question' ) ?: get_sub_field( 'title' );
				$a = get_sub_field( 'answer' ) ?: ( get_sub_field( 'content' ) ?: get_sub_field( 'description' ) );
				if ( ! empty( $q ) || ! empty( $a ) ) {
					$faq_items[] = array(
						'question' => $q,
						'answer'   => $a,
					);
				}
			}
			break;
		}
	}
}

// Method B: Raw block_data fallback
if ( empty( $faq_items ) && ! empty( $block_data ) ) {
	$faq_count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(faqs|faq_list|faq_items|questions)$/', $k ) && is_numeric( $v ) ) {
			$faq_count = (int) $v;
			break;
		}
	}

	for ( $i = 0; $i < $faq_count; $i++ ) {
		$q = '';
		$a = '';
		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/(faqs|faq_items|questions)_{$i}_(question|title)$/", $k ) ) {
				$q = $v;
			}
			if ( preg_match( "/(faqs|faq_items|questions)_{$i}_(answer|content|description)$/", $k ) ) {
				$a = $v;
			}
		}
		if ( ! empty( $q ) || ! empty( $a ) ) {
			$faq_items[] = array(
				'question' => $q,
				'answer'   => $a,
			);
		}
	}
}

$accordion_id = 'accordion-' . preg_replace( '/[^a-zA-Z0-9_-]/', '', $block_id );
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" data-aos="zoom-in" data-aos-duration="600">
	<div class="container">
		<?php if ( ! empty( $short_heading ) ) : ?>
			<h6 class="short-hd mb-2 text-center"><?php echo esc_html( $short_heading ); ?></h6>
		<?php endif; ?>

		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2 mb-4 text-center text-white"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<div class="accordion" id="<?php echo esc_attr( $accordion_id ); ?>">
				<?php foreach ( $faq_items as $index => $item ) :
					$collapse_id = $accordion_id . '-collapse-' . $index;
					$is_first    = 0 === $index;
					?>
					<div class="accordion-item mb-3">
						<div class="accordion-header">
							<button class="accordion-button <?php echo $is_first ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $collapse_id ); ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $collapse_id ); ?>">
								<p><?php echo esc_html( $item['question'] ); ?></p>
							</button>
						</div>
						<div id="<?php echo esc_attr( $collapse_id ); ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" data-bs-parent="#<?php echo esc_attr( $accordion_id ); ?>">
							<div class="accordion-body">
								<?php if ( strpos( $item['answer'], '<p>' ) !== false ) : ?>
									<?php echo wp_kses_post( $item['answer'] ); ?>
								<?php else : ?>
									<p><?php echo wp_kses_post( nl2br( $item['answer'] ) ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
