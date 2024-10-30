<?php
/**
 * Handle frontend scripts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Frontend_Scripts class.
 */
class MemberHero_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered.
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered.
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized.
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_css_queries' ), 9999999999 );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );

		add_action( 'memberhero_update_options_style', array( __CLASS__, 'save_styles' ) );
	}

	/**
	 * Get styles for the frontend.
	 */
	public static function get_styles() {
		return apply_filters(
			'memberhero_enqueue_styles',
			array(
				'memberhero-layout'	=> array(
					'src'     => self::get_asset_url( 'assets/css/memberhero-layout.css' ),
					'deps'    => '',
					'version' => MEMBERHERO_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'memberhero-style'	=> array(
					'src'     => self::get_theme(),
					'deps'    => '',
					'version' => MEMBERHERO_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'memberhero-general'	=> array(
					'src'     => self::get_asset_url( 'assets/css/memberhero.css' ),
					'deps'    => '',
					'version' => MEMBERHERO_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'memberhero-media'	=> array(
					'src'     => self::get_asset_url( 'assets/css/memberhero-media.css' ),
					'deps'    => '',
					'version' => MEMBERHERO_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
			)
		);
	}

	/**
	 * Get correct theme to load.
	 */
	public static function get_theme() {
		$custom = get_option( 'memberhero_style' );
		if ( $custom && file_exists( memberhero_plugin_uploads_path() . '/' . wp_basename( $custom ) ) ) {
			return $custom;
		} else {
			return self::get_asset_url( 'assets/css/memberhero-style.css' );
		}
	}

	/**
	 * Return asset URL.
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'memberhero_get_asset_url', plugins_url( $path, MEMBERHERO_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = MEMBERHERO_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = MEMBERHERO_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = MEMBERHERO_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = MEMBERHERO_VERSION, $media = 'all', $has_rtl = false, $inline = false ) {
		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl, $inline );
		}
		wp_enqueue_style( $handle );

		if ( $inline ) {
			wp_add_inline_style( $handle, self::get_inline_css( $handle ) );
		}
	}

	/**
	 * Register all scripts.
	 */
	private static function register_scripts() {
		$register_scripts = array(
			'jquery-tiptip'				=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-tiptip/jquery-tiptip.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-toggles'			=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-toggles/jquery-toggles.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-modal'				=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-modal/jquery-modal.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-selectize' 			=> array(
				'src'	  => self::get_asset_url( 'assets/js/jquery-selectize/jquery-selectize.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-raty'				=> array(
				'src'	  => self::get_asset_url( 'assets/js/jquery-raty/jquery-raty.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-textcomplete'		=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-textcomplete/jquery-textcomplete.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-emojione'			=> array(
				'src'	  =>self::get_asset_url( 'assets/js/jquery-emojione/jquery-emojione.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-emoji'				=> array(
				'src'	  => self::get_asset_url( 'assets/js/jquery-emoji/jquery-emoji.js' ),
				'deps'    => array( 'jquery', 'jquery-emojione', 'jquery-textcomplete' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-croppie'			=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-croppie/jquery-croppie.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'resize-sensor'				=> array(
				'src'     => self::get_asset_url( 'assets/js/css-element-queries/ResizeSensor.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'element-queries'			=> array(
				'src'     => self::get_asset_url( 'assets/js/css-element-queries/ElementQueries.js' ),
				'deps'    => array( 'jquery' ),
				'version' => MEMBERHERO_VERSION,
			),
			'jquery-memberhero'			=> array(
				'src'     => self::get_asset_url( 'assets/js/jquery-memberhero/jquery-memberhero.js' ),
				'deps'    => array( 'jquery', 'jquery-tiptip', 'jquery-toggles', 'jquery-modal', 'jquery-selectize', 'jquery-raty', 'jquery-emoji' ),
				'version' => MEMBERHERO_VERSION,
			),
			'memberhero-profile'		=> array(
				'src'     => self::get_asset_url( 'assets/js/frontend/profile.js' ),
				'deps'    => array( 'jquery', 'jquery-croppie' ),
				'version' => MEMBERHERO_VERSION,
			),
			'memberhero'				=> array(
				'src'     => self::get_asset_url( 'assets/js/frontend/memberhero.js' ),
				'deps'    => apply_filters( 'memberhero_javascript_deps', array( 'jquery', 'jquery-memberhero' ) ),
				'version' => MEMBERHERO_VERSION,
			),
		);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all styles.
	 */
	private static function register_styles() {
		$register_styles = array(

		);
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		if ( ! did_action( 'before_memberhero_init' ) ) {
			return;
		}

		self::register_scripts();
		self::register_styles();

		// Global frontend scripts.
		self::enqueue_script( 'memberhero' );

		// Profile.
		if ( is_memberhero_profile_page() ) {
			self::enqueue_script( 'memberhero-profile' );
		}

		// CSS Styles.
		$enqueue_styles = self::get_styles();
		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {

				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}
				if ( ! isset( $args['inline'] ) ) {
					$args['inline'] = false;
				}

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'], $args['inline'] );
			}
		}
	}

	/**
	 * CSS element queries.
	 */
	public static function load_css_queries() {
		self::enqueue_script( 'resize-sensor' );
		self::enqueue_script( 'element-queries' );
	}

	/**
	 * Localize a script once.
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
			$data = self::get_script_data( $handle );

			if ( ! $data ) {
				return;
			}

			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 */
	private static function get_script_data( $handle ) {
		global $wp;

		switch ( $handle ) {
			case 'memberhero':
				$params = array(
					'ajaxurl'   => memberhero()->ajax_url(),
					'yes'		=> __( 'yes', 'memberhero' ),
					'no'		=> __( 'no', 'memberhero' ),
					'svg'		=> memberhero()->plugin_url() . '/assets/images/feather-sprite.svg#',
					'nonces'	=> apply_filters( 'memberhero_ajax_nonces', array(
						'cancel_pending_email' 	=> wp_create_nonce( 'memberhero-cancel-email' ),
						'resend_confirmation'	=> wp_create_nonce( 'memberhero-resend-confirmation' ),
						'save_avatar'			=> wp_create_nonce( 'memberhero-save-avatar' ),
						'save_cover'			=> wp_create_nonce( 'memberhero-save-cover' ),
						'remove_avatar'			=> wp_create_nonce( 'memberhero-remove-avatar' ),
						'remove_cover'			=> wp_create_nonce( 'memberhero-remove-cover' ),
						'unblock_user'			=> wp_create_nonce( 'memberhero-unblock-user' ),
						'block_user'			=> wp_create_nonce( 'memberhero-block-user' ),
						'delete_user'			=> wp_create_nonce( 'memberhero-delete-user' ),
						'confirmation_code'		=> wp_create_nonce( 'memberhero-confirmation-code' ),
						'approve_user'			=> wp_create_nonce( 'memberhero-approve-user' ),
						'reject_user'			=> wp_create_nonce( 'memberhero-reject-user' ),
						'search_user'			=> wp_create_nonce( 'memberhero-search-user' ),
					) ),
					'saved'		=> apply_filters( 'memberhero_saved_messages', array(
						'true'				=> __( 'Changes have been saved successfully.', 'memberhero' ),
						'username_changed'  => __( 'Your username has been changed.', 'memberhero' ),
						'password_changed'  => __( 'Your password has been changed.', 'memberhero' ),
						'confirm_email'		=> __( 'A confirmation email has been sent to you.', 'memberhero' ),
						'email_changed'		=> __( 'Your email has been changed.', 'memberhero' ),
						'pending_account'	=> __( 'Your account needs manual review.', 'memberhero' ),
					) ),
					'emoji' 	=> array(
						'search' 		=> __( 'Search emojis', 'memberhero' ),
						'buttontitle'	=> __( 'Use the TAB key to insert emoji faster', 'memberhero' ),
					),
				);
				break;
			default:
				$params = false;
		}

		return apply_filters( 'memberhero_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}

	/**
	 * Get inline css.
	 */
	public static function get_inline_css( $handle = '', $memberhero_css = '' ) {
		return apply_filters( 'memberhero_inline_styles', $memberhero_css, $handle );
	}

	/**
	 * Save styles for the frontend.
	 */
	public static function save_styles() {
		if ( isset( $_POST ) ) {
			$data = $_POST;
		}

		$defaults = apply_filters( 'memberhero_default_base_colors', array(
			'memberhero_primary_bg_color'	=> '#027FD2',
			'memberhero_primary_color'		=> '#66757f',
			'memberhero_alert_color'		=> '#bb0000',
		) );

		$data = array_intersect_key( $data, $defaults );

		// Avoid empty colors.
		foreach( $data as $key => $color ) {
			if ( empty( sanitize_hex_color( $color ) ) ) {
				$data[ $key ] = $defaults[ $key ];
			}
		}

		$old = self::get_rgb( $defaults );
		$new = self::get_rgb( array_merge( $defaults, $data ) );

		self::save_css( $old, $new );

		foreach( $data as $key => $value ) {
			update_option( $key, memberhero_clean( $value ) );
		}
	}

	/**
	 * Convert hex color array to rgb color array.
	 */
	public static function get_rgb( $array ) {
		foreach( $array as $key => $hex ) {
			list($r, $g, $b) = sscanf( $hex, "#%02x%02x%02x" );
			$array[ $key ] = "{$r}, {$g}, {$b}";
		}
		return $array;
	}

	/**
	 * Save the css file.
	 */
	public static function save_css( $old = array(), $new = array() ) {
		$contents = @file_get_contents( memberhero()->plugin_path() . '/assets/css/memberhero-style.css' );

		if ( empty( $contents ) ) {
			return;
		}

		add_filter( 'upload_dir', array( __CLASS__, 'upload_dir' ) );

		foreach( $new as $name => $rgb ) {
			$contents = str_replace( $old[ $name ], $new[ $name ], $contents );
		}

		$current = get_option( 'memberhero_style' );
		if ( $current ) {
			wp_delete_file( memberhero_plugin_uploads_path() . '/' . wp_basename( $current ) );
			wp_delete_file( memberhero_plugin_uploads_path() . '/' . wp_basename( str_replace( '.css', '-rtl.css', $current ) ) );
		}

		$upload 	= wp_upload_bits( 'memberhero-style.css', null, apply_filters( 'memberhero_new_style_content', $contents ), null );
		$upload_rtl = wp_upload_bits( 'memberhero-style-rtl.css', null, apply_filters( 'memberhero_new_rtl_style_content', $contents ), null );

		if ( isset( $upload['url'] ) ) {
			update_option( 'memberhero_style', $upload['url'] );
		}

		remove_filter( 'upload_dir', array( __CLASS__, 'upload_dir' ) );
	}

	/**
	 * Filter uploads directory during css file creation.
	 */
	public static function upload_dir( $upload_dir ) {

		$css_upload_dir = array(
			'path'   => $upload_dir['basedir'] . '/memberhero_uploads',
			'url'    => $upload_dir['baseurl'] . '/memberhero_uploads',
			'subdir' => '/memberhero_uploads',
		) + $upload_dir;

		return $css_upload_dir;
	}

}

MemberHero_Frontend_Scripts::init();