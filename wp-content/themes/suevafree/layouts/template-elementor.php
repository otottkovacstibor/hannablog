<?php

	/*
	Template Name: SuevaFree Elementor
	*/
	
	get_header();	
	do_action( 'suevafree_top_sidebar', 'top-sidebar-area');
	do_action( 'suevafree_header_sidebar', 'header-sidebar-area');
	
?>

<div id="content" class="content">
	
	<div <?php post_class(); ?> >
                
		<?php 
					
			while ( have_posts() ) : the_post();
			
				the_content();
						
			endwhile;
						
		?>
            
	</div>
    
    <div class="clear"></div>
    
</div>

<?php 

	do_action( 'suevafree_full_sidebar', 'full-sidebar-area');
	get_footer(); 
	
?>