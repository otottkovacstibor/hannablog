<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_scroll_sidebar_function')) {

	function suevafree_scroll_sidebar_function($theme_location, $menu_class) {

?>
        
        <div id="sidebar-wrapper">
            
            <div id="scroll-sidebar" class="clearfix">
            
                <div class="navigation"><i class="fa fa-times open"></i></div>	
        		
                <div class="wrap">
                
                    <div class="post-article widget-box">
                    
                        <h3 class="title"><?php esc_html_e( 'Menu','suevafree'); ?></h3>
                        
                        <nav class="suevafree-menu suevafree-mobile-menu suevafree-vertical-menu">
    
                            <?php
                            
                                wp_nav_menu( 
                                
                                    array(
                                        'theme_location' => $theme_location,
                                        'menu_class' => $menu_class,
                                        'container' => 'false',
                                        'menu_id' => 'widgetmenus',
                                    )
                                
                                ); 
                            
                            ?>
                            
                        </nav>
                    
                    </div>
                    
                    <div class="post-article">
                        
                        <div class="copyright">
                                
                            <?php do_action('suevafree_copyright'); ?>
                                    
                        </div>
    
                        <?php if ( suevafree_setting('suevafree_header_social_buttons') && suevafree_setting('suevafree_header_social_buttons') == "on" ) : ?>
    
                            <div class="social-buttons">
                                    
                                <?php do_action( 'suevafree_socials' ); ?>
                                    
                            </div>
        
                        <?php endif; ?>
    
                    </div>
                
                </div>
                
            </div>
        
        </div>
                
<?php
	
	}

	add_action( 'suevafree_scroll_sidebar', 'suevafree_scroll_sidebar_function', 10, 2 );

}

?>