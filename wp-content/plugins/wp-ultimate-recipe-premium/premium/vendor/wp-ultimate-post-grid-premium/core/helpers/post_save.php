<?php

class WPUPG_Post_Save {

    public function __construct()
    {
        add_action( 'save_post', array( $this, 'save' ), 10, 2 );
    }

    public function save( $post_id, $post )
    {
        if( $post->post_type != WPUPG_POST_TYPE )
        {
            if ( !isset( $_POST['wpupg_nonce'] ) || !wp_verify_nonce( $_POST['wpupg_nonce'], 'grid' ) ) {
                return;
            }

            // Basic metadata
            $fields = array(
                'wpupg_custom_link',
                'wpupg_custom_image',
                'wpupg_custom_image_id',
            );

            foreach ( $fields as $field )
            {
                $old = get_post_meta( $post_id, $field, true );
                $new = isset( $_POST[$field] ) ? $_POST[$field] : null;

                // Update or delete meta data if changed
                if( isset( $new ) && $new != $old ) {
                    update_post_meta( $post_id, $field, $new );
                } elseif ( $new == '' && $old ) {
                    delete_post_meta( $post_id, $field, $old );
                }
            }
        }
    }
}