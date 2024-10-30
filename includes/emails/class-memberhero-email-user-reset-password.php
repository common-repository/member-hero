<?php
/**
 * User Reset Password.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_User_Reset_Password class.
 */
class MemberHero_Email_User_Reset_Password extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'user_reset_password';
		$this->is_user_email  = true;

		$this->title       = esc_html__( 'Reset password', 'memberhero' );
		$this->description = esc_html__( 'User "reset password" emails are sent when users reset their passwords.', 'memberhero' );

		$this->template_html  = 'emails/user-reset-password.php';
		$this->template_plain = 'emails/plain/user-reset-password.php';

		// Trigger.
		add_action( 'memberhero_reset_password_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( 'Password reset for {site_title}', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Reset your password', 'memberhero' );
	}

	/**
	 * Trigger.
	 */
	public function trigger( $user_login = '', $reset_key = '' ) {
		$this->setup_locale();

		if ( $user_login && $reset_key ) {
			$this->object     = memberhero_get_user( $user_login );
			$this->reset_key  = $reset_key;
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
				'user'       	=> $this->object,
				'reset_key'     => $this->reset_key,
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
				'reset_key'     => $this->reset_key,
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			)
		);
	}

}

return new MemberHero_Email_User_Reset_Password();