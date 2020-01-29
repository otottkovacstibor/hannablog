<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* Socials */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_socials_function')) {

	function suevafree_socials_function() {

		$allowed = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'class' => array(),
				'target' => array()
			),
			'i' => array(
				'class' => array(),
			)
		);

		$socials = array ( 
			
			"facebook" => array( "icon" => "fa fa-facebook" , "target" => "_blank" ),
			"twitter" => array( "icon" => "fa fa-twitter" , "target" => "_blank" ),
			"flickr" => array( "icon" => "fa fa-flickr" , "target" => "_blank" ),
			"linkedin" => array( "icon" => "fa fa-linkedin" , "target" => "_blank" ),
			"slack" => array( "icon" => "fa fa-slack" , "target" => "_blank" ),
			"pinterest" => array( "icon" => "fa fa-pinterest" , "target" => "_blank" ),
			"tumblr" => array( "icon" => "fa fa-tumblr" , "target" => "_blank" ),
			"soundcloud" => array( "icon" => "fa fa-soundcloud" , "target" => "_blank" ),
			"spotify" => array( "icon" => "fa fa-spotify" , "target" => "_blank" ),
			"youtube" => array( "icon" => "fa fa-youtube" , "target" => "_blank" ),
			"vimeo" => array( "icon" => "fa fa-vimeo" , "target" => "_blank" ),
			"vk" => array( "icon" => "fa fa-vk" , "target" => "_blank" ),
			"instagram" => array( "icon" => "fa fa-instagram" , "target" => "_blank" ),
			"deviantart" => array( "icon" => "fa fa-deviantart" , "target" => "_blank" ),
			"github" => array( "icon" => "fa fa-github" , "target" => "_blank" ),
			"xing" => array( "icon" => "fa fa-xing" , "target" => "_blank" ),
			"dribbble" => array( "icon" => "fa fa-dribbble" , "target" => "_blank" ),
			"dropbox" => array( "icon" => "fa fa-dropbox" , "target" => "_blank" ),
			"whatsapp" => array( "icon" => "fa fa-whatsapp" , "target" => "_self" ),
			"telegram" => array( "icon" => "fa fa-telegram" , "target" => "_self" ),
			"trello" => array( "icon" => "fa fa-trello" , "target" => "_self"),
			"twitch" => array( "icon" => "fa fa-twitch" , "target" => "_self"),
			"tripadvisor" => array( "icon" => "fa fa-tripadvisor" , "target" => "_self"),
			"vine" => array( "icon" => "fa fa-vine" , "target" => "_self"),
			"skype" => array( "icon" => "fa fa-skype" , "target" => "_self" ),
			"email" => array( "icon" => "fa fa-envelope" , "target" => "_self" ),
		
		);

		$i = 0;
		$html = '';
		
		foreach ( $socials as $k => $v) { 
		
			if ( suevafree_setting('suevafree_footer_'.$k.'_button') ): 

				$i++;	
				$html.= '<a href="'.esc_url(suevafree_setting('suevafree_footer_'.$k.'_button'), array( 'http', 'https', 'tel', 'skype', 'mailto' ) ).'" target="'.esc_attr($v['target']).'" title="'.esc_attr($k).'" class="social"> <i class="'.esc_attr($v['icon']).'" ></i></a> ';
			
			endif;
			
		}
		
		if ( suevafree_setting('suevafree_footer_rss_button') == "on" ): 
		
			$i++;	
			$html.= '<a href="'. esc_url(get_bloginfo('rss2_url')). '" title="Rss" class="social rss"><i class="fa fa-rss" ></i></a> ';
		
		endif; 
			
		if ( $i > 0 && !suevafree_setting('suevafree_footer_social_buttons') || suevafree_setting('suevafree_footer_social_buttons') == 'on' ) {
			
			echo '<div class="col-md-7">';
			echo '<div class="social-buttons">';
			echo wp_kses($html, $allowed);
			echo '</div>';
			echo '</div>';

		}
		
	}
	
	add_action( 'suevafree_socials', 'suevafree_socials_function', 10, 2 );

}

?>