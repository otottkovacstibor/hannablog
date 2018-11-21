<?php

class WPUPG_Pagination_Load_More extends WPUPG_Premium_Addon {

    public function __construct( $name = 'pagination-load-more' ) {
        parent::__construct( $name );

        add_action( 'init', array( $this, 'assets' ) );

        add_filter( 'wpupg_get_posts_args', array( $this, 'get_posts_args' ), 10, 3 );
        add_filter( 'wpupg_pagination_shortcode', array( $this, 'pagination_shortcode' ), 10, 2 );

        add_action( 'wp_ajax_wpupg_get_more_posts', array( $this, 'ajax_get_more_posts' ) );
        add_action( 'wp_ajax_nopriv_wpupg_get_more_posts', array( $this, 'ajax_get_more_posts' ) );
    }

    public function assets() {
        if( !is_admin() ) {
            WPUltimatePostGrid::get()->helper( 'assets' )->add(
                array(
                    'name' => 'pagination-load-more',
                    'file' => $this->addonPath . '/js/pagination-load-more.js',
                    'premium' => true,
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    )
                )
            );
        }
    }

    public function ajax_get_more_posts()
    {
        if( check_ajax_referer( 'wpupg_grid', 'security', false ) )
        {
            $grid = $_POST['grid'];
            $posts = isset( $_POST['posts'] ) ? $_POST['posts'] : null;
            $page_to = intval( $_POST['page'] );
            $page_from = isset( $_POST['all'] ) ? 1 : $page_to;

            $post = get_page_by_path( $grid, OBJECT, WPUPG_POST_TYPE );

            if( !is_null( $post ) ) {
                $grid = new WPUPG_Grid($post);

                $pages  = '';

                for( $page = $page_from; $page <= $page_to; $page++ ) {
                    $page_to_load = is_array( $posts) ? -1 : $page;
                    $pages .= $grid->draw_posts( $page_to_load, $posts );
                }

                echo $pages;
            }
        }

        die();
    }

    public function get_posts_args( $args, $page, $grid ) {
        if( $grid->pagination_type() == 'load_more' || $grid->pagination_type() == 'load_more_filter' ) {
            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['load_more'];

            if( $page == 0 ) {
                $args['posts_per_page'] = $pagination_options['initial_posts'];
            } elseif( $page == -1) {
                $args['posts_per_page'] = $pagination_options['posts_on_click'];
            } else {
                $args['posts_per_page'] = $pagination_options['posts_on_click'];
                $args['offset'] = $pagination_options['initial_posts'] + ( ( $page - 1 ) * $pagination_options['posts_on_click'] );
            }
        }

        return $args;
    }

    public function pagination_shortcode( $pagination, $grid ) {
        if( $grid->pagination_type() == 'load_more' || $grid->pagination_type() == 'load_more_filter' ) {
            $pagination_style = $grid->pagination_style();

            $style_data = ' data-margin-vertical="' . $pagination_style['margin_vertical'] . '"';
            $style_data .= ' data-margin-horizontal="' . $pagination_style['margin_horizontal'] . '"';
            $style_data .= ' data-padding-vertical="' . $pagination_style['padding_vertical'] . '"';
            $style_data .= ' data-padding-horizontal="' . $pagination_style['padding_horizontal'] . '"';
            $style_data .= ' data-border-width="' . $pagination_style['border_width'] . '"';

            $style_data .= ' data-background-color="' . $pagination_style['background_color'] . '"';
            $style_data .= ' data-text-color="' . $pagination_style['text_color'] . '"';
            $style_data .= ' data-border-color="' . $pagination_style['border_color'] . '"';

            $style_data .= ' data-active-background-color="' . $pagination_style['background_active_color'] . '"';
            $style_data .= ' data-active-text-color="' . $pagination_style['text_active_color'] . '"';
            $style_data .= ' data-active-border-color="' . $pagination_style['border_active_color'] . '"';

            $style_data .= ' data-hover-background-color="' . $pagination_style['background_hover_color'] . '"';
            $style_data .= ' data-hover-text-color="' . $pagination_style['text_hover_color'] . '"';
            $style_data .= ' data-hover-border-color="' . $pagination_style['border_hover_color'] . '"';


            $pagination_options = $grid->pagination();
            $pagination_options = $pagination_options['load_more'];

            $grid_posts = $grid->posts();
            $nbr_posts = count( $grid_posts['all'] );

            if( $nbr_posts > $pagination_options['initial_posts'] ) {
                $nbr_pages = ceil( ( $nbr_posts - $pagination_options['initial_posts'] ) / floatval( $pagination_options['posts_on_click'] ) );

                $pagination .= '<div id="wpupg-grid-' . $grid->slug() . '-pagination" class="wpupg-pagination wpupg-pagination-load_more" style="text-align: ' . $pagination_style['alignment'] . ';" data-grid="' . $grid->slug() . '" data-type="load_more"' . $style_data . '>';
                $pagination .= '<div class="wpupg-pagination-button" data-page="0" data-total-pages="' . $nbr_pages . '">' . $pagination_options['button_text'] . '</div>';
                $pagination .= '</div>';
            }
        }

        return $pagination;
    }
}

WPUltimatePostGrid::loaded_addon( 'pagination-load-more', new WPUPG_Pagination_Load_More() );