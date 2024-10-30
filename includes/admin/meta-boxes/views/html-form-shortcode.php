<?php
/**
 * Form shortcode metabox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p>
	<?php echo esc_html( $the_form->get_shortcode() ); ?>
</p>