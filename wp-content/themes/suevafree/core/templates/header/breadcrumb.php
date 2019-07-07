<?php 

/**
 * Wp in Progress
 * 
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_get_breadcrumb_function')) {

	function suevafree_get_breadcrumb_function() {
		
?>
    
		<div id="breadcrumb_wrapper">
        
            <div class="container">
            
                <div class="row">
                    
                    <div class="col-md-12">
                    
                        <?php
						
							echo '<ul id="breadcrumb">';
							
							if ( !suevafree_is_woocommerce_active('is_woocommerce') ) {
								
								echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . __('Home', 'suevafree') . "</a></li> / ";
								
								if ( is_category() ) {
									
									echo '<li>' . suevafree_get_archive_title(). '</li>';
					
								} elseif (is_singular('post') && !is_attachment()) {
									
									echo "<li>" . the_category(' </li> / <li> ') . '</li> / <li> ' . esc_html(get_the_title()) . '</li>';
						
								} elseif ( is_page() ) {
									
									echo "<li>" . esc_html(get_the_title()) . '</li>';
						
								} else if ( suevafree_get_archive_title()) {
								
									echo "<li>" . suevafree_get_archive_title() . "</li>";
								
								} else if ( is_search() ) {
									
									global $s; 
									echo "<li>" . __( '<span>Search </span> results for ', 'suevafree' ) . $s . "</li>";
								
								} else if ( is_404() ) {
					
									echo "<li>" . __( 'Page 404', 'suevafree' ) . "</li>";
								
								} else if ( is_attachment() ) {
					
									echo "<li>" . __( 'Attachment: ', 'suevafree' ) . esc_html(get_the_title()) . "</li>";
						
								} else if ( is_singular('team') ) {
					
									echo "<li>" . __( 'Team: ', 'suevafree' ) . esc_html(get_the_title()) . "</li>";
								
								} else if ( is_singular('service') ) {
					
									echo "<li>" . __( 'Service: ', 'suevafree' ) . esc_html(get_the_title()) . "</li>";
								
								} else if ( is_singular('testimonial') ) {
					
									echo "<li>" . __( 'Testimonial: ', 'suevafree' ) . esc_html(get_the_title()) . "</li>";
								
								} 
						
							} else if ( suevafree_is_woocommerce_active('is_woocommerce') ) {
						
								woocommerce_breadcrumb(
									array(
										'wrap_before' => '',
										'wrap_after'  => '',
										'before'      => '<li>',
										'after'       => '</li>',
										'home'        => _x( 'Home', 'breadcrumb', 'suevafree' ),
									)
								);
						
							}
							
							echo '</ul>';
		
						?>
                        
                    </div>
    
                </div>
                
            </div>
        
        </div>
    
<?php 
	
			
	}
	
	add_action( 'suevafree_get_breadcrumb', 'suevafree_get_breadcrumb_function', 10, 2 );

}

?>