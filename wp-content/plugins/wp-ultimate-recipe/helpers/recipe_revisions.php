<?php

class WPURP_Recipe_Revisions {

    public function __construct()
    {
        add_action( 'save_post', array( $this, 'save_revision' ), 10, 2 );
        add_action( 'wp_restore_post_revision', array( $this, 'restore_revision' ), 10, 2 );
    }

    /**
     * Handles saving of recipe revisions
     */
    public function save_revision( $id, $post )
    {
        $parent_id = wp_is_post_revision( $id );

        if( $parent_id ) {
            $parent = get_post( $parent_id );

            if ( $parent->post_type == WPURP_POST_TYPE ) {
                $recipe = new WPURP_Recipe( $parent );

                $fields = $recipe->fields();

                foreach ( $fields as $field ) {
                    $meta = get_post_meta( $parent->ID, $field, true );

                    if ( false !== $meta ) {
                        add_metadata( 'post', $id, $field, $meta );
                    }
                }
            }
        }
    }

    public function restore_revision( $post_id, $revision_id )
    {
        $post = get_post( $post_id );
	    $revision = get_post( $revision_id );

        if ( $post->post_type == WPURP_POST_TYPE ) {
            $recipe = new WPURP_Recipe( $post );

            $fields = $recipe->fields();

            foreach ( $fields as $field ) {
                $meta  = get_metadata( 'post', $revision->ID, $field, true );

                if ( false !== $meta ) {
                    update_post_meta( $post_id, $field, $meta );
                } else {
                    delete_post_meta( $post_id, $field );
                }
            }
        }
    }
}