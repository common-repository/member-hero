<?php
/**
 * Installation functions and actions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Install class.
 */
class MemberHero_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . MEMBERHERO_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
	}

	/**
	 * Check version and run the updater is required.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'memberhero_version' ), memberhero()->version, '<' ) ) {
			self::install();
			do_action( 'memberhero_updated' );
		}
	}

	/**
	 * Install.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'memberhero_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'memberhero_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		memberhero_maybe_define_constant( 'MemberHero_INSTALLING', true );

		self::remove_admin_notices();
		self::create_options();
		self::create_tables();
		self::create_roles();
		self::setup_environment();
		self::create_cron_jobs();
		self::create_files();
		self::maybe_enable_setup_wizard();
		self::update_memberhero_version();

		delete_transient( 'memberhero_installing' );

		do_action( 'memberhero_flush_rewrite_rules' );
		do_action( 'memberhero_installed' );
	}

	/**
	 * Reset any notices added to admin.
	 */
	private static function remove_admin_notices() {
		include_once dirname( __FILE__ ) . '/admin/class-memberhero-admin-notices.php';
		MemberHero_Admin_Notices::remove_all_notices();
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	public static function create_options() {
		// Include settings so that we can run through defaults.
		include_once dirname( __FILE__ ) . '/admin/class-memberhero-admin-settings.php';

		$settings = MemberHero_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}

		// Define other defaults if not in setting screens.
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema.
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
CREATE TABLE {$wpdb->prefix}memberhero_api_keys (
  key_id BIGINT UNSIGNED NOT NULL auto_increment,
  user_id BIGINT UNSIGNED NOT NULL,
  description varchar(200) NULL,
  permissions varchar(10) NOT NULL,
  consumer_key char(64) NOT NULL,
  consumer_secret char(43) NOT NULL,
  nonces longtext NULL,
  truncated_key char(7) NOT NULL,
  last_access datetime NULL default null,
  PRIMARY KEY  (key_id),
  KEY consumer_key (consumer_key),
  KEY consumer_secret (consumer_secret)
) $collate;
		";

		return $tables;
	}

	/**
	 * Return a list of tables. Used to make sure all tables are dropped when uninstalling the plugin
	 * in a single site or multi site environment.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}memberhero_api_keys",
			"{$wpdb->prefix}memberhero_drip_content",
			"{$wpdb->prefix}memberhero_notifications",
			"{$wpdb->prefix}memberhero_chats",
			"{$wpdb->prefix}memberhero_chat_messages",
		);

		/**
		 * Filter the list of known tables.
		 */
		$tables = apply_filters( 'memberhero_install_get_tables', $tables );

		return $tables;
	}

	/**
	 * Setup environment - post types, taxonomies, endpoints.
	 */
	private static function setup_environment() {
		MemberHero_Post_types::register_post_types();
		memberhero()->query->init_query_vars();
		memberhero()->query->add_endpoints();
		memberhero()->api->add_endpoint();
	}

	/**
	 * See if we need the wizard or not.
	 */
	private static function maybe_enable_setup_wizard() {
		if ( apply_filters( 'memberhero_enable_setup_wizard', true ) && self::is_new_install() ) {
			MemberHero_Admin_Notices::add_notice( 'install' );
			set_transient( '_memberhero_activation_redirect', 1, 30 );
		}
	}

	/**
	 * Is this a brand new install?
	 */
	private static function is_new_install() {
		return is_null( get_option( 'memberhero_version', null ) );
	}

	/**
	 * Update version to current.
	 */
	private static function update_memberhero_version() {
		delete_option( 'memberhero_version' );
		add_option( 'memberhero_version', memberhero()->version );
	}

	/**
	 * Drop tables.
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 */
	public static function wpmu_drop_tables( $tables ) {
		return array_merge( $tables, self::get_tables() );
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Create plugin-specific roles
		_x( 'Community manager', 'User role', 'memberhero' );
		_x( 'Member', 'User role', 'memberhero' );

		memberhero_add_role(
			'community_manager',
			'Community manager',
			$wp_roles->roles['administrator']['capabilities']
		);

		memberhero_add_role(
			'member',
			'Member',
			$wp_roles->roles['subscriber']['capabilities']
		);

		update_option( 'memberhero_roles', array(
			'community_manager',
			'member'
		) );

		// Add default capabilities to all roles.
		foreach( $wp_roles->roles as $role => $data ) {
			if ( in_array( $role, memberhero_get_admin_roles() ) ) {
				foreach( memberhero_get_admin_capabilities() as $capability => $bool ) {
					$wp_roles->add_cap( $role, $capability );
				}
			}
			foreach( memberhero_get_default_capabilities() as $capability => $bool ) {
				$wp_roles->add_cap( $role, $capability );
			}
		}

	}

	/**
	 * Add more cron schedules.
	 */
	public static function cron_schedules( $schedules ) {
		$schedules[ '6_hours' ] = array(
			'interval' => 21600,
			'display'  => __( 'Every 6 hours', 'memberhero' ),
		);
		$schedules[ 'weekly' ] = array(
			'interval' => 604800,
			'display'  => __( 'Weekly', 'memberhero' ),
		);
		$schedules[ 'monthly' ] = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'memberhero' ),
		);
		return $schedules;
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		wp_clear_scheduled_hook( 'memberhero_delete_rejected_users' );
		wp_clear_scheduled_hook( 'memberhero_delete_unconfirmed_users' );

		// Delete rejected users.
		$delete_recurrence = get_option( 'memberhero_delete_users_event_recurrence', 'daily' );
		if ( $delete_recurrence != '' ) {
			wp_schedule_event( time() + 10, $delete_recurrence, 'memberhero_delete_rejected_users' );
		}

		// Delete unconfirmed email users.
		if ( get_option( 'memberhero_delete_unconfirmed_emails_duration', 6 ) != '' ) {
			wp_schedule_event( time() + 10, 'hourly', 'memberhero_delete_unconfirmed_users' );
		}
	}

	/**
	 * Create files/directories.
	 */
	private static function create_files() {
		$upload_dirs = array(
			'',
			'profile_avatars',
			'profile_banners',
			'profile_photos',
			'profile_files',
		);

		foreach( $upload_dirs as $upload_dir ) {
			memberhero_create_upload_folder( $upload_dir );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=memberhero-settings' ) . '" aria-label="' . esc_attr__( 'View Member Hero settings', 'memberhero' ) . '">' . __( 'General Settings', 'memberhero' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( MEMBERHERO_PLUGIN_BASENAME === $file ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'memberhero_docs_url', 'https://docs.memberhero.pro' ) ) . '" aria-label="' . esc_attr__( 'View Member Hero documentation', 'memberhero' ) . '">' . __( 'Docs', 'memberhero' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'memberhero_support_url', 'https://memberhero.pro/support/' ) ) . '" aria-label="' . esc_attr__( 'Visit Member Hero support', 'memberhero' ) . '">' . __( 'Support', 'memberhero' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * Uninstall.
	 */
	public static function uninstall() {
		global $wpdb;

		/*
		 * Only remove ALL plugin and page data if MEMBERHERO_REMOVE_ALL_DATA constant is set to true in user's
		 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
		 * and to ensure only the site owner can perform this action.
		 */
		if ( defined( 'MEMBERHERO_REMOVE_ALL_DATA' ) && true === MEMBERHERO_REMOVE_ALL_DATA ) {

			// Check for needed files.
			if ( ! function_exists( 'memberhero_maybe_define_constant' ) ) {
				include_once dirname( __FILE__ ) . '/memberhero-core-functions.php';
			}

			// Delete tables.
			self::drop_tables();

			// Delete options.
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'memberhero\_%';" );
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'widget\_memberhero\_%';" );
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%\_memberhero\_%';" );

			// Delete usermeta.
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'memberhero\_%';" );
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%\_memberhero\_%';" );

			// Delete posts.
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'memberhero_form', 'memberhero_field', 'memberhero_role', 'memberhero_list', 'mh_subscription', 'mh_discount', 'mh_transaction' );" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_form%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_login%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_register%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_lostpassword%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_list%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_account%';" );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_content LIKE '%memberhero_profile%';" );
			$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

			// Roles + capabilities.
			self::remove_roles();

			// Remove directories and files.
			self::remove_files();

			// Clear any cached data that has been removed.
			wp_cache_flush();
		}
	}

	/**
	 * Remove roles and capabilities.
	 */
	public static function remove_roles() {
		global $wpdb, $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Let's remove all plugin roles.
		$roles = get_option( 'memberhero_roles' );
		if ( $roles ) {
			foreach( $roles as $role ) {
				foreach( array_merge( memberhero_get_default_capabilities(), memberhero_get_admin_capabilities(), memberhero_get_wp_admin_capabilities() ) as $cap => $bool ) {
					$wp_roles->remove_cap( $role, $cap );
				}
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = 'memberhero_role' AND post_name = %s", $role ) );
				remove_role( $role );
			}
			delete_option( 'memberhero_roles' );
		}

		// Let's remove plugin capabilities from other roles.
		foreach( $wp_roles->roles as $role => $data ) {
			foreach( array_merge( memberhero_get_default_capabilities(), memberhero_get_admin_capabilities() ) as $cap => $bool ) {
				$wp_roles->remove_cap( $role, $cap );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = 'memberhero_role' AND post_name = %s", $role ) );
		}
	}

	/**
	 * Remove files and folders.
	 */
	public static function remove_files() {
		global $wp_filesystem;

		// Initialize the WP filesystem.
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$upload_dir     = wp_upload_dir();
		$memberhero_uploads 	= $upload_dir['basedir'] . '/memberhero_uploads';

		$wp_filesystem->rmdir( $memberhero_uploads, true );
	}

}

MemberHero_Install::init();