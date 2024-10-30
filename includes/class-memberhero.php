<?php
/**
 * Member Hero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class.
 */
final class MemberHero {

	/**
	 * Member Hero version.
	 */
	public $version = '1.0.9';

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Countries instance.
	 */
	public $countries = null;

	/**
	 * Translations instance.
	 */
	public $translations = null;

	/**
	 * API Object.
	 */
	public $api;

	/**
	 * Session Object.
	 */
	public $session;

	/**
	 * Processing form ID.
	 */
	public static $form_id = null;

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
	 * Auto-load in-accessible properties on demand.
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'mailer' ), true ) ) {
			return $this->$key();
		}
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'memberhero_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {
		register_activation_hook( MEMBERHERO_PLUGIN_FILE, array( 'MemberHero_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'MemberHero_Shortcodes', 'init' ) );
	}

	/**
	 * Init when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_memberhero_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Load class instances.
		$this->countries      = new MemberHero_Countries();

		// Init action.
		do_action( 'memberhero_init' );
	}

	/**
	 * Define Constants.
	 */
	public function define_constants() {
		$this->define( 'MEMBERHERO_ABSPATH', dirname( MEMBERHERO_PLUGIN_FILE ) . '/' );
		$this->define( 'MEMBERHERO_PLUGIN_BASENAME', plugin_basename( MEMBERHERO_PLUGIN_FILE ) );
		$this->define( 'MEMBERHERO_VERSION', $this->version );
		$this->define( 'MEMBERHERO_TEMPLATE_DEBUG_MODE', false );
	}

	/**
	 * Define constant if not already set.
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		/**
		 * Class autoloader.
		 */
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-autoloader.php';

		/**
		 * Abstract classes.
		 */
		include_once MEMBERHERO_ABSPATH . 'includes/abstracts/abstract-memberhero-settings-api.php';

		/**
		 * Core classes.
		 */
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-core-functions.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-license-handler.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-ajax.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-post-types.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-install.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-shortcodes.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-query.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-background-emailer.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-countries.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-session.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-import-url.php';
		include_once MEMBERHERO_ABSPATH . 'includes/api/class-memberhero-api.php';

		/**
		 * Data stores
		 */
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-data-store-wp.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-form-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-field-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-role-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-list-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-posts-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-pages-data-store.php';
		include_once MEMBERHERO_ABSPATH . 'includes/data-stores/class-memberhero-categories-data-store.php';

		/**
		 * Libraries
		 */
		include_once MEMBERHERO_ABSPATH . 'includes/libraries/nav-menus/menu-item-custom-fields.php';

		if ( $this->is_request( 'admin' ) ) {
			include_once MEMBERHERO_ABSPATH . 'includes/admin/class-memberhero-admin.php';
		}

		// Front-end use only.
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		$this->query 	= new MemberHero_Query();
		$this->api   	= new MemberHero_API();
		$this->session	= new MemberHero_Session();
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-notice-functions.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-template-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-upload-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-field-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-list-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-user-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-profile-hooks.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-frontend-scripts.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-form-handler.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-content-restriction.php';
		include_once MEMBERHERO_ABSPATH . 'includes/class-memberhero-uploader.php';
	}

	/**
	 * Function used to Init Template Functions
	 */
	public function include_template_functions() {
		include_once MEMBERHERO_ABSPATH . 'includes/memberhero-template-functions.php';
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/memberhero/memberhero-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/memberhero-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'memberhero' );

		unload_textdomain( 'memberhero' );
		load_textdomain( 'memberhero', WP_LANG_DIR . '/memberhero/memberhero-' . $locale . '.mo' );
		load_plugin_textdomain( 'memberhero', false, plugin_basename( dirname( MEMBERHERO_PLUGIN_FILE ) ) . '/i18n/languages' );

		// Load translations from options.
		$this->translations = get_option( 'memberhero_translations' );
	}

	/**
	 * Get the plugin url.
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', MEMBERHERO_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( MEMBERHERO_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 */
	public function template_path() {
		return apply_filters( 'memberhero_template_path', 'memberhero/' );
	}

	/**
	 * Get Ajax URL.
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Email class.
	 */
	public function mailer() {
		return MemberHero_Emails::instance();
	}

}