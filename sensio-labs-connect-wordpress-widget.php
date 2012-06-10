<?php
/*
Plugin Name: Sensio Labs Connect Wordpress Widget
Plugin URI: http://www.jongotlin.se
Description: Display your Sensio Labs badges collection on your WordPress site.
Version: 0.1
Author: Jon Gotlin
Author URI: http://www.jongotlin.se
License: MIT
*/

class Sensio_Labs_Connect_Wordpress_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(false, $name = 'Sensio Labs badges', array( 'description' => 'Display your Sensio Labs badges collection on your WordPress site.' ) );
	}

	public function form( $instance ) {
		$username = esc_attr($instance['username']);
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = esc_attr($instance[ 'title' ]);
		}
		else {
			$title = 'My cool Sensio Labs badges';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title') ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" type="text" value="<?php echo $title ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ) ?>">Sensio Connect username:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('username') ?>" name="<?php echo $this->get_field_name('username') ?>" type="text" value="<?php echo $username ?>" />
		</p>		
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {

        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);

        $wprr_args = array(
        	'timeout' => 30,
        	'headers' => array('Connection' => 'Close','Accept' => '*/*') 
    	);
        $url = 'https://connect.sensiolabs.com/profile/'.$new_instance['username'].'.json';

        $response = wp_remote_request($url, $wprr_args);
        
        $user_data = json_decode($response['body'], true);
        
        $instance['badges'] = $user_data['badges'];

		return $instance;
	}
	
	public function widget($args, $instance) {
		echo '<h3 class="widget-title">' . $instance['title'] . '</h3><p>';
		foreach ($instance['badges'] as $badge) {
		    echo '<a href="'.$badge['url'].'" title="'.$badge['name'].'"><img src="'.$badge['picture_url'].'" style="padding:3px"></a>';
		}
		echo '</p>';
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "Sensio_Labs_Connect_Wordpress_Widget" );' ) );