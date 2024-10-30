<?php
/**
 * Form data metabox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="panel-wrap form_data">

	<ul class="form_data_tabs memberhero-tabs">
		<?php foreach ( self::get_tabs() as $key => $tab ) : ?>
			<li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ', (array) $tab['class'] ) : '' ); ?>">
				<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><?php echo memberhero_svg_icon( esc_attr( $tab['icon'] ) ); ?><span><?php echo esc_html( $tab['label'] ); ?></span></a>
			</li>
		<?php endforeach; ?>
		<?php do_action( 'memberhero_form_write_data_tabs' ); ?>
	</ul>

	<?php
		self::output_tabs();
		do_action( 'memberhero_form_data_panels' );
	?>
	<div class="clear"></div>
</div>