<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="roles_role_caps" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		$opts = array( 'publish_memberhero_roles', 'edit_memberhero_roles', 'delete_memberhero_roles' );
		foreach( $opts as $opt ) {
			memberhero_wp_switch(
				array(
					'id'        	=> $opt,
					'label'			=> memberhero_get_cap_title( $opt ),
					'value'			=> $the_role->get_cap( $opt ),
					'cbvalue'		=> 1
				)
			);
		}
		?>
	</div>

	<?php do_action( 'memberhero_role_caps_roles_panel' ); ?>

</div>