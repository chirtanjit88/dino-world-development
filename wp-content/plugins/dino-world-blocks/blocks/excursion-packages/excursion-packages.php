<?php
/**
 * Block Name: Excursion Packages
 *
 * Powered by ACF / SCF Block Fields:
 * - heading (Text)
 * - packages (Repeater)
 *   - package_title (Text)
 *   - subtitle (Text)
 *   - features (Repeater)
 *     - feature_text (Text)
 *   - price_text (Text)
 *   - is_featured (True/False)
 *   - information (WYSIWYG Editor)
 *   - button (Link)
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during backend preview render.
 * @param int    $post_id    The post ID the block is rendering on.
 *
 * @package Dino_World_Blocks
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'ex-packeg-' . ( ! empty( $block['id'] ) ? $block['id'] : uniqid() );
$class_name = 'ex-packeg sec-padding1';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$block_data = ! empty( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

// 1. Heading
$heading = '';
if ( function_exists( 'get_field' ) ) {
	$heading = get_field( 'heading' ) ?: ( get_field( 'title' ) ?: get_field( 'section_title' ) );
}

if ( empty( $heading ) ) {
	foreach ( $block_data as $k => $v ) {
		if ( empty( $k ) || $k[0] === '_' ) continue;
		if ( preg_match( '/(heading|title|section_title)$/', $k ) ) {
			$heading = $v;
			break;
		}
	}
}

// Helper to parse features list from array / strings
$parse_features = function( $val ) {
	$result = array();
	if ( empty( $val ) ) {
		return $result;
	}
	if ( is_array( $val ) ) {
		foreach ( $val as $row ) {
			if ( is_array( $row ) ) {
				$t = ! empty( $row['feature_text'] ) ? $row['feature_text'] : ( ! empty( $row['text'] ) ? $row['text'] : ( ! empty( $row['item'] ) ? $row['item'] : ( ! empty( $row['title'] ) ? $row['title'] : '' ) ) );
				if ( ! empty( $t ) ) {
					$result[] = $t;
				}
			} elseif ( is_string( $row ) && ! empty( $row ) ) {
				$result[] = $row;
			}
		}
	} elseif ( is_string( $val ) ) {
		if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $val, $matches ) && ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $li ) {
				$li = trim( wp_strip_all_tags( $li ) );
				if ( ! empty( $li ) ) $result[] = $li;
			}
		} else {
			$lines = explode( "\n", str_replace( "\r", "", $val ) );
			foreach ( $lines as $line ) {
				$line = trim( wp_strip_all_tags( $line ) );
				if ( ! empty( $line ) ) $result[] = $line;
			}
		}
	}
	return $result;
};

// Helper to normalize link array / string / serialized data
$parse_link = function( $val, $default_title = '' ) {
	if ( empty( $val ) ) {
		return null;
	}
	if ( is_string( $val ) && ( strpos( $val, 'a:' ) === 0 || strpos( $val, 's:' ) === 0 ) ) {
		$unserialized = @unserialize( $val );
		if ( false !== $unserialized ) {
			$val = $unserialized;
		}
	}
	if ( is_string( $val ) && strpos( $val, '{' ) === 0 ) {
		$decoded = json_decode( $val, true );
		if ( is_array( $decoded ) ) {
			$val = $decoded;
		}
	}

	$link = array(
		'url'    => '',
		'title'  => $default_title,
		'target' => '_self',
	);

	if ( is_array( $val ) ) {
		$link['url']    = ! empty( $val['url'] ) ? $val['url'] : ( ! empty( $val['link'] ) ? $val['link'] : '' );
		$link['title']  = ! empty( $val['title'] ) ? $val['title'] : ( ! empty( $val['text'] ) ? $val['text'] : ( ! empty( $val['label'] ) ? $val['label'] : $default_title ) );
		$link['target'] = ! empty( $val['target'] ) ? $val['target'] : '_self';
	} elseif ( is_string( $val ) ) {
		$link['url']   = trim( $val );
		$link['title'] = ! empty( $default_title ) ? $default_title : __( 'Book Now', 'dino-world' );
	}

	if ( empty( $link['url'] ) ) {
		return null;
	}
	if ( empty( $link['title'] ) ) {
		$link['title'] = ! empty( $default_title ) ? $default_title : __( 'Book Now', 'dino-world' );
	}
	return $link;
};

// 2. Packages List
$packages = array();

// Method A: ACF have_rows
if ( function_exists( 'have_rows' ) && have_rows( 'packages' ) ) {
	while ( have_rows( 'packages' ) ) {
		the_row();
		$title       = get_sub_field( 'package_title' ) ?: ( get_sub_field( 'title' ) ?: get_sub_field( 'heading' ) );
		$subtitle    = get_sub_field( 'subtitle' ) ?: ( get_sub_field( 'package_subtitle' ) ?: get_sub_field( 'sub_heading' ) );
		$price       = get_sub_field( 'price_text' ) ?: ( get_sub_field( 'price' ) ?: get_sub_field( 'pricing' ) );
		$is_featured = (bool) ( get_sub_field( 'is_featured' ) ?: ( get_sub_field( 'featured' ) ?: get_sub_field( 'highlighted' ) ) );
		$information = get_sub_field( 'information' ) ?: ( get_sub_field( 'info' ) ?: ( get_sub_field( 'package_info' ) ?: get_sub_field( 'description' ) ) );
		$raw_button  = get_sub_field( 'button' ) ?: ( get_sub_field( 'btn' ) ?: ( get_sub_field( 'link' ) ?: get_sub_field( 'action_button' ) ) );
		$button      = $parse_link( $raw_button, __( 'Enquire Now', 'dino-world' ) );

		$enquire_shortcode = get_sub_field( 'enquire_form_shortcode' ) ?: ( get_sub_field( 'form_shortcode' ) ?: ( get_sub_field( 'shortcode' ) ?: '' ) );
		if ( empty( $enquire_shortcode ) ) {
			$b_title = ! empty( $button['title'] ) ? $button['title'] : '';
			$b_url   = ! empty( $button['url'] ) ? $button['url'] : '';
			if ( '#' === $b_url || empty( $b_url ) || stripos( $b_title, 'enquire' ) !== false ) {
				$enquire_shortcode = '[forminator_form id="310"]';
			}
		}

		$feats = array();
		if ( have_rows( 'features' ) ) {
			while ( have_rows( 'features' ) ) {
				the_row();
				$f_txt = get_sub_field( 'feature_text' ) ?: ( get_sub_field( 'text' ) ?: ( get_sub_field( 'item' ) ?: get_sub_field( 'title' ) ) );
				if ( ! empty( $f_txt ) ) {
					$feats[] = $f_txt;
				}
			}
		} else {
			$raw_f = get_sub_field( 'features' ) ?: ( get_sub_field( 'items' ) ?: get_sub_field( 'list' ) );
			$feats = $parse_features( $raw_f );
		}

		if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) || ! empty( $information ) || ! empty( $button ) ) {
			$packages[] = array(
				'title'                  => $title,
				'subtitle'               => $subtitle,
				'features'               => $feats,
				'price'                  => $price,
				'is_featured'            => $is_featured,
				'information'            => $information,
				'button'                 => $button,
				'enquire_form_shortcode' => $enquire_shortcode,
			);
		}
	}
}

// Method B: ACF get_field array
if ( empty( $packages ) && function_exists( 'get_field' ) ) {
	$rep_keys = array( 'packages', 'package_cards', 'cards', 'items', 'excursion_packages' );
	foreach ( $rep_keys as $rk ) {
		$g_pkgs = get_field( $rk );
		if ( ! empty( $g_pkgs ) && is_array( $g_pkgs ) ) {
			foreach ( $g_pkgs as $item ) {
				if ( is_array( $item ) ) {
					$title       = ! empty( $item['package_title'] ) ? $item['package_title'] : ( ! empty( $item['title'] ) ? $item['title'] : ( ! empty( $item['heading'] ) ? $item['heading'] : '' ) );
					$subtitle    = ! empty( $item['subtitle'] ) ? $item['subtitle'] : ( ! empty( $item['package_subtitle'] ) ? $item['package_subtitle'] : ( ! empty( $item['sub_heading'] ) ? $item['sub_heading'] : '' ) );
					$price       = ! empty( $item['price_text'] ) ? $item['price_text'] : ( ! empty( $item['price'] ) ? $item['price'] : ( ! empty( $item['pricing'] ) ? $item['pricing'] : '' ) );
					$is_featured = ! empty( $item['is_featured'] ) || ! empty( $item['featured'] ) || ! empty( $item['highlighted'] );
					$information = ! empty( $item['information'] ) ? $item['information'] : ( ! empty( $item['info'] ) ? $item['info'] : ( ! empty( $item['package_info'] ) ? $item['package_info'] : ( ! empty( $item['description'] ) ? $item['description'] : '' ) ) );
					$raw_button  = ! empty( $item['button'] ) ? $item['button'] : ( ! empty( $item['btn'] ) ? $item['btn'] : ( ! empty( $item['link'] ) ? $item['link'] : null ) );
					$button      = $parse_link( $raw_button, __( 'Enquire Now', 'dino-world' ) );

					$enquire_shortcode = ! empty( $item['enquire_form_shortcode'] ) ? $item['enquire_form_shortcode'] : ( ! empty( $item['form_shortcode'] ) ? $item['form_shortcode'] : ( ! empty( $item['shortcode'] ) ? $item['shortcode'] : '' ) );
					if ( empty( $enquire_shortcode ) ) {
						$b_title = ! empty( $button['title'] ) ? $button['title'] : '';
						$b_url   = ! empty( $button['url'] ) ? $button['url'] : '';
						if ( '#' === $b_url || empty( $b_url ) || stripos( $b_title, 'enquire' ) !== false ) {
							$enquire_shortcode = '[forminator_form id="310"]';
						}
					}

					$raw_f       = ! empty( $item['features'] ) ? $item['features'] : ( ! empty( $item['items'] ) ? $item['items'] : '' );
					$feats       = $parse_features( $raw_f );

					if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) || ! empty( $information ) || ! empty( $button ) ) {
						$packages[] = array(
							'title'                  => $title,
							'subtitle'               => $subtitle,
							'features'               => $feats,
							'price'                  => $price,
							'is_featured'            => $is_featured,
							'information'            => $information,
							'button'                 => $button,
							'enquire_form_shortcode' => $enquire_shortcode,
						);
					}
				}
			}
			if ( ! empty( $packages ) ) {
				break;
			}
		}
	}
}

// Method C: Raw block_data fallback (handles nested repeaters in block preview)
if ( empty( $packages ) && ! empty( $block_data ) ) {
	$count = 0;
	foreach ( $block_data as $k => $v ) {
		if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/^packages$/', $k ) && is_numeric( $v ) ) {
			$count = (int) $v;
			break;
		}
	}
	if ( ! $count ) {
		foreach ( $block_data as $k => $v ) {
			if ( ! empty( $k ) && $k[0] !== '_' && preg_match( '/(packages|package_cards|cards)$/', $k ) && is_numeric( $v ) ) {
				$count = (int) $v;
				break;
			}
		}
	}

	for ( $i = 0; $i < $count; $i++ ) {
		$title       = '';
		$subtitle    = '';
		$price       = '';
		$is_featured = false;
		$information = '';
		$button_data = null;
		$btn_url     = '';
		$btn_title   = '';
		$btn_target  = '_self';
		$feats       = array();

		foreach ( $block_data as $k => $v ) {
			if ( empty( $k ) || $k[0] === '_' ) continue;
			if ( preg_match( "/packages_{$i}_(package_title|title|heading)$/", $k ) ) {
				$title = $v;
			}
			if ( preg_match( "/packages_{$i}_(subtitle|package_subtitle|sub_heading)$/", $k ) ) {
				$subtitle = $v;
			}
			if ( preg_match( "/packages_{$i}_(price_text|price|pricing)$/", $k ) ) {
				$price = $v;
			}
			if ( preg_match( "/packages_{$i}_(is_featured|featured|highlighted)$/", $k ) ) {
				$is_featured = (bool) $v;
			}
			if ( preg_match( "/packages_{$i}_(information|info|package_info|description)$/", $k ) ) {
				$information = $v;
			}
			if ( preg_match( "/packages_{$i}_(button|btn|link|action_button)$/", $k ) ) {
				$button_data = $v;
			}
			if ( preg_match( "/packages_{$i}_button_url$/", $k ) ) {
				$btn_url = $v;
			}
			if ( preg_match( "/packages_{$i}_button_title$/", $k ) ) {
				$btn_title = $v;
			}
			if ( preg_match( "/packages_{$i}_button_target$/", $k ) ) {
				$btn_target = $v;
			}
			if ( preg_match( "/packages_{$i}_(enquire_form_shortcode|form_shortcode|shortcode)$/", $k ) ) {
				$enquire_shortcode = $v;
			}
			if ( preg_match( "/packages_{$i}_features_\d+_(feature_text|text|item|title)$/", $k ) && ! empty( $v ) ) {
				$feats[] = $v;
			}
		}

		if ( empty( $button_data ) && ! empty( $btn_url ) ) {
			$button_data = array(
				'url'    => $btn_url,
				'title'  => $btn_title,
				'target' => $btn_target,
			);
		}
		$button = $parse_link( $button_data, __( 'Enquire Now', 'dino-world' ) );

		if ( empty( $enquire_shortcode ) ) {
			$b_title = ! empty( $button['title'] ) ? $button['title'] : '';
			$b_url   = ! empty( $button['url'] ) ? $button['url'] : '';
			if ( '#' === $b_url || empty( $b_url ) || stripos( $b_title, 'enquire' ) !== false ) {
				$enquire_shortcode = '[forminator_form id="310"]';
			}
		}

		if ( empty( $feats ) ) {
			foreach ( $block_data as $k => $v ) {
				if ( empty( $k ) || $k[0] === '_' ) continue;
				if ( preg_match( "/packages_{$i}_(features|items)$/", $k ) ) {
					$feats = $parse_features( $v );
				}
			}
		}

		if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $feats ) || ! empty( $price ) || ! empty( $information ) || ! empty( $button ) ) {
			$packages[] = array(
				'title'                  => $title,
				'subtitle'               => $subtitle,
				'features'               => $feats,
				'price'                  => $price,
				'is_featured'            => $is_featured,
				'information'            => $information,
				'button'                 => $button,
				'enquire_form_shortcode' => $enquire_shortcode,
			);
		}
	}
}

$green_tick_url  = get_template_directory_uri() . '/assets/image/green-tick.png';
$arrow_icon_url  = get_template_directory_uri() . '/assets/image/right-arrow.png';
$is_admin_preview = ! empty( $is_preview ) || is_admin();
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="title-2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $packages ) ) : ?>
			<div class="row justify-content-center g-4">
				<?php
				$total_pkgs = count( $packages );
				if ( 1 === $total_pkgs ) {
					$col_class = 'col-lg-6 col-md-8 col-sm-10';
				} elseif ( 2 === $total_pkgs ) {
					$col_class = 'col-lg-5 col-md-6';
				} elseif ( 3 === $total_pkgs ) {
					$col_class = 'col-lg-4 col-md-6';
				} elseif ( 4 === $total_pkgs ) {
					$col_class = 'col-lg-3 col-md-6';
				} else {
					$col_class = 'col-lg-4 col-md-6';
				}

				foreach ( $packages as $index => $item ) :
					$card_class = 'xp-card';
					if ( ! empty( $item['is_featured'] ) ) {
						$card_class .= ' xp-card1';
					}
					$delay_attr = ! $is_admin_preview ? ' data-aos="fade-up" data-aos-delay="' . esc_attr( $index * 100 ) . '"' : '';
					?>
					<div class="<?php echo esc_attr( $col_class ); ?> d-flex"<?php echo $delay_attr; ?>>
						<div class="<?php echo esc_attr( $card_class ); ?> w-100">
							<div class="xp-card-top">
								<?php if ( ! empty( $item['title'] ) ) : ?>
									<div class="xp-hd">
										<p><?php echo esc_html( $item['title'] ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $item['subtitle'] ) ) : ?>
									<div class="xp-sub-hd">
										<p><?php echo esc_html( $item['subtitle'] ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $item['features'] ) ) : ?>
									<div class="xp-features-list">
										<?php foreach ( $item['features'] as $f_text ) : ?>
											<div class="xp-txt">
												<div class="xp-img">
													<img src="<?php echo esc_url( $green_tick_url ); ?>" alt="" />
												</div>
												<div class="xp-inf">
													<p><?php echo esc_html( $f_text ); ?></p>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="xp-card-bottom">
								<?php if ( ! empty( $item['price'] ) ) : ?>
									<?php
									$price_output = $item['price'];
									// If price contains numbers like $22 and does not already have <span>, wrap with <span>
									if ( strpos( $price_output, '<span>' ) === false ) {
										$price_output = preg_replace( '/(\$\d+(\.\d+)?)/', '<span>$1</span>', $price_output );
									}
									?>
									<div class="xp-price">
										<p><?php echo wp_kses_post( $price_output ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $item['information'] ) ) : ?>
									<div class="xp-info">
										<?php
										$info_content = $item['information'];
										if ( strpos( $info_content, '<p>' ) === false && strpos( $info_content, '<div' ) === false ) {
											$info_content = wpautop( $info_content );
										}
										echo wp_kses_post( $info_content );
										?>
									</div>
								<?php endif; ?>

								<?php
								$has_popup  = ! empty( $item['enquire_form_shortcode'] );
								$modal_id   = 'dino-modal-' . sanitize_html_class( $block_id . '-' . $index );
								$btn_title  = ! empty( $item['button']['title'] ) ? $item['button']['title'] : __( 'Enquire Now', 'dino-world' );
								$btn_url    = ! empty( $item['button']['url'] ) ? $item['button']['url'] : '#';
								$btn_target = ! empty( $item['button']['target'] ) ? $item['button']['target'] : '_self';
								?>

								<?php if ( ! empty( $item['button'] ) || $has_popup ) : ?>
									<div class="xp-btn">
										<?php if ( $has_popup ) : ?>
											<a class="primary-btn dino-modal-open-btn"
											   href="#<?php echo esc_attr( $modal_id ); ?>"
											   role="button"
											   data-dino-modal-target="#<?php echo esc_attr( $modal_id ); ?>"
											   data-bs-toggle="modal"
											   data-bs-target="#<?php echo esc_attr( $modal_id ); ?>">
												<?php echo esc_html( $btn_title ); ?>
												<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
											</a>
										<?php else : ?>
											<a class="primary-btn" href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_target ); ?>">
												<?php echo esc_html( $btn_title ); ?>
												<img src="<?php echo esc_url( $arrow_icon_url ); ?>" alt="" />
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $packages ) ) : ?>
		<?php foreach ( $packages as $index => $item ) : ?>
			<?php
			if ( empty( $item['enquire_form_shortcode'] ) ) {
				continue;
			}
			$modal_id        = 'dino-modal-' . sanitize_html_class( $block_id . '-' . $index );
			$modal_pkg_title = ! empty( $item['title'] ) ? $item['title'] : __( 'School Excursion', 'dino-world' );
			?>
			<div class="dino-excursion-modal modal fade" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $modal_id ); ?>-label" aria-hidden="true" role="dialog">
				<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
					<div class="modal-content dino-modal-card">
						<div class="dino-modal-header">
							<div class="dino-modal-title-group">
								<span class="dino-modal-kicker"><?php echo esc_html( $modal_pkg_title ); ?></span>
								<h3 class="dino-modal-heading" id="<?php echo esc_attr( $modal_id ); ?>-label">
									<?php esc_html_e( 'Bookings & Excursion Enquiry', 'dino-world' ); ?>
								</h3>
								<?php if ( ! empty( $item['subtitle'] ) ) : ?>
									<p class="dino-modal-subheading"><?php echo esc_html( $item['subtitle'] ); ?></p>
								<?php endif; ?>
							</div>
							<button type="button" class="dino-modal-close" data-bs-dismiss="modal" data-dino-close aria-label="<?php esc_attr_e( 'Close', 'dino-world' ); ?>">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
									<line x1="18" y1="6" x2="6" y2="18"></line>
									<line x1="6" y1="6" x2="18" y2="18"></line>
								</svg>
							</button>
						</div>
						<div class="modal-body dino-modal-body">
							<div class="dino-modal-form-container">
								<?php echo do_shortcode( $item['enquire_form_shortcode'] ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</section>

<script>
(function() {
	function setupDinoModals() {
		var triggers = document.querySelectorAll('.dino-modal-open-btn');
		triggers.forEach(function(btn) {
			if (btn.getAttribute('data-dino-bound')) return;
			btn.setAttribute('data-dino-bound', 'true');

			btn.addEventListener('click', function(e) {
				e.preventDefault();
				var targetSelector = this.getAttribute('data-dino-modal-target') || this.getAttribute('data-bs-target') || this.getAttribute('href');
				if (!targetSelector || targetSelector === '#') return;
				var modal = document.querySelector(targetSelector);
				if (!modal) return;

				// Move modal to body to prevent any CSS transform clipping
				if (modal.parentElement !== document.body) {
					document.body.appendChild(modal);
				}

				// Bootstrap 5 Modal API
				if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
					var bsModal = window.bootstrap.Modal.getOrCreateInstance(modal);
					bsModal.show();
				} else {
					openFallbackModal(modal);
				}

				// Trigger resize so Forminator calculates field widths
				setTimeout(function() {
					window.dispatchEvent(new Event('resize'));
				}, 150);
			});
		});

		function openFallbackModal(modal) {
			var backdrop = document.querySelector('.dino-modal-backdrop');
			if (!backdrop) {
				backdrop = document.createElement('div');
				backdrop.className = 'dino-modal-backdrop';
				document.body.appendChild(backdrop);
				backdrop.addEventListener('click', closeFallbackModals);
			}
			backdrop.classList.add('show');
			modal.classList.add('show', 'is-active');
			modal.style.display = 'block';
			document.body.classList.add('modal-open');
			document.body.style.overflow = 'hidden';
		}

		function closeFallbackModals() {
			document.querySelectorAll('.dino-excursion-modal').forEach(function(m) {
				if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
					var inst = window.bootstrap.Modal.getInstance(m);
					if (inst) inst.hide();
				}
				m.classList.remove('show', 'is-active');
				m.style.display = 'none';
			});
			var backdrop = document.querySelector('.dino-modal-backdrop');
			if (backdrop) backdrop.classList.remove('show');
			document.body.classList.remove('modal-open');
			document.body.style.overflow = '';
		}

		document.querySelectorAll('.dino-modal-close, [data-dino-close]').forEach(function(closeBtn) {
			if (closeBtn.getAttribute('data-dino-bound')) return;
			closeBtn.setAttribute('data-dino-bound', 'true');
			closeBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var modal = this.closest('.dino-excursion-modal');
				if (modal && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
					var inst = window.bootstrap.Modal.getInstance(modal);
					if (inst) {
						inst.hide();
						return;
					}
				}
				closeFallbackModals();
			});
		});

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeFallbackModals();
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setupDinoModals);
	} else {
		setupDinoModals();
	}
})();
</script>
