<?php
/**
 * URL field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-data">

		<a href="<?php echo esc_url( $field[ 'value' ] ); ?>" rel="nofollow" target="_blank">
			<?php
				echo memberhero_esc_url( $field[ 'value' ] );
			?>
		</a>

		<?php do_action( 'memberhero_url_field_output', $field ); ?>

	</div>

</fieldset>