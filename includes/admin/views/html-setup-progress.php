<?php
/**
 * Admin View: Setup - Progress.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h1><?php esc_html_e( 'Setting up', 'memberhero' ); ?></h1>

<form method="post">

	<p><?php esc_html_e( 'We are now setting up MemberHero on your website.', 'memberhero' ); ?></p>

	<div class="memberhero-setup-checklist">
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Creating default forms and fields', 'memberhero' ); ?></div>
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Creating default member directory', 'memberhero' ); ?></div>
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Creating default profile and account pages', 'memberhero' ); ?></div>
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Creating default registration and login pages', 'memberhero' ); ?></div>
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Enabling user registration', 'memberhero' ); ?></div>
		<div class="memberhero-setup-progress"><span><?php echo memberhero_svg_icon( 'check' ); ?></span><?php _e( 'Creating endpoints and clearing cache', 'memberhero' ); ?></div>
	</div>

	<?php wp_nonce_field( 'memberhero-setup' ); ?>

	<p class="memberhero-setup-actions step">
		<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'memberhero' ); ?>" name="save_step" disabled><?php esc_html_e( 'Continue', 'memberhero' ); ?></button>
	</p>

</form>