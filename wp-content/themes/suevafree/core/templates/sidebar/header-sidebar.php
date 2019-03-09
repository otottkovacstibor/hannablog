<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_header_sidebar_function')) {

	function suevafree_header_sidebar_function( $name ) {
	
		if ( is_active_sidebar($name) ) : ?>
			
			<div id="header_sidebar" class="container sidebar-area">
			
				<div class="row">
				
					<div class="col-md-12">
						
						<?php dynamic_sidebar($name) ?>
												
					</div>
	
				</div>
				
			</div>
				
<?php 
	
		endif;
		
	}

	add_action( 'suevafree_header_sidebar', 'suevafree_header_sidebar_function', 10, 2 );

}

?>