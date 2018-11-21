<?php 

	get_header();
	
	do_action('suevafree_top_sidebar', 'home-top-sidebar-area');
	do_action('suevafree_header_sidebar', 'home-header-sidebar-area');

	if ( !suevafree_setting('suevafree_home') || suevafree_setting('suevafree_home') == 'masonry' ) {
				
		get_template_part('layouts/home', 'masonry'); 

	} else if ( strstr(suevafree_setting('suevafree_home'), 'sidebar' )) { 

		get_template_part('layouts/home','sidebar'); 

	} else { 
		
		get_template_part('layouts/home', 'default'); 
			
	}

	do_action('suevafree_full_sidebar', 'full-sidebar-area');

	get_footer(); 
	
?>