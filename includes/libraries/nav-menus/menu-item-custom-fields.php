<?php
/**
 * Menu Item Custom Fields
 *
 * @package Menu_Item_Custom_Fields
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 * 
 * https://github.com/kucrut/wp-menu-item-custom-fields
 */

if ( ! class_exists( 'Menu_Item_Custom_Fields' ) ) :

	/*
	 * Menu Item Custom Fields Loader
	 */
	class Menu_Item_Custom_Fields {

		/**
		 * Add filter
		 */
		public static function load() {
			add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_walker' ), 99 );
			if ( ! is_admin() ) {
				add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'conditional_nav_menu' ), null, 3 );
			}
		}

		/**
		 * Replace default menu editor walker with ours
		 *
		 * We don't actually replace the default walker. We're still using it and
		 * only injecting some HTMLs.
		 */
		public static function _filter_walker( $walker ) {
			$walker = 'Menu_Item_Custom_Fields_Walker';
			if ( ! class_exists( $walker ) ) {
				require_once dirname( __FILE__ ) . '/walker-nav-menu-edit.php';
			}

			return $walker;
		}

		/**
		 * This is for frontend output. To exclude specific menu items based on menu item meta.
		 */
		public static function conditional_nav_menu( $items, $menu, $args ) {
			// Iterate over the items to search and exclude.
			foreach ( $items as $key => $item ) {
				$who_view = get_post_meta( $item->ID, 'menu-item-who_can_view', true );
				// Just skip. Show to all users.
				if ( $who_view === 'all' || ! $who_view ) {
					continue;
				}
				// Exclude item that is for logged in user only while guest is viewing.
				if ( 'user' === $who_view && ! is_user_logged_in() ) {
					unset( $items[ $key ] );
				}
				// Exclude item that is for guests only when a user is logged in.
				if ( 'guest' === $who_view && is_user_logged_in() ) {
					unset( $items[ $key ] );
				}
			}
			return $items;
		}

	}
	add_action( 'wp_loaded', array( 'Menu_Item_Custom_Fields', 'load' ), 9 );

endif;

require_once dirname( __FILE__ ) . '/menu-item-custom-fields-setup.php';