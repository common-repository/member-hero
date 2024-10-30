<?php
/**
 * My Profile cover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_cover' );
?>

<div class="memberhero-profile-header">

	<div class="memberhero-profile-cover <?php echo memberhero_get_profile_cover_classes(); ?>">

		<?php if ( is_memberhero_editing_profile() ) : ?>

			<a href="#" class="memberhero-profile-cover-edit-overlay memberhero-dropdown-init" rel="memberhero-dropdown-cover">
				<?php if ( ! memberhero_user_uploaded_cover() ) : ?>
					<span>
						<?php echo memberhero_svg_icon( 'image' ); ?>
						<b><?php esc_html_e( 'Add a header photo', 'memberhero' ); ?></b>
					</span>
				<?php else : ?>
					<span>
						<?php echo memberhero_svg_icon( 'image' ); ?>
						<b><?php esc_html_e( 'Change your header photo', 'memberhero' ); ?></b>
					</span>
				<?php endif; ?>
			</a>

			<?php do_action( 'memberhero_user_cover_dropdown' ); ?>

			<div class="memberhero-cover-cropper-holder">
				<div class="memberhero-cover-cropper">

				</div>
				<div class="memberhero-cover-cropper-actions">
					<a href="#" class="memberhero-button nav memberhero-cancel-cover"><?php esc_html_e( 'Cancel', 'memberhero' ); ?></a>
					<a href="#" class="memberhero-button main memberhero-crop-cover" data-user_id="<?php echo $the_user->user_id; ?>"><?php esc_html_e( 'Apply', 'memberhero' ); ?></a>
				</div>
			</div>

		<?php endif; ?>

		<div class="memberhero-profile-coverbg" style="background-image: url( '<?php echo memberhero_get_user_cover_url(); ?>' );">

			<?php if ( memberhero_is_in_loop() ) : ?><a href="<?php echo memberhero_get_profile_url(); ?>"></a><?php endif; ?>

		</div>

	</div>

</div>

<?php do_action( 'memberhero_after_profile_cover' ); ?>