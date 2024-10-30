<?php
/**
 * User Email Confirmation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_User_Email_Confirmation class.
 */
class MemberHero_Email_User_Email_Confirmation extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'user_email_confirmation';
		$this->is_user_email  = true;

		$this->title       = esc_html__( 'Email confirmation', 'memberhero' );
		$this->description = esc_html__( 'User "email confirmation" emails are sent when new users register and need to confirm their account.', 'memberhero' );

		$this->template_html  = 'emails/user-email-confirmation.php';
		$this->template_plain = 'emails/plain/user-email-confirmation.php';
		$this->placeholders   = array(
			'{site_title}'   		=> $this->get_blogname(),
			'{confirmation_code}'   => '',
		);

		// Trigger.
		add_action( 'memberhero_confirm_email_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( '{confirmation_code} is your {site_title} confirmation code', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Confirm your email address', 'memberhero' );
	}

	/**
	 * Trigger.
	 */
	public function trigger( $user_data = '', $confirmation_code = '' ) {
		$this->setup_locale();

		if ( is_object( $user_data ) && $confirmation_code ) {
			$this->object     		 					= $user_data;
			$this->confirmation_code 					= $confirmation_code;
			$this->recipient  		 					= $this->object->user_email;
			$this->placeholders['{confirmation_code}'] 	= substr_replace( $confirmation_code, '-', 3, 0 );
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
				'user'				=> $this->object,
				'confirmation_code' => $this->confirmation_code,
				'email_heading' 	=> $this->get_heading(),
				'blogname'     		=> $this->get_blogname(),
				'sent_to_admin' 	=> false,
				'plain_text'    	=> false,
				'email'        	 	=> $this,
			)
		);
	}

	/**
	 * Get content plain.
	 */
	public function get_content_plain() {
		return memberhero_get_template_html(
			$this->template_plain, array(
				'user'				=> $this->object,
				'confirmation_code' => $this->confirmation_code,
				'email_heading' 	=> $this->get_heading(),
				'blogname'      	=> $this->get_blogname(),
				'sent_to_admin' 	=> false,
				'plain_text'    	=> true,
				'email'         	=> $this,
			)
		);
	}

}

return new MemberHero_Email_User_Email_Confirmation();