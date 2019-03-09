<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('suevafree_pagination_function')) {

	function suevafree_pagination_function( $type ) {
		
		global $wp_query,$post;
		
		$big = 999999999; 

		$allowed = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'class' => array(),
				'target' => array()
			),
			'span' => array(
				'class' => array(),
			)
		);

		if ( $type == "archive" ) { 
		
			$paginate_links = paginate_links( array(
				
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'prev_text'    => '&laquo;',
				'next_text'    => '&raquo;',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $wp_query->max_num_pages,
				'add_args' => false,
			
			));
		
		} else if ( $type == "home" ) { 
	
			$total = $wp_query->max_num_pages ; 
			$paginate_links = paginate_links( array(
				
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'prev_text'    => '&laquo;',
				'next_text'    => '&raquo;',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $total,
				'add_args' => false,
			
			));
		
		} else if ( $type == "page" ) { 
	
			$args = array('post_type' => 'post', 'paged' => suevafree_paged(), 'posts_per_page' => get_option('posts_per_page'));
			$query = new WP_Query( $args );
			
			$paginate_links = paginate_links( array(
			
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'prev_text'    => '&laquo;',
				'next_text'    => '&raquo;',
				'current' => suevafree_paged(),
				'total' => $query->max_num_pages ,
				'add_args' => false,
			
			));
	
		}
	
		if (!empty($paginate_links)) 
			echo '<div class="wp-pagenavi">' . wp_kses($paginate_links, $allowed) .'</div>';
		
	} 

	add_action( 'suevafree_pagination', 'suevafree_pagination_function', 10, 2 );

}

?>