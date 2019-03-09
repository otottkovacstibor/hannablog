<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_quote_format_function')) {

	function suevafree_quote_format_function() {

	?>
        
        <div class="post-article quote">
            
            <i class="fa fa-quote-left"></i>
            <blockquote> <p> <?php echo esc_html(suevafree_postmeta( 'suevafree_quote_text' )); ?> </p> </blockquote>
            <p><?php echo esc_html(suevafree_postmeta( 'suevafree_quote_author' )); ?></p>
            
        </div>

	<?php

	}

	add_action( 'suevafree_quote_format', 'suevafree_quote_format_function', 10, 2 );

}

?>