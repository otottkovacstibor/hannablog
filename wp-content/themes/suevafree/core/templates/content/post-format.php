<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_postformat_function')) {

	function suevafree_postformat_function($post_thumbnail = 'suevafree_thumbnail') {

		if (
		 
			in_array(get_post_type( get_the_ID()), array('page', 'team', 'service', 'testimonial', 'product'))) { 
				
				$postformats = get_post_type( get_the_ID());
		
		} else if (
		
			!get_post_format() || 
			in_array( get_post_format(), array('status', 'chat', 'audio', 'video', 'gallery')) ||
			in_array( get_post_format(), array('link', 'quote', 'aside')) && ( !suevafree_setting('suevafree_post_format_layout') || suevafree_setting('suevafree_post_format_layout') == 'off' )) { 
				
				$postformats = 'default';
		
		} else {
			
			$postformats = get_post_format();
		
		}
		
		do_action( 'suevafree_' . $postformats . '_format', $post_thumbnail );
	
	}

	add_action( 'suevafree_postformat', 'suevafree_postformat_function', 10, 2 );

}

?>