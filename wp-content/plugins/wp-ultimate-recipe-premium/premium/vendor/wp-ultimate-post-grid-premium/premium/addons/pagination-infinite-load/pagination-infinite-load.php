<?php

class WPUPG_Pagination_Infinite_Load extends WPUPG_Premium_Addon {

    public function __construct( $name = 'pagination-infinite-load' ) {
        parent::__construct( $name );

        add_action( 'init', array( $this, 'assets' ) );

        add_filter( 'wpupg_get_posts_args', array( $this, 'get_posts_args' ), 10, 3 );
        add_filter( 'wpupg_pagination_shortcode', array( $this, 'pagination_shortcode' ), 10, 2 );
    }

    public function assets() {
        if( !is_admin() ) {
            WPUltimatePostGrid::get()->helper( 'assets' )->add(
                array(
                    'name' => 'pagination-infinite-load',
                    'file' => $this->addonPath . '/js/pagination-infinite-load.js',
                    'premium' => true,
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    )
                )
            );
        }
    }

    public function get_posts_args( $args, $page, $grid ) {
        if( $grid->pagination_type() == 'infinite_load' ) {
            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['infinite_load'];

            if( $page == 0 ) {
                $args['posts_per_page'] = $pagination_options['initial_posts'];
            } elseif( $page == -1) {
                $args['posts_per_page'] = $pagination_options['posts_on_scroll'];
            } else {
                $args['posts_per_page'] = $pagination_options['posts_on_scroll'];
                $args['offset'] = $pagination_options['initial_posts'] + ( ( $page - 1 ) * $pagination_options['posts_on_scroll'] );
            }
        }

        return $args;
    }

    public function pagination_shortcode( $pagination, $grid ) {
        if( $grid->pagination_type() == 'infinite_load' ) {
            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['infinite_load'];

            $grid_posts = $grid->posts();
            $nbr_posts = count( $grid_posts['all'] );

            if( $nbr_posts > $pagination_options['initial_posts'] ) {
                $pagination .= '<div id="wpupg-grid-' . $grid->slug() . '-pagination" class="wpupg-pagination wpupg-pagination-infinite_load" data-grid="' . $grid->slug() . '" data-type="infinite_load">';
                $pagination .= '</div>';
            }
        }

        return $pagination;
    }
}

WPUltimatePostGrid::loaded_addon( 'pagination-infinite-load', new WPUPG_Pagination_Infinite_Load() );