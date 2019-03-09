<?php


new suevafree_metaboxes (

'post', array (
	
	array(  'name' => 'Navigation',  
			'type' => 'navigation',  
			'item' => array( 
			
				'general' => esc_html__( 'General settings', 'suevafree'), 
				'quote_post' => esc_html__( 'Quote Post','suevafree'),
				'link_post' => esc_html__( 'Link Post','suevafree')
	
			),   
			
			'start' => '<ul>', 
			'end' => '</ul>'),  
	
	array(  'type' => 'begintab',
			'tab' => 'general',
			'element' =>
	
			array(  'name' => esc_html__( 'General settings','suevafree'),
					'type' => 'title',
			),
	
			array(  'name' => esc_html__( 'Template','suevafree'),
					'desc' => esc_html__( 'Select a template for this page','suevafree'),
					'id' => 'suevafree_template',
					'type' => 'select',
					'options' => array(
						'full' => esc_html__( 'Full Width','suevafree'),
						'left-sidebar' =>  esc_html__( 'Left Sidebar','suevafree'),
						'right-sidebar' => esc_html__( 'Right Sidebar','suevafree'),
				  ),
				  
					'std' => 'right-sidebar',
			
			),
	
	),
	
	array(	'type' => 'endtab'),
	
	array(  'type' => 'begintab',
			'tab' => 'quote_post',
			'element' =>
		  
			array(  'name' => esc_html__( 'Quote Post Options','suevafree'),
					'type' => 'title',
			),
				  
			array(  'name' => esc_html__( 'Quote','suevafree'),
					'desc' => esc_html__( 'Insert the text of quote','suevafree'),
					'id' => 'suevafree_quote_text',
					'type' => 'textarea',
			),
			
			array(  'name' => esc_html__( 'Author','suevafree'),
					'desc' => esc_html__( 'Insert the author of quote','suevafree'),
					'id' => 'suevafree_quote_author',
					'type' => 'text',
			),
			
	),
	
	array(	'type' => 'endtab'),
	
	array(  'type' => 'begintab',
			'tab' => 'link_post',
			'element' =>
		  
			array(  'name' => esc_html__( 'Link Post Options','suevafree'),
					'type' => 'title',
			),
			
			array(  'name' => esc_html__( 'Name of url','suevafree'),
					'desc' => esc_html__( 'Insert the name of your link','suevafree'),
					'id' => 'suevafree_url_link_name',
					'type' => 'text',
			),
			
			array(  'name' => esc_html__( 'Link','suevafree'),
					'desc' => esc_html__( 'Insert the link with http, for example http://www.wpinprogress.com','suevafree'),
					'id' => 'suevafree_url_link',
					'type' => 'text',
			),
	
	),
	
	array(	'type' => 'endtab'),
	array(	'type' => 'endtab')
	
	)
	
);

?>