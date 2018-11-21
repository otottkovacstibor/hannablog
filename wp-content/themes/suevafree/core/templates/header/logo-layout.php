<?php

/**
 * Wp in Progress
 * 
 * @package Wordpress
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_logo_layout_function')) {

	function suevafree_logo_layout_function( $description ) { 
	
		if ( suevafree_setting('suevafree_custom_logo') ) :
				
			echo "<a href='" . esc_url(home_url('/')) . "' title='" . get_bloginfo('name') . "' class='image-logo'>";
			echo "<img src='" . esc_url(suevafree_setting('suevafree_custom_logo')) . "' alt='logo'>"; 
			echo "</a>";
					
		else: 

			echo "<a href='" . esc_url(home_url('/')) . "' title='" . get_bloginfo('name') . "' class='logo'>";
			bloginfo('name');
			
			if ( isset($description) && $description == "on" )
				echo "<span>".get_bloginfo('description')."</span>";
			
			echo "</a>";

		endif; 
	
	}

	add_action( 'suevafree_logo_layout', 'suevafree_logo_layout_function', 10, 2 );

}

?>