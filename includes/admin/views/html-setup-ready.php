<?php
/**
 * Admin View: Setup - Ready.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We've made it! Don't prompt the user to run the wizard again.
MemberHero_Admin_Notices::remove_notice( 'install' );

?>

<h1><?php esc_html_e( 'That&#39;s it! Your community is ready.', 'memberhero' ); ?></h1>

<p><?php esc_html_e( 'Thank you. Member Hero is now installed and ready to be configured.', 'memberhero' ); ?></p>

<h5><?php esc_html_e( 'What would you like to do next?', 'memberhero' ); ?></h5>

<div class="memberhero-setup-item-grid memberhero-setup-actions">
	<div class="memberhero-setup-item"><a href="<?php echo admin_url(); ?>" class="button button-primary"><?php esc_html_e( 'Visit dashboard', 'memberhero' ); ?></a></div>
	<div class="memberhero-setup-item"><a href="<?php echo esc_url( admin_url( 'admin.php?page=memberhero-settings' ) ); ?>" class="button"><?php esc_html_e( 'Review settings', 'memberhero' ); ?></a></div>
</div>

<h5><?php esc_html_e( 'Here are a few more things you can do', 'memberhero' ); ?></h5>

<div class="memberhero-setup-item-grid">
	<div class="memberhero-setup-item"><a href="https://memberhero.pro/roadmap/" class="button" target="_blank"><?php esc_html_e( 'Product Roadmap', 'memberhero' ); ?></a></div>
	<div class="memberhero-setup-item"><a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="<?php echo esc_attr( $this->tweets[ array_rand( $this->tweets, 1 ) ] ); ?>" data-url="https://bit.ly/2pVJpUJ" data-related="" data-show-count="false" data-size="large"><?php esc_html_e( 'Tweet to the world', 'memberhero' ); ?></a></div>
</div>

<div class="memberhero-setup-center">
	<?php echo sprintf( __( 'Thank you for installing <a href="https://memberhero.pro" target="_blank">Member Hero</a>. Need help? Please check out our %s guide and %s.', 'memberhero' ), '<a href="https://docs.memberhero.pro/collection/1-getting-started" target="_blank">' . __( 'Getting started', 'memberhero' ) . '</a>', '<a href="https://docs.memberhero.pro" target="_blank">' . __( 'documentation', 'memberhero' ). '</a>' ); ?>
</div>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>