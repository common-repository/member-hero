<?php
/**
 * User Email Change.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_User_Email_Change class.
 */
class MemberHero_Email_User_Email_Change extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'user_email_change';
		$this->is_user_email  = true;

		$this->title       = esc_html__( 'Email change', 'memberhero' );
		$this->description = esc_html__( 'User "email change" emails are sent when users change their email.', 'memberhero' );

		$this->template_html  = 'emails/user-email-change.php';
		$this->template_plain = 'emails/plain/user-email-change.php';

		// Trigger.
		add_action( 'memberhero_email_change_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( 'Confirm your new email for {site_title}', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Confirm your new email', 'memberhero' );
	}

	/**
	 * Trigger.
	 */
	public function trigger( $user_data = '', $secret_key = '' ) {
		$this->setup_locale();

		if ( $user_data && $secret_key ) {
			$this->object     = $user_data;
			$this->secret_key = $secret_key;
			$this->recipient  = $this->object->get_pending_email();
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
				'user'       	=> $this->object,
				'secret_key'    => $this->secret_key,
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
				'user'    		=> $this->object,
				'secret_key'    => $this->secret_key,
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			)
		);
	}

}

return new MemberHero_Email_User_Email_Change();