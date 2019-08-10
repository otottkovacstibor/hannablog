<div id="content" class="container">
	
    <?php 
	
		do_action('suevafree_searched_item');
		do_action('suevafree_masonry', esc_attr(suevafree_setting('suevafree_search_layout')), 'col-md-12'); 
		
	?>

</div>
    
<?php do_action( 'suevafree_pagination', 'archive'); ?>