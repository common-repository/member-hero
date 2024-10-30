<?php
/**
 * Upload Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Uploader class.
 */
class MemberHero_Uploader {

	/**
	 * Globals.
	 */
	public $user_id = 0;
	public $key = null;
	public $type = null;
	public $data = array();
	public $mimes = array();

	/**
	 * Contains the WP upload handler.
	 */
	public $wp_upload = null;

	/**
	 * The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		if ( ! function_exists( 'wp_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
	}

	/**
	 * Base64 Image Upload.
	 */
	public function base64_upload( $type, $args ) {

		$this->base64 	= ! empty( $args['memberhero_image'] ) ? $args['memberhero_image'] : '';
		$this->user_id 	= absint( $args['user_id'] );
		$this->type 	= $type;

		if ( ! $this->base64 || ! $this->user_id || ! is_user_logged_in() ) {
			return array( 'error' => __( 'You are not allowed to do this.', 'memberhero' ) );
		}

		if ( ! in_array( $this->type, array( 'avatar', 'cover' ) ) ) {
			return array( 'error' => __( 'You are not allowed to do this.', 'memberhero' ) );
		}

		/**
		 * Create an image from base encoded string.
		 */
		$this->image = str_replace( 'data:image/png;base64,', '', $this->base64 );
		$this->image = base64_decode( $this->image );
		
		// Create image and check its colour.
		if ( memberhero_invalid_image_string_color( $this->image ) ) {
			return array( 'error' => __( 'Invalid image. Please try again.', 'memberhero' ) );
		}

		// Check file extension on server.
		$f = finfo_open();
		$mime_type = finfo_buffer( $f, $this->image, FILEINFO_MIME_TYPE );

		if ( ! in_array( $mime_type, memberhero_get_image_mimes() ) ) {
			return array( 'error' => __( 'Unsupported image file extension.', 'memberhero' ) );
		}

		$upload_dir 		= wp_upload_dir();
		$upload_path 		= $upload_dir['basedir'] . '/memberhero_uploads/';
		$hashed_filename 	= memberhero_unique_filename() . '.jpg';
		$upload_file 		= @file_put_contents( $upload_path . $hashed_filename, $this->image );

		add_filter( 'wp_editor_set_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
		add_filter( 'jpeg_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );

		/**
		 * Save the source image using WP image editor.
		 */
		$editor = wp_get_image_editor( $upload_path . $hashed_filename );
		if ( is_wp_error( $editor ) ) {
			return array( 'error' => $editor->get_error_message() );
		}
		$editor->save( $upload_path . $hashed_filename );

		/**
		 * Generate file array for WP.
		 */
		$file             	= array();
		$file['error']    	= '';
		$file['tmp_name'] 	= $upload_path . $hashed_filename;
		$file['name']     	= $hashed_filename;
		$file['type']     	= 'image/jpg';
		$file['size']     	= filesize( $upload_path . $hashed_filename );

		/**
		 * WP handle upload on the server.
		 */
		add_filter( 'upload_dir', 'memberhero_upload_dir' );
		$file_return = wp_handle_sideload(
			$file,
			array(
				'test_form' 				=> false,
				'unique_filename_callback' 	=> 'memberhero_upload_filename_callback'
			)
		);
		remove_filter( 'upload_dir', 'memberhero_upload_dir' );

		/**
		 * Fired before photo is updated.
		 */
		do_action( "memberhero_pre_{$this->type}_update", $this->user_id, $file_return );

		/**
		 * Update the usermeta.
		 */
		update_user_meta( $this->user_id, "_memberhero_profile_{$this->type}", memberhero_clean( wp_unslash( wp_basename( $file_return['file'] ) ) ) );

		/**
		 * Fired when photo is updated.
		 */
		do_action( "memberhero_{$this->type}_updated", $this->user_id, $file_return );

		/**
		 * Create photo thumbnails.
		 */
		add_filter( 'image_make_intermediate_size', "memberhero_rename_{$this->type}_thumbnails" );
		$sizes = call_user_func( "memberhero_get_{$this->type}_sizes" );
		foreach( $sizes as $width ) {
			$height = $width;
			if ( $this->type == 'cover' ) {
				$height = floor( $width / 2.70 );
			}
			image_make_intermediate_size( $file_return['file'], $width, $height, true );
		}
		remove_filter( 'image_make_intermediate_size', "memberhero_rename_{$this->type}_thumbnails" );

		remove_filter( 'wp_editor_set_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
		remove_filter( 'jpeg_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );

		return apply_filters( "memberhero_upload_{$this->type}_return", $file_return, $this );
	}

	/**
	 * Start a user upload.
	 */
	public function user_upload( $user_id, $key, $data ) {

		// Set up properties.
		$this->user_id 	= absint( $user_id );
		$this->key 		= memberhero_clean( $key );
		$this->data 	= memberhero_clean( $data );
		$this->type 	= array_key_exists( 'type', $data['field'] ) ? $data['field']['type'] : '';
		$this->mimes 	= array_key_exists( 'mimes', $data['field'] ) ? $data['field']['mimes'] : '';

		// Verify credentials.
		if ( ! $this->verify() ) {
			return;
		}

		// WP handle uploading.
		$this->wp_upload();

		// Handle the upload result.
		if ( ! empty( $this->wp_upload['error'] ) ) {
			return;
		} else {
			$this->finish();
		}

	}

	/**
	 * Import avatar from remote URL.
	 */
	public function import_from_url( $user_id, $avatar, $type = 'avatar' ) {

		$this->user_id 	= absint( $user_id );
		$this->type    	= $type;
		$url        	= esc_url( $avatar );

		// Verify credentials.
		if ( ! $this->verify() ) {
			return;
		}

		add_filter( 'wp_check_filetype_and_ext', 'memberhero_check_filetype_and_ext', 100, 4 );
		add_filter( 'upload_dir', 'memberhero_upload_dir' );
		add_filter( 'wp_editor_set_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
		add_filter( 'jpeg_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
		add_filter( 'image_make_intermediate_size', "memberhero_rename_{$this->type}_thumbnails" );

		$timeout_seconds = 10;

		// Download file to temp dir
		$temp_file = download_url( $url, $timeout_seconds );

		if ( ! is_wp_error( $temp_file ) ) {

			// Array based on $_FILE as seen in PHP file uploads
			$file = array(
				'name'     => memberhero_unique_filename() . '.jpg',
				'type'     => 'image/jpg',
				'tmp_name' => $temp_file,
				'error'    => 0,
				'size'     => filesize( $temp_file ),
			);

			$overrides = array(
				'test_form' 				=> false,
				'test_type' 				=> true,
				'unique_filename_callback' 	=> 'memberhero_upload_filename_callback',
				'mimes'						=> memberhero_get_image_mimes(),
			);

			// Move the temporary file into the uploads directory
			$file_return = wp_handle_sideload( $file, $overrides );

			// Error handling.
			if ( ! empty( $file_return['error'] ) ) {
				return $file_return;
			} else {
				/**
				 * Fired before photo is updated.
				 */
				do_action( "memberhero_pre_{$this->type}_update", $this->user_id, $file_return );

				/**
				 * Update the usermeta.
				 */
				update_user_meta( $this->user_id, "_memberhero_profile_{$this->type}", memberhero_clean( wp_unslash( wp_basename( $file_return['file'] ) ) ) );

				/**
				 * Fired when photo is updated.
				 */
				do_action( "memberhero_{$this->type}_updated", $this->user_id, $file_return );

				/**
				 * Create photo thumbnails.
				 */
				$sizes = call_user_func( "memberhero_get_{$this->type}_sizes" );
				foreach( $sizes as $width ) {
					$height = $width;
					if ( $this->type == 'cover' ) {
						$height = floor( $width / 2.70 );
					}
					image_make_intermediate_size( $file_return['file'], $width, $height, true );
				}

				remove_filter( 'wp_check_filetype_and_ext', 'memberhero_check_filetype_and_ext', 100, 4 );
				remove_filter( 'upload_dir', 'memberhero_upload_dir' );
				remove_filter( 'image_make_intermediate_size', "memberhero_rename_{$this->type}_thumbnails" );
				remove_filter( 'wp_editor_set_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
				remove_filter( 'jpeg_quality', "memberhero_editor_set_{$this->type}_quality", 999, 2 );
			}
		} else {
			return array( 'error' => $temp_file->get_error_message() );
		}
	}

	/**
	 * Verify the user can upload.
	 */
	public function verify() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! memberhero_user_can_edit_profile( $this->user_id ) ) {
			return false;
		}

		if ( ! in_array( $this->type, memberhero_supported_upload_types() ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Let WP handle the upload.
	 */
	public function wp_upload() {
		add_filter( 'wp_check_filetype_and_ext', 'memberhero_check_filetype_and_ext', 100, 4 );
		add_filter( 'upload_dir', 'memberhero_upload_dir' );

		$overrides = array(
			'test_form' 				=> false,
			'test_type' 				=> true,
			'unique_filename_callback' 	=> 'memberhero_upload_filename_callback',
		);

		// For file upload, respect mimes.
		if ( is_array( $this->mimes ) && $this->type == 'file' ) {
			$overrides['mimes'] = $this->mimes;
		}

		// Force image mimes for image uploads.
		if ( $this->type == 'image' ) {
			$overrides['mimes'] = memberhero_get_image_mimes();
		}

		$this->wp_upload = wp_handle_upload( $_FILES[ $this->key ], $overrides );

		remove_filter( 'upload_dir', 'memberhero_upload_dir' );
		remove_filter( 'wp_check_filetype_and_ext', 'memberhero_check_filetype_and_ext', 100, 4 );
	}

	/**
	 * After upload is complete.
	 */
	public function finish() {
		// Add the upload data to user.
		$this->add_upload();

		do_action( "memberhero_uploaded_{$this->type}", $this->user_id, $this->wp_upload, $this->key, $this->data );

		return $this->wp_upload;
	}

	/**
	 * Add the upload to user meta.
	 */
	public function add_upload() {
		$file = wp_basename( $this->wp_upload[ 'file' ] );

		// Check current file and remove.
		$current = get_user_meta( $this->user_id, $this->key, true );
		if ( $current ) {
			$file_path = memberhero_plugin_uploads_path( $this->type ) . '/' . $current;
			wp_delete_file( $file_path );
		}

		// Update user meta with file data.
		$files = get_user_meta( $this->user_id, '_memberhero_files', true );

		if ( ! empty( $files ) && is_array( $files ) ) {
			array_push( $files, $file );
		} else {
			$files = array( $file );
		}

		update_user_meta( $this->user_id, '_memberhero_files', $files );
		update_user_meta( $this->user_id, $this->key, $file );
	}

}