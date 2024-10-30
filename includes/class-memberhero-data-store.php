<?php
/**
 * Custom searches in the WP database.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Data_Store class.
 */
class MemberHero_Data_Store {

	/**
	 * Contains an instance of the data store class that we are working with.
	 */
	private $instance = null;

	/**
	 * Contains an array of default supported data stores.
	 */
	private $stores = array(
		'form'          => 'MemberHero_Form_Data_Store',
		'field'         => 'MemberHero_Field_Data_Store',
		'role'			=> 'MemberHero_Role_Data_Store',
		'list'   		=> 'MemberHero_List_Data_Store',
		'posts'			=> 'MemberHero_Posts_Data_Store',
		'pages'			=> 'MemberHero_Pages_Data_Store',
		'categories'	=> 'MemberHero_Categories_Data_Store',
	);

	/**
	 * Contains the name of the current data store's class name.
	 */
	private $current_class_name = '';

	/**
	 * The object type this store works with.
	 */
	private $object_type = '';

	/**
	 * Tells which object to use.
	 */
	public function __construct( $object_type ) {
		$this->object_type = $object_type;
		$this->stores      = apply_filters( 'memberhero_data_stores', $this->stores );

		// If this object type can't be found, check to see if we can load one
		if ( ! array_key_exists( $object_type, $this->stores ) ) {
			$pieces      = explode( '-', $object_type );
			$object_type = $pieces[0];
		}

		if ( array_key_exists( $object_type, $this->stores ) ) {
			$store = apply_filters( 'memberhero_' . $object_type . '_data_store', $this->stores[ $object_type ] );
			if ( is_object( $store ) ) {
				if ( ! $store instanceof MemberHero_Object_Data_Store_Interface ) {
					throw new Exception( __( 'Invalid data store.', 'memberhero' ) );
				}
				$this->current_class_name = get_class( $store );
				$this->instance           = $store;
			} else {
				if ( ! class_exists( $store ) ) {
					throw new Exception( __( 'Invalid data store.', 'memberhero' ) );
				}
				$this->current_class_name = $store;
				$this->instance           = new $store();
			}
		} else {
			throw new Exception( __( 'Invalid data store.', 'memberhero' ) );
		}
	}

	/**
	 * Only store the object type to avoid serializing the data store instance.
	 */
	public function __sleep() {
		return array( 'object_type' );
	}

	/**
	 * Re-run the constructor with the object type.
	 */
	public function __wakeup() {
		$this->__construct( $this->object_type );
	}

	/**
	 * Data stores can define additional functions
	 */
	public function __call( $method, $parameters ) {
		if ( is_callable( array( $this->instance, $method ) ) ) {
			$object = array_shift( $parameters );
			return call_user_func_array( array( $this->instance, $method ), array_merge( array( &$object ), $parameters ) );
		}
	}

	/**
	 * Loads a data store.
	 */
	public static function load( $object_type ) {
		return new MemberHero_Data_Store( $object_type );
	}

	/**
	 * Returns the class name of the current data store.
	 */
	public function get_current_class_name() {
		return $this->current_class_name;
	}

}