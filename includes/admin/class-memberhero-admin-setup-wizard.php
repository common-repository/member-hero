<?php
/**
 * Setup Wizard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Setup_Wizard class.
 */
class MemberHero_Admin_Setup_Wizard {

	/**
	 * Current step
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 */
	private $steps = array();

	/**
	 * Tweets user can optionally send after install
	 */
	private $tweets = array(
		'I just set up a new community with #WordPress and @getmemberhero!',
	);

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'memberhero_enable_setup_wizard', true ) && current_user_can( 'manage_memberhero' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'memberhero-setup', '' );
	}

	/**
	 * Register/enqueue scripts and styles for the Setup Wizard.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'memberhero-setup', memberhero()->plugin_url() . '/assets/css/memberhero-setup.css', array( 'dashicons', 'install' ), MEMBERHERO_VERSION );

		// Add RTL support.
		wp_style_add_data( 'memberhero-setup', 'rtl', 'replace' );

		wp_register_script( 'memberhero-setup', memberhero()->plugin_url() . '/assets/js/admin/memberhero-setup.js', array( 'jquery', 'jquery-tiptip', 'jquery-toggles' ), MEMBERHERO_VERSION );
		wp_localize_script(
			'memberhero-setup',
			'memberhero_setup_params',
			array(
				'current_step'	=> isset( $this->steps[ $this->step ] ) ? $this->step : false,
			)
		);
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'memberhero-setup' !== $_GET['page'] ) {
			return;
		}

		$default_steps = array(
			'initial' 		=> array(
				'name'    	=> __( 'Welcome', 'memberhero' ),
				'handler' 	=> array( $this, 'memberhero_setup_initial_save' ),
			),
			'progress'     	=> array(
				'name'    	=> __( 'Setting up', 'memberhero' ),
				'handler' 	=> array( $this, 'memberhero_setup_progress_save' ),
			),
			'ready'			=> array(
				'name'		=> __( 'Ready!', 'memberhero' ),
				'handler' 	=> '',
			),
		);

		$this->steps = apply_filters( 'memberhero_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->setup_wizard_save(), $this );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get the URL for the next step's screen.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'Member Hero &rsaquo; Setup Wizard', 'memberhero' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'memberhero-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="memberhero-setup wp-core-ui">
			<h1 id="memberhero-logo"><a href="https://memberhero.pro" title="<?php esc_html_e( 'Member Hero', 'memberhero' ); ?>"><img src="<?php echo memberhero()->plugin_url(); ?>/assets/images/memberhero-logo.png" alt="<?php esc_html_e( 'Member Hero Logo', 'memberhero' ); ?>" /></a></h1>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
			<?php if ( 'initial' === $this->step ) : ?>
				<a class="memberhero-setup-footer-links" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Not right now', 'memberhero' ); ?></a>
			<?php elseif ( 'initial' !== $this->step && 'ready' !== $this->step && 'progress' !== $this->step ) : ?>
				<a class="memberhero-setup-footer-links" href="<?php echo esc_url( $this->get_next_step_link() ); ?>"><?php esc_html_e( 'Skip this step', 'memberhero' ); ?></a>
			<?php endif; ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps      = $this->steps;
		?>
		<ol class="memberhero-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) {
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );

				if ( $step_key === $this->step ) {
					?>
					<li class="active"><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				} elseif ( $is_completed ) {
					?>
					<li class="done">
						<a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
					</li>
					<?php
				} else {
					?>
					<li><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				}
			}
			?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		global $thepostid;
		$thepostid = -1;
		echo '<div class="memberhero-setup-content">';
		if ( ! @include dirname( __FILE__ ) . '/views/html-setup-' . $this->step . '.php' ) {
			esc_html_e( 'Failed to load setup wizard.', 'memberhero' );
		}
		echo '</div>';
	}

	/**
	 * Save progress.
	 */
	public function setup_wizard_save() {
		check_admin_referer( 'memberhero-setup' );
		call_user_func( $this->steps[ $this->step ]['handler'], $this );
		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save initial step.
	 */
	public function memberhero_setup_initial_save() {

	}

	/**
	 * Save progress step.
	 */
	public function memberhero_setup_progress_save() {

		// Create base.
		$defaults = array(
			'_forms',
			'_fields',
			'_roles',
			'_lists',
		);

		foreach( $defaults as $items ) {
			call_user_func( 'memberhero_create_default' . $items );
		}

		// Create pages.
		$pages[ 'profile' ] = array(
			'name'    => _x( 'profile', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Profile', 'Page title', 'memberhero' ),
			'content' => '[memberhero_profile]',
		);

		$pages[ 'account' ] = array(
			'name'    => _x( 'account', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Account', 'Page title', 'memberhero' ),
			'content' => '[memberhero_account]',
		);

		$pages[ 'register' ] = array(
			'name'    => _x( 'register', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Register', 'Page title', 'memberhero' ),
			'content' => '[memberhero_register]',
		);

		$pages[ 'login' ] = array(
			'name'    => _x( 'login', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Login', 'Page title', 'memberhero' ),
			'content' => '[memberhero_login]',
		);

		$pages[ 'lostpassword' ] = array(
			'name'    => _x( 'lost-password', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Lost password', 'Page title', 'memberhero' ),
			'content' => '[memberhero_lostpassword]',
		);

		$pages[ 'list' ] = array(
			'name'    => _x( 'members', 'Page slug', 'memberhero' ),
			'title'   => _x( 'Members', 'Page title', 'memberhero' ),
			'content' => '[memberhero_list]',
		);

		foreach ( (array) $pages as $key => $page ) {
			memberhero_create_page( esc_sql( $page['name'] ), 'memberhero_' . $key . '_page_id', $page['title'], $page['content'], '' );
		}

		// Enable registration.
		update_option( 'users_can_register', 1 );

		// Done with page setup. Save permalinks.
		update_option( 'memberhero_queue_flush_rewrite_rules', 'yes' );

		memberhero()->query->init_query_vars();
		memberhero()->query->add_endpoints();
	}

}

new MemberHero_Admin_Setup_Wizard();