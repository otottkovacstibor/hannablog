<div id="content" class="container">

	<div class="row" id="blog">
    
        <div class="<?php echo suevafree_template('span') .' '. suevafree_template('sidebar'); ?>">

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
                                
                            <h1><?php esc_html_e( 'Not found',"suevafree" ) ?></h1>
                            <p><?php esc_html_e( 'Sorry, no posts found',"suevafree" ) ?></p>
                 
                        </article>
                
                    </div>
	
				<?php 
                    
                    endif;
                
                ?> 
        
            </div>
        
        </div>
        
		<?php do_action( 'suevafree_side_sidebar', 'home-sidebar-area'); ?>
           
    </div>

	<?php do_action( 'suevafree_pagination', 'home'); ?>

</div>