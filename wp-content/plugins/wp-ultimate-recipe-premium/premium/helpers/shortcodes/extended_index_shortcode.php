<?php

class WPURP_Extended_Index_Shortcode {

    public function __construct()
    {
        remove_shortcode( 'ultimate-recipe-index' );
        add_shortcode( 'ultimate-recipe-index', array( $this, 'extended_index_shortcode' ) );
    }

    // TODO Could use some refactoring
    function extended_index_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'headers' => 'false',
            'group_by' => 'none',
            'sort_by' => 'title',
            'sort_order' => 'ASC',
            'limit_author' => '',
            'limit_by_tag' => 'false',
            'limit_by_values' => '',
            'limit_recipes' => '-1',
        ), $options );

        $headers = $options['headers'] == 'false' ? false : true;
        $group_by = strtolower( $options['group_by'] );

        if( $headers && $group_by == 'none' ) {
            $group_by = 'name';
        }

        $sort_by = strtolower( $options['sort_by'] );
        $sort_order = strtoupper( $options['sort_order'] );
        $limit_author = $options['limit_author'];
        $limit_by_tag = $options['limit_by_tag'] == 'false' || $options['limit_by_values'] == 'false' || !taxonomy_exists( $options['limit_by_tag'] ) ? false : strtolower( $options['limit_by_tag'] );
        $limit_by_values = strtolower( $options['limit_by_values'] );

        $limit_recipes_nbr = intval( $options['limit_recipes'] ) != 0 ? intval( $options['limit_recipes'] ) : -1;

        $sort_by = in_array( $sort_by, array( 'author', 'name', 'title', 'date', 'rand') ) ? $sort_by : 'title';
        $sort_order = in_array( $sort_order, array( 'ASC', 'DESC' ) ) ? $sort_order : 'ASC';

        $recipes_grouped = array();
        $limit_recipes = array();

        if( $limit_by_tag != false && strlen( $limit_by_values ) > 0 ) {
            $tag_values = explode( ';', $limit_by_values );

            foreach( $tag_values as $term )
            {
                $limit_recipes = array_merge(
                    $limit_recipes,
                    WPUltimateRecipe::get()->query()->order_by( $sort_by )->order( $sort_order )->taxonomy( $limit_by_tag )->term( $term )->get()
                );
            }
        }
        else
        {
            $limit_recipes = WPUltimateRecipe::get()->query()->get();
        }

        // Only recipes from a specific author or list of authors
        if( !is_null( $limit_author ) && $limit_author != '' )
        {
            if( $limit_author == 'current_user' ) {
                $limit_author = get_current_user_id();
            }

            if( $limit_author == 0 ) { // Not logged in
                $limit_recipes = array();
            } else {
                $limit_recipes = $this->intersect_recipe_arrays(
                    $limit_recipes,
                    WPUltimateRecipe::get()->query()->order_by( $sort_by )->order( $sort_order )->author( $limit_author )->get()
                );
            }
        }

        if( !in_array( $group_by, array( 'none', 'name', 'title' ) ) && taxonomy_exists( $group_by ) )
        {
            $terms = get_terms( $group_by );

            foreach( $terms as $term ) {
                $recipes = WPUltimateRecipe::get()->query()->order_by( $sort_by )->order( $sort_order )->taxonomy( $group_by )->term( $term->slug )->limit( $limit_recipes_nbr )->get();
                $recipes = $this->intersect_recipe_arrays( $limit_recipes, $recipes );

                if( count( $recipes ) > 0 ) {
                    $recipes_grouped[] = array(
                        'header' => $term->name,
                        'recipes' => $recipes
                    );
                }
            }
        }
        else if( in_array( $group_by, array( 'name', 'title' ) ) )
        {
            $recipes = WPUltimateRecipe::get()->query()->order_by( 'title' )->order( $sort_order )->limit( $limit_recipes_nbr )->get();
            $recipes = $this->intersect_recipe_arrays( $limit_recipes, $recipes );

            $letters = array();

            foreach( $recipes as $recipe )
            {
                $title = $recipe->title();

                if($title != '')
                {
                    $first_letter = strtoupper( mb_substr( $title, 0, 1 ) );

                    if( !in_array( $first_letter, $letters ) )
                    {
                        $letters[] = $first_letter;
                        $recipes_grouped[] = array(
                            'header' => $first_letter,
                            'recipes' => array( $recipe )
                        );
                    } else {
                        $recipes_grouped[count( $recipes_grouped ) - 1]['recipes'][] = $recipe;
                    }
                }
            }
        }
        else if( $group_by == 'author' )
        {
            $args = array(
                'orderby' => 'display_name',
                'who' => 'authors'
            );
            $user_query = new WP_User_Query( $args );

            if( !empty( $user_query->results ) ) {
                foreach( $user_query->results as $user ) {

                    $author_recipes = $this->intersect_recipe_arrays(
                        $limit_recipes,
                        WPUltimateRecipe::get()->query()->order_by( $sort_by )->order( $sort_order )->limit( $limit_recipes_nbr )->author( $user->ID )->get()
                    );

                    if( !empty( $author_recipes )) {
                        $recipes_grouped[] = array(
                            'header' => $user->display_name,
                            'recipes' => $author_recipes
                        );
                    }
                }
            }
        }
        else
        {
            $recipes = $this->intersect_recipe_arrays(
                $limit_recipes,
                WPUltimateRecipe::get()->query()->order_by( $sort_by )->order( $sort_order )->taxonomy( $group_by )->limit( $limit_recipes_nbr )->get()
            );

            $recipes_grouped[] = array(
                'header' => false,
                'recipes' => $recipes
            );
        }

        $out = '<div class="wpurp-index-container">';
        if( count( $recipes_grouped ) == 0 ) {
            $out .= __( "You have to create a recipe first, check the 'Recipes' menu on the left.", 'wp-ultimate-recipe' );
        }
        else if( $recipes_grouped[0]['header'] == false )
        {
            foreach( $recipes_grouped[0]['recipes'] as $recipe )
            {
                $out .= '<a href="' . $recipe->link() . '">';
                $out .= $recipe->title();
                $out .= '</a><br/>';
            }
        }
        else
        {
            foreach( $recipes_grouped as $recipes_group )
            {
                $out .= '<h2>';
                $out .= $recipes_group['header'];
                $out .= '</h2>';

                foreach( $recipes_group['recipes'] as $recipe )
                {
                    $out .= '<a href="' . $recipe->link() . '">';
                    $out .= $recipe->title();
                    $out .= '</a><br/>';
                }
            }
        }

        $out .= '</div>';

        return $out;
    }

    /**
     * Limit array 2 by array 1
     */
    private function intersect_recipe_arrays( $arr1, $arr2 )
    {
        $allowed_recipes = array();

        foreach( $arr1 as $recipe )
        {
            $allowed_recipes[] = $recipe->ID();
        }

        foreach( $arr2 as $index => $recipe )
        {
            if( !in_array( $recipe->ID(), $allowed_recipes ) ) {
                unset( $arr2[$index] );
            }
        }

        return $arr2;
    }
}