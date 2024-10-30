<?php
/**
 * Menu item custom fields setup.
 *
 * @package Menu_Item_Custom_Fields_Setup
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 * 
 * https://github.com/kucrut/wp-menu-item-custom-fields
 */

/**
 * Menu item metadata class.
 */
class Menu_Item_Custom_Fields_Setup {

	/**
	 * Holds our custom fields
	 */
	protected static $fields = array();

	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );

		self::$fields = array(
			'who_can_view' 	=> __( 'Who can view this?', 'memberhero' ),
		);
	}

	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( self::$fields as $_key => $label ) {
			$key = sprintf( 'menu-item-%s', $_key );

			// Sanitize
			if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				// Do some checks here...
				$value = $_POST[ $key ][ $menu_item_db_id ];
			} else {
				$value = null;
			}

			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {
		foreach ( self::$fields as $_key => $label ) :
			$key   = sprintf( 'menu-item-%s', $_key );
			$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
			$name  = sprintf( '%s[%s]', $key, $item->ID );
			$value = get_post_meta( $item->ID, $key, true );
			$class = sprintf( 'field-%s', $_key );

			// Show input based on custom field ID.
			switch( $_key ) :
				// Who can view.
				case 'who_can_view' :
					// Default value.
					if ( $value == '' ) {
						$value = 'all';
					}
				?>
					<p class="description description-wide <?php echo esc_attr( $class ) ?>">
						<?php echo esc_html( $label ); ?>
						<br />
						<label>
							<input type="radio" class="widefat <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="all" <?php checked( esc_attr( $value ), 'all' ); ?> /><?php echo esc_html_e( 'Everyone', 'memberhero' ); ?>
						</label>
						<label>
							<input type="radio" class="widefat <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="guest" <?php checked( esc_attr( $value ), 'guest' ); ?> /><?php echo esc_html_e( 'Non-logged in users', 'memberhero' ); ?>
						</label>
						<label>
							<input type="radio" class="widefat <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="user" <?php checked( esc_attr( $value ), 'user' ); ?> /><?php echo esc_html_e( 'Logged-in users', 'memberhero' ); ?>
						</label>
					</p>
				<?php
				break;
				// Text Input.
				default :
				?>
					<p class="description description-wide <?php echo esc_attr( $class ) ?>">
						<?php printf(
							'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
							esc_attr( $id ),
							esc_html( $label ),
							esc_attr( $name ),
							esc_attr( $value )
						) ?>
					</p>
				<?php
				break;
			endswitch;

		endforeach;
	}

	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns = array_merge( $columns, self::$fields );

		return $columns;
	}

}

Menu_Item_Custom_Fields_Setup::init();