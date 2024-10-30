<?php
/**
 * Emails Controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Emails class.
 */
class MemberHero_Emails {

	/**
	 * Array of email notification classes
	 */
	public $emails = array();

	/**
	 * The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Background emailer class.
	 */
	protected static $background_emailer = null;

	/**
	 * Main Instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Hook in all transactional emails.
	 */
	public static function init_transactional_emails() {
		$email_actions = apply_filters(
			'memberhero_email_actions', array(
				'',
			)
		);

		if ( apply_filters( 'memberhero_defer_transactional_emails', false ) ) {
			self::$background_emailer = new MemberHero_Background_Emailer();

			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'queue_transactional_email' ), 10, 10 );
			}
		} else {
			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
			}
		}
	}

	/**
	 * Queues transactional email so it's not sent in current request if enabled,
	 * otherwise falls back to send now.
	 */
	public static function queue_transactional_email() {
		if ( is_a( self::$background_emailer, 'MemberHero_Background_Emailer' ) ) {
			self::$background_emailer->push_to_queue(
				array(
					'filter' => current_filter(),
					'args'   => func_get_args(),
				)
			);
		} else {
			call_user_func_array( array( __CLASS__, 'send_transactional_email' ), func_get_args() );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 */
	public static function send_queued_transactional_email( $filter = '', $args = array() ) {
		if ( apply_filters( 'memberhero_allow_send_queued_transactional_email', true, $filter, $args ) ) {
			self::instance(); // Init self so emails exist.

			do_action_ref_array( $filter . '_notification', $args );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 */
	public static function send_transactional_email( $args = array() ) {
		try {
			$args = func_get_args();
			self::instance(); // Init self so emails exist.
			do_action_ref_array( current_filter() . '_notification', $args );
		} catch ( Exception $e ) {
			$error  = 'Transactional email triggered fatal error for callback ' . current_filter();
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				trigger_error( $error, E_USER_WARNING );
			}
		}
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'memberhero_email_header', array( $this, 'email_header' ) );
		add_action( 'memberhero_email_footer', array( $this, 'email_footer' ) );
		add_action( 'memberhero_email_user_meta', array( $this, 'user_meta' ), 10, 4 );

		add_filter( 'memberhero_email_user_meta_fields', array( $this, 'default_user_meta' ), 1, 3 );

		// Hook for replacing {site_title} in email-footer.
		add_filter( 'memberhero_email_footer_text', array( $this, 'email_footer_replace_site_title' ) );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'memberhero_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once dirname( __FILE__ ) . '/emails/class-memberhero-email.php';

		$this->emails['MemberHero_Email_New_Account']              	= include 'emails/class-memberhero-email-new-account.php';
		$this->emails['MemberHero_Email_Account_Pending']         	= include 'emails/class-memberhero-email-account-pending.php';
		$this->emails['MemberHero_Email_User_New_Account']   		= include 'emails/class-memberhero-email-user-new-account.php';
		$this->emails['MemberHero_Email_User_Email_Confirmation'] 	= include 'emails/class-memberhero-email-user-email-confirmation.php';
		$this->emails['MemberHero_Email_User_Email_Change']   		= include 'emails/class-memberhero-email-user-email-change.php';
		$this->emails['MemberHero_Email_User_Reset_Password']   	= include 'emails/class-memberhero-email-user-reset-password.php';
		$this->emails['MemberHero_Email_User_Password_Changed'] 	= include 'emails/class-memberhero-email-user-password-changed.php';
		$this->emails['MemberHero_Email_User_Account_Pending']		= include 'emails/class-memberhero-email-user-account-pending.php';
		$this->emails['MemberHero_Email_User_Account_Approved']		= include 'emails/class-memberhero-email-user-account-approved.php';
		$this->emails['MemberHero_Email_User_Account_Rejected']		= include 'emails/class-memberhero-email-user-account-rejected.php';

		$this->emails = apply_filters( 'memberhero_email_classes', $this->emails );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 */
	public function get_from_name() {
		return wp_specialchars_decode( get_option( 'memberhero_email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 */
	public function get_from_address() {
		return sanitize_email( get_option( 'memberhero_email_from_address' ) );
	}

	/**
	 * Get the email header.
	 */
	public function email_header( $email_heading ) {
		memberhero_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		memberhero_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Filter callback to replace {site_title} in email footer
	 */
	public function email_footer_replace_site_title( $string ) {
		return str_replace( '{site_title}', $this->get_blogname(), $string );
	}

	/**
	 * Wraps a message in the email template.
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'memberhero_email_header', $email_heading, null );

		echo wpautop( wptexturize( $message ) );

		do_action( 'memberhero_email_footer', null );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new MemberHero_Email();
		return $email->send( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * Get blog name formatted for emails.
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Add user meta to email templates.
	 */
	public function user_meta( $user, $sent_to_admin = false, $plain_text = false, $email = '' ) {
		$fields = apply_filters( 'memberhero_email_user_meta_fields', array(), $sent_to_admin, $user );

		if ( $fields ) {

			if ( $plain_text ) {

				foreach ( $fields as $field ) {
					if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
						echo $field['label'] . ': ' . $field['value'] . "\n"; // WPCS: XSS ok.
					}
				}
			} else {

				echo '<table>';
				foreach ( $fields as $field ) {
					if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
						echo '<tr>
								<td><strong>' . $field['label'] . '</strong></td>
								<td>' . $field['value'] . '</td>
							</tr>'; // WPCS: XSS ok.
					}
				}
				echo '</table><p class="hr"></p>';
			}
		}
	}

	/**
	 * Default user meta.
	 */
	public function default_user_meta( $fields, $sent_to_admin, $user ) {

		$fields[] = array(
			'label'	=> __( 'Username', 'memberhero' ),
			'value' => wptexturize( $user->user_login ),
		);

		$fields[] = array(
			'label'	=> __( 'Email', 'memberhero' ),
			'value' => wptexturize( $user->user_email ),
		);

		return $fields;
	}

}