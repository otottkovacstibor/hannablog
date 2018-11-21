<?php 

if (!function_exists('suevafree_css_custom')) {

	function suevafree_css_custom() { 

		$css = '<style type="text/css">';

			/* =================== HEADER STYLE =================== */
		
			if ( get_header_image() )

				$css .=  "
					#header.header-5  { 
						background-image: url(".esc_html(get_header_image()).");
						-webkit-background-size: cover !important;
						-moz-background-size: cover !important;
						-o-background-size: cover !important;
						background-size: cover !important;
						background-attachment: fixed;
					}"; 

			/* =================== BEGIN PAGE WIDTH =================== */
			
		
			if (suevafree_setting('suevafree_screen1')) {
			
				$css .= "@media (min-width:768px) {.container{width:".intval(suevafree_setting('suevafree_screen1'))."px}}"; 
				$css .= "@media (min-width:768px) {.container.block{width:" . (intval(suevafree_setting('suevafree_screen1'))-10) . "px}}"; 
				$css .= "@media (min-width:768px) {.container.grid-container{width:" . (intval(suevafree_setting('suevafree_screen1'))-20) . "px}}"; 
			
			}
			
			if (suevafree_setting('suevafree_screen2')) {
				
				$css .= "@media (min-width:992px) {.container{width:".intval(suevafree_setting('suevafree_screen2'))."px}}"; 
				$css .= "@media (min-width:992px) {.container.block{width:" . (intval(suevafree_setting('suevafree_screen2'))-10) . "px}}"; 
				$css .= "@media (min-width:768px) {.container.grid-container{width:" . (intval(suevafree_setting('suevafree_screen2'))-20) . "px}}"; 
			
			}
			
			if (suevafree_setting('suevafree_screen3'))  {
				
				$css .= "@media (min-width:1200px){.container{width:".intval(suevafree_setting('suevafree_screen3'))."px}}"; 
				$css .= "@media (min-width:1200px){.container.block{width:" . (intval(suevafree_setting('suevafree_screen3'))-10) . "px}}"; 
				$css .= "@media (min-width:768px) {.container.grid-container{width:" . (intval(suevafree_setting('suevafree_screen3'))-20) . "px}}"; 
			
			}
			
			if (suevafree_setting('suevafree_screen4'))  {
				
				$css .= "@media (min-width:1400px){.container{width:".intval(suevafree_setting('suevafree_screen4'))."px}}"; 
				$css .= "@media (min-width:1400px){.container.block{width:" . (intval(suevafree_setting('suevafree_screen4'))-10) . "px}}"; 
				$css .= "@media (min-width:768px) {.container.grid-container{width:" . (intval(suevafree_setting('suevafree_screen4'))-20) . "px}}"; 
			
			}
			
			/* =================== END PAGE WIDTH =================== */

			/* =================== BEGIN LOGO STYLE =================== */
		
			if (suevafree_setting('suevafree_logo_font_size')) 
				$css .= "#logo a.logo { font-size:".esc_html(suevafree_setting('suevafree_logo_font_size'))."; }"; 
			
			if (suevafree_setting('suevafree_logo_description_font_size')) 
				$css .= "#logo a.logo span { font-size:".esc_html(suevafree_setting('suevafree_logo_description_font_size'))."; }";
			
			if (suevafree_setting('suevafree_logo_description_top_margin') ) 
				$css .= "#logo a.logo span { margin-top:".esc_html(suevafree_setting('suevafree_logo_description_top_margin'))."; }"; 

			/* =================== END LOGO STYLE =================== */
			
			/* =================== BEGIN NAV STYLE =================== */
		
			if ( suevafree_setting('suevafree_menu_font_size') ) :
			
				$css .= ".suevafree-menu ul li a { font-size:".esc_html(suevafree_setting('suevafree_menu_font_size'))."; }"; 
				$css .= ".suevafree-menu ul ul li a { font-size:" . ( str_replace("px", "", esc_html(suevafree_setting('suevafree_menu_font_size'))) - 2 ) . "px;}"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_menu_font_weight') )
				$css .= ".suevafree-menu ul li a { font-weight:" . esc_html(suevafree_setting('suevafree_menu_font_weight')) . ";}"; 
		
			if ( suevafree_setting('suevafree_menu_text_transform') )
				$css .= ".suevafree-menu ul li a { text-transform:" . esc_html(suevafree_setting('suevafree_menu_text_transform')) . ";}"; 
		
			/* =================== END NAV STYLE =================== */
			
			/* =================== BEGIN CONTENT STYLE =================== */
			
			if ( suevafree_setting('suevafree_content_font_size') ) :
				
				$css .= '
					.post-article a, 
					.post-article p,
					.post-article .dropcap, 
					.post-article li, 
					.post-article address, 
					.post-article dd, 
					.post-article blockquote, 
					.post-article td, 
					.post-article th,
					.post-article span,
					.sidebar-area a, 
					.sidebar-area p, 
					.sidebar-area li, 
					.sidebar-area address, 
					.sidebar-area dd, 
					.sidebar-area blockquote, 
					.sidebar-area td, 
					.sidebar-area th,
					.sidebar-area span,
					.textwidget { font-size:' . esc_html(suevafree_setting('suevafree_content_font_size')) . '}';
				
			endif;
			
			/* =================== END CONTENT STYLE =================== */
			
			/* =================== START TITLE STYLE =================== */
			
			if ( suevafree_setting('suevafree_titles_font_weight') ) :
			
				$css .= '
					h1.title, 
					h2.title, 
					h3.title, 
					h4.title, 
					h5.title, 
					h6.title, 
					h1.title a, 
					h2.title a, 
					h3.title a, 
					h4.title a, 
					h5.title a, 
					h6.title a, 
					h1, 
					h2, 
					h3, 
					h4, 
					h5, 
					h6, 
					.logged-in-as, 
					.title a, 
					.post-container .category, 
					.post-container .category h1, 
					.post-container .category h1 span, 
					.post-container .portfolio, 
					.post-container .portfolio h1, 
					.post-container .search, 
					.post-container .search h1 { font-weight:' . esc_html(suevafree_setting('suevafree_titles_font_weight')) . ';} ';
			
			endif;
			
			if ( suevafree_setting('suevafree_titles_text_transform') ) :
			
				$css .= '
					h1.title, 
					h2.title, 
					h3.title, 
					h4.title, 
					h5.title, 
					h6.title, 
					h1.title a, 
					h2.title a, 
					h3.title a, 
					h4.title a, 
					h5.title a, 
					h6.title a, 
					h1, 
					h2, 
					h3, 
					h4, 
					h5, 
					h6, 
					.logged-in-as, 
					.title a, 
					.post-container .category, 
					.post-container .category h1, 
					.post-container .category h1 span, 
					.post-container .portfolio, 
					.post-container .portfolio h1, 
					.post-container .search, 
					.post-container .search h1 { text-transform:' . esc_html(suevafree_setting('suevafree_titles_text_transform')) . ';} ';
			
			endif;

			if ( suevafree_setting('suevafree_h1_font_size') ) :
			
				$css .=  "
					h1, 
					h1.title, 
					h1.title a { font-size:" . esc_html(suevafree_setting('suevafree_h1_font_size')) . "; }"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_h2_font_size') ) :
			
				$css .=  "
					h2, 
					h2.title, 
					h2.title a { font-size:" . esc_html(suevafree_setting('suevafree_h2_font_size')) . "; }"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_h3_font_size') ) :
			
				$css .=  "
					h3, 
					h3.title, 
					h3.title a { font-size:" . esc_html(suevafree_setting('suevafree_h3_font_size')) . "; }"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_h4_font_size') ) :
			
				$css .=  "
					h4, 
					h4.title, 
					h4.title a { font-size:" . esc_html(suevafree_setting('suevafree_h4_font_size')) . "; }"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_h5_font_size') ) :
			
				$css .=  "
					h5, 
					h5.title, 
					h5.title a { font-size:" . esc_html(suevafree_setting('suevafree_h5_font_size')) . "; }"; 
			
			endif;
			
			if ( suevafree_setting('suevafree_h6_font_size') ) :
			
				$css .=  "
					h6, 
					h6.title, 
					h6.title a { font-size:" . esc_html(suevafree_setting('suevafree_h6_font_size')) . "; }"; 
			
			endif;
			
			/* =================== END TITLE STYLE =================== */
				
			/* =================== END LINK STYLE =================== */
		
		$css .= '</style>';
		
		echo str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
		
	}

	add_action('wp_head', 'suevafree_css_custom');

}

?>