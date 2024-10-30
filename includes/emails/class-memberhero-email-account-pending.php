<?php
/**
 * Account Pending Email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Email_Account_Pending class.
 */
class MemberHero_Email_Account_Pending extends MemberHero_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'account_pending';
		$this->title          = __( 'Account pending notification', 'memberhero' );
		$this->description    = __( 'Account pending notification emails are sent to chosen recipient(s) when a new user is registered and requires admin approval.', 'memberhero' );
		$this->template_html  = 'emails/admin-account-pending.php';
		$this->template_plain = 'emails/plain/admin-account-pending.php';
		$this->placeholders   = array(
			'{user_login}'   => '',
		);

		// Trigger.
		add_action( 'memberhero_pending_user_notification', array( $this, 'trigger' ), 10, 4 );

		// Call parent constructor.
		parent::__construct();

		// Other settings.
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return __( '@{user_login} account pending', 'memberhero' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return __( '@{user_login} account pending', 'memberhero' );
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
			$this->placeholders['{user_login}']   = esc_attr( $user->user_login );
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
			$this->template_html,
			array(
				'user'				 => $this->object,
				'postdata'			 => $this->postdata,
				'form'				 => $this->form,
				'role'				 => $this->role,
				'email_heading'      => $this->get_heading(),
				'blogname'      	 => $this->get_blogname(),
				'sent_to_admin'      => true,
				'plain_text'         => false,
				'email'              => $this,
			)
		);
	}

	/**
	 * Get content plain.
	 */
	public function get_content_plain() {
		return memberhero_get_template_html(
			$this->template_plain,
			array(
				'user'				 => $this->object,
				'postdata'			 => $this->postdata,
				'form'				 => $this->form,
				'role'				 => $this->role,
				'email_heading'      => $this->get_heading(),
				'blogname'      	 => $this->get_blogname(),
				'sent_to_admin'      => true,
				'plain_text'         => true,
				'email'              => $this,
			)
		);
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'memberhero' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'memberhero' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'memberhero' ),
				'default' => 'yes',
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'memberhero' ),
				'type'        => 'text',
				/* translators: %s: WP admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'memberhero' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'placeholder' => '',
				'default'     => '',
				'desc_tip'    => true,
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'memberhero' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email heading', 'memberhero' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'memberhero' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'memberhero' ),
				'default'     => 'html',
				'class'       => 'email_type memberhero-select small',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

}

return new MemberHero_Email_Account_Pending();