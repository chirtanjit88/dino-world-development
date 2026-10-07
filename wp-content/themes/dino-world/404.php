<?php
/**
 * 404 Error Page Template
 *
 * @package Dino_World
 */

get_header();
?>

<div class="error-404-sec sec-py py-5 text-center">
	<div class="container py-5" data-aos="zoom-in">
		<h1 class="display-1 fw-bold mb-3"><?php esc_html_e( '404', 'dino-world' ); ?></h1>
		<h2 class="mb-4"><?php esc_html_e( 'Page Not Found', 'dino-world' ); ?></h2>
		<p class="text-muted mx-auto mb-4" style="max-width: 500px;">
			<?php esc_html_e( 'The page you are looking for might have been removed or is temporarily unavailable.', 'dino-world' ); ?>
		</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn custom-theme-btn">
			<?php esc_html_e( 'Back to Home', 'dino-world' ); ?>
		</a>
	</div>
</div>

<?php
get_footer();
