<?php

class WPUPG_Meta_Box {

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'add_meta_box' ), 10 );
        add_action( 'admin_init', array( $this, 'add_meta_box_data_source' ), 12 );
        add_action( 'admin_init', array( $this, 'add_meta_box_filter' ), 14 );
        add_action( 'admin_init', array( $this, 'add_meta_box_grid' ), 16 );
        add_action( 'admin_init', array( $this, 'add_meta_box_pagination' ), 18 );
    }

    public function add_meta_box()
    {
        add_meta_box(
            'wpupg_meta_box_shortcode',
            __( 'Shortcode', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_shortcode' ),
            WPUPG_POST_TYPE,
            'side',
            'high'
        );

        if( !WPUltimatePostGrid::is_premium_active() ) {
            add_meta_box(
                'wpupg_meta_box_premium_only',
                __( 'Premium Only', 'wp-ultimate-post-grid' ),
                array( $this, 'meta_box_premium_only' ),
                WPUPG_POST_TYPE,
                'side',
                'default'
            );
        }
    }

    public function add_meta_box_data_source()
    {
        add_meta_box(
            'wpupg_meta_box_data_source',
            __( 'Data Source', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_data_source' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wpupg_meta_box_limit_posts',
            __( 'Limit Posts', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_limit_posts' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function add_meta_box_filter()
    {
        add_meta_box(
            'wpupg_meta_box_filter',
            __( 'Filter', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_filter' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wpupg_meta_box_isotope_filter',
            __( 'Isotope Filter', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_isotope_filter' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function add_meta_box_grid()
    {
        add_meta_box(
            'wpupg_meta_box_grid',
            __( 'Grid', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_grid' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function add_meta_box_pagination()
    {
        add_meta_box(
            'wpupg_meta_box_pagination',
            __( 'Pagination', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_pagination' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wpupg_meta_box_pagination_style',
            __( 'Pagination Style', 'wp-ultimate-post-grid' ),
            array( $this, 'meta_box_pagination_style' ),
            WPUPG_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function meta_box_shortcode( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_shortcode.php' );
    }

    public function meta_box_premium_only( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_premium_only.php' );
    }

    public function meta_box_data_source( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_data_source.php' );
    }

    public function meta_box_limit_posts( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_limit_posts.php' );
    }

    public function meta_box_filter( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_filter.php' );
    }

    public function meta_box_isotope_filter( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_isotope_filter.php' );
    }

    public function meta_box_grid( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_grid.php' );
    }

    public function meta_box_pagination( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_pagination.php' );
    }

    public function meta_box_pagination_style( $post )
    {
        $grid = new WPUPG_Grid( $post );
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_pagination_style.php' );
    }
}