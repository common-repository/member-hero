<?php
/**
 * Posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $items->have_posts() ) : ?>
<div class="memberhero-section memberhero-section-posts">

	<?php while ( $items->have_posts() ) : $items->the_post(); ?>
	<div class="memberhero-item memberhero-item-alt" data-permalink="<?php the_permalink(); ?>">
		<div class="memberhero-item-image"><a href="<?php echo memberhero_get_profile_url(); ?>"><?php echo get_avatar( $the_user->ID, 50 ); ?></a></div>
		<div class="memberhero-item-info">

			<div class="memberhero-item-header">
				<div class="memberhero-item-head">
					<span class="memberhero-item-subhead">
						<span class="mc-item-username">
							<a href="<?php echo memberhero_get_profile_url(); ?>" class="memberhero-item-head-anchor"><strong><?php echo $the_user->display_name; ?></strong></a>
							<span class="memberhero-item-sublink">@<?php echo esc_html( $the_user->user_login ); ?></span>
							&middot; <a href="<?php the_permalink(); ?>" title="<?php memberhero_the_date(); ?>" class="memberhero-item-meta"><?php memberhero_the_date_diff(); ?></a>
						</span>
					</span>
				</div>
			</div>

			<div class="memberhero-item-text"><?php the_title(); ?></div>

			<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" class="memberhero-item-feature">
				<?php the_post_thumbnail(); ?>
			</a>
			<?php endif; ?>

		</div>
	</div>
	<?php endwhile; ?>

</div>

<?php else : ?>

	<?php if ( $the_user->ID == get_current_user_id() ) : ?>

	<div class="memberhero-section is-centered">
		<div class="memberhero-strong-heading"><?php _e( 'You haven’t created any posts.', 'memberhero' ); ?></div>
		<div class="memberhero-subheading"><?php _e( 'When you create a post, It will be listed here.', 'memberhero' ); ?></div>
	</div>

	<?php else : ?>

	<div class="memberhero-section is-centered">
		<div class="memberhero-strong-heading"><?php printf( __( '@%s haven’t created any posts.', 'memberhero' ), $the_user->user_login ); ?></div>
		<div class="memberhero-subheading"><?php _e( 'When they do, they’ll be listed here.', 'memberhero' ); ?></div>
	</div>

	<?php endif; ?>

<?php endif; ?>