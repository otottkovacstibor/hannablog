<?php 

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* LAYOUT 1 */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_before_content_1_function')) {

	function suevafree_before_content_1_function() {
		
		if ( !suevafree_is_single() ) {

			do_action('suevafree_post_title', 'blog' ); 

		} else {

			if ( !suevafree_is_woocommerce_active('is_cart') ) :
	
				if ( suevafree_is_single() && !is_page_template() ) :
							 
					do_action('suevafree_post_title', 'single');
							
				else :
					
					do_action('suevafree_post_title', 'blog'); 
							 
				endif;
	
			endif;

		}
		
		do_action('suevafree_post_details');
		
	} 
	
	add_action( 'suevafree_before_content_1', 'suevafree_before_content_1_function', 10, 2 );

}

/*-----------------------------------------------------------------------------------*/
/* LAYOUT 2 */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_before_content_2_function')) {

	function suevafree_before_content_2_function() {
		
		if ( has_category() ) :

			echo '<span class="entry-category">';
			the_category(' . ');
			echo '</span>';
	
		endif;
		
		if ( !suevafree_is_single() ) {

			do_action('suevafree_post_title', 'blog' ); 

		} else {

			if ( !suevafree_is_woocommerce_active('is_cart') ) :
	
				if ( suevafree_is_single() && !is_page_template() ) :
							 
					do_action('suevafree_post_title', 'single');
							
				else :
					
					do_action('suevafree_post_title', 'blog'); 
							 
				endif;
	
			endif;

		}

		echo '<span class="entry-date">' . esc_html__('On ','suevafree') . esc_html(get_the_date()) . esc_html__(' by ','suevafree') . wp_kses_post(get_the_author_posts_link()) . '</span>';

	} 
	
	add_action( 'suevafree_before_content_2', 'suevafree_before_content_2_function', 10, 2 );

}

/*-----------------------------------------------------------------------------------*/
/* LAYOUT 3 */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_before_content_3_function')) {

	function suevafree_before_content_3_function() {
		
		if ( !suevafree_is_single() ) {

			do_action('suevafree_post_title', 'blog' ); 

		} else {

			if ( !suevafree_is_woocommerce_active('is_cart') ) :
	
				if ( suevafree_is_single() && !is_page_template() ) :
							 
					do_action('suevafree_post_title', 'single');
							
				else :
					
					do_action('suevafree_post_title', 'blog'); 
							 
				endif;
	
			endif;

		}

	} 
	
	add_action( 'suevafree_before_content_3', 'suevafree_before_content_3_function', 10, 2 );

}

/*-----------------------------------------------------------------------------------*/
/* LAYOUT 4 */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_before_content_4_function')) {

	function suevafree_before_content_4_function() {
		
		if ( !suevafree_is_single() ) {

			do_action('suevafree_post_title', 'blog' ); 

		} else {

			if ( !suevafree_is_woocommerce_active('is_cart') ) :
	
				if ( suevafree_is_single() && !is_page_template() ) :
							 
					do_action('suevafree_post_title', 'single');
							
				else :
					
					do_action('suevafree_post_title', 'blog'); 
							 
				endif;
	
			endif;

		}
		
		echo '<div class="line"></div>';

	} 
	
	add_action( 'suevafree_before_content_4', 'suevafree_before_content_4_function', 10, 2 );

}

?>