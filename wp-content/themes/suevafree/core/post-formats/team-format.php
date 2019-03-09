<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !function_exists('suevafree_team_format_function')) {

	function suevafree_team_format_function($post_thumbnail = 'suevafree_thumbnail') {

		do_action('suevafree_thumbnail', 
			
			array(	'id' => $post_thumbnail, 
					'type' => 'default', 
					'icon' => esc_attr(suevafree_setting( 'suevafree_display_icon', 'off' )) 
			) 
			
		); 
	
	?>
		
        <div class="post-article post-details <?php echo esc_attr(suevafree_setting('suevafree_post_align', '')) . ' ' . esc_attr(suevafree_setting('suevafree_title_align', '')); ?>">
        
            <?php 
            
				if ( !suevafree_is_single() ) {
		
					do_action('suevafree_post_title', 'blog' ); 

					if ( !suevafree_setting('suevafree_view_readmore') || suevafree_setting('suevafree_view_readmore') == "on" ) {
						
						the_excerpt(); 
					
					} else if (suevafree_setting('suevafree_view_readmore') == "off" ) {
						
						the_content(); 
					
					}

				} else {
		
					do_action('suevafree_post_title', 'single');
					the_content();
					do_action('suevafree_ek_socials');
		
				}
		
            ?>

        </div>

	<?php

	}

	add_action( 'suevafree_team_format', 'suevafree_team_format_function', 10, 2 );

}

?>