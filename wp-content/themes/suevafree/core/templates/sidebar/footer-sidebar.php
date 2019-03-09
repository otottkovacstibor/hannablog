<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_bottom_sidebar_function')) {

	function suevafree_bottom_sidebar_function() {
		
		$name = 'bottom-sidebar-area';

		if ( is_active_sidebar( $name ) ) : ?>

			<div id="footer_widgets">
				
                <div class="container sidebar-area">
                
                    <div class="row">
                    
                        <?php dynamic_sidebar($name) ?>
                                                    
                    </div>
                    
                </div>
				
			</div>

<?php 
	
		endif;
		
	}

	add_action( 'suevafree_bottom_sidebar', 'suevafree_bottom_sidebar_function', 10, 2 );

}

?>