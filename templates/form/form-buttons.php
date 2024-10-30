<?php
/**
 * Form Buttons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( ! empty( $first_button ) ) : ?>

<div class="memberhero-buttons <?php echo implode( ' ', memberhero_get_buttons_classes() ); ?> memberhero-buttons-<?php echo ! empty( $second_button ) ? 2 : 1; ?>">

	<input type="submit" value="<?php echo esc_attr( $first_button ); ?>" class="memberhero-button main">

	<?php if ( is_memberhero_editing_profile() ) : ?>

		<a href="<?php echo esc_url( memberhero_get_profile_endpoint_url( 'view' ) ); ?>" class="memberhero-button nav memberhero-button-cancel">
			<?php esc_html_e( 'Cancel', 'memberhero' ); ?>
		</a>

	<?php endif; ?>

	<?php if ( ! empty( $second_button ) ) : ?>

		<a href="<?php echo ! empty( $second_button_url ) ? esc_url( $second_button_url ) : esc_url( $the_form->get_second_button_url() ); ?>" class="memberhero-button nav">
			<?php echo esc_html( $second_button ); ?>
		</a>

	<?php endif; ?>

</div>

<?php endif; ?>