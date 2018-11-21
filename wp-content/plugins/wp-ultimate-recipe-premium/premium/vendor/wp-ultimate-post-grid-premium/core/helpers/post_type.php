<?php

class WPUPG_Post_Type {

    public function __construct()
    {
        add_action( 'init', array( $this, 'register_post_type' ), 1);
    }

    public function register_post_type()
    {
        $name = __( 'Grids', 'wp-ultimate-post-grid' );
        $singular = __( 'Grid', 'wp-ultimate-post-grid' );
        
        $args = apply_filters( 'wpupg_register_post_type',
            array(
                'labels' => array(
                    'name' => $name,
                    'singular_name' => $singular,
                    'add_new' => __( 'Add New', 'wp-ultimate-post-grid' ),
                    'add_new_item' => __( 'Add New', 'wp-ultimate-post-grid' ) . ' ' . $singular,
                    'edit' => __( 'Edit', 'wp-ultimate-post-grid' ),
                    'edit_item' => __( 'Edit', 'wp-ultimate-post-grid' ) . ' ' . $singular,
                    'new_item' => __( 'New', 'wp-ultimate-post-grid' ) . ' ' . $singular,
                    'view' => __( 'View', 'wp-ultimate-post-grid' ),
                    'view_item' => __( 'View', 'wp-ultimate-post-grid' ) . ' ' . $singular,
                    'search_items' => __( 'Search', 'wp-ultimate-post-grid' ) . ' ' . $name,
                    'not_found' => __( 'No', 'wp-ultimate-post-grid' ) . ' ' . $name . ' ' . __( 'found.', 'wp-ultimate-post-grid' ),
                    'not_found_in_trash' => __( 'No', 'wp-ultimate-post-grid' ) . ' ' . $name . ' ' . __( 'found in trash.', 'wp-ultimate-post-grid' ),
                    'parent' => __( 'Parent', 'wp-ultimate-post-grid' ) . ' ' . $singular,
                ),
                'public' => true,
                'exclude_from_search' => true,
                'show_ui' => true,
                'menu_position' => 20,
                'supports' => array( 'title' ),
                'taxonomies' => array(),
                'menu_icon' => 'dashicons-grid-view', //WPUltimatePostGrid::get()->coreUrl . '/img/icon_16.png',
                'has_archive' => false,
                'rewrite' => false,
            )
        );

        register_post_type( WPUPG_POST_TYPE, $args );
    }
}