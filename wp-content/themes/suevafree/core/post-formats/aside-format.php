<?php


/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_aside_format_function')) {

	function suevafree_aside_format_function() {

	?>

        <div class="post-article aside">
        
            <?php do_action('suevafree_after_content'); ?> 
        
        </div>
        
	<?php

	}

	add_action( 'suevafree_aside_format','suevafree_aside_format_function', 10, 2 );

}

?>