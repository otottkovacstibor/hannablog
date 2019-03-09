<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_header_layout_3_function')) {

	function suevafree_header_layout_3_function($theme_location, $menu_class) { 
		
	?>
        
            <div id="wrapper">
        
                <div id="header-wrapper" class="header-3">
                    
                    <header id="header" >
                        
                        <div class="container">
                        
                            <div class="row">
                                    
                                <div class="col-md-12" >
                                        
                                    <div id="logo">
                                    
                                    	<?php 
										
											do_action( 'suevafree_logo_layout', 'on');
											echo suevafree_header_cart(); 
										
										?>
                        
                                    </div>
                            
                                    <nav class="suevafree-menu suevafree-general-menu tinynav-menu">
                                                
                                        <?php

											wp_nav_menu( array(
                                        		'theme_location' => $theme_location,
                                        		'menu_class' => $menu_class,
												'container' => 'false',
												'depth' => 3
												)
											); 

                                        ?>
                            
                                    </nav> 
                                                       
                                </div>
                                
                            </div>
                            
                        </div>
                            
                    </header>
                        
                </div>
                
<?php
		
	}

	add_action( 'suevafree_header_layout_3', 'suevafree_header_layout_3_function', 10, 2 );

}

?>