<?php

class WPUPG_Pagination_Load_Filter extends WPUPG_Premium_Addon {

    public function __construct( $name = 'pagination-load-filter' ) {
        parent::__construct( $name );

        add_action( 'init', array( $this, 'assets' ) );

        add_filter( 'wpupg_get_posts_args', array( $this, 'get_posts_args' ), 10, 3 );
        add_filter( 'wpupg_pagination_shortcode', array( $this, 'pagination_shortcode' ), 11, 2 );

        add_action( 'wp_ajax_wpupg_get_filter_posts', array( $this, 'ajax_get_filter_posts' ) );
        add_action( 'wp_ajax_nopriv_wpupg_get_filter_posts', array( $this, 'ajax_get_filter_posts' ) );
    }

    public function assets() {
        if( !is_admin() ) {
            WPUltimatePostGrid::get()->helper( 'assets' )->add(
                array(
                    'name' => 'pagination-load-filter',
                    'file' => $this->addonPath . '/js/pagination-load-filter.js',
                    'premium' => true,
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    )
                )
            );
        }
    }

    public function ajax_get_filter_posts()
    {
        if( check_ajax_referer( 'wpupg_grid', 'security', false ) )
        {
            $grid = $_POST['grid'];
            $posts = $_POST['posts'];

            $post = get_page_by_path( $grid, OBJECT, WPUPG_POST_TYPE );

            if( !is_null( $post ) ) {
                $grid = new WPUPG_Grid($post);
                echo $grid->draw_posts( 0, $posts );
            }
        }

        die();
    }

    public function get_posts_args( $args, $page, $grid ) {
        if( $grid->pagination_type() == 'load_filter' ) {
            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['load_filter'];

            $args['posts_per_page'] = $pagination_options['initial_posts'];
        }

        return $args;
    }

    public function pagination_shortcode( $pagination, $grid ) {
        if( $grid->pagination_type() == 'load_filter' || $grid->pagination_type() == 'load_more_filter' ) {
            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['load_filter'];

            $grid_posts = $grid->posts();
            $nbr_posts = count( $grid_posts['all'] );

            if( $nbr_posts > $pagination_options['initial_posts'] ) {
                $pagination .= '<div id="wpupg-grid-' . $grid->slug() . '-pagination" class="wpupg-pagination wpupg-pagination-load_filter" data-grid="' . $grid->slug() . '" data-type="load_filter">';
                $pagination .= '</div>';
            }
        }

        return $pagination;
    }
}

WPUltimatePostGrid::loaded_addon( 'pagination-load-filter', new WPUPG_Pagination_Load_Filter() );