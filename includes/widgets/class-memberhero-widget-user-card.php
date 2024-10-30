<?php
/**
 * User Card Widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Widget_User_Card class.
 */
class MemberHero_Widget_User_Card extends MemberHero_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'memberhero_widget memberhero_widget_user_card';
		$this->widget_description = __( 'Display the logged in user card.', 'memberhero' );
		$this->widget_id          = 'memberhero_widget_user_card';
		$this->widget_name        = __( 'Member Hero - User card', 'memberhero' );
		$this->settings           = array(
			'title'         => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'memberhero' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 */
	public function widget( $args, $instance ) {
		global $the_user;

		if ( ! is_user_logged_in() ) {
			return;
		}

		$the_user = memberhero_get_user( get_current_user_id() );
		$the_user->in_widget = true;

		$this->widget_start( $args, $instance );

		echo '<div class="memberhero">
				<div class="memberhero-widget-wrap">';

		memberhero_get_template( 'widgets/user-card.php' );

		echo '</div>
			</div>';

		$this->widget_end( $args );
	}

}