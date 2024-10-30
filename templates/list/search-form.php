<?php
/**
 * Member list search form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-search-form-wrap">

	<form class="memberhero-search-form" method="get" action="" autocomplete="off">
		<input type="text" name="ws" id="ws" class="memberhero-search-input" value="" placeholder="<?php echo esc_attr_e( 'Search members...', 'memberhero' ); ?>" />
		<button type="submit"><?php echo memberhero_svg_icon( 'search' ); ?></button>
	</form>

</div>