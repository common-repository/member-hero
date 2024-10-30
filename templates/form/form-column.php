<?php
/**
 * Display of a single form column.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	
?>

<div class="memberhero-col">

	<?php
		foreach( ( array ) $the_form->fields_in( $row, $col ) as $key => $data ) :

			$field = memberhero_setup_field( $data );
			$scope = memberhero_get_scope();

			// Show the custom field template.
			if ( memberhero_user_can_access_field( $field, $scope ) ) {
				memberhero_get_template( $scope . '/' . $field['type'] . '.php', array( 'field' => $field ) );

				do_action( 'memberhero_load_' . $field['type'], $field, $scope );
			}

		endforeach;
	?>

</div>