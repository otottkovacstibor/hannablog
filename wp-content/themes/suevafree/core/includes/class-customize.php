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

if( !class_exists( 'suevafree_customize' ) ) {

	class suevafree_customize {
	
		public $theme_fields;
	
		public function __construct( $fields = array() ) {
	
			$this->theme_fields = $fields;

			add_action ('admin_init' , array( &$this, 'admin_scripts' ) );
			add_action ('customize_register' , array( &$this, 'customize_panel' ) );
			add_action ('customize_controls_enqueue_scripts' , array( &$this, 'customize_scripts' ) );

		}

		public function admin_scripts() {
	
			global $wp_version, $pagenow;

			wp_enqueue_style( "thickbox" );
			add_thickbox();
		
			$file_dir = get_template_directory_uri() . "/core/admin/assets/";

			if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'edit.php' ) {
				wp_enqueue_style( 'wp-color-picker' ); 
			}

			wp_enqueue_style ( 'suevafree-panel',  $file_dir . 'css/panel.css' ); 
			wp_enqueue_script( 'suevafree-script', $file_dir . 'js/panel.js', array('jquery', 'wp-color-picker'),'1.0.0',TRUE ); 
			wp_enqueue_style ( 'suevafree-on_off', $file_dir . 'css/on_off.css' ); 
			wp_enqueue_script( 'suevafree-on_off', $file_dir . 'js/on_off.js', array('jquery'),'1.0.0',TRUE ); 
			wp_enqueue_script( "jquery-ui-core", array('jquery'));
			wp_enqueue_script( "jquery-ui-tabs", array('jquery'));

		}
		
		public function customize_scripts() {
	
			wp_enqueue_style ( 
				'suevafree-customizer', 
				get_template_directory_uri() . '/core/admin/assets/css/customize.css', 
				array(), 
				''
			);
	
		}
		
		public function customize_panel ( $wp_customize ) {
			
			global $wp_version;

			$theme_panel = $this->theme_fields ;
	
			foreach ( $theme_panel as $element ) {
				
				switch ( $element['type'] ) {
						
					case 'panel' :
					
						$wp_customize->add_panel( $element['id'], array(
						
							'title' => $element['title'],
							'priority' => $element['priority'],
							'description' => $element['description'],
						
						) );
				 
					break;
					
					case 'section' :
							
						$wp_customize->add_section( $element['id'], array(
						
							'title' => $element['title'],
							'panel' => $element['panel'],
							'priority' => $element['priority'],
							'description' => $element['description'],

						));
						
					break;
	
					case 'text' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => 'sanitize_text_field',
							'default' => $element['std'],
	
						) );
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						) );
								
					break;
	
					case 'pixel_size' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'default' => $element['std'],
							'sanitize_callback' => array( &$this, 'pixel_size_sanize' ),

						));
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => 'text',
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'number' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => 'absint',
							'default' => $element['std'],
	
						));
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'upload' :
								
						$wp_customize->add_setting( $element['id'], array(
	
							'default' => $element['std'],
							'capability' => 'edit_theme_options',
							'sanitize_callback' => array( &$this, 'image_upload_sanize' )
	
						) );
	
						$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, $element['id'], array(
						
							'label' => $element['label'],
							'mime_type' => 'image',
							'description' => $element['description'],
							'section' => $element['section'],
							'settings' => $element['id'],
						
						)));
	
					break;
	
					case 'url' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => 'esc_url_raw',
							'default' => $element['std'],
	
						) );
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'color' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => 'sanitize_hex_color',
							'default' => $element['std'],
	
						));
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'button' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => array( &$this, 'button_sanize' ),
							'default' => $element['std'],
	
						));
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => 'url',
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'textarea' :
								
						$wp_customize->add_setting( $element['id'], array(
						
							'sanitize_callback' => 'sanitize_textarea_field',
							'default' => $element['std'],
	
						) );
												 
						$wp_customize->add_control( $element['id'] , array(
						
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
										
						));
								
					break;
	
					case 'select' :
								
						$wp_customize->add_setting( $element['id'], array(
	
							'sanitize_callback' => array( &$this, 'select_sanize' ),
							'default' => $element['std'],
	
						) );
	
						$wp_customize->add_control( $element['id'] , array(
							
							'type' => $element['type'],
							'section' => $element['section'],
							'label' => $element['label'],
							'description' => $element['description'],
							'choices'  => $element['options'],
										
						));
								
					break;

					case 'suevafree-customize-info' :
	
						$wp_customize->add_section( $element['id'], array(
						
							'title' => $element['title'],
							'priority' => $element['priority'],
							'capability' => 'edit_theme_options',
	
						));
	
						$wp_customize->add_setting(  $element['id'], array(
							'sanitize_callback' => 'esc_url_raw'
						));
						 
						$wp_customize->add_control( new Suevafree_Customize_Info_Control( $wp_customize,  $element['id'] , array(
							'section' => $element['section'],
						)));		
											
					break;

				}
				
			}

			if ( suevafree_is_woocommerce_active() ) :
			
				$wp_customize->remove_control( 'woocommerce_catalog_rows');
				$wp_customize->remove_control( 'woocommerce_catalog_columns');
				
			endif;
	
			if (!suevafree_is_woocommerce_active())
				$wp_customize->remove_control( 'suevafree_woocommerce_category_layout');

	   }
	
		public function select_sanize ($value, $setting) {

			global $wp_customize;
			
			$control = $wp_customize->get_control( $setting->id );
		 
			if ( array_key_exists( $value, $control->choices ) ) {
			
				return $value;
			
			} else {
			
				return $setting->default;
			
			}

		}
	
		public function button_sanize ( $value, $setting ) {
			
			$sanize = array (
			
				'suevafree_footer_email_button' => 'mailto:',
				'suevafree_footer_skype_button' => 'skype:',
				'suevafree_footer_whatsapp_button' => 'tel:',
			
			);
	
			if (!isset($value) || $value == '' || $value == $sanize[$setting->id]) {

				return '';

			} elseif (!strstr($value, $sanize[$setting->id])) {
	
				return $sanize[$setting->id] . $value;
	
			} else {
	
				return esc_url_raw($value, array('mailto', 'skype', 'tel'));
	
			}
	
		}
		
		public function image_upload_sanize ( $file, $setting ) {

			$mimes = array (
				'jpg|jpeg|jpe' 	=> 'image/jpeg',
				'gif' 			=> 'image/gif',
				'png' 			=> 'image/png',
			);
			
			$file_ext = wp_check_filetype ($file, $mimes );
			return	$file_ext['ext'] ? $file : $setting->default;

		}

		public function pixel_size_sanize($value) {
			
			return absint(str_replace('px', '', $value)) . 'px';
	
		}
		
	}

}

if ( class_exists( 'WP_Customize_Control' ) ) {

	class Suevafree_Customize_Info_Control extends WP_Customize_Control {

		public $type = "suevafree-customize-info";

		public function render_content() { ?>

            <div class="inside">

				<h2><?php esc_html_e('Upgrade to Sueva Premium.','suevafree');?></h2> 

                <p><?php esc_html_e("Upgrade to the premium version of Sueva, to enable 600+ Google Fonts, Unlimited sidebars, Portfolio section and much more.","suevafree");?></p>

                <ul>
                
                    <li><a class="button purchase-button" href="<?php echo esc_url( 'https://www.themeinprogress.com/sueva/?ref=2&campaign=sueva-panel' ); ?>" title="<?php esc_attr_e('Upgrade to Sueva premium','suevafree');?>" target="_blank"><?php esc_html_e('Upgrade to Sueva premium','suevafree');?></a></li>
                
                </ul>

            </div>

            <div class="inside">

                <h2><?php esc_html_e('SuevaFree child themes.','suevafree');?></h2> 
    
                <p><?php esc_html_e("You can also choose one of available premium child themes for SuevaFree","suevafree");?></p>

                <ul>
                
                    <li><a class="button purchase-button" href="<?php echo esc_url( 'https://www.themeinprogress.com/products-tag/suevafree-wordpress-child-themes/?ref=2&campaign=sueva-panel' ); ?>" title="<?php esc_attr_e('SuevaFree child themes','suevafree');?>" target="_blank"><?php esc_html_e('SuevaFree child themes','suevafree');?></a></li>
                
                </ul>

            </div>

            <div class="inside">

                <h2><?php esc_html_e('WordPress Support Service.','suevafree');?></h2> 
    
                <p><?php esc_html_e("We are excited to announce the opening of our new Premium WordPress Support Service, to solve all technical issues like code issues, white screen issues, theme and plugin issues and much more.","suevafree");?></p>

                <ul>
                
                    <li><a class="button purchase-button" href="<?php echo esc_url( 'https://www.themeinprogress.com/products/wordpress-services/?ref=2&campaign=sueva-panel' ); ?>" title="<?php esc_attr_e('WordPress Support','suevafree');?>" target="_blank"><?php esc_html_e('WordPress Support','suevafree');?></a></li>
                
                </ul>

            </div>

            <div class="inside">

                <h2><?php esc_html_e('SuevaFree Support','suevafree');?></h2> 

                <p><?php _e("If you've opened a new support ticket from <strong>WordPress.org</strong>, please send a reminder to <strong>support@wpinprogress.com</strong>, to get a faster reply.","suevafree");?></p>

                <ul>
                
                    <li><a class="button" href="<?php echo esc_url( 'https://wordpress.org/support/theme/'.get_stylesheet() ); ?>" title="<?php esc_attr_e('Open a new ticket','suevafree');?>" target="_blank"><?php esc_html_e('Open a new ticket','suevafree');?></a></li>
                    <li><a class="button" href="<?php echo esc_url( 'mailto:support@wpinprogress.com' ); ?>" title="<?php esc_attr_e('Send a reminder','suevafree');?>" target="_blank"><?php esc_html_e('Send a reminder','suevafree');?></a></li>
                
                </ul>
    

                <p><?php _e("If you like this theme and support, <strong>I'd appreciate</strong> any of the following:","suevafree");?></p>

                <ul>
                
                    <li><a class="button" href="<?php echo esc_url( 'https://wordpress.org/support/view/theme-reviews/'.get_stylesheet().'#postform' ); ?>" title="<?php esc_attr_e('Rate this Theme','suevafree');?>" target="_blank"><?php esc_html_e('Rate this Theme','suevafree');?></a></li>
                    <li><a class="button" href="<?php echo esc_url( 'https://www.facebook.com/WpInProgress' ); ?>" title="<?php esc_attr_e('Like on Facebook','suevafree');?>" target="_blank"><?php esc_html_e('Like on Facebook','suevafree');?></a></li>
                    <li><a class="button" href="<?php echo esc_url( 'http://eepurl.com/SknoL' ); ?>" title="<?php esc_attr_e('Subscribe our newsletter','suevafree');?>" target="_blank"><?php esc_html_e('Subscribe our newsletter','suevafree');?></a></li>
                
                </ul>
    
            </div>
    
		<?php

		}
	
	}

}

?>