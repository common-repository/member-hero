<?php
/**
 * Admin View: Notice - Install.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="message" class="updated memberhero-message">

	<p><?php echo wp_kses_post( '<strong>Welcome to Member Hero</strong> &#8211; You&lsquo;re almost ready to start your online community :)', 'memberhero' ); ?></p>

	<p class="submit">

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=memberhero-setup' ) ); ?>" class="button-primary"><?php esc_html_e( 'Run the Setup Wizard', 'memberhero' ); ?></a> 

		<a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'memberhero-hide-notice', 'install' ), 'memberhero_hide_notices_nonce', '_memberhero_notice_nonce' ) ); ?>">
			<?php esc_html_e( 'Skip setup', 'memberhero' ); ?>
		</a>

	</p>

</div>