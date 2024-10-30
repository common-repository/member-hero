<?php
/**
 * Taxonomies Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Taxonomies class.
 */
class MemberHero_Admin_Taxonomies {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Adding fields.
		add_action( 'category_add_form_fields', 	array( $this, 'add_form_fields' ), 10, 2 );
		add_action( 'category_edit_form_fields',	array( $this, 'edit_form_fields' ), 10, 2 );

		// Saving fields.
		add_action( 'created_category', 	array( $this, 'save_taxonomy_fields' ), 10, 2 );
		add_action( 'edited_category',		array( $this, 'save_taxonomy_fields' ), 10, 2 );
	}

	/**
	 * Add form fields.
	 */
	public function add_form_fields( $taxonomy ) {

		$access	 	= 'everyone';
		$roles  	= array();
		$redirect	= 'home';
		$custom_url = '';
		$options 	= array(
			'home'			=> __( 'Home page', 'memberhero' ),
			'login'			=> __( 'Login page', 'memberhero' ),
			'register'		=> __( 'Registration page', 'memberhero' ),
			'profile'		=> __( 'Profile', 'memberhero' ),
			'account'		=> __( 'Account', 'memberhero' ),
			'custom'		=> __( 'Custom URL', 'memberhero' ),
		);

		echo '<div class="form-field memberhero-field-group">';

		include 'meta-boxes/views/html-content-restrict.php';

		echo '</div>';
	}

	/**
	 * Edit form fields.
	 */
	public function edit_form_fields( $term, $taxonomy ) {

		$access 		= get_term_meta( $term->term_id, '_memberhero_access', true );
		$roles  		= get_term_meta( $term->term_id, '_memberhero_roles', true );
		$redirect  		= get_term_meta( $term->term_id, '_memberhero_redirect', true );
		$custom_url  	= get_term_meta( $term->term_id, '_memberhero_redirect_url', true );

		// Default access
		if ( ! $access ) {
			$access = 'everyone';
		}

		if ( ! $roles ) {
			$roles = array();
		}

		$options = array(
			'home'			=> __( 'Home page', 'memberhero' ),
			'login'			=> __( 'Login page', 'memberhero' ),
			'register'		=> __( 'Registration page', 'memberhero' ),
			'profile'		=> __( 'Profile', 'memberhero' ),
			'account'		=> __( 'Account', 'memberhero' ),
			'custom'		=> __( 'Custom URL', 'memberhero' ),
		);

		?>
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for=""><?php _e( 'Content restriction', 'memberhero' ); ?></label>
			</th>
			<td>
				<div class="form-field memberhero-field-group">
				<?php include 'meta-boxes/views/html-content-restrict.php'; ?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save taxonomy fields.
	 */
	public function save_taxonomy_fields( $term_id, $tag_id ) {

		$access 	= isset( $_POST[ '_memberhero_access' ] ) ? memberhero_clean( $_POST[ '_memberhero_access' ] ) : '';
		$roles 		= isset( $_POST[ '_memberhero_roles' ] ) ? memberhero_clean( $_POST[ '_memberhero_roles' ] ) : array();
		$redirect 	= isset( $_POST[ '_memberhero_redirect' ] ) ? memberhero_clean( $_POST[ '_memberhero_redirect' ] ) : '';
		$custom_url = isset( $_POST[ '_memberhero_redirect_url' ] ) ? memberhero_clean( $_POST[ '_memberhero_redirect_url' ] ) : '';

		update_term_meta( $term_id, '_memberhero_access', $access );
		update_term_meta( $term_id, '_memberhero_roles', $roles );
		update_term_meta( $term_id, '_memberhero_redirect', $redirect );
		update_term_meta( $term_id, '_memberhero_redirect_url', $custom_url );
	}

}

new MemberHero_Admin_Taxonomies();