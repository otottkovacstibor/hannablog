<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_side_sidebar_function')) {

	function suevafree_side_sidebar_function($name) { 
	
		if ( !suevafree_setting('suevafree_sidebar_layout') || suevafree_setting('suevafree_sidebar_layout')  == "default" ) : 
	
	?>

			<div id="sidebar" class="col-md-4 sidebar-area">
	
				<div class="post-container">
                
					<div class="post-article">
    
						<?php 
        
                            if ( is_active_sidebar($name)) { 
                                
                                dynamic_sidebar($name);
                                
                            } else { 

                                the_widget( 'WP_Widget_Calendar',
                                array(	'title'=> esc_html__('Calendar',"suevafree")),
                                array(	'before_widget' => '<div class="widget-box widget_calendar">',
                                        'after_widget'  => '</div>',
                                        'before_title'  => '<h4 class="title">',
                                        'after_title'   => '</h4>'
                                ));
                    

                                the_widget( 'WP_Widget_Archives','',
                                array(	'before_widget' => '<div class="widget-box widget_archive">',
                                        'after_widget'  => '</div>',
                                        'before_title'  => '<h4 class="title">',
                                        'after_title'   => '</h4>'
                                ));
                    
                                the_widget( 'WP_Widget_Categories','',
                                array(	'before_widget' => '<div class="widget-box widget_categories">',
                                        'after_widget'  => '</div>',
                                        'before_title'  => '<h4 class="title">',
                                        'after_title'   => '</h4>'
                                ));
                                
                            } 
                            
                        ?>
                        
					</div>
                    
                </div>
                            
            </div>

		<?php 
            
            else: 
                
        ?>

            <div id="sidebar" class="col-md-4 sneak_sidebar sidebar-area">
                        
                <div class="post-container">
    
                    <?php 
    
                        if ( is_active_sidebar($name)) { 
                            
                            dynamic_sidebar($name);
                            
                        } else { 
                                    
                            the_widget( 'WP_Widget_Archives','',
                            array(	'before_widget' => '<div class="post-article widget_archive">',
                                    'after_widget'  => '</div>',
                                    'before_title'  => '<h4 class="title">',
                                    'after_title'   => '</h4>'
                            ));
                
                            the_widget( 'WP_Widget_Calendar',
                            array(	'title'=> esc_html__('Calendar',"suevafree")),
                            array(	'before_widget' => '<div class="post-article widget_calendar">',
                                    'after_widget'  => '</div>',
                                    'before_title'  => '<h4 class="title">',
                                    'after_title'   => '</h4>'
                            ));
                
                            the_widget( 'WP_Widget_Categories','',
                            array(	'before_widget' => '<div class="post-article widget_categories">',
                                    'after_widget'  => '</div>',
                                    'before_title'  => '<h4 class="title">',
                                    'after_title'   => '</h4>'
                            ));
                            
                        } 
                        
                    ?>
                        
                </div>
                            
            </div>
               
	<?php 
		
		endif; 
		
	}

	add_action( 'suevafree_side_sidebar', 'suevafree_side_sidebar_function', 10, 2 );

}

?>