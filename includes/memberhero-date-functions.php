<?php
/**
 * Date Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The difference between two timestamps in minutes/seconds.
 */
function memberhero_get_minute_diff( $time1, $time2 ) {
	$diff = round( ( $time1 - $time2 ) / 60 );

	if ( $diff < 1 ) {
		$diff = $time1 - $time2;
		return sprintf( _n( '%s second', '%s seconds', $diff ), $diff );
	}

	return sprintf( _n( '%s minute', '%s minutes', $diff ), $diff );
}

/**
 * Convert a timestamp to a user-friendly UI time.
 */
function memberhero_timestamp_to_ui( $time = null ) {
	$result = '';

	$current 	= time();
	$total_time = $current - $time;
	$years      = floor( $total_time / 31536000 );
	$days       = floor( $total_time / 86400 );
	$hours      = floor( $total_time / 3600 );
	$minutes    = intval( ( $total_time / 60 ) % 60 );

	if ( $years >= 1 ) {
		$result = '';
	} elseif ( $days >= 1 ) {
		$result = date( 'M j', $time );
	} elseif ( $hours >= 1 ) {
		$result = sprintf( __( '%dh', 'memberhero' ), $hours );
	} elseif ( $minutes >= 1 ) {
		$result = sprintf( __( '%dm', 'memberhero' ), $minutes );
	} else {
		$result = sprintf( __( '%ds', 'memberhero' ), $total_time );
	}

	return apply_filters( 'memberhero_timestamp_to_ui', $result );
}

/**
 * Get a date/time from given timestamp.
 */
function memberhero_get_date_from_timestamp( $time = null ) {

	return apply_filters( 'memberhero_get_date_from_timestamp', date( 'M j, Y, g:i A', $time ) );
}

/**
 * Display the post date and time within the loop.
 */
function memberhero_the_date( $date = null ) {
	if ( ! $date ) {
		echo sprintf( __( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), strtotime( get_the_date() ) ), date_i18n( memberhero_time_format(), strtotime( get_the_time() ) ) );
	} else {
		echo sprintf( __( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), strtotime( $date ) ), date_i18n( memberhero_time_format(), strtotime( $date ) ) );
	}
}

/**
 * Display a more human readable date format for post.
 */
function memberhero_the_date_diff( $time = null ) {
	if ( $time ) {
		$timestamp = strtotime( $time );
	} else {
		$timestamp = get_the_time( 'U' );
	}
	echo sprintf( __( '%s ago', 'memberhero' ), human_time_diff( $timestamp, current_time( 'timestamp' ) ) );
}

/**
 * Get default date format. Used mostly by admin pages.
 */
function memberhero_get_default_date_format() {
	return apply_filters( 'memberhero_get_default_date_format', 'j/n/Y g:ia' );
}

/**
 * Get the date within the loop.
 */
function memberhero_get_the_date() {
	return get_the_date( memberhero_get_default_date_format() );
}

/**
 * Get date format.
 */
function memberhero_get_date_format() {
	return apply_filters( 'memberhero_get_date_format', 'j M Y' );
}

/**
 * Date Format - Allows to change date format for plugin use.
 */
function memberhero_date_format() {
	return apply_filters( 'memberhero_date_format', get_option( 'date_format' ) );
}

/**
 * Time Format - Allows to change time format for plugin use.
 */
function memberhero_time_format() {
	return apply_filters( 'memberhero_time_format', get_option( 'time_format' ) );
}

/**
 * Returns date with GMT difference.
 */
function memberhero_get_date_from_gmt( $date, $format = '' ) {
	$date   	= strtotime( get_date_from_gmt( $date ) );
	$memberhero_date 	= date_i18n( $format, $date );

	return $memberhero_date;
}

/**
 * Returns years as an array.
 */
function memberhero_get_years( $back_to = 1905 ) {
	return apply_filters( 'memberhero_get_years', array_combine( range( date( 'Y' ), $back_to ), range( date('Y'), $back_to ) ) );
}

/**
 * Returns months as an array.
 */
function memberhero_get_months() {
	$months = array(
		1  => __( 'Jan', 'memberhero' ),
		2  => __( 'Feb', 'memberhero' ),
		3  => __( 'Mar', 'memberhero' ),
		4  => __( 'Apr', 'memberhero' ),
		5  => __( 'May', 'memberhero' ),
		6  => __( 'Jun', 'memberhero' ),
		7  => __( 'Jul', 'memberhero' ),
		8  => __( 'Aug', 'memberhero' ),
		9  => __( 'Sept', 'memberhero' ),
		10 => __( 'Oct', 'memberhero' ),
		11 => __( 'Nov', 'memberhero' ),
		12 => __( 'Dec', 'memberhero' ),
	);

	return apply_filters( 'memberhero_get_months', $months );
}

/**
 * Returns days as an array.
 */
function memberhero_get_days() {
	$days = array();
	for( $i = 1; $i <= 31; $i++ ) {
		$days[ $i ] = $i;
	}
	return apply_filters( 'memberhero_get_days', $days );
}

/**
 * Shows a select dropdown for years.
 */
function memberhero_dropdown_years( $field = null ) {
	memberhero_dropdown_datepick( 'year', __( 'Year', 'memberhero' ), 1994, $field );
}

/**
 * Shows a select dropdown for months.
 */
function memberhero_dropdown_months( $field = null ) {
	memberhero_dropdown_datepick( 'month', __( 'Month', 'memberhero' ), date( 'n' ), $field );
}

/**
 * Shows a select dropdown for days.
 */
function memberhero_dropdown_days( $field = null ) {
	memberhero_dropdown_datepick( 'day', __( 'Day', 'memberhero' ), date( 'j' ), $field );
}

/**
 * Shows a select dropdown for day, month or year.
 */
function memberhero_dropdown_datepick( $id, $name, $default = null, $field = null ) {
	$selected = $default;
	if ( ! empty( $field[ 'value' ] ) && is_array( $field[ 'value' ] ) && array_key_exists( $id, $field[ 'value'] ) ) {
		$selected = $field[ 'value' ][ $id ];
	}
	?>
	<select class="memberhero-select short" data-placeholder="<?php echo esc_attr( $name ); ?>" name="<?php memberhero_form_prefix(); ?><?php echo esc_attr( $field[ 'key' ] ); ?>[<?php echo $id; ?>]">
		<option value=""><?php echo esc_html( $name ); ?></option>
		<?php foreach( call_user_func( 'memberhero_get_' . $id . 's' ) as $key => $value ) { ?>
		<option value="<?php echo $key; ?>" <?php selected( $selected, $key ); ?> ><?php echo $value; ?></option>
		<?php } ?>
	</select>
	<?php
}

/**
 * Convert array-based date into readable date string.
 */
function memberhero_array_to_date( $array ) {
	$date = $array[ 'day' ] . '-' . $array[ 'month' ] . '-' . $array[ 'year' ];

	return date( memberhero_get_date_format(), strtotime( $date ) );
}