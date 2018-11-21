<?php 

	get_header();

	do_action('suevafree_top_sidebar', 'top-sidebar-area'); 
	do_action('suevafree_header_sidebar', 'header-sidebar-area'); 

	if ( !suevafree_setting('suevafree_category_layout') || suevafree_setting('suevafree_category_layout') == 'masonry' ) {
				
		get_template_part('layouts/archive', 'masonry'); 
	
	} else if ( strstr(suevafree_setting('suevafree_category_layout'), 'sidebar' )) { 

		get_template_part('layouts/archive','sidebar'); 

	} else { 
		
		get_template_part('layouts/archive', 'default'); 
			
	}

	do_action('suevafree_full_sidebar', 'full-sidebar-area'); 

	get_footer(); 

?>