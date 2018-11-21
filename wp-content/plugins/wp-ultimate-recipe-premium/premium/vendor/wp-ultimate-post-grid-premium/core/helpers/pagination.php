<?php

class WPUPG_Pagination {

    public function __construct()
    {
        add_action( 'wp_ajax_wpupg_get_page', array( $this, 'ajax_get_page' ) );
        add_action( 'wp_ajax_nopriv_wpupg_get_page', array( $this, 'ajax_get_page' ) );
    }

    public function ajax_get_page()
    {
        if( check_ajax_referer( 'wpupg_grid', 'security', false ) )
        {
            $grid = $_POST['grid'];
            $page = intval( $_POST['page'] );

            $post = get_page_by_path( $grid, OBJECT, WPUPG_POST_TYPE );

            if( !is_null( $post ) ) {
                $grid = new WPUPG_Grid($post);

                echo $grid->draw_posts( $page );
            }
        }

        die();
    }
}