<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_post_title_function')) {

	function suevafree_post_title_function($type) {
		
		global $post;
		
		$title = get_the_title();
		
		if ( !empty($title) ) {
		
			if ( $type == "blog" ) { 
		
				echo '<h3 class="title"><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html($title) . '</a></h3>';
		
			} else if ( $type == "single" ) {
				
				echo '<h1 class="title">' . esc_html($title) .'</h1>';
	
			}
	
		}
	
	}

	add_action( 'suevafree_post_title', 'suevafree_post_title_function', 10, 2 );

}

?>