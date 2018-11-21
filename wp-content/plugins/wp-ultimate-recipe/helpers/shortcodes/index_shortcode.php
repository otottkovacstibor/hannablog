<?php

class WPURP_Index_Shortcode {

    public function __construct()
    {
        add_shortcode( 'ultimate-recipe-index', array( $this, 'index_shortcode' ) );
    }

    function index_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'headers' => 'false'
        ), $options );

        $query = WPUltimateRecipe::get()->helper( 'query_recipes' );
        $recipes = $query->order_by( 'title' )->order( 'ASC' )->get();

        $out = '<div class="wpurp-index-container">';
        if( $recipes ) {

            $letters = array();

            foreach( $recipes as $recipe )
            {
                $title = $recipe->title();

                if( $title )
                {
                    if ( $options['headers'] != 'false' )
                    {
                        $first_letter = strtoupper( mb_substr( $title, 0, 1 ) );

                        if( !in_array( $first_letter, $letters ) )
                        {
                            $letters[] = $first_letter;
                            $header_out = '<h2>';
                            $header_out .= $first_letter;
                            $header_out .= '</h2>';

                            $out .= apply_filters( 'wpurp_index_header', $header_out, $first_letter );
                        }
                    }

                    $recipe_out = '<a href="' . $recipe->link() . '">';
                    $recipe_out .= $title;
                    $recipe_out .= '</a><br/>';

                    $out .= apply_filters( 'wpurp_index_recipe', $recipe_out, $recipe );
                }
            }
        }
        else
        {
            $out .= __( "You have to create a recipe first, check the 'Recipes' menu on the left.", 'wp-ultimate-recipe' );
        }
        $out .= '</div>';

        return apply_filters( 'wpurp_index', $out );
    }
}