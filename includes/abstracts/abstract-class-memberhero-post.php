<?php
/**
 * Custom post type.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Abstract_Post class.
 */
abstract class MemberHero_Abstract_Post {

	/**
	 * Post type.
	 */
	public $post_type = '';

	/**
	 * Meta keys.
	 */
	public $internal_meta_keys = array(

	);

	/**
	 * Is this a request?
	 */
	public $is_request = false;

	/**
	 * Array containing post input.
	 */
	public $postdata = array();

	/**
	 * Array containing wrong fields.
	 */
	public $error_fields = array();

	/**
	 * JS redirection.
	 */
	public $js_redirect = '';

	/**
	 * Constructor.
	 */
	public function __construct( $post_id = '' ) {
		$this->id = absint( $post_id );

		foreach( apply_filters( $this->post_type . '_meta_keys', $this->internal_meta_keys ) as $key ) {
			$this->{$key} = '';
		}

		if ( $post_id > 0 ) {
			$this->read_meta();
		}
	}

	/**
	 * Get shortcode.
	 */
	public function get_shortcode() {
		if ( $this->post_type == 'memberhero_list' ) {
			return '[memberhero_list id=' . $this->id . ']';
		}
		if ( is_memberhero_default_form( $this ) ) {
			return '[memberhero_' . $this->type . ']';
		} else {
			return '[memberhero_form id=' . $this->id . ']';
		}
	}

	/**
	 * Set.
	 */
	public function set( $prop, $value ) {
		$this->{$prop} = $value;
	}

	/**
	 * Generate a unique slug based on given name.
	 */
	public function get_unique_slug( $slug ) {
		return wp_unique_post_slug( $slug, 0, 'publish', $this->post_type, 0 );
	}

	/**
	 * Read metadata.
	 */
	public function read_meta() {

		// Get post meta.
		$meta = get_post_custom( $this->id );

		if ( ! is_array( $meta ) ) {
			return;
		}

		foreach( $meta as $key => $value ) {
			if ( in_array( $key, apply_filters( $this->post_type . '_meta_keys', $this->internal_meta_keys ) ) ) {
				$this->{$key} = is_serialized( $value[0] ) ? unserialize( $value[0] ) : $value[0];
			}
		}

		// Get post data.
		$data = get_post( $this->id );
		if ( ! isset( $data->post_name ) ) {
			return;
		}

		$this->post_title 	= $data->post_title;
		$this->post_name 	= $data->post_name;
	}

	/**
	 * Save.
	 */
	public function save( $props ) {
		if ( ! $props || $this->id <= 0 ) {
			return;
		}

		foreach( $props as $key => $value ) {
			if ( in_array( $key, apply_filters( $this->post_type . '_meta_keys', $this->internal_meta_keys ) ) ) {
				update_post_meta( $this->id, $key, $value );
			}
		}

		if ( method_exists( $this, '_save' ) ) {
			$this->_save( $props );
		}

		do_action( "{$this->post_type}_saved", $this->id, $props );
	}

	/**
	 * Inert.
	 */
	public function insert() {
		global $wpdb;

		$user_id = get_current_user_id();

		$defaults = array(
			'post_author'           => $user_id,
			'post_content'          => '',
			'post_content_filtered' => '',
			'post_title'            => '',
			'post_excerpt'          => '',
			'post_status'           => 'publish',
			'post_type'             => $this->post_type,
			'post_date'				=> current_time( 'mysql' ),
			'post_mime_type'		=> '',
			'comment_status'        => 'closed',
			'ping_status'           => 'closed',
			'post_password'         => '',
			'to_ping'               => '',
			'pinged'                => '',
			'post_parent'           => 0,
			'menu_order'            => 0,
			'guid'                  => '',
		);

		$postarr 			= wp_parse_args( $this, $defaults );
		$postarr 			= sanitize_post( $postarr, 'db' );
		$post_ID 			= 0;
		$update  			= false;
		$guid    			= $postarr['guid'];
		$post_type 			= $postarr['post_type'];
		$post_title   		= $postarr['post_title'];
		$post_content 		= $postarr['post_content'];
		$post_excerpt 		= $postarr['post_excerpt'];
		$post_name 			= $postarr['post_name'];
		$post_date 			= $postarr['post_date'];
		$post_date_gmt 		= get_gmt_from_date( $post_date );
		$post_modified     	= $post_date;
		$post_modified_gmt 	= $post_date_gmt;
		$comment_status 	= $postarr['comment_status'];
		$post_author        = $postarr['post_author'];
		$post_mime_type 	= $postarr['post_mime_type'];
		$ping_status		= $postarr['ping_status'];
		$post_status		= $postarr['post_status'];
		$post_password      = $postarr['post_password'];
		$to_ping			= $postarr['to_ping'];
		$pinged				= $postarr['pinged'];
		$post_parent        = $postarr['post_parent'];
		$menu_order         = $postarr['menu_order'];
		$post_content_filtered = $postarr['post_content_filtered'];

		// Expected_slashed (everything!).
		$data = compact( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'post_mime_type', 'guid' );

		$data  = wp_unslash( $data );
		$where = array( 'ID' => $post_ID );

		$check_name = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts 
			WHERE post_type = %s 
			AND post_name = %s 
			AND 1=1",
			$post_type,
			$post_name
		) );

		if ( $check_name ) {
			return 0;
		}

		if ( false === $wpdb->insert( $wpdb->posts, $data ) ) {
			return 0;
		}

		$post_ID = (int) $wpdb->insert_id;

		// Use the newly generated $post_ID.
		$where = array( 'ID' => $post_ID );

		$current_guid = get_post_field( 'guid', $post_ID );

		// Set GUID.
		if ( '' == $current_guid ) {
			$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), $where );
		}

		clean_post_cache( $post_ID );

		$this->id = $post_ID;
	}

	/**
	 * Delete.
	 */
	public function delete() {
		if ( method_exists( $this, '_delete' ) ) {
			$this->_delete();
		}
	}

	/**
	 * Duplicate.
	 */
	public function duplicate() {
		global $wp_roles;

		$title 		= sprintf( esc_html__( 'Duplicate: %s', 'memberhero' ), $this->post_title );
		$slug 		= str_replace( '-', '_', $this->get_unique_slug( $this->post_name ) );
		$meta_input = array();

		foreach( $this->internal_meta_keys as $key ) {
			$meta_input[ $key ] = $this->{$key};
		}

		if ( $this->post_type == 'memberhero_field' ) {
			$meta_input['key'] = $slug;
		} else if ( $this->post_type == 'memberhero_role' ) {
			$meta_input['label'] = $title;
			$meta_input['name'] = $slug;
		}

		$new = new static();
		$new->set( 'post_title', $title );
		$new->set( 'post_name', $slug );
		$new->set( 'meta_input', (array) $meta_input );
		$new->insert();
		$new->save( $new->meta_input );

		if ( $this->post_type == 'memberhero_role' ) {
			$new->add_new( $slug, $title, $wp_roles->roles[$this->post_name]['capabilities'] );
		}

	}

	/**
	 * Set default ID into an option.
	 */
	public function set_default() {
		$post_type = $this->post_type;
		$post_type = str_replace( 'memberhero_', 'memberhero_default_', $post_type );

		update_option( $post_type, $this->id );
	}

}