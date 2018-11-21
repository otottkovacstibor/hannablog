<?php

class WPUPG_Grid_Save {

    public function __construct()
    {
        add_action( 'save_post', array( $this, 'save' ), 10, 2 );
    }

    public function save( $post_id, $post )
    {
        if( $post->post_type == WPUPG_POST_TYPE )
        {
            if ( !isset( $_POST['wpupg_nonce'] ) || !wp_verify_nonce( $_POST['wpupg_nonce'], 'grid' ) ) {
                return;
            }

            $grid = new WPUPG_Grid( $post_id );

            // Basic metadata
            $fields = $grid->fields();

            foreach ( $fields as $field )
            {
                $old = get_post_meta( $post_id, $field, true );
                $new = isset( $_POST[$field] ) ? $_POST[$field] : null;

                // Field specific adjustments
                if( isset( $new ) && $field == 'wpupg_post_types' ) {
                    $new = array( $new );
                }

                // Update or delete meta data if changed
                if( isset( $new ) && $new != $old ) {
                    update_post_meta( $post_id, $field, $new );
                } elseif ( $new == '' && $old ) {
                    delete_post_meta( $post_id, $field, $old );
                }
            }

            // Limit rules

            $limit_rules = array();

            if( isset( $_POST['wpupg_limit_posts_rule'] ) ) {
                foreach( $_POST['wpupg_limit_posts_rule'] as $id => $limit_rule ) {
                    if($id != 0) {
                        $field = $limit_rule['field'];
                        $type = $limit_rule['type'];

                        $field_parts = explode( '|', $field );
                        $values_name = 'values_' . $field_parts[0] . '_' . $field_parts[1];

                        if( isset( $limit_rule[$values_name] ) && $limit_rule[$values_name] ) {
                            $values = $limit_rule[$values_name];

                            if( !is_array( $values ) ) $values = explode( ';', $values );

                            $limit_rules[] = array(
                                'field' => $field,
                                'post_type' => $field_parts[0],
                                'taxonomy' => $field_parts[1],
                                'values' => $values,
                                'type' => $type,
                            );
                        }
                    }
                }
            }

            update_post_meta( $post_id, 'wpupg_limit_rules', $limit_rules );

            // Filter Taxonomies
            $post_type = $_POST['wpupg_post_types'];

            if( isset( $_POST['wpupg_filter_taxonomy_' . $post_type] ) ) {
                $filter_taxonomies = $_POST['wpupg_filter_taxonomy_' . $post_type];
                update_post_meta( $post_id, 'wpupg_filter_taxonomies', $filter_taxonomies );
            }

            // Filter Limit Terms
            $filter_limit_terms = array();
            if( isset( $filter_taxonomies ) ) {
                foreach( $filter_taxonomies as $filter_taxonomy ) {
                    if( isset( $_POST['wpupg_filter_limit_terms_' . $filter_taxonomy] ) ) {
                        $filter_limit_terms[$filter_taxonomy] = $_POST['wpupg_filter_limit_terms_' . $filter_taxonomy];
                    }
                }
            }
            update_post_meta( $post_id, 'wpupg_filter_limit_terms', $filter_limit_terms );

            // Filter style metadata
            $styles = $grid->filter_style_fields();
            $filter_style = array();

            foreach( $styles as $style => $fields ) {
                $filter_style[$style] = array();

                foreach( $fields as $field => $default ) {
                    $field_name  = 'wpupg_' . $style . '_filter_style_' . $field;
                    if( isset( $_POST[$field_name] ) ) {
                        $filter_style[$style][$field] = $_POST[$field_name];
                    }
                }
            }

            update_post_meta( $post_id, 'wpupg_filter_style', $filter_style );

            // Pagination metadata
            $pagination_fields = $grid->pagination_fields();
            $pagination = array();

            foreach( $pagination_fields as $type => $fields ) {
                $pagination[$type] = array();

                foreach( $fields as $field => $default ) {
                    $field_name  = 'wpupg_pagination_' . $type . '_' . $field;
                    if( isset( $_POST[$field_name] ) ) {
                        $pagination[$type][$field] = $_POST[$field_name];
                    }
                }
            }

            update_post_meta( $post_id, 'wpupg_pagination', $pagination );

            // Pagination style metadata
            $pagination_style_fields = $grid->pagination_style_fields();
            $pagination_style = array();

            foreach( $pagination_style_fields as $field => $default ) {
                $field_name  = 'wpupg_pagination_style_' . $field;
                if( isset( $_POST[$field_name] ) ) {
                    $pagination_style[$field] = $_POST[$field_name];
                }
            }

            update_post_meta( $post_id, 'wpupg_pagination_style', $pagination_style );

            // Cache gets automatically generated in WPUPG_Grid_Cache
        }
    }
}