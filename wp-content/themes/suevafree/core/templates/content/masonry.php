<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_masonry_function')) {
	
	function suevafree_masonry_function ( $span, $error ) { 
	
	?>

        <div class="row masonry" id="masonry">
                
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
           
                <div <?php post_class(); ?>>
            
                    <?php do_action('suevafree_postformat', 'suevafree_thumbnail_s'); ?>
            
                </div>
        
			<?php endwhile; else: ?>
        
                <div class="post-container <?php echo esc_attr($error); ?>">
            
                    <article class="post-article">
                            
                        <h1> <?php esc_html_e( 'Not found.','suevafree' ) ?> </h1>
                        <p> <?php esc_html_e( 'Sorry, no items found, in this section.','suevafree' ) ?> </p>
             
                    </article>
            
                </div>
            
            <?php endif; ?>
                
        </div>
		
<?php 
	
	} 

	add_action( 'suevafree_masonry', 'suevafree_masonry_function', 10, 2 );

} 

?>