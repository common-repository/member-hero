<?php
/**
 * User Account Approved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_User_Account_Approved class.
 */
class MemberHero_Email_User_Account_Approved extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'user_account_approved';
		$this->is_user_email  = true;

		$this->title       	  = esc_html__( 'Account approved', 'memberhero' );
		$this->description 	  = esc_html__( 'User "account approved" emails are sent when user account is approved.', 'memberhero' );

		$this->template_html  = 'emails/user-account-approved.php';
		$this->template_plain = 'emails/plain/user-account-approved.php';

		// Trigger.
		add_action( 'memberhero_user_approved_notification', array( $this, 'trigger' ), 10, 1 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( 'Your {site_title} account was approved', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Your account was approved', 'memberhero' );
	}

	/**
	 * Trigger.
	 */
	public function trigger( $user_data = '' ) {
		$this->setup_locale();

		if ( $user_data ) {
			$this->object     = $user_data;
			$this->recipient  = $this->object->user_email;
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 */
	public function get_content_html() {
		return memberhero_get_template_html(
			$this->template_html, array(
				'user'			=> $this->object,
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			)
		);
	}

	/**
	 * Get content plain.
	 */
	public function get_content_plain() {
		return memberhero_get_template_html(
			$this->template_plain, array(
				'user'			=> $this->object,
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			)
		);
	}

}

return new MemberHero_Email_User_Account_Approved();