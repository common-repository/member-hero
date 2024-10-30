<?php
/**
 * Upload Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate a unique file name for uploads.
 */
function memberhero_upload_filename_callback( $dir, $name, $ext ) {
	return memberhero_unique_filename() . $ext;
}

/**
 * Returns the plugin uploads path.
 */
function memberhero_plugin_uploads_path( $name = '' ) {
	$upload_dir = wp_upload_dir();

	$path = $upload_dir['basedir'] . '/memberhero_uploads';

	if ( $name ) {
		$path .= memberhero_get_upload_folder( $name );
	}

	return apply_filters( 'memberhero_plugin_uploads_path', $path );
}

/**
 * Returns an uploaded file URL from the uploads directory.
 */
function memberhero_generate_upload_url( $file, $type = null ) {
	$upload_dir = wp_get_upload_dir();

	$memberhero_upload = $upload_dir['baseurl'] . '/memberhero_uploads' . memberhero_get_upload_folder( $type );

	$return_url = $memberhero_upload . '/' . memberhero_clean( wp_unslash( $file ) );

	return apply_filters( 'memberhero_generate_upload_url', $return_url, $file, $type );
}

/**
 * Get uploads URL.
 */
function memberhero_get_uploads_url() {

	$upload_dir = wp_get_upload_dir();

	$url = $upload_dir['baseurl'] . '/memberhero_uploads';

	return apply_filters( 'memberhero_get_uploads_url', $url );
}

/**
 * Get upload sub-directory depending on upload type.
 */
function memberhero_get_upload_folder( $type ) {
	$dir = null;

	switch( $type ) {
		case 'avatar' :
			$dir = '/profile_avatars';
		break;

		case 'cover' :
			$dir = '/profile_banners';
		break;

		case 'image' :
			$dir = '/profile_photos';
		break;

		case 'file' :
		default :
			$dir = '/profile_files';
		break;
	}

	return apply_filters( 'memberhero_get_upload_folder', $dir, $type );
}

/**
 * Create a new uploads folder for plugin use.
 */
function memberhero_create_upload_folder( $dir ) {
	$upload_dir      = wp_upload_dir();

	// Clean up the dir name.
	$dir = untrailingslashit( memberhero_clean( strtolower( trim( $dir ) ) ) );

	if ( ! empty( $dir ) ) {
		$dir = '/' . $dir;
	}

	$files = array(
		array(
			'base'    => $upload_dir['basedir'] . '/memberhero_uploads' . $dir,
			'file'    => 'index.html',
			'content' => '',
		),
		array(
			'base'    => $upload_dir['basedir'] . '/memberhero_uploads' . $dir,
			'file'    => '.htaccess',
			'content' => 'Options -Indexes',
		),
	);

	foreach ( $files as $file ) {
		if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
			$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' );
			if ( $file_handle ) {
				fwrite( $file_handle, $file['content'] );
				fclose( $file_handle );
			}
		}
	}
}

/**
 * Handle file upload, including images.
 */
function memberhero_handle_upload( $user_id = 0, $key, $data = array() ) {
	global $the_upload;

	$the_upload = new MemberHero_Uploader();

	$the_upload->user_upload( $user_id, $key, $data );
}

/**
 * Upload an avatar.
 */
function memberhero_upload_avatar( $args = array() ) {
	global $the_upload;

	$the_upload = new MemberHero_Uploader();

	return $the_upload->base64_upload( 'avatar', $args );
}

/**
 * Upload a header photo.
 */
function memberhero_upload_cover( $args = array() ) {
	global $the_upload;

	$the_upload = new MemberHero_Uploader();

	return $the_upload->base64_upload( 'cover', $args );
}

/**
 * Checks decoded image string for true color.
 */
function memberhero_invalid_image_string_color( $string ) {
	$image = @imagecreatefromstring( $string );
	$width = imagesx( $image );
	$height = imagesy( $image );
	$pixel = imagecreatetruecolor( 1, 1 );

	imagecopyresampled( $pixel, $image, 0, 0, 0, 0, 1, 1, $width, $height );

	if ( imagecolorat( $pixel, 0, 0 ) == 0 ) {
		return true;
	}
	return false;
}

/**
 * Checks if a given field data is an actual file upload.
 */
function memberhero_is_file_upload( $key = null, $data = array() ) {
	if ( isset( $data['field'] ) ) {
		unset( $data['field'] );
	}
	if ( isset( $_FILES[ $key ] ) && ( $_FILES[ $key ] === $data ) ) {
		return true;
	}
	return false;
}

/**
 * Get valid image mimes.
 */
function memberhero_get_image_mimes() {
	$array = array(
		'jpg|jpeg|jpe'  => 'image/jpeg',
		'gif'			=> 'image/gif',
		'png'			=> 'image/png',
	);

	return apply_filters( 'memberhero_get_image_mimes', $array );
}

/**
 * Supported upload types.
 */
function memberhero_supported_upload_types() {
	return apply_filters( 'memberhero_supported_upload_types', array( 'avatar', 'cover', 'file', 'image' ) );
}

/**
 * Get file extension from path.
 */
function memberhero_file_extension( $path ) {
	return pathinfo( $path, PATHINFO_EXTENSION );
}