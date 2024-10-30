<?php
/**
 * Upload Hooks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'memberhero_pre_avatar_update', 'memberhero_pre_avatar_update', 10, 2 );
add_action( 'memberhero_pre_cover_update', 'memberhero_pre_cover_update', 10, 2 );

/**
 * Before updating an avatar.
 */
function memberhero_pre_avatar_update( $user_id, $file ) {
	memberhero_delete_user_avatar( $user_id );
}

/**
 * Before updating cover photo.
 */
function memberhero_pre_cover_update( $user_id, $file ) {
	memberhero_delete_user_cover( $user_id );
}

/**
 * Set avatar upload quality.
 */
function memberhero_editor_set_avatar_quality( $quality, $mime = '' ) {
	return get_option( 'memberhero_avatar_upload_quality', 100 );
}

/**
 * Set cover upload quality.
 */
function memberhero_editor_set_cover_quality( $quality, $mime = '' ) {
	return get_option( 'memberhero_cover_upload_quality', 75 );
}

/**
 * Re-generate avatar filenames.
 */
function memberhero_rename_avatar_thumbnails( $image ) {
	return memberhero_rename_thumbnails( 'avatar', $image );
}

/**
 * Re-generate cover photo filenames.
 */
function memberhero_rename_cover_thumbnails( $image ) {
	return memberhero_rename_thumbnails( 'cover', $image );
}

/**
 * Re-generate filenames to replace the (-) with (_)
 */
function memberhero_rename_thumbnails( $name = 'avatar', $image ) {
	$sizes = call_user_func( "memberhero_get_{$name}_sizes" );

	foreach( $sizes as $width ) {
		$height = $width;
		if ( $name == 'cover' ) {
			$height = floor( $width / 2.70 );
		}
		if ( strstr( $image, "{$width}x{$height}" ) ) {
			$new_name = str_replace( "-{$width}x{$height}", "_{$width}x{$height}", $image );

			$did_it = rename( $image, $new_name );

			if ( $did_it )
				return $new_name;
		}
	}

	return $image;
}

/**
 * A filter for WP uploads to change upload destination.
 */
function memberhero_upload_dir( $upload_dir ) {
	global $the_upload;

	$subdir = null;

	// Maybe add upload to plugin sub-directory.
	if ( isset( $the_upload->type ) && in_array( $the_upload->type, memberhero_supported_upload_types() ) ) {
		$subdir = memberhero_get_upload_folder( $the_upload->type );
	}

	$memberhero_upload_dir = array(
		'path'   => $upload_dir['basedir'] . '/memberhero_uploads' . $subdir,
		'url'    => $upload_dir['baseurl'] . '/memberhero_uploads' . $subdir,
		'subdir' => '/memberhero_uploads' . $subdir,
	) + $upload_dir;

	return $memberhero_upload_dir;
}

/**
 * Fixes bug with some upload mimes. Should be used within plugin scope only.
 */
function memberhero_check_filetype_and_ext( $data, $file, $filename, $mimes ) {
	global $the_upload;

	$filetype 		= wp_check_filetype( $filename, $mimes );
	$image_mimes 	= memberhero_get_image_mimes();

	// This code ensures that we can only upload safe listed files.
	$mimes = get_allowed_mime_types();
	if ( ! in_array( $filetype['type'], $mimes ) ) {
		return false;
	}

	// Image uploads should allow only image uploads.
	if ( $the_upload->type == 'image' ) {
		if ( ! in_array( $filetype['type'], $image_mimes ) ) {
			return false;
		}
	}

	return [
		'ext'             => $filetype['ext'],
		'type'            => $filetype['type'],
		'proper_filename' => $data['proper_filename']
    ];

}