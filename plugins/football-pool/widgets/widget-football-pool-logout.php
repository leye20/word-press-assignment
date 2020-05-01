<?php
/**
 * Widget: Logout Widget
 */

defined( 'ABSPATH' ) or die( 'Cannot access widgets directly.' );
add_action( 'widgets_init', function() { register_widget( 'Football_Pool_Logout_Widget' ); } );

// dummy var for translation files
$fp_translate_this = __( 'Log out Widget', 'football-pool' );
$fp_translate_this = __( 'add a log out/log in button.', 'football-pool' );

class Football_Pool_Logout_Widget extends Football_Pool_Widget {
	protected $widget = array(
		'name' => 'Log out Widget',
		'description' => 'add a log out/log in button.',
		'do_wrapper' => false, 
		
		'fields' => array(
			array(
				'name' => 'Title',
				'desc' => '',
				'id' => 'title',
				'type' => 'text',
				'std' => ''
			),
		)
	);
	
	public function html( $title, $args, $instance ) {
		extract( $args );
		
		//$return_url = apply_filters( 'the_permalink', get_permalink( @get_the_ID() ) );
		$return_url = Football_Pool_Utils::full_url();
		$output = '';
		
		global $current_user;
		wp_get_current_user();
		if ( $current_user->ID > 0 ) {
			$output .= sprintf( '<a class="widget button logout" href="%s" title="%s">%s</a>'
								, wp_logout_url( $return_url )
								, esc_attr( __( 'Log out', 'football-pool' ) )
								, __( 'Log out', 'football-pool' )
						);
		} else {
			$output .= sprintf( '<a class="widget button logout" href="%s" title="%s">%s</a>'
								, wp_login_url( $return_url )
								, esc_attr( __( 'Log in', 'football-pool' ) )
								, __( 'Log in', 'football-pool' )
						);
		}
		
		echo apply_filters( 'footballpool_widget_html_logout', $output );
	}
	
	public function __construct() {
		$classname = str_replace( '_', '', get_class( $this ) );
		
		parent::__construct( 
			$classname, 
			( isset( $this->widget['name'] ) ? $this->widget['name'] : $classname ), 
			$this->widget['description']
		);
	}
}
