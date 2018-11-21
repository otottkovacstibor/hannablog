<?php

class WPUPG_Grid_Cache {

    public function __construct()
    {
        add_action( 'add_attachment', array( $this, 'updated_attachment' ) );
        add_action( 'edit_attachment', array( $this, 'updated_attachment' ) );
        add_action( 'save_post', array( $this, 'updated_post' ), 11, 2 );
        add_action( 'edited_terms', array( $this, 'updated_term' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'regenerate_grids_check' ) );
    }

    public function updated_attachment( $id )
    {
        $this->update_grids_with_post_type( 'attachment' );
    }

    public function updated_post( $id, $post )
    {
        $update_post_post_type = $post->post_type;

        if( $update_post_post_type == WPUPG_POST_TYPE )
        {
            $this->generate( $id );
        } else {
            $this->update_grids_with_post_type( $update_post_post_type );
        }
    }

    public function update_grids_with_post_type( $post_type )
    {
        $args = array(
            'post_type' => WPUPG_POST_TYPE,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'nopaging' => true,
        );

        $query = new WP_Query( $args );
        $posts = $query->have_posts() ? $query->posts : array();

        foreach ( $posts as $grid_post )
        {
            $grid = new WPUPG_Grid( $grid_post );

            if( in_array( $post_type, $grid->post_types() ) ) {
                $this->generate( $grid->ID() );
            }
        }
    }

    public function updated_term( $term_id, $taxonomy )
    {
        $args = array(
            'post_type' => WPUPG_POST_TYPE,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'nopaging' => true,
        );

        $query = new WP_Query( $args );
        $posts = $query->have_posts() ? $query->posts : array();

        $grid_ids = array();
        foreach ( $posts as $post )
        {
            $grid = new WPUPG_Grid( $post );

            if( in_array( $taxonomy, $grid->filter_taxonomies() ) ) {
                $grid_ids[] = $grid->ID();
            }
        }

        if( count( $grid_ids ) > 0 ) {
            update_option( 'wpupg_regenerate_grids_check', $grid_ids );
        }
    }

    public function regenerate_grids_check()
    {
        $grid_ids = get_option( 'wpupg_regenerate_grids_check', false );

        if( $grid_ids ) {
            foreach( $grid_ids as $grid_id ) {
                $this->generate( $grid_id );
            }

            update_option( 'wpupg_regenerate_grids_check', false );
        }
    }

    public function generate( $grid_id )
    {
        $grid = new WPUPG_Grid( $grid_id );

        $generated = $this->dynamic_generate( $grid );

        update_post_meta( $grid->ID(), 'wpupg_posts', $generated['cache'] );
        update_post_meta( $grid->ID(), 'wpupg_filter', $generated['filter'] );
    }

    public function dynamic_generate( $grid ) {
        // Get Posts
        $args = array(
            'post_type' => $grid->post_types(),
            'post_status' => $grid->post_status(),
            'posts_per_page' => -1,
            'nopaging' => true,
            'order' => $grid->order(),
            'fields' => 'ids',
        );

        if( $grid->order_by() == 'custom' ) {
            $args['meta_key'] = $grid->order_custom_key();
            $args['orderby'] = $grid->order_custom_key_numeric() ? 'meta_value_num' : 'meta_value';
        } else {
            $args['orderby'] = $grid->order_by();
        }

        // Images Only
        if( $grid->images_only() ) {
            if( in_array( 'attachment', $grid->post_types() ) ) {
                $args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png';
            } else {
                $args['meta_query'] = array(
                    array(
                        'key' => '_thumbnail_id',
                        'value' => '0',
                        'compare' => '>'
                    ),
                );
            }
        }

        $query = new WP_Query( $args );
        $posts = $query->have_posts() ? $query->posts : array();

        $post_ids = array_map( 'intval', $posts );

        $post_ids = apply_filters( 'wpupg_grid_cache_post_ids', $post_ids, $grid );

        // Limit Total # Posts
        if( $grid->limit_posts_number() ) {
            $post_ids = array_slice($post_ids, 0, $grid->limit_posts_number() );
        }

        $cache = array(
            'all' => $post_ids,
        );

        $taxonomies = $grid->filter_taxonomies();
        $limit_terms = $grid->filter_limit_terms();

        // Cache arrays
        $posts_per_term = array();
        $terms_per_post = array();
        $filter_terms = array();

        // Loop over all terms
        foreach( $post_ids as $post_id ) {
            if( !isset( $terms_per_post[$post_id] ) ) $terms_per_post[$post_id] = array();

            foreach( $taxonomies as $taxonomy ) {
                if( !isset( $posts_per_term[$taxonomy] ) ) $posts_per_term[$taxonomy] = array();

                $terms = wp_get_post_terms( $post_id, $taxonomy );

                // Get parent terms if enabled
                if( $grid->filter_match_parents() ) {
                    $parent_ids = array();
                    $parents = array();

                    foreach( $terms as $term ) {
                        if( $term->parent != 0 ) {
                            $parent_ids[] = $term->parent;
                        }
                    }

                    while( count( $parent_ids ) > 0 )
                    {
                        $children_ids = $parent_ids;
                        $parent_ids = array();

                        foreach( $children_ids as $child ) {
                            $term = get_term( $child, $taxonomy );
                            $parents[] = $term;

                            if( $term->parent != 0 ) {
                                $parent_ids[] = $term->parent;
                            }
                        }
                    }

                    $terms = array_merge( $terms, $parents );
                    $handled_terms = array();
                }

                $post_taxonomy_term_ids = array();

                foreach( $terms as $term ) {
                    // Check if terms are being limited
                    if( $grid->filter_limit() ) {
                        if( $grid->filter_limit_exclude() ) {
                            if( isset( $limit_terms[$taxonomy] ) && in_array( $term->term_id, $limit_terms[$taxonomy] ) ) continue;
                        } else {
                            if( !isset( $limit_terms[$taxonomy] ) || !in_array( $term->term_id, $limit_terms[$taxonomy] ) ) continue;
                        }
                    }

                    $slug = urldecode( $term->slug );

                    // Make sure we only handle each term once
                    if( $grid->filter_match_parents() ) {
                        if( in_array( $slug, $handled_terms ) ) continue;
                        $handled_terms[] = $slug;
                    }

                    // Posts per term
                    if( !isset( $posts_per_term[$taxonomy][$slug] ) ) $posts_per_term[$taxonomy][$slug] = array();
                    $posts_per_term[$taxonomy][$slug][] = $post_id;

                    // Terms per post
                    $post_taxonomy_term_ids[] = $slug;

                    // Filter terms
                    $filter_terms[$slug] = array(
                        'taxonomy' => $taxonomy,
                        'name' => $term->name,
                    );
                }

                $terms_per_post[$post_id][$taxonomy] = $post_taxonomy_term_ids;
            }
        }

        $cache['taxonomies'] = $posts_per_term;
        $cache['terms'] = $terms_per_post;

        // Generate Filter
        $filter = '';

        if( count( $filter_terms ) > 0 && $grid->filter_type() == 'isotope' ) {
            $filter_style = $grid->filter_style();

            // All Button
            if( $filter_style['isotope']['all_button_text'] ) {
                $filter .= '<div class="wpupg-filter-item wpupg-filter-isotope-term wpupg-filter-tag- active">' . $filter_style['isotope']['all_button_text'] . '</div>';
            }

            $filter_terms_order = array_keys( $filter_terms );
            sort( $filter_terms_order );
            $filter_terms_order = apply_filters( 'wpupg_grid_cache_filter_isotope_term_order', $filter_terms_order, $grid );

            foreach( $filter_terms_order as $slug ) {
                $options = isset( $filter_terms[$slug] ) ? $filter_terms[$slug] : false;

                if( $options ) {
                    $filter .= '<div class="wpupg-filter-item wpupg-filter-isotope-term wpupg-filter-tag-' . $slug . '" data-taxonomy="' . $options['taxonomy'] . '" data-filter="' . $slug . '">' . $options['name'] . '</div>';
                }
            }
        }

        // Update Metadata
        $cache = apply_filters( 'wpupg_grid_cache_posts', $cache, $grid );
        $filter = apply_filters( 'wpupg_grid_cache_filter', $filter, $cache, $grid );

        return array(
            'cache' => $cache,
            'filter' => $filter,
        );
    }
}