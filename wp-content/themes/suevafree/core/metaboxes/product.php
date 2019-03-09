<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

new suevafree_metaboxes (

'product', array (
	
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
	
	),
	
	array(  'type' => 'endtab'),
	array(	'type' => 'endtab')
	
	)
	
);

?>