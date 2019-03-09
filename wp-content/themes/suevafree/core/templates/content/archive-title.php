<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_archive_title_function')) {

	function suevafree_archive_title_function( $row = "on" ) {

		if ( !suevafree_setting('suevafree_view_category_title') || suevafree_setting('suevafree_view_category_title') == "on" ) :
			
			$before = "<div class='row'>";
			$after = "</div>";
			
			if ( $row == "off" ) :
			
				$before = "";
				$after = "";
			
			endif;
			
			$allowed = array(
				'div' => array(
					'class' => array(),
				),
				'span' => array(
					'class' => array(),
				),
			);

			echo wp_kses($before, $allowed);
			
?>
			
			<div class="post-container col-md-12" >
		
				<article class="post-article category">
						
					<h1><?php echo wp_kses(suevafree_get_archive_title(), $allowed); ?></h1>
		
				</article>
		
			</div>
	
<?php

			echo wp_kses($after, $allowed);
		
		endif;
		
	} 
	
	add_action( 'suevafree_archive_title', 'suevafree_archive_title_function', 10, 2 );

}

?>