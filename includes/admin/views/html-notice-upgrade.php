<?php
/**
 * Admin View: Notice - Access
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$now = time(); // or your date as well
$your_date = strtotime("2020-07-15");
$datediff = $now - $your_date;
$days = absint( round( $datediff / ( 60 * 60 * 24 ) ) );
?>

<div id="message" class="updated memberhero-message memberhero-access">

	<a class="memberhero-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'memberhero-hide-notice', 'memberhero_lifetime_access' ), 'memberhero_hide_notices_nonce', '_memberhero_notice_nonce' ) ); ?>">
		<?php esc_html_e( 'Dismiss', 'memberhero' ); ?>
	</a>

	<div class="mhero-dynamic-msg">

	<p class="mhero-lead"><?php echo sprintf( __( 'Warning: In %s days, the existing features of Member Hero will be gone forever.', 'memberhero' ), $days ); ?></p>

	<p><?php echo __( 'On 15 July 2020, this plugin will become an email newsletter connection plugin.', 'memberhero' ); ?></p>

	<p><?php echo __( 'It will connect Wordpress to Mailchimp, ConvertKit, Mailerlite and more.<br />And allow users to automatically send blog posts as email newsletters without leaving WordPress.', 'memberhero' ); ?></p>

	<p><?php echo __( 'The current Member Hero features will be moved to a premium plugin, that&rsquo;s being completely rebuilt from the ground up.', 'memberhero' ); ?></p>

	<p class="mhero-lead"><?php echo __( 'Don&rsquo;t worry, we won’t leave you hanging.', 'memberhero' ); ?></p>

	<p><?php echo __( 'We’re offering you, and anyone else with an active Member Hero install, free lifetime access to all features of the new premium plugin we’re building.', 'memberhero' ); ?></p>

	<p><a href="#" class="button button-primary memberhero-get-access"><?php _e( 'Get free lifetime access to Member Hero Pro', 'memberhero' ); ?></a></p>

	</div>

</div>