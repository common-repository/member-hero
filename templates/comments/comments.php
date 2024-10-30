<?php
/**
 * Comments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $items ) : ?>
<div class="memberhero-section memberhero-section-comments">

	<?php foreach( $items as $comment ) : ?>
	<div class="memberhero-item memberhero-item-alt" data-permalink="<?php echo get_comment_link( $comment->comment_ID ); ?>">
		<div class="memberhero-item-image"><a href="<?php echo memberhero_get_profile_url(); ?>"><?php echo get_avatar( $the_user->ID, 50 ); ?></a></div>
		<div class="memberhero-item-info">

			<div class="memberhero-item-header">
				<div class="memberhero-item-head">
					<span class="memberhero-item-subhead">
						<span class="mc-item-username">
							<a href="<?php echo memberhero_get_profile_url(); ?>" class="memberhero-item-head-anchor"><strong><?php echo $the_user->display_name; ?></strong></a>
							<span class="memberhero-item-sublink">@<?php echo esc_html( $the_user->user_login ); ?></span>
							&middot; <a href="<?php echo get_comment_link( $comment->comment_ID ); ?>" title="<?php memberhero_the_date( $comment->comment_date_gmt ); ?>" class="memberhero-item-meta"><?php memberhero_the_date_diff( $comment->comment_date_gmt ); ?></a>
						</span>
					</span>
				</div>
			</div>

			<div class="memberhero-item-flex">
				<?php echo memberhero_svg_icon( 'corner-up-right' ); ?>
				<span><a href="<?php echo get_permalink( $comment->comment_post_ID ); ?>"><?php echo get_the_title( $comment->comment_post_ID ); ?></a></span>
			</div>

			<div class="memberhero-item-text"><?php echo $comment->comment_content; ?></div>

		</div>
	</div>
	<?php endforeach; ?>

</div>

<?php else : ?>

	<?php if ( $the_user->ID == get_current_user_id() ) : ?>

	<div class="memberhero-section is-centered">
		<div class="memberhero-strong-heading"><?php _e( 'You haven’t made any comments.', 'memberhero' ); ?></div>
		<div class="memberhero-subheading"><?php _e( 'When you add a comment, It will be listed here.', 'memberhero' ); ?></div>
	</div>

	<?php else : ?>

	<div class="memberhero-section is-centered">
		<div class="memberhero-strong-heading"><?php printf( __( '@%s haven’t made any comments.', 'memberhero' ), $the_user->user_login ); ?></div>
		<div class="memberhero-subheading"><?php _e( 'When they do, they’ll be listed here.', 'memberhero' ); ?></div>
	</div>

	<?php endif; ?>

<?php endif; ?>