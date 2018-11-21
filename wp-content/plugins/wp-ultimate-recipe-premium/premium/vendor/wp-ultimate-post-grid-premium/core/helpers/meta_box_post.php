<?php

class WPUPG_Meta_Box_Post {

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'add_meta_box' ), 10 );
    }

    public function add_meta_box()
    {
        $post_types = get_post_types( '', 'objects' );

        unset( $post_types[WPUPG_POST_TYPE] );
        unset( $post_types['revision'] );
        unset( $post_types['nav_menu_item'] );

        foreach( $post_types as $post_type => $options ) {
            add_meta_box(
                'wpupg_meta_box_post',
                'WP Ultimate Post Grid',
                array( $this, 'meta_box_post' ),
                $post_type,
                'normal',
                'high'
            );
        }

    }

    public function meta_box_post( $post )
    {
        include( WPUltimatePostGrid::get()->coreDir . '/helpers/meta_boxes/meta_box_post.php' );
    }
}