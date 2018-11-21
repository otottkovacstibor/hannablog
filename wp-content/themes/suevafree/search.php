<?php 

	get_header(); 

	do_action('suevafree_top_sidebar', 'top-sidebar-area');
	do_action('suevafree_header_sidebar', 'header-sidebar-area');

	if ( !suevafree_setting('suevafree_search_layout') || suevafree_setting('suevafree_search_layout') == 'masonry' ) {
				
		get_template_part('layouts/search', 'masonry'); 

	} else if ( strstr(suevafree_setting('suevafree_search_layout'), 'sidebar' )) { 

		get_template_part('layouts/search','sidebar'); 

	} else { 
		
		get_template_part('layouts/search', 'default'); 
			
	}

	do_action('suevafree_full_sidebar', 'full-sidebar-area'); 
	get_footer(); 

?>