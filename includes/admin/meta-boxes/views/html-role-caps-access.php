<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="access_role_caps" class="panel memberhero_options_panel hidden">

	<div class="options_group">
		<?php
		$opts = array( 'memberhero_log_in' );
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

	<?php do_action( 'memberhero_role_caps_access_panel' ); ?>

</div>