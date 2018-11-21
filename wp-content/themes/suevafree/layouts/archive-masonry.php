<div class="container">
	
    <?php 
	
		do_action('suevafree_archive_title');
		do_action('suevafree_masonry', suevafree_setting('suevafree_category_layout'), 'col-md-12'); 
		
	?>

</div>
    
<?php do_action( 'suevafree_pagination', 'archive'); ?>