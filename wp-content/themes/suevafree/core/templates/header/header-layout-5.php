<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_header_layout_5_function')) {

	function suevafree_header_layout_5_function($theme_location, $menu_class) { 
			
			do_action('suevafree_scroll_sidebar', $theme_location, $menu_class );

	?>

            <div id="wrapper">
        
                <div id="overlay-body"></div>
				
                <header id="header" class="header-5">
                    
					<div id="logo">
									
						<?php do_action( 'suevafree_logo_layout', 'on' ); ?>
                                	
					</div>
                    
                    <div class="navigation"><i class="fa fa-bars"></i> </div>
            
                </header>
<?php
		
	}

	add_action( 'suevafree_header_layout_5', 'suevafree_header_layout_5_function', 10, 2 );

}

?>