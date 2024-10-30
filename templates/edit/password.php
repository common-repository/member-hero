<?php
/**
 * Password field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset class="memberhero-field <?php echo implode( ' ', $field['field_class'] ); ?>">

	<?php memberhero_get_template( 'field/label.php', array( 'field' => $field ) ); ?>

	<div class="memberhero-input <?php echo implode( ' ', $field['control_class'] ); ?>">

		<?php memberhero_get_template( 'field/icon.php', array( 'field' => $field ) ); ?>

		<input 
				type="password" 
				name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				id="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field['key'] ); ?>" 
				value="" 
				class="<?php echo implode( ' ', $field['input_class'] ); ?>" 
				<?php echo implode( ' ', $field['attributes'] ); ?> 
		/> 

		<?php if ( empty( $field['hide_toggle'] ) ) : ?>
		<div class="memberhero-pw-visible tips is-hidden" data-tip="<?php esc_html_e( 'Show password', 'memberhero' ); ?>" data-show="<?php esc_html_e( 'Show password', 'memberhero' ); ?>" data-hide="<?php esc_html_e( 'Hide password', 'memberhero' ); ?>">
			<?php echo memberhero_svg_icon( 'eye' ); ?>
		</div>
		<?php endif; ?>

	</div>

	<?php if ( ! empty( $field['helper'] ) ) : ?>
		<div class="memberhero-helper"><?php echo wp_kses_post( $field['helper'] ); ?></div>
	<?php endif; ?>

</fieldset>