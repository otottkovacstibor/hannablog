<?php


/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_link_format_function')) {

	function suevafree_link_format_function() {

	?>
	
        <div class="post-article link">
        
            <a href="<?php echo esc_url(suevafree_postmeta('suevafree_url_link' )); ?>" target="_blank">
            
                <i class="fa fa-link"></i>
                <?php echo esc_attr(suevafree_postmeta( 'suevafree_url_link_name' )); ?>
            
            </a>
           
        </div>
        
	<?php

	}

	add_action( 'suevafree_link_format', 'suevafree_link_format_function', 10, 2 );

}

?>