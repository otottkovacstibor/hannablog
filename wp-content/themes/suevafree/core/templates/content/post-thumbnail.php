<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_thumbnail_function')) {

	function suevafree_thumbnail_function( $params ) {

		global $post;
		
		if ( $params['type'] == "default" ) {
		
			if ( '' != get_the_post_thumbnail() ) { 
			
			?>
			
				<div class="pin-container">
					
					<?php 
						
						the_post_thumbnail($params['id']);
						
						if ( $params['icon'] == "on" ):
						
							echo suevafree_posticon();	
						
						endif;
					
					?>
                    
				</div>
			
			<?php } 
	
		} else if ( $params['type'] == "overlay" ) {
		
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), $params['id']);
			
			if (!empty($thumb)) :
			
		?>
			
			<div class="pin-container">

                <div class="overlay-thumbnail">
					
                    <img src="<?php echo esc_url($thumb[0]); ?>" class="attachment-blog wp-post-image" alt="post image" title="post image"> 
                    <a href="<?php echo get_permalink($post->ID); ?>" ></a>

				</div>
		
        	</div>
				
		<?php
		
		endif;
		
		}
	
	}

	add_action( 'suevafree_thumbnail', 'suevafree_thumbnail_function', 10, 3 );

}

?>