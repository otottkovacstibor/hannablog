<?php 

	get_header();
	do_action( 'suevafree_top_sidebar', 'top-sidebar-area');
	do_action( 'suevafree_header_sidebar', 'header-sidebar-area');
	
?>

<div id="content" class="container content">
	
    <div class="row">
       
        <div class="<?php echo suevafree_template('span') . " " . suevafree_template('sidebar'); ?>">
        	
            <div class="row">
        
                <div <?php post_class(); ?> >
                
                    <?php 
					
						while ( have_posts() ) : the_post();
							
							do_action('suevafree_postformat', suevafree_thumb_size('single', suevafree_template('span')) );
							wp_link_pages(array('before' => '<div class="suevafree-pagination">', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>') );
						
						endwhile;
						
					?>
            
                </div>
        
			</div>
        
        </div>

		<?php 	
			
			if ( suevafree_template('span') == "col-md-8" ) : 

				do_action('suevafree_side_sidebar', 'side-sidebar-area' );
	 
			endif; 
		
		?>

    </div>
    
</div>

<?php 

	do_action( 'suevafree_full_sidebar', 'full-sidebar-area');
	get_footer(); 
	
?>