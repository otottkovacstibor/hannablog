<?php

class WPURP_Favorite_Recipes extends WPURP_Premium_Addon {

    public function __construct( $name = 'favorite-recipes' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );

        // Ajax
        add_action( 'wp_ajax_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );

        // Shortcode
        add_shortcode( 'ultimate-recipe-favorites', array( $this, 'favorite_recipes_shortcode' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/js/favorite-recipes.js',
                'premium' => true,
                'public' => true,
                'setting' => array( 'favorite_recipes_enabled', '1' ),
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_favorite_recipe',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_favorite_recipe' ),
                )
            )
        );
    }

    public function ajax_favorite_recipe()
    {
        if(check_ajax_referer( 'wpurp_favorite_recipe', 'security', false ) )
        {
            $recipe_id = intval( $_POST['recipe_id'] );
            $user_id = get_current_user_id();

            $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            if( in_array( $recipe_id, $favorites ) ) {
                $key = array_search( $recipe_id, $favorites );
                unset( $favorites[$key ] );
            } else {
                $favorites[] = $recipe_id;
            }

            update_user_meta( $user_id, 'wpurp_favorites', $favorites );
        }

        die();
    }

    public static function is_favorite_recipe( $recipe_id )
    {
        $user_id = get_current_user_id();

        $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
        $favorites = is_array( $favorites ) ? $favorites : array();

        return in_array( $recipe_id, $favorites );
    }

    public function favorite_recipes_shortcode( $options )
    {
        $options = shortcode_atts( array(
        ), $options );

        $user_id = get_current_user_id();

        $output = '';

        if( $user_id !== 0 ) {
            $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            $recipes = WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if( count( $favorites ) == 0 || count( $recipes ) == 0 ) {
                $output .= '<p class="wpurp-no-favorite-recipes">' . __( "You don't have any favorite recipes.", 'wp-ultimate-recipe' ) . '</p>';
            } else {
                $output .= '<ul class="wpurp-favorite-recipes">';
                foreach ( $recipes as $recipe ) {
                    $item = '<li><a href="' . $recipe->link() . '">' . $recipe->title() . '</a></li>';
                    $output .= apply_filters( 'wpurp_favorite_recipes_item', $item, $recipe );
                }
                $output .= '</ul>';
            }
        }

        return $output;
    }
}

WPUltimateRecipe::loaded_addon( 'favorite-recipes', new WPURP_Favorite_Recipes() );