<?php
/**
 * Account confirmation email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo esc_html( $email_heading ) . "\n";

echo "==========================================\n\n";

echo "==========================================\n\n";

echo esc_html( apply_filters( 'memberhero_email_footer_text', get_option( 'memberhero_email_footer_text_plain' ) ) );