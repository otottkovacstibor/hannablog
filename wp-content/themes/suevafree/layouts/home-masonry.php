<div id="content" class="container masonry-container">
	
	<?php 
	
		do_action('suevafree_masonry', esc_attr(suevafree_setting('suevafree_home')), 'col-md-12'); 
		do_action( 'suevafree_pagination', 'home'); 
	
	?>

</div>