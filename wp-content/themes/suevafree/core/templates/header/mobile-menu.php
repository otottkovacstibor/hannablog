<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_mobile_menu_function')) {

	function suevafree_mobile_menu_function($theme_location, $menu_class) {

?>

        <div id="mobile-wrapper">
            
            <div id="mobile-scroll" class="clearfix">
            
				<div class="mobilemenu-box">

					<div class="mobile-navigation">
                    	
                        <i class="fa fa-times open"></i>
                        
					</div>	

					<nav class="suevafree-menu suevafree-mobile-menu suevafree-vertical-menu">
                                            
						<?php 
										
							wp_nav_menu( array(
							
								'theme_location' => $theme_location,
								'menu_class' => $menu_class,
								'container' => 'false',
							
								)
							
							); 
										
						?>
                                        
                    </nav> 
                        
				</div>
                    
            </div>
        
        </div>
        
<?php
	
	}

	add_action( 'suevafree_mobile_menu', 'suevafree_mobile_menu_function', 10, 2 );

}

?>