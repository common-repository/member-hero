<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="redirect_form_data" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		memberhero_wp_select(
			array(
				'id'          		=> 'redirect',
				'value'       		=> $the_form->redirect,
				'label'       		=> __( 'Redirection', 'memberhero' ),
				'options'     		=> memberhero_get_form_redirect_options(),
				'default'			=> 'none',
				'description' 		=> __( 'Where user will be redirected after a successful submission of this form.', 'memberhero' ),
				'desc_tip'			=> true,
			)
		);

		memberhero_wp_text_input(
			array(
				'id'          		=> 'redirect_uri',
				'value'       		=> $the_form->redirect_uri,
				'label'       		=> __( 'Custom URL Redirect', 'memberhero' ),
				'placeholder' 		=> 'http://',
				'description' 		=> __( 'Users who fill this form will be sent to this custom URL.', 'memberhero' ),
				'desc_tip'			=> true,
				'wrapper_class' 	=> 'show_if_redirect_eq_custom hidden',
			)
		);
		?>
	</div>

	<?php do_action( 'memberhero_form_data_redirect_panel' ); ?>

</div>