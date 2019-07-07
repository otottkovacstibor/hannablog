<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

define( 'SUEVAFREE_MIN_PHP_VERSION', '5.3' );

/*-----------------------------------------------------------------------------------*/
/* Switches back to the previous theme if the minimum PHP version is not met */
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'suevafree_check_php_version' ) ) {

	function suevafree_check_php_version() {
	
		if ( version_compare( PHP_VERSION, SUEVAFREE_MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', 'suevafree_min_php_not_met_notice' );
			switch_theme( get_option( 'theme_switched' ));
			return false;
	
		};
	}

	add_action( 'after_switch_theme', 'suevafree_check_php_version' );

}

/*-----------------------------------------------------------------------------------*/
/* An error notice that can be displayed if the Minimum PHP version is not met */
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'suevafree_min_php_not_met_notice' ) ) {

	function suevafree_min_php_not_met_notice() {
		?>
		<div class="notice notice-error is_dismissable">
			<p>
				<?php esc_html_e('You need to update your PHP version to run this theme.', 'suevafree' ); ?><br />
				<?php
				printf(
					esc_html__( 'Actual version is: %1$s, required version is: %2$s.', 'suevafree' ),
					PHP_VERSION,
					SUEVAFREE_MIN_PHP_VERSION
				);
				?>
			</p>
		</div>
		<?php
	
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Woocommerce is active */
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'suevafree_is_woocommerce_active' ) ) {
	
	function suevafree_is_woocommerce_active( $type = "" ) {
	
        global $woocommerce;

        if ( isset( $woocommerce ) ) {
			
			if ( !$type || call_user_func($type) ) {
            
				return true;
			
			}
			
		}
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* IS SINGLE */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_is_single')) {

	function suevafree_is_single() {
		
		if ( is_single() || is_page() || is_singular('portfolio') ) :
		
			return true;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* GET ARCHIVE TITLE */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_get_the_archive_title')) {

	function suevafree_get_archive_title() {
		
		if ( is_category() ) {
			$title = sprintf( esc_html__( 'Category: %s', 'suevafree' ), single_cat_title( '', false ) );
		} elseif ( is_tag() ) {
			$title = sprintf( esc_html__( 'Tag: %s', 'suevafree' ), single_tag_title( '', false ) );
		} elseif ( is_author() ) {
			$title = sprintf( esc_html__( 'Author: %s', 'suevafree' ), '<span class="vcard">' . get_the_author() . '</span>' );
		} elseif ( is_year() ) {
			$title = sprintf( esc_html__( 'Year: %s', 'suevafree' ), get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'suevafree' ) ) );
		} elseif ( is_month() ) {
			$title = sprintf( esc_html__( 'Month: %s', 'suevafree' ), get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'suevafree' ) ) );
		} elseif ( is_day() ) {
			$title = sprintf( esc_html__( 'Day: %s', 'suevafree' ), get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'suevafree' ) ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = esc_html_x( 'Asides', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = esc_html_x( 'Galleries', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = esc_html_x( 'Images', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = esc_html_x( 'Videos', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = esc_html_x( 'Quotes', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = esc_html_x( 'Links', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = esc_html_x( 'Statuses', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = esc_html_x( 'Audio', 'post format archive title', 'suevafree' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = esc_html_x( 'Chats', 'post format archive title', 'suevafree' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = sprintf( esc_html__( 'Archives: %s', 'suevafree' ), post_type_archive_title( '', false ) );
		} elseif ( is_tax() ) {
			$tax = get_taxonomy( get_queried_object()->taxonomy );
			$title = sprintf( esc_html__( '%1$s: %2$s', 'suevafree' ), $tax->labels->singular_name, single_term_title( '', false ) );
		}
	
		if ( isset($title) )  :
			return $title;
		else:
			return false;
		endif;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* Theme settings */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_setting')) {

	function suevafree_setting($id, $default = '' ) {
	
		$suevafree_setting = get_theme_mod($id);
		
		if($suevafree_setting):
		
			return $suevafree_setting;
		
		else:
		
			return $default;
		
		endif;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* Post meta */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_postmeta')) {

	function suevafree_postmeta( $id, $default = '' ) {
	
		global $post, $wp_query;
		
		if (suevafree_is_woocommerce_active('is_shop')) :
	
			$content_ID = get_option('woocommerce_shop_page_id');
	
		else :
	
			$content_ID = $post->ID;
	
		endif;

		$val = get_post_meta( $content_ID , $id, TRUE);
		
		if ( !empty($val) ) :
			
			return $val;
			
		else:
				
			return $default;
			
		endif;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* ALLOWED PROTOCOLS */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_kses_allowed_protocols')) {

	function suevafree_kses_allowed_protocols($protocols) {
		
		$protocols[] = 'skype';
		return $protocols;
	
	}

	add_filter( 'kses_allowed_protocols', 'suevafree_kses_allowed_protocols');

}

/*-----------------------------------------------------------------------------------*/
/* RESPONSIVE EMBED */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_embed_html')) {
	
	function suevafree_embed_html( $html ) {
		return '<div class="embed-container">' . $html . '</div>';
	}
	 
	add_filter( 'embed_oembed_html', 'suevafree_embed_html', 10, 3 );
	add_filter( 'video_embed_html', 'suevafree_embed_html' );
	
}

/*-----------------------------------------------------------------------------------*/
/* BODY CLASSES */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_body_classes_function')) {

	function suevafree_body_classes_function($classes) {

		global $wp_customize;

		if ( isset( $wp_customize ) ) :

			$classes[] = 'customizer_active';
				
		endif;

		if ( suevafree_setting('suevafree_menu_layout') == "menubar" ) :
				
			$classes[] = 'menubar';
	
		endif;

		if ( suevafree_setting('suevafree_header_layout') == "header_layout_2" ) :
				
			$classes[] = 'scroll_header';
	
		endif;

		if ( suevafree_setting('suevafree_view_footer') == "off" ) :
				
			$classes[] = 'hide_footer';
	
		endif;

		if ( suevafree_setting('suevafree_readmore_layout') == "sneak" ) :
				
			$classes[] = 'sneak_button';
	
		endif;

		if ( suevafree_setting('suevafree_body_layout') == "minimal" ) :
				
			$classes[] = 'minimal_layout';
	
		endif;
		
		if ( suevafree_setting('suevafree_footer_layout') <> '' ) :
				
			$classes[] = esc_attr(suevafree_setting('suevafree_footer_layout'));
	
		endif;
		
		if ( suevafree_setting('suevafree_thumb_triangle') == "on" ) :
				
			$classes[] = 'thumb_triangle_off';
	
		endif;

		if ( suevafree_setting('suevafree_thumb_hover') == "on" ) :
				
			$classes[] = 'thumb_hover_off';
	
		endif;
		
		if ( suevafree_setting('suevafree_nicescroll') == "on" ) :
				
			$classes[] = 'nicescroll';
	
		endif;
		
		if ( suevafree_setting('suevafree_disable_box_shadow') == "on" ) :
				
			$classes[] = 'disable_box_shadow';
	
		endif;
		
		if ( suevafree_setting('suevafree_enable_dropcap') == "on" ) :
				
			$classes[] = 'enable_dropcap';
	
		endif;
		
		return $classes;
	
	}
	
	add_filter('body_class', 'suevafree_body_classes_function');

}

/*-----------------------------------------------------------------------------------*/
/* POST CLASSES */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('suevafree_post_class')) {

	function suevafree_post_class($classes) {	

		$masonry  = 'post-container masonry-item col-md-4';
		$standard = 'post-container col-md-12';

		if ( !suevafree_is_single() ) {

			if ( is_home() ) {
				
				if ( !suevafree_setting('suevafree_home') || suevafree_setting('suevafree_home') == "masonry" ) {
	
					$classes[] = $masonry;
	
				} else {
	
					$classes[] = $standard;
	
				}
				
			} else if ( is_archive() ) {
	
				if ( !suevafree_setting('suevafree_category_layout') || suevafree_setting('suevafree_category_layout') == "masonry" ) {
	
					$classes[] = $masonry;
	
				} else {
	
					$classes[] = $standard;
	
				}
				
			} else if ( is_search() ) {
				
				if ( !suevafree_setting('suevafree_search_layout') || suevafree_setting('suevafree_search_layout') == "masonry" ) {
	
					$classes[] = $masonry;
	
				} else {
	
					$classes[] = $standard;
	
				}
			
			}

		} else if ( suevafree_is_single() && suevafree_is_woocommerce_active('is_cart') ) {
		
			$classes[] = 'post-container col-md-12 woocommerce_cart_page';

		} else if ( suevafree_is_single() && !suevafree_is_woocommerce_active('is_product') ) {

			$classes[] = 'post-container col-md-12';

		} else if ( is_page() ) {

			$classes[] = 'full';

		}

		return $classes;
		
	}
	
	add_filter('post_class', 'suevafree_post_class');

}

/*-----------------------------------------------------------------------------------*/
/* Content template */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_template')) {

	function suevafree_template($id) {
	
		$template = array ( 
		
			"full" => "col-md-12" , 
			"left-sidebar" => "col-md-8" , 
			"right-sidebar" => "col-md-8"
		
		);
	
		$span = $template["right-sidebar"];
		$sidebar =  "right-sidebar";

		if ( suevafree_is_woocommerce_active('is_woocommerce') && ( suevafree_is_woocommerce_active('is_product_category') || suevafree_is_woocommerce_active('is_product_tag') ) && suevafree_setting('suevafree_woocommerce_category_layout') ) {
		
			$span = $template[esc_attr(suevafree_setting('suevafree_woocommerce_category_layout'))];
			$sidebar =  esc_attr(suevafree_setting('suevafree_woocommerce_category_layout'));

		} else if ( suevafree_is_woocommerce_active('is_woocommerce') && is_search() && suevafree_postmeta('suevafree_template') ) {
					
			$span = $template[esc_attr(suevafree_postmeta('suevafree_template'))];
			$sidebar =  esc_attr(suevafree_postmeta('suevafree_template'));
	
		} else if ( ( is_single() || suevafree_is_woocommerce_active('is_shop') ) && suevafree_postmeta('suevafree_template') ) {
					
			$span = $template[esc_attr(suevafree_postmeta('suevafree_template'))];
			$sidebar =  esc_attr(suevafree_postmeta('suevafree_template'));

		} else if ( ! suevafree_is_woocommerce_active('is_woocommerce') && ( is_category() || is_tag() || is_month() ) && suevafree_setting('suevafree_category_layout') ) {

			$span = $template[esc_attr(suevafree_setting('suevafree_category_layout'))];
			$sidebar =  esc_attr(suevafree_setting('suevafree_category_layout'));
						
		} else if ( is_home() && suevafree_setting('suevafree_home') ) {
					
			$span = $template[esc_attr(suevafree_setting('suevafree_home'))];
			$sidebar =  esc_attr(suevafree_setting('suevafree_home'));

		} else if ( ! suevafree_is_woocommerce_active('is_woocommerce') && is_search() && suevafree_setting('suevafree_search_layout') ) {

			$span = $template[esc_attr(suevafree_setting('suevafree_search_layout'))];
			$sidebar =  esc_attr(suevafree_setting('suevafree_search_layout'));
						
		} else if ( suevafree_is_woocommerce_active('is_shop') ) {
			
			if ( strstr(get_page_template_slug( wc_get_page_id('shop') ), 'left' )) { 

				$span = $template["left-sidebar"];
				$sidebar =  "left-sidebar";
		
			} else if ( strstr(get_page_template_slug( wc_get_page_id('shop') ), 'right' )) {
				
				$span = $template["right-sidebar"];
				$sidebar =  "right-sidebar";
					
			} else {
				
				$span = $template["full"];
				$sidebar =  'full';
					
			}

		}

		return ${$id};
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* SIDEBAR */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_sidebar_list')) {

	function suevafree_sidebar_list( $sidebar_type) {
	
		$suevafree_sidebars = get_option(suevafree_themename());

		if ( $sidebar_type == "side" || $sidebar_type == "onepage" ) :

			$default = array( $sidebar_type."-sidebar-area" => "Default" );

		else:

			$default = array("none" => "None", $sidebar_type."-sidebar-area" => "Default");

		endif;
		
		return $default;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* GET PAGED */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_paged')) {

	function suevafree_paged() {
		
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		} else {
			$paged = 1;
		}
		
		return $paged;
		
	}

}

/*-----------------------------------------------------------------------------------*/
/* Prettyphoto at post gallery */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('suevafree_prettyPhoto')) {

	function suevafree_prettyPhoto( $html, $id, $size, $permalink, $icon, $text ) {
		
		if ( ! $permalink )
			return str_replace( '<a', '<a data-rel="prettyPhoto" ', $html );
		else
			return $html;
	}

	add_filter( 'wp_get_attachment_link', 'suevafree_prettyPhoto', 10, 6);

}

/*-----------------------------------------------------------------------------------*/
/* Excerpt more */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('suevafree_hide_excerpt_more')) {

	function suevafree_hide_excerpt_more() {
		return '';
	}

	add_filter('the_content_more_link', 'suevafree_hide_excerpt_more');
	add_filter('excerpt_more', 'suevafree_hide_excerpt_more');

}

/*-----------------------------------------------------------------------------------*/
/* Customize excerpt more */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('suevafree_customize_excerpt_more')) {

	function suevafree_customize_excerpt_more( $excerpt ) {
	
		global $post;

		if ( suevafree_is_single() ) :

			return $excerpt;

		else:

			$allowed = array(
				'span' => array(
					'class' => array(),
				),
			);
	
			$class = 'button ' . esc_attr(suevafree_setting('suevafree_readmore_layout'));
			$button = esc_html__('Read More','suevafree');
			$container = 'class="read-more ' . esc_attr(suevafree_setting('suevafree_readmore_align')) . '"';
	
			if ( suevafree_setting('suevafree_readmore_layout') == "default" || !suevafree_setting('suevafree_readmore_layout') ) : 
			
				$class = 'button default';
				$button = esc_html__('Read More','suevafree');
				$container = 'class="read-more ' . esc_attr(suevafree_setting('suevafree_readmore_align')) . '"';
	
			else :
	
				$class = 'nobutton';
				$button = ' [&hellip;] ';
				$container = '';
	
			endif;
		
			if ( 
				( $pos=strpos($post->post_content, '<!--more-->') ) && 
				!has_excerpt($post->ID)
			): 
				
				$content = apply_filters( 'the_content', get_the_content());
			
			else:
				
				$content = $excerpt;
		
			endif;
	
			return $content. '<a '. wp_kses($container, $allowed) . ' href="' . esc_url(get_permalink($post->ID)) . '" title="'.esc_attr__('Read More','suevafree').'"> <span class="'.esc_attr($class).'">'.$button.'</span></a>';

		endif;
		

	}
	
	add_filter( 'get_the_excerpt', 'suevafree_customize_excerpt_more' );

}

/*-----------------------------------------------------------------------------------*/
/* Remove category list rel */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('suevafree_remove_category_list_rel')) {

	function suevafree_remove_category_list_rel($output) {
		$output = str_replace('rel="category"', '', $output);
		return $output;
	}

	add_filter('wp_list_categories', 'suevafree_remove_category_list_rel');
	add_filter('the_category', 'suevafree_remove_category_list_rel');

}

/*-----------------------------------------------------------------------------------*/
/* Remove thumbnail dimensions */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_remove_thumbnail_dimensions')) {

	function suevafree_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
	
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
		return $html;
	
	}

	add_filter( 'post_thumbnail_html', 'suevafree_remove_thumbnail_dimensions', 10, 3 );

}
  
/*-----------------------------------------------------------------------------------*/
/* Remove css gallery */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_my_gallery_style')) {

	function suevafree_my_gallery_style() {
		return "<div class='gallery'>";
	}

	add_filter( 'gallery_style', 'suevafree_my_gallery_style', 99 );

}

/*-----------------------------------------------------------------------------------*/
/* POST ICON */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_posticon')) {

	function suevafree_posticon() {
	
		$icons = array ( 
			'video' => 'fa fa-play' , 
			'gallery' => 'fa fa-camera' , 
			'audio' => 'fa fa-volume-up' , 
			'chat' => 'fa fa-users', 
			'status' => 'fa fa-keyboard-o', 
			'image' => 'fa fa-picture-o' ,
			'quote' => 'fa fa-quote-left', 
			'link' => 'fa fa-external-link', 
			'aside' => 'fa fa-file-text-o', 
		);
	
		if ( get_post_format() ) { 
		
			$icon = '<span class="post-icon"><i class="'.$icons[get_post_format()].'"></i><span>' . ucfirst( strtolower( get_post_format() )) . '</span></span>'; 
		
		} else {
		
			$icon = '<span class="post-icon"><i class="fa fa-pencil-square-o"></i><span>' . esc_html__( "Article","suevafree") . '</span></span>'; 
		
		}

		return $icon;
	
	}

}

/*-----------------------------------------------------------------------------------*/
/* WIDGETS WITHOUT PADDING */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('suevafree_widget_class')) {

	function suevafree_widget_class( $params ) {
		
		$name = $params[0]['widget_name'];
		$id = $params[0]['id'];

		/*-----------------------------------------------------------------------------------*/
		/* SIDE SIDEBAR AREA */
		/*-----------------------------------------------------------------------------------*/   

		if ( in_array( $id, array("side-sidebar-area", "home-sidebar-area", "category-sidebar-area", "search-sidebar-area")) && suevafree_setting('suevafree_sidebar_layout') == "sneak" )
			$params[0]['before_widget'] = preg_replace( '/class="widget-box/', 'class="post-article', $params[0]['before_widget'], 1 );

		/*-----------------------------------------------------------------------------------*/
		/* NO PADDING */
		/*-----------------------------------------------------------------------------------*/   

		$check = array(
		
			"Instagram Slider",
			"SuevaFree Call To Action",
			"SuevaFree Team Slideshow",
			"SuevaFree Testimonial Slideshow",
			"SuevaFree News Slideshow",
			"SuevaFree Services Widget",
			"SuevaFree Contact Form Widget",
			"SuevaFree Counter Widget"
			
		);

		if ( in_array( $name, $check ) )
			$params[0]['before_widget'] = preg_replace( '/class="post-article/', 'class="no-padding', $params[0]['before_widget'], 1 );

		return $params;
		
	}

	add_filter( 'dynamic_sidebar_params', 'suevafree_widget_class' );

}

/*-----------------------------------------------------------------------------------*/
/* THUMBNAIL SIZE */
/*-----------------------------------------------------------------------------------*/         

if (!function_exists('suevafree_thumb_size')) {

	function suevafree_thumb_size($section, $span) {

		$thumbnails = array(

			'single_col-md-12' => 'suevafree_thumbnail',
			'single_col-md-8' => 'suevafree_thumbnail_l',
			
		);
		
		return $thumbnails[$section . '_' . $span];
	
	}

}

/*-----------------------------------------------------------------------------------*/ 
/* STYLES AND SCRIPTS */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_scripts_styles')) {

	function suevafree_scripts_styles() {
	
		wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), '3.3.7' );
		wp_enqueue_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', array(), '4.7.0' );
		wp_enqueue_style('prettyPhoto', get_template_directory_uri() . '/assets/css/prettyPhoto.css', array(), '3.1.6' );
		wp_enqueue_style('suevafree-minimal-layout', get_template_directory_uri() . '/assets/css/minimal-layout.css', array(), '1.0.0' );
		wp_enqueue_style('suevafree-template', get_template_directory_uri() . '/assets/css/template.css', array(), '1.0.0' );
		wp_enqueue_style('suevafree-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array(), '1.0.0' );

		$fonts_args = array(
			'family' =>	str_replace('|', '%7C','Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic|Raleway:400,800,900,700,600,500,300,200,100|Allura'),
			'subset' =>	'latin,greek,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic'
		);
		
		wp_enqueue_style( 'suevafree_google_fonts', add_query_arg ( $fonts_args, "https://fonts.googleapis.com/css" ), array(), null );

		$header_layout = esc_attr(suevafree_setting( 'suevafree_header_layout', 'header_layout_1'));
		wp_enqueue_style( 'suevafree-' . $header_layout , get_template_directory_uri() . '/assets/css/header/' . $header_layout . '.css' ); 

		if ( get_theme_mod('suevafree_skin') ) 
			wp_enqueue_style( 'suevafree-' . get_theme_mod('suevafree_skin') , get_template_directory_uri() . '/assets/skins/' . get_theme_mod('suevafree_skin') . '.css' ); 
		
		wp_enqueue_script('jquery-easing', get_template_directory_uri() . '/assets/js/jquery.easing.js' , array('jquery'), '1.3', TRUE ); 
		wp_enqueue_script('jquery-nicescroll', get_template_directory_uri() . '/assets/js/jquery.nicescroll.js' , array('jquery'), '3.7.6', TRUE ); 
		wp_enqueue_script('jquery.scrollTo', get_template_directory_uri() . '/assets/js/jquery.scrollTo.js' , array('jquery'), '2.1.2', TRUE ); 
		wp_enqueue_script('prettyPhoto', get_template_directory_uri() . '/assets/js/prettyPhoto.js' , array('jquery'), '3.1.4', TRUE ); 
		wp_enqueue_script('tinynav', get_template_directory_uri() . '/assets/js/tinynav.js' , array('jquery'), '1.1', TRUE ); 
		wp_enqueue_script('tipsy', get_template_directory_uri() . '/assets/js/tipsy.js' , array('jquery'), '1.0.0a', TRUE ); 
		wp_enqueue_script('touchSwipe.js', get_template_directory_uri() . '/assets/js/touchSwipe.js' , array('jquery'), '1.6.18', TRUE ); 
		wp_enqueue_script('suevafree-template', get_template_directory_uri() . '/assets/js/template.js' , array('jquery', 'imagesloaded', 'masonry'), '1.0.0', TRUE ); 
	
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

		wp_enqueue_script('html5shiv', get_template_directory_uri().'/assets/scripts/html5shiv.js', FALSE, '3.7.3');
		wp_script_add_data('html5shiv', 'conditional', 'IE 8' );
		
		wp_enqueue_script('selectivizr', get_template_directory_uri().'/assets/scripts/selectivizr.js', FALSE, '1.0.3b');
		wp_script_add_data('selectivizr', 'conditional', 'IE 8' );

	}

	add_action( 'wp_enqueue_scripts', 'suevafree_scripts_styles' );

}

/*-----------------------------------------------------------------------------------*/
/* THEME SETUP */
/*-----------------------------------------------------------------------------------*/   

if (!function_exists('suevafree_setup')) {

	function suevafree_setup() {
		
		global $content_width;

		if ( !isset($content_width) )
			$content_width = esc_attr(suevafree_setting('suevafree_screen3', '1170'));
		
		load_theme_textdomain( 'suevafree', get_template_directory() . '/languages');
	
		add_filter('widget_text', 'do_shortcode');

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'title-tag' );

		add_theme_support( 'custom-background', array(
			'default-color' => 'f3f3f3',
		));

		register_default_headers( array(
			'default-image' => array(
				'url'           => get_template_directory_uri() . '/assets/images/background/header.jpg',
				'thumbnail_url' => get_template_directory_uri() . '/assets/images/background/resized-header.jpg',
				'description'   => __( 'Default image', 'suevafree' )
			),
		));

		add_theme_support( 'custom-header', array( 
			'width'         => 1920,
			'height'        => 1200,
			'default-image' => get_template_directory_uri() . '/assets/images/background/header.jpg',
			'header-text' 	=> false
		));

		add_theme_support( 'post-formats', 
			
			array( 
				'aside',
				'gallery',
				'quote',
				'video',
				'audio',
				'link',
				'status',
				'chat',
				'image'
			)
		
		);
		
		add_image_size( 'suevafree_thumbnail_s',  esc_attr(suevafree_setting('suevafree_thumbnail_s_width', '360')),  esc_attr(suevafree_setting('suevafree_thumbnail_s_height', '182')), TRUE ); 
		add_image_size( 'suevafree_thumbnail_l',  esc_attr(suevafree_setting('suevafree_thumbnail_l_width', '750')),  esc_attr(suevafree_setting('suevafree_thumbnail_l_height', '379')), TRUE ); 
		add_image_size( 'suevafree_thumbnail',    esc_attr(suevafree_setting('suevafree_thumbnail_width', '1170')),   esc_attr(suevafree_setting('suevafree_thumbnail_height', '690')), TRUE ); 

		add_image_size( 'suevafree_large', 449,304, TRUE ); 
		add_image_size( 'suevafree_medium', 290,220, TRUE ); 
		add_image_size( 'suevafree_small', 211,150, TRUE ); 

		register_nav_menu('main-menu', esc_html__('Main menu', 'suevafree'));
		register_nav_menu('one-page-menu', esc_html__('One Page menu', 'suevafree'));

		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/aside-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/default-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/image-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/link-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/page-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/product-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/quote-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/service-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/team-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/post-formats/testimonial-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/breadcrumb.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/header-layout-1.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/header-layout-2.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/header-layout-3.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/header-layout-4.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/header-layout-5.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/logo-layout.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/mobile-menu.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/header/scroll-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/after-content.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/archive-title.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/before-content.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/masonry.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/post-details.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/post-format.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/post-thumbnail.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/post-title.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/content/search-title.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/bottom-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/footer-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/header-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/onepage-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/side-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/sidebar/top-sidebar.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/footer/copyright.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/footer/pagination.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/templates/footer/social_buttons.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-customize.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-metaboxes.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-notice.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/includes/class-plugin-activation.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/admin/customize/customize.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/function-required-plugins.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/function-style.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/function-widgets.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/functions/function-woocommerce.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/post.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/product.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/service.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/team.php' );
		require_once( trailingslashit( get_template_directory() ) . '/core/metaboxes/testimonial.php' );

	}

	add_action( 'after_setup_theme', 'suevafree_setup' );

}

?>