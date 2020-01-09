<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_post_details_function')) {
	
	function suevafree_post_details_function() { 
	
		global $post;
	
	?>
	
		<div class="line"> 
		
			<div class="entry-info">
		   
				<span><i class="fa fa-clock-o"></i><?php echo esc_html(get_the_date()); ?></span>
                
				<?php 
				
					if ( ( !suevafree_setting('suevafree_view_comments') || suevafree_setting('suevafree_view_comments') == "on" ) && comments_open() ) : ?>
                    
                        <span> <i class="fa fa-comments-o"></i>
                            <?php echo comments_number( '<a href="'.esc_url(get_permalink($post->ID)).'#respond">'.__( "0 comments","suevafree").'</a>', '<a href="'.esc_url(get_permalink($post->ID)).'#comments">1 '.__( "comment","suevafree").'</a>', '<a href="'.esc_url(get_permalink($post->ID)).'#comments">% '.__( "comments","suevafree").'</a>' ); ?>
                        </span>
                    
						<?php echo suevafree_posticon();  ?>
                    
                    	<span> <i class="fa fa-tags"></i><?php the_category(', '); ?></span>
                    	<?php if ( suevafree_setting('suevafree_view_author') == "on" ) : ?>
                        <span> <i class="fa fa-user"></i><?php echo wp_kses_post(get_the_author_posts_link()); ?></span>
    
				<?php 
				
					endif; 
				
				endif; 
					
				?>

			</div>
	
		</div>
	
<?php
	
	} 

	add_action( 'suevafree_post_details', 'suevafree_post_details_function', 10, 2 );

}

?>