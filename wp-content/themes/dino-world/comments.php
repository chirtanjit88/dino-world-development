<?php
/**
 * Comments Template for Dinosaur World
 *
 * @package Dino_World
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area mt-5 pt-4 border-top">
	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title mb-4">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One Thought on &ldquo;%1$s&rdquo;', 'dino-world' ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s Thought on &ldquo;%2$s&rdquo;', '%1$s Thoughts on &ldquo;%2$s&rdquo;', $comment_count, 'comments title', 'dino-world' ) ),
					number_format_i18n( $comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			}
			?>
		</h3>

		<ul class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ul',
					'short_ping'  => true,
					'avatar_size' => 54,
				)
			);
			?>
		</ul>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => '<span class="nav-prev-text">&larr; ' . esc_html__( 'Older Comments', 'dino-world' ) . '</span>',
				'next_text' => '<span class="nav-next-text">' . esc_html__( 'Newer Comments', 'dino-world' ) . ' &rarr;</span>',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments alert alert-warning"><?php esc_html_e( 'Comments are closed.', 'dino-world' ); ?></p>
	<?php endif; ?>

	<?php
	$commenter     = wp_get_current_commenter();
	$req           = get_option( 'require_name_email' );
	$aria_req      = ( $req ? " aria-required='true'" : '' );
	$arrow_icon_url = get_template_directory_uri() . '/assets/image/right-arrow.png';

	$fields = array(
		'author' => '<div class="row g-3 mb-3"><div class="col-md-6"><div class="comment-form-author form-group"><label for="author" class="form-label">' . esc_html__( 'Name', 'dino-world' ) . ( $req ? ' <span class="required text-danger">*</span>' : '' ) . '</label><input id="author" name="author" type="text" class="form-control comment-input" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' placeholder="' . esc_attr__( 'Your Name', 'dino-world' ) . '" /></div></div>',
		'email'  => '<div class="col-md-6"><div class="comment-form-email form-group"><label for="email" class="form-label">' . esc_html__( 'Email', 'dino-world' ) . ( $req ? ' <span class="required text-danger">*</span>' : '' ) . '</label><input id="email" name="email" type="email" class="form-control comment-input" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' placeholder="' . esc_attr__( 'Your Email', 'dino-world' ) . '" /></div></div></div>',
	);

	comment_form(
		array(
			'fields'               => $fields,
			'comment_field'        => '<div class="comment-form-comment form-group mb-3"><label for="comment" class="form-label">' . esc_html_x( 'Comment', 'noun', 'dino-world' ) . ' <span class="required text-danger">*</span></label><textarea id="comment" name="comment" class="form-control comment-textarea" rows="5" required="required" placeholder="' . esc_attr__( 'Share your thoughts or questions about this expedition...', 'dino-world' ) . '"></textarea></div>',
			'class_submit'         => 'orange-btn border-0 cursor-pointer',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">' . esc_html__( 'Post Comment', 'dino-world' ) . ' <img src="' . esc_url( $arrow_icon_url ) . '" alt="" /></button>',
			'title_reply'          => __( 'Leave an Expedition Note', 'dino-world' ),
			'title_reply_to'       => __( 'Leave an Expedition Note to %s', 'dino-world' ),
			'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title mb-3">',
			'title_reply_after'    => '</h3>',
			'cancel_reply_before'  => ' <small class="cancel-comment-reply">',
			'cancel_reply_after'   => '</small>',
		)
	);
	?>
</div>
