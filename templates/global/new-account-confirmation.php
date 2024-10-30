<?php
/**
 * Confirmation code template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-info big memberhero-new-account-confirmation">

	<h1><?php esc_html_e( 'Check your email!', 'memberhero' ); ?></h1>

	<p>
		<?php printf( wp_kses_post( __( 'We&lsquo;ve sent a 6-digit confirmation code to %s. It will expire shortly, so enter it soon.', 'memberhero' ) ), '<strong>' . esc_attr( $user->user_email ) . '</strong>' ); ?>
	</p>

	<div class="memberhero-code" data-user_id="<?php echo absint( $user->ID ); ?>">
		<div class="memberhero-code-group">
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" value="" /></div>
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" value="" /></div>
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" value="" /></div>
		</div>
		<div class="memberhero-code-span">&mdash;</div>
		<div class="memberhero-code-group">
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" /></div>
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" value="" /></div>
			<div class="memberhero-code-input"><input type="text" class="memberhero-inline-input" maxlength="1" value="" /></div>
		</div>
	</div>

	<div class="memberhero-code-callback">
		<span class="memberhero-info-area"><?php esc_html_e( 'Keep this window open while checking for your code. Remember to try your spam folder!', 'memberhero' ); ?></span>
		<span class="memberhero-info"><?php esc_html_e( 'Please hold on. We&lsquo;re validating your account...', 'memberhero' ); ?></span>
		<span class="memberhero-error"><?php esc_html_e( 'Your confirmation code is invalid or may have expired.', 'memberhero' ); ?></span>
	</div>

</div>