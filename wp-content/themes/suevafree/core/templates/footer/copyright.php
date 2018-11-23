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

/*-----------------------------------------------------------------------------------*/
/* Socials */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_copyright_function')) {

	function suevafree_copyright_function() {
		
		if ( suevafree_setting('suevafree_copyright_text')) :

			echo '<p>' . stripslashes(suevafree_setting('suevafree_copyright_text')) . '</p>';

		else:
		
			echo '<p>Copyright ' . get_bloginfo("name") . ' ' . date_i18n("Y") . ' - Theme by <a href="'.esc_url('https://www.themeinprogress.com/').'" target="_blank">ThemeinProgress</a></p>';
			
		endif;
		
	}
	
	add_action( 'suevafree_copyright', 'suevafree_copyright_function', 10, 2 );

}

?>