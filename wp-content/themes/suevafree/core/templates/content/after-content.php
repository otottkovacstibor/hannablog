<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_after_content_function')) {

	function suevafree_after_content_function() { 
	
		if ( suevafree_get_archive_title() || is_search() || is_home() ) {
	
			if ( !suevafree_setting('suevafree_view_readmore') || suevafree_setting('suevafree_view_readmore') == "on" ) {
				
				the_excerpt(); 
			
			} else if (suevafree_setting('suevafree_view_readmore') == "off" ) {
				
				the_content(); 
			
			}
	
		} else {
		
			the_content();
	
			the_tags( '<footer class="line"><span class="entry-info"><strong>' . esc_html__( 'Tags:', 'suevafree' ) . '</strong> ', ', ', '</span></footer>' );
	
			if ( ( !suevafree_setting('suevafree_view_comments') || suevafree_setting('suevafree_view_comments') == "on" ) && comments_open() ) : 
	
				comments_template();
	
			endif;
		
		}
	
	} 

	add_action( 'suevafree_after_content', 'suevafree_after_content_function', 10, 2 );

}

?>