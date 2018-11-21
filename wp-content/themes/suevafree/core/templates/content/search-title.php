<?php 

/**
 * Wp in Progress
 * 
 * @package Wordpress
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_searched_item_function')) {

	function suevafree_searched_item_function( $row = "on" ) {
		
		global $s;

		if ( !suevafree_setting('suevafree_view_searched_item') || suevafree_setting('suevafree_view_searched_item') == "on" ) :
			
			$allowed = array(
				'div' => array(
					'class' => array(),
				),
			);

			$before = '<div class="row">';
			$after = '</div>';
	
			if ( $row == "off" ) :
			
				$before = '';
				$after = '';
			
			endif;
			
			echo wp_kses($before, $allowed);
			
?>
			
			<div class="post-container col-md-12" >
		
				<article class="post-article category">
						
                    <h1><span><?php esc_html_e( 'Search', 'suevafree' ) ?></span> <?php esc_html_e( 'results for', 'suevafree' ) ?> <strong><?php echo esc_attr($s); ?> </strong></h1>
		
				</article>
		
			</div>
	
<?php
	
			echo wp_kses($after, $allowed);

		endif;
		
	} 
	
	add_action( 'suevafree_searched_item', 'suevafree_searched_item_function', 10, 2 );

}

?>