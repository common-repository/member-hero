<?php
/**
 * Show user's social links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile-meta">

	<?php foreach( $links as $link ) : ?>

		<div class="memberhero-profile-metadata memberhero-metadata-social"><?php echo $link; ?></div>

	<?php endforeach; ?>

</div>