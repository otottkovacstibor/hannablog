<?php

class WPURP_Recipe_Grid extends WPURP_Premium_Addon {

    public function __construct( $name = 'recipe-grid' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'admin_init', array( $this, 'check_terms_reset' ) );

        add_action( 'admin_init', array( $this, 'updated_terms_check' ) );
        add_action( 'edited_terms', array( $this, 'updated_terms' ), 10, 2 );
        add_action( 'save_post', array( $this, 'reset_saved_post_terms' ), 10, 2 );

        // Ajax
        add_action( 'wp_ajax_recipe_grid_get_recipes', array( $this, 'ajax_recipe_grid_get_recipes' ) );
        add_action( 'wp_ajax_nopriv_recipe_grid_get_recipes', array( $this, 'ajax_recipe_grid_get_recipes' ) );

        // Shortcode
        add_shortcode( 'ultimate-recipe-grid', array( $this, 'recipe_grid_shortcode' ));
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => WPUltimateRecipe::get()->coreUrl . '/vendor/select2/select2.min.css',
                'direct' => true,
                'public' => true,
                'shortcode' => 'ultimate-recipe-grid',
            ),
            array(
                'file' => $this->addonPath . '/css/recipe-grid.css',
                'premium' => true,
                'public' => true,
                'shortcode' => 'ultimate-recipe-grid',
            ),
            array(
                'name' => 'select2',
                'file' => '/vendor/select2/select2.min.js',
                'public' => true,
                'shortcode' => 'ultimate-recipe-grid',
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'name' => 'recipe-grid',
                'file' => $this->addonPath . '/js/recipe-grid.js',
                'premium' => true,
                'public' => true,
                'shortcode' => 'ultimate-recipe-grid',
                'deps' => array(
                    'jquery',
                    'select2',
                ),
                'data' => array(
                    'name' => 'wpurp_recipe_grid',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_recipe_grid' ),
                )
            )
        );
    }

    public function check_terms_reset()
    {
        if( isset( $_GET['wpurp_reset_recipe_grid_terms'] ) ) {
            $recipes = WPUltimateRecipe::get()->query()->all();

            foreach ( $recipes as $recipe )
            {
                WPUltimateRecipe::get()->helper( 'recipe_save' )->update_recipe_terms( $recipe->ID() );
            }

            WPUltimateRecipe::get()->helper( 'notices' )->add_admin_notice( '<strong>WP Ultimate Recipe</strong> The Recipe Grid terms have been reset' );
        }
    }

    public function reset_saved_post_terms( $id, $post )
    {
        if( $post->post_type == 'recipe' )
        {
            // Other case gets handled by the recipe_save helper
            if ( !isset( $_POST['recipe_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['recipe_meta_box_nonce'], 'recipe' ) )
            {
                WPUltimateRecipe::get()->helper( 'recipe_save' )->update_recipe_terms( $id );
            }
        }
    }

    /*
     * Has to be done in two steps to make sure we update the latest version
     */
    public function updated_terms( $term_id, $taxonomy )
    {
        $term = get_term( $term_id, $taxonomy );
        $recipes = WPUltimateRecipe::get()->query()->taxonomy( $taxonomy )->term( $term->slug )->get();
        $recipe_ids = array();

        foreach ( $recipes as $recipe )
        {
            $recipe_ids[] = $recipe->ID();
        }

        if( count( $recipe_ids ) != 0 ) {
            update_option( 'wpurp_update_recipe_grid_terms', $recipe_ids );
        }
    }

    public function updated_terms_check()
    {
        $recipe_ids = get_option( 'wpurp_update_recipe_grid_terms', false );
        if( $recipe_ids ) {
            foreach( $recipe_ids as $recipe_id ) {
                WPUltimateRecipe::get()->helper( 'recipe_save' )->update_recipe_terms( $recipe_id );
            }

            update_option( 'wpurp_update_recipe_grid_terms', false );
        }
    }

    public function ajax_recipe_grid_get_recipes()
    {
        if( check_ajax_referer( 'wpurp_recipe_grid', 'security', false ) )
        {
            $grid = $_POST['grid'];
            $grid['name'] = $_POST['grid_name'];

            // TODO Limit recipes
            $recipes = WPUltimateRecipe::get()->query()->ids( $grid['recipes'] )->order( $grid['order'] )->order_by( $grid['orderby'] )->get();

            // Filter Recipes
            foreach( $recipes as $index => $recipe )
            {
                if( $grid['match_parents'] ) {
                    $recipe_terms = $recipe->terms_with_parents();
                } else {
                    $recipe_terms = $recipe->terms();
                }

                $recipe_in_grid = true;
                foreach( $grid['filters'] as $taxonomy => $filters )
                {
                    if( !is_array( $filters ) ) {
                        unset( $grid['filters'][$taxonomy] );
                    } else {
                        $match = false;

                        foreach( $filters as $filter ) {
                            $match = in_array( intval( $filter ), $recipe_terms[$taxonomy] );

                            if( $grid['match_all'] && $match == false ) break;
                            if( !$grid['match_all'] && $match == true ) break;
                        }

                        if( !$match ) {
                            unset( $recipes[$index] );
                            break;
                        }
                    }
                }
            }


            // Output Recipes
            // TODO Refactor
            $out = '';

            if( count( $recipes ) == 0 ) {
                $out .= '<div>' . __( 'No recipes found.', 'wp-ultimate-recipe' ) . '</div>';
            }
            else
            {
                foreach( $recipes as $recipe )
                {
                    $thumb = $recipe->image_url( 'thumbnail' );

                    if( !is_null( $thumb ) || !$grid['images_only'] )
                    {
                        $out .= '<div class="recipe recipe-card" id="'.$grid['name'].'-recipe-' . $recipe->ID() . '" data-link="' . $recipe->link() . '">';
                        $recipe_output = $recipe->output_string( 'grid', $grid['template'] );
                        $out .= apply_filters( 'wpurp_output_recipe_grid', $recipe_output, $recipe );
                        $out .= '</div>';
                    }
                }
            }

            echo $out;
        }

        die();
    }

    public function recipe_grid_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'name' => 'default',
            'template' => 'default',
            'sort_by' => 'title',
            'sort_order' => 'ASC',
            'no_filter' => 'false',
            'filter' => 'all',
            'multiselect' => 'true',
            'match_all' => 'true',
            'match_parents' => 'true',
            'limit_author' => '',
            'limit_by_tag' => 'false',
            'limit_by_values' => '',
            'images_only' => 'false',
            'limit' => '999'
        ), $options );

        $name = preg_replace("/\W/", '', $options['name']);
        $template = strtolower( $options['template'] );
        $sort_by = strtolower( $options['sort_by'] );
        $sort_order = strtoupper( $options['sort_order'] );
        $no_filter = strtolower( $options['no_filter'] );
        $filter = strtolower( $options['filter'] );
        $multiselect = strtolower( $options['multiselect'] );
        $match_all = strtolower( $options['match_all'] );
        $match_parents = strtolower( $options['match_parents'] );
        $limit_author = $options['limit_author'];
        $limit_by_tag = $options['limit_by_tag'] == 'false' ? false : strtolower( $options['limit_by_tag'] );
        $limit_by_values = str_replace( ';', ',', strtolower( $options['limit_by_values'] ) );
        $images_only = strtolower( $options['images_only'] );
        $limit = intval( $options['limit'] );

        $sort_by = in_array( $sort_by, array( 'author', 'name', 'title', 'date', 'rating', 'rand' ) ) ? $sort_by : 'title';
        $sort_order = in_array( $sort_order, array( 'ASC', 'DESC' ) ) ? $sort_order : 'ASC';
        $no_filter = $no_filter == 'true' ? true : false;
        $multiselect = $multiselect == 'true' ? true : false;
        $match_all = $match_all == 'true' ? true : false;
        $match_parents = $match_parents == 'true' ? true : false;
        $filter_options = explode( ',', $filter );
        if ( $filter_options[0] == 'all' ) {
            $all = true;
        } else {
            $all = false;
        }
        $images_only = $images_only != 'false' ? true : false;

        // Get Recipe IDs
        $recipe_ids = array();

        if( $limit_by_tag && strlen( $limit_by_values ) > 0 ) {
            $tag_values = explode( ',', $limit_by_values );

            foreach( $tag_values as $term )
            {
                $recipe_ids = array_merge(
                    $recipe_ids,
                    WPUltimateRecipe::get()->query()->author( $limit_author )->taxonomy( $limit_by_tag )->term( $term )->images_only( $images_only )->ids_only()->get()
                );
            }
        } else {
            $recipe_ids = WPUltimateRecipe::get()->query()->author( $limit_author )->images_only( $images_only )->ids_only()->get();
        }

        $recipe_ids = apply_filters( 'wpurp_recipe_grid_recipe_ids', $recipe_ids, $name );

        // Output variables
        $filters_out = '';
        $out = '';

        // Recipe Grid Filters
        $js_filters = array();
        if( !$no_filter )
        {
            // Filter Taxonomies
            $taxonomies = WPUltimateRecipe::get()->tags();

            if( in_array( 'category', $filter_options ) || ( $all && WPUltimateRecipe::option( 'recipe_tags_filter_categories', '0' ) == '1' ) ) {
                $taxonomies['category'] = array(
                    'labels' => array(
                        'name' => __( 'Categories', 'wp-ultimate-recipe' )
                    )
                );
            }

            if( in_array( 'post_tag', $filter_options ) || ( $all && WPUltimateRecipe::option( 'recipe_tags_filter_tags', '0' ) == '1' ) ) {
                $taxonomies['post_tag'] = array (
                    'labels' => array(
                        'name' => __( 'Tags', 'wp-ultimate-recipe' )
                    )
                );
            }

            // Order taxonomies by order in shortcode
            $taxonomies = array_merge(array_flip($filter_options), $taxonomies);

            $used_terms = array();

            foreach( $taxonomies as $taxonomy => $options ) {
                $used_terms[$taxonomy] = wp_get_object_terms( $recipe_ids, $taxonomy, array( 'fields' => 'ids' ) );
            }

            $used_terms = apply_filters( 'wpurp_recipe_grid_filter_terms', $used_terms );


            $filters_out .= '<div class="wpurp-recipe-grid-filter-box">';

            foreach( $taxonomies as $taxonomy => $options ) {
                if( is_array( $options ) && ( $all || in_array( $taxonomy, $filter_options ) ) )
                {
                    $args = array(
                        'show_option_none' => 'none',
                        'taxonomy' => $taxonomy,
                        'echo' => 0,
                        'hide_empty' => 1,
                        'class' => 'wpurp-recipe-grid-filter',
                        'show_count' => 0,
                        'orderby' => 'name',
                        'hide_if_empty' => true
                    );
                    $placeholder = $options['labels']['name'];

                    $options = get_categories( $args );

                    if( $multiselect )
                    {
                        $empty_option = '';
                        $multiple = ' multiple';
                    } else {
                        $empty_option = '<option></option>';
                        $multiple = '';
                    }

                    $select = '<select name="recipe-'.$taxonomy.'" id="recipe-'.$taxonomy.'" class="wpurp-recipe-grid-filter" data-grid-name="'.$name.'" data-placeholder="'.$placeholder.'"'. $multiple .'>';
                    $select .= $empty_option;

                    $nbr_valid_options = 0;
                    foreach( $options as $option ) {
                        if( in_array( $option->term_id, $used_terms[$taxonomy] ) ) {
                            $select .= '<option value="'.$option->term_id.'">'.$option->name.'</option>';
                            $nbr_valid_options++;
                        }
                    }

                    $select .= '</select>';

                    if( $nbr_valid_options > 0 ) {
                        $filters_out .= $select;
                    }

                    $js_filters[] = array( $taxonomy => 0 );
                }
            }

            $filters_out .= '</div>';
        }

        // Recipe Grid Cards
        $out .= '<div class="wpurp-recipe-grid-container" id="wpurp-recipe-grid-'.$name.'" data-grid-name="'.$name.'">';
        if( count( $recipe_ids ) == 0 ) {
            $out .= '<div>' . __( 'No recipes found.', 'wp-ultimate-recipe' ) . '</div>';
        }
        else
        {
            // Get actual recipe data
            $recipes = WPUltimateRecipe::get()->query()->ids( $recipe_ids )->order( $sort_order )->order_by( $sort_by )->limit( $limit )->get();

            foreach( $recipes as $recipe )
            {
                $thumb = $recipe->image_url( 'thumbnail' );

                if( !is_null( $thumb ) || !$images_only)
                {
                    $out .= '<div class="recipe recipe-card" id="'.$name.'-recipe-' . $recipe->ID() . '" data-link="' . $recipe->link() . '">';
                    $recipe_output = $recipe->output_string( 'grid', $template );
                    $out .= apply_filters( 'wpurp_output_recipe_grid', $recipe_output, $recipe );
                    $out .= '</div>';
                }
            }

            // TODO Load more recipes
//            if( count( $recipes ) > $limit ) {
//                $out .= '<div class="recipe-grid-load-more"><a href="#">' . __( 'Load more recipes', 'wp-ultimate-recipe' ) . '</a></div>';
//            }
        }
        $out .= '</div>';

        $script_name = WPUltimateRecipe::option( 'assets_use_minified', '1' ) == '1' ? 'wpurp_script_minified' : 'recipe-grid';

        wp_localize_script( $script_name, 'wpurp_recipe_grid_' . $name,
            array(
                'recipes' => $recipe_ids,
                'template' => $template,
                'orderby' => $sort_by,
                'order' => $sort_order,
                'limit' => $limit,
                'images_only' => $images_only,
                'filters' => $js_filters,
                'match_all' => $match_all,
                'match_parents' => $match_parents,
            )
        );

        return $filters_out . $out;
    }
}

WPUltimateRecipe::loaded_addon( 'recipe-grid', new WPURP_Recipe_Grid() );