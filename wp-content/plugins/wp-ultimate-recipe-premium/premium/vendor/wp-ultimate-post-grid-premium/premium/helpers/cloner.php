<?php

class WPUPG_Cloner {

    public function __construct()
    {
        add_action( 'init', array( $this, 'assets' ) );

        add_action( 'wp_ajax_clone_grid', array( $this, 'ajax_clone_grid' ) );
    }

    public function assets()
    {
        WPUltimatePostGrid::get()->helper( 'assets' )->add(
            array(
                'file' => '/js/cloner.js',
                'premium' => true,
                'admin' => true,
                'page' => 'grid_posts',
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpupg_cloner',
                    'ajax_url' => WPUltimatePostGrid::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'clone_grid' )
                )
            )
        );
    }

    public function ajax_clone_grid()
    {
        $grid_id = intval( $_POST['grid'] );

        if( check_ajax_referer( 'clone_grid', 'security', false ) && WPUPG_POST_TYPE == get_post_type( $grid_id ) )
        {
            $grid = get_post( $grid_id );

            $post = array(
                'post_title' => $grid->post_title,
                'post_type'	=> WPUPG_POST_TYPE,
                'post_status' => 'draft',
                'post_author' => get_current_user_id(),
            );

            $clone_id = wp_insert_post( $post );

            $custom_fields = get_post_custom( $grid_id );
            foreach ( $custom_fields as $key => $value ) {
                add_post_meta( $clone_id, $key, maybe_unserialize( $value[0] ) );
            }

            $url = admin_url( 'post.php?post=' . $clone_id . '&action=edit' );
            echo json_encode( array( 'redirect' => $url ) );
        }
        die();
    }
}