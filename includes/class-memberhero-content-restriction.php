<?php
/**
 * Content Restriction.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Content_Restriction class.
 */
class MemberHero_Content_Restriction {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'single_post' ), 10 );
		add_action( 'template_redirect', array( __CLASS__, 'single_post_in_cat' ), 11 );
		add_action( 'template_redirect', array( __CLASS__, 'single_category' ), 12 );
	}

	/**
	 * Detect a post/page and redirect if user has no access.
	 */
	public static function single_post() {
		global $post;

		// We know at this time we deal with post or page.
		if ( ! is_single() && ! is_page() ) {
			return;
		}

		$thepostid = isset( $post->ID ) ? absint( $post->ID ) : 0;

		if ( ! $thepostid ) {
			return;
		}

		$access 		= get_post_meta( $thepostid, '_memberhero_access', true );
		$roles  		= get_post_meta( $thepostid, '_memberhero_roles', true );
		$redirect  		= get_post_meta( $thepostid, '_memberhero_redirect', true );
		$custom_url  	= get_post_meta( $thepostid, '_memberhero_redirect_url', true );

		if ( $access === 'everyone' ) {
			return;
		} else if ( $access === 'guests' ) {

			if ( is_user_logged_in() ) {
				self::redirect_to( $redirect, $custom_url );
			}

		} else if ( $access === 'members' ) {
			// Guests automatically do not have access.
			if ( ! is_user_logged_in() ) {
				self::redirect_to( $redirect, $custom_url );
			} else {

				// Let's check if only specific roles should access.
				if ( ! empty( $roles ) && is_array( $roles ) ) {
					$user = memberhero_get_user( get_current_user_id() );
					$role = $user->get_role();
					if ( ! in_array( $role, $roles ) ) {
						self::redirect_to( $redirect, $custom_url );
					}
				}
			}
		}
	}

	/**
	 * Check a post that's locked behind a category.
	 */
	public static function single_post_in_cat() {
		global $post;

		// Only single posts.
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$thepostid = isset( $post->ID ) ? absint( $post->ID ) : 0;

		if ( ! $thepostid ) {
			return;
		}

		$cats = wp_get_post_categories( $thepostid );
		if ( empty( $cats ) ) {
			return;
		}

		// loop through categories and redirect when we found a restricted category.
		foreach( $cats as $thecatid ) {
			$access 		= get_term_meta( $thecatid, '_memberhero_access', true );
			$roles  		= get_term_meta( $thecatid, '_memberhero_roles', true );
			$redirect  		= get_term_meta( $thecatid, '_memberhero_redirect', true );
			$custom_url  	= get_term_meta( $thecatid, '_memberhero_redirect_url', true );

			if ( $access === 'everyone' ) {
				continue;
			} else if ( $access === 'guests' ) {

				if ( is_user_logged_in() ) {
					self::redirect_to( $redirect, $custom_url );
				}

			} else if ( $access === 'members' ) {
				// Guests automatically do not have access.
				if ( ! is_user_logged_in() ) {
					self::redirect_to( $redirect, $custom_url );
				} else {

					// Let's check if only specific roles should access.
					if ( ! empty( $roles ) && is_array( $roles ) ) {
						$user = memberhero_get_user( get_current_user_id() );
						$role = $user->get_role();
						if ( ! in_array( $role, $roles ) ) {
							self::redirect_to( $redirect, $custom_url );
						}
					}
				}
			}
		}
	}

	/**
	 * Check if we have landed on a protected category.
	 */
	public static function single_category() {
		global $wp_query;

		if ( ! is_category() ) {
			return;
		}

		$thecatid = $wp_query->get_queried_object()->term_id;

		$access 		= get_term_meta( $thecatid, '_memberhero_access', true );
		$roles  		= get_term_meta( $thecatid, '_memberhero_roles', true );
		$redirect  		= get_term_meta( $thecatid, '_memberhero_redirect', true );
		$custom_url  	= get_term_meta( $thecatid, '_memberhero_redirect_url', true );

		if ( $access === 'everyone' ) {
			return;
		} else if ( $access === 'guests' ) {

			if ( is_user_logged_in() ) {
				self::redirect_to( $redirect, $custom_url );
			}

		} else if ( $access === 'members' ) {
			// Guests automatically do not have access.
			if ( ! is_user_logged_in() ) {
				self::redirect_to( $redirect, $custom_url );
			} else {

				// Let's check if only specific roles should access.
				if ( ! empty( $roles ) && is_array( $roles ) ) {
					$user = memberhero_get_user( get_current_user_id() );
					$role = $user->get_role();
					if ( ! in_array( $role, $roles ) ) {
						self::redirect_to( $redirect, $custom_url );
					}
				}
			}
		}
	}

	/**
	 * Handles the redirection part.
	 */
	public static function redirect_to( $redirect, $custom_url ) {
		$url = home_url();

		switch( $redirect ) {
			case 'home' :
				$url = home_url();
			break;
			case 'login' :
				$url = memberhero_login_url();
			break;
			case 'register' :
				$url = memberhero_register_url();
			break;
			case 'profile' :
				$url = memberhero_get_page_permalink( 'profile' );
			break;
			case 'account' :
				$url = memberhero_get_page_permalink( 'account' );
			break;
			case 'custom' :
				$url = esc_url( $custom_url );
			break;
			default :
				$url = apply_filters( 'memberhero_restricted_content_redirect_uri', $url );
			break;
		}

		if ( $url ) {
			exit( wp_redirect( $url ) );
		}
	}

}

MemberHero_Content_Restriction::init();