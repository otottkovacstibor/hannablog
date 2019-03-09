<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

new suevafree_metaboxes (

'service', array (
	
	array(  'name' => 'Navigation',  
			'type' => 'navigation',  
		  
			'item' => array( 
				'general' => esc_html__( 'General settings', 'suevafree'), 
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
						'right-sidebar' => esc_html__( 'Right Sidebar','suevafree')),
				  
					'std' => 'right-sidebar',
			
			),
			
			array(  'name' => esc_html__( 'Background color','suevafree'),
					'desc' => esc_html__( 'Choose a background color for this service.','suevafree'),
					'id' => 'suevafree_service_background',
					'type' => 'color',
					'std' => '#c44141'
			),
			
			array(  'name' => esc_html__( 'Background color at hover.','suevafree'),
					'desc' => esc_html__( 'Choose a background color at hover for this service.','suevafree'),
					'id' => 'suevafree_service_background_hover',
					'type' => 'color',
					'std' => '#993232'
			),
			
			array(  'name' => esc_html__( 'Font Awesome Icon','suevafree'),
					'desc' => esc_html__( 'Insert the name of a Font Awesome icon for this service.','suevafree'),
					'id' => 'suevafree_service_icon',
					'type' => 'text',
					'std' => 'fa-heart',
			),
	
			array(  'name' => esc_html__( 'Link','suevafree'),
					'desc' => esc_html__( 'Do you want to link the service name to the details?','suevafree'),
					'id' => 'suevafree_service_link',
					'type' => 'on-off',
					'std' => 'off',
			),
			
	),
	
	array(  'type' => 'endtab'),
	array(	'type' => 'endtab')
	
	)
	
);


?>