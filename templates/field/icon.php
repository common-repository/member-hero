<?php
/**
 * Field Icon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( in_array( 'has-icon', $field['control_class'] ) ) : ?>

	<span class="memberhero-icon">
		<?php echo memberhero_svg_icon( esc_attr( $field[ 'icon' ] ) ); ?>
	</span>

<?php endif; ?>