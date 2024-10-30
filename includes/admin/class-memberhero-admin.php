<?php
/**
 * Admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin class.
 */
class MemberHero_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_init', array( $this, 'preview_emails' ) );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		add_action( 'admin_init', array( $this, 'admin_redirects' ) );
		add_action( 'admin_footer', 'memberhero_print_js', 25 );
		add_action( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
		add_action( 'admin_footer', array( $this, 'add_signup_modal' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/memberhero-admin-functions.php';
		include_once dirname( __FILE__ ) . '/memberhero-meta-box-functions.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-post-types.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-taxonomies.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-menus.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-notices.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-assets.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-api-keys.php';
		include_once dirname( __FILE__ ) . '/class-memberhero-admin-import-users.php';

		// Setup/welcome
		if ( ! empty( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'memberhero-setup':
					include_once dirname( __FILE__ ) . '/class-memberhero-admin-setup-wizard.php';
				break;
			}
		}
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		switch ( $screen->id ) {
			case 'dashboard' :
				include 'class-memberhero-admin-dashboard.php';
				break;
			case 'users':
			case 'user':
			case 'profile':
			case 'user-edit':
				include dirname( __FILE__ ) . '/class-memberhero-admin-users.php';
				break;
		}
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Preview email template.
	 */
	public function preview_emails() {

		if ( isset( $_GET['preview_memberhero_mail'] ) ) {
			if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'preview-mail' ) ) ) {
				die( __( 'Security check.', 'memberhero' ) );
			}

			// load the mailer class.
			$mailer = memberhero()->mailer();

			// get the preview email subject.
			$email_heading = __( 'HTML email preview', 'memberhero' );

			// get the preview email content.
			ob_start();
			include 'views/html-email-template-preview.php';
			$message = ob_get_clean();

			// create a new email.
			$email = new MemberHero_Email();

			// wrap the content with the email template and then add styles.
			$message = apply_filters( 'memberhero_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $message ) ) );

			echo $message;
			exit;
		}
	}

	/**
	 * Check if admin access should be prevented.
	 */
	public function prevent_admin_access() {
		global $pagenow;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( is_admin() && is_user_logged_in() && ! current_user_can( 'memberhero_view_wpadmin' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				exit( wp_safe_redirect( home_url() ) );
			}
		}
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function admin_redirects() {

		// Setup wizard redirect.
		if ( get_transient( '_memberhero_activation_redirect' ) && apply_filters( 'memberhero_enable_setup_wizard', true ) ) {
			$do_redirect  = true;
			$current_page = isset( $_GET['page'] ) ? memberhero_clean( wp_unslash( $_GET['page'] ) ) : false;

			// On these pages, or during these events, postpone the redirect.
			if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_memberhero' ) ) {
				$do_redirect = false;
			}

			// On these pages, or during these events, disable the redirect.
			if ( 'memberhero-setup' === $current_page || ! MemberHero_Admin_Notices::has_notice( 'install' ) || apply_filters( 'memberhero_prevent_automatic_wizard_redirect', false ) || isset( $_GET['activate-multi'] ) ) {
				delete_transient( '_memberhero_activation_redirect' );
				$do_redirect = false;
			}

			if ( $do_redirect ) {
				delete_transient( '_memberhero_activation_redirect' );
				wp_safe_redirect( admin_url( 'index.php?page=memberhero-setup' ) );
				exit;
			}
		}
	}

	/**
	 * Change the admin footer text on Member Hero admin pages.
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_memberhero' ) || ! function_exists( 'memberhero_get_screen_ids' ) ) {
			return $footer_text;
		}

		$current_screen  	= get_current_screen();
		$memberhero_pages        	= memberhero_get_screen_ids();

		// Set only Member Hero pages.
		$memberhero_pages = array_diff( $memberhero_pages, array( 'profile', 'user-edit' ) );

		// Check to make sure we're on a admin page.
		if ( isset( $current_screen->id ) && apply_filters( 'memberhero_display_admin_footer_text', in_array( $current_screen->id, $memberhero_pages, true ) ) ) {
			$footer_text = '<span id="footer-thankyou">' . sprintf( wp_kses_post( __( 'Thank you for creating with %s. | Thank you for using %s!', 'memberhero' ) ), 
				'<a href="https://wordpress.org/" target="_blank">' . __( 'WordPress', 'memberhero' ) . '</a>',
				'<a href="https://memberhero.pro" target="_blank">' . __( 'Member Hero', 'memberhero' ) . '</a>' ) . '</span>';
		}

		return $footer_text;
	}

    /**
     * Add signup modal layout.
     */
	function add_signup_modal() {
		global $pagenow;

		require_once dirname( __FILE__ ) . '/views/html-popup-access.php';
	}

}

return new MemberHero_Admin();