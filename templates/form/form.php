<?php
/**
 * Form Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $the_form->has_fields() && $the_form->id ) {
	return;
}

$type     = esc_attr( $the_form->type );
$endpoint = memberhero()->query->get_current_endpoint();

?>

<?php do_action( "memberhero_before_{$type}_form" ); ?>

<form 
		class="memberhero-form <?php echo implode( ' ', memberhero_get_form_classes() ); ?>" 
		action="" 
		method="post" 
		accept-charset="utf-8" 
		enctype="multipart/form-data" 
		data-ajax="<?php echo $the_form->use_ajax; ?>" 
		<?php memberhero_print_form_inline_styles(); ?>
	>

	<?php memberhero_print_notices(); ?>

	<?php do_action( "memberhero_before_{$type}_form_content" ); ?>

	<?php memberhero_form_note( $atts ); ?>

	<?php memberhero_form_loop( $atts ); ?>

	<?php do_action( "memberhero_{$endpoint}_form" ); ?>

	<?php do_action( "memberhero_after_{$type}_form_content" ); ?>

	<?php memberhero_form_buttons( $atts ); ?>

</form>

<?php do_action( "memberhero_after_{$type}_form" ); ?>