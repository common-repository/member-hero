<?php
/**
 * Admin View: Setup - Welcome.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h1><?php esc_html_e( 'Welcome!', 'memberhero' ); ?></h1>

<form method="post">

	<p><?php esc_html_e( 'Our automated setup wizard helps you install Member Hero quickly and painlessly.', 'memberhero' ); ?></p>

	<?php wp_nonce_field( 'memberhero-setup' ); ?>

	<p class="memberhero-setup-actions step">
		<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Get started', 'memberhero' ); ?>" name="save_step"><?php esc_html_e( 'Get started', 'memberhero' ); ?></button>
	</p>

</form>