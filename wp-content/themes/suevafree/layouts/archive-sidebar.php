<div id="content" class="container">

	<div class="row">
    
        <div class="<?php echo suevafree_template('span') .' '. suevafree_template('sidebar'); ?>">

    		<?php do_action('suevafree_archive_title'); ?> 

            <div class="row"> 
        
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                    <div <?php post_class(); ?>>
                
                        <?php do_action('suevafree_postformat', 'suevafree_thumbnail_l'); ?>
                        <div class="clear"></div>
                        
                    </div>
		
				<?php 
                
                    endwhile; 
                    else:  
                
                ?>

                    <div class="post-container col-md-12" >
                
                        <article class="post-article not-found">
                                
                            <h1><?php esc_html_e( 'Not found.',"suevafree" ) ?></h1>
                            <p><?php esc_html_e( 'Sorry, no posts matched into ',"suevafree" ) ?> <strong>: <?php single_cat_title(); ?> </strong></p>
                 
                        </article>
                
                    </div>
	
				<?php 
                    
                    endif;
                
                ?> 
        
            </div>
        
        </div>
        
		<?php do_action('suevafree_side_sidebar', 'category-sidebar-area' ); ?>
           
    </div>

	<?php do_action( 'suevafree_pagination', 'archive'); ?>

</div>