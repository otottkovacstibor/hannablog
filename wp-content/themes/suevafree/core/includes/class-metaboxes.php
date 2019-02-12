<?php

/**
 * This file belongs to the TIP Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'suevafree_metaboxes' ) ) {

	class suevafree_metaboxes {
	   
		public $posttype;
		public $metaboxes_fields;
	   
		public function __construct( $posttype, $fields = array() ) {
	
			$this->posttype = $posttype;
			$this->metaboxes_fields = $fields;
			
			add_action( 'add_meta_boxes', array( &$this, 'new_metaboxes' ) ); 
			add_action( 'save_post', array( &$this, 'suevafree_metaboxes_save' ) );
		}
	
		public function new_metaboxes() {
	
			$posttype = $this->posttype ;
			add_meta_box( $posttype, ucfirst($posttype).' settings', array( &$this, 'metaboxes_panel' ), $posttype, 'normal', 'high' );
		
		}
		
		public function metaboxes_panel() {
	
			$metaboxes_fields = $this->metaboxes_fields ;
	
			global $post, $post_id;
			
			foreach ($metaboxes_fields as $value) {
			switch ( $value['type'] ) { 
		
			case 'navigation': ?>
			
				<div id="tabs" class="suevafree_metaboxes">
						
					<ul>
			
						<?php 
						
							foreach ($value['item'] as $option => $name ) { 
								echo "<li class='".$option."'><a href='#".str_replace(" ", "", $option)."'>".$name."</a></li>"; 
							} 
						?>
                        
                        <li class="clear"></li>
                        
					</ul>
					   
			<?php	
					
			break;
		
			case 'begintab': ?>

				<div id="<?php echo esc_attr($value['tab']);?>">
		
			<?php	
					
			break;
		
			case 'endtab': ?>
			
				</div>
		
			<?php	
					
			break;
			
			}
			
			foreach ($value as $field) {
		
			if (isset($field['type'])) : 
		
				switch ( $field['type'] ) { 
		
					case 'start':  ?>
					<div class="postformat" id="<?php echo esc_attr($field['id']); ?>">
				
					<?php break;
					
					case 'end':  ?>
					</div>
					
					<?php break;
					
					case "on-off": ?>
				
					<div class="suevafree_inputbox">
		
						<div class="input-left">
		
							<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label>
							<p><?php echo esc_attr($field['desc']); ?></p>
                            
						</div>
						
                        <div class="input-right">
		
								<div class="bool-slider <?php if ( suevafree_postmeta($field['id']) != "") { echo stripslashes( suevafree_postmeta($field['id'])); } else { echo esc_attr($field['std']); } ?>">
									
									<div class="inset">
										<div class="control"></div>
									</div>
									
									<input name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" type="hidden" value="<?php if ( suevafree_postmeta( $field['id']) != "") { echo esc_attr(suevafree_postmeta( $field['id'])); } else { echo esc_attr($field['std']); } ?>" class="on-off" />
	
								</div>  
								
								<div class="clear"></div>      
						
						</div>	
							
						<div class="clear"></div>
						
					</div>
				
					<?php break;
		
					case 'title':  ?>

					<div class="suevafree_inputbox">

						<h3 class="title"><?php echo esc_attr($field['name']); ?></h3>
						<div class="clear"></div>

					</div>

					<?php break;
		
					case 'text':  ?>
					
					<div class="suevafree_inputbox">
						
						<div class="input-left">
						
							<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label><br />
							<em> <?php echo esc_attr($field['desc']); ?> </em>
							
						</div>
						
						<div class="input-right">
						
							<input name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" type="<?php echo esc_attr($field['type']); ?>" value="<?php if ( suevafree_postmeta( $field['id']) != "") { echo esc_attr(suevafree_postmeta( $field['id'])); } ?>" />
							
						</div>
						
						<div class="clear"></div>
					
                    </div>
				
					<?php break;
		
					case 'color':  ?>
					
					<div class="suevafree_inputbox">
						
						<div class="input-left">
						
							<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label><br />
							<em> <?php echo esc_attr($field['desc']); ?> </em>
							
						</div>
						
						<div class="input-right">
						
							<input name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" type="text" value="<?php if ( suevafree_postmeta($field['id']) != "") { echo esc_attr(suevafree_postmeta($field['id'])); } else { echo esc_attr($field['std']); } ?>" data-default-color="<?php if ( suevafree_postmeta($field['id']) != "") { echo esc_attr(suevafree_postmeta($field['id'])); } else { echo esc_attr($field['std']); } ?>" class="suevafree_color_picker" />

						</div>
						
						<div class="clear"></div>
					
                    </div>
				
					<?php break;
		
					case 'select':  ?>
					
					<div class="suevafree_inputbox">
						
						<div class="input-left">
						
							<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label>
							<em> <?php echo esc_attr($field['desc']); ?> </em>
							
						</div>
						
						<div class="input-right">
                        
							<select name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" >
								
								<?php foreach ($field['options'] as $option => $values) { ?>
								<option <?php if ( suevafree_postmeta($field['id']) == $option || ( !suevafree_postmeta($field['id']) && $field['std'] == $option )) { echo 'selected="selected"'; } ?> value="<?php echo esc_attr($option); ?>"><?php echo esc_attr($values); ?></option><?php } ?>  
                                
							</select>
                            
                            <span></span>
						
						</div>
						
						<div class="clear"></div>
					
                    </div>
                    
					<?php break;
		
					case 'taxonomy-select':  
					
					$slideshows = get_terms("slideshows");
					foreach ($slideshows as $slideshow)	
						{
							$wp_terms[$slideshow->term_id] = $slideshow->name;
						}
					?>
					
					<div class="suevafree_inputbox">
					
                    	<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label>
                        
						<select name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" >
						
                        	<option value="all"> All </option>
							<?php foreach ( $wp_terms as $option => $values) { ?>
                            <option <?php if (suevafree_postmeta( $field['id']) == $option) { echo 'selected="selected"'; } ?> value="<?php echo esc_attr($option); ?>"><?php echo esc_attr($values); ?></option><?php } ?>
						
                        </select>
						
                        <em> <?php echo esc_attr($field['desc']); ?> </em>
					
                    </div>
				
				
					<?php break;
		
					case 'textarea':  ?>
							
					<div class="suevafree_inputbox">
						
						<div class="input-left">
						
                        	<label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_attr($field['name']); ?></label><br />
							<em> <?php echo esc_attr($field['desc']); ?> </em>
						
                        </div>
						
                        <div class="input-right">
                        
                            <textarea name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" type="<?php echo esc_attr($field['type']); ?>" ><?php if ( suevafree_postmeta( $field['id']) != "") { echo stripslashes(suevafree_postmeta( $field['id'])); } ?></textarea>
						
                        </div>
						
                        <div class="clear"></div>
					
                    </div>
							
					<?php break;
				
					}
				
				endif;
				
				}
			
			}
	
		}
		
		public function suevafree_metaboxes_save() {
		
			global $post_id, $post;
				
			$metaboxes_fields = $this->metaboxes_fields ;
		
			foreach ( $metaboxes_fields as $value ) {
					
				foreach ($value as $field) {

					if ( isset($field['id']) && isset ($_POST[$field['id']] ) ) {

						$new = $_POST[$field['id']];
						update_post_meta( $post_id , $field['id'], sanitize_text_field($new) );
							
							
					}
						
				}
					
			}
				
		}
				
	}

}

?>