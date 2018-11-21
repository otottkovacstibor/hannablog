<?php

if (!function_exists('suevafree_loadwidgets')) {

	function suevafree_loadwidgets() {
		
		register_sidebar(array(
		
			'name' => esc_html__('Side Sidebar','suevafree'),
			'id'   => 'side-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown after the content.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('One Page','suevafree'),
			'id'   => 'onepage-sidebar-area',
			'description'   => esc_html__('This sidebar area is recommended to manage the one page section.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="%2$s onepage-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('Home Side Sidebar','suevafree'),
			'id'   => 'home-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the side of your homepage.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('Category Sidebar','suevafree'),
			'id'   => 'category-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the side of category page.','suevafree'),
			'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));
	
		register_sidebar(array(
		
			'name' => esc_html__('Search Sidebar','suevafree'),
			'id'   => 'search-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the side of search page.','suevafree'),
			'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('Top Sidebar','suevafree'),
			'id'   => 'top-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the top of your content (Recommended for Revolution Slider).', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="post-container %2$s"><article class="post-article">',
			'after_widget'  => '</article></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('Home Top Sidebar','suevafree'),
			'id'   => 'home-top-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the top of your homepage (Recommended for Revolution Slider).', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="post-container %2$s"><article class="post-article">',
			'after_widget'  => '</article></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));
		
		register_sidebar(array(

			'name' => esc_html__('Header Sidebar','suevafree'),
			'id'   => 'header-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown before the content.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="post-container %2$s"><article class="post-article">',
			'after_widget'  => '</article></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(
		
			'name' => esc_html__('Home Header Sidebar','suevafree'),
			'id'   => 'home-header-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown before the content of your homepage.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="post-container %2$s"><article class="post-article">',
			'after_widget'  => '</article></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));

		register_sidebar(array(

			'name' => esc_html__('Full Sidebar','suevafree'),
			'id'   => 'full-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the bottom of your content, with a full width layout (Recommended for Instagram widget)', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="post-container %2$s"><article class="post-article">',
			'after_widget'  => '</article></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));
	
		register_sidebar(array(

			'name' => esc_html__('Bottom Sidebar','suevafree'),
			'id'   => 'bottom-sidebar-area',
			'description'   => esc_html__('This sidebar will be shown at the end of your content.', 'suevafree'),
			'before_widget' => '<div id="%1$s" class="col-md-3 %2$s"><div class="widget-box">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h4 class="title">',
			'after_title'   => '</h4>'
		
		));
		
	}

	add_action( 'widgets_init', 'suevafree_loadwidgets', 99 );

}

?>