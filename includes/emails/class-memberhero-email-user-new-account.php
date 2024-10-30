<?php
/**
 * User New Account.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_User_New_Account class.
 */
class MemberHero_Email_User_New_Account extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'user_new_account';
		$this->is_user_email  = true;

		$this->title       = esc_html__( 'New account', 'memberhero' );
		$this->description = esc_html__( 'User "new account" emails are sent when a user registers.', 'memberhero' );

		$this->template_html  = 'emails/user-new-account.php';
		$this->template_plain = 'emails/plain/user-new-account.php';

		// Trigger.
		add_action( 'memberhero_new_user_notification', array( $this, 'trigger' ), 10, 4 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( 'Your {site_title} account information', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Your account is now ready!', 'memberhero' );
	}

	/**
	 * Trigger.
	 */
	public function trigger( $user = '', $postdata = '', $form = '', $role = '' ) {
		$this->setup_locale();

		if ( is_object( $user ) ) {
			$this->object     = $user;
			$this->postdata   = $postdata;
			$this->form       = $form;
			$this->role       = $role;
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
				'postdata'		=> $this->postdata,
				'form'			=> $this->form,
				'role'			=> $this->role,
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
				'postdata'		=> $this->postdata,
				'form'			=> $this->form,
				'role'			=> $this->role,
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			)
		);
	}

}

return new MemberHero_Email_User_New_Account();