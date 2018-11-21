<?php

class WPURP_Amp {

    public function __construct()
    {
        // Use init instead of amp_init or add_rewrite_endpoint doesn't work
        add_action( 'init', array( $this, 'amp_support' ) );

        add_filter( 'amp_post_template_metadata', array( $this, 'metadata' ), 10, 2 );
        add_filter( 'the_content', array( $this, 'content_filter' ), 10 );
    }

    public function amp_support()
    {
        if( defined( 'AMP_QUERY_VAR' ) ) {
            add_rewrite_endpoint( AMP_QUERY_VAR, EP_PERMALINK | EP_RECIPE );
            add_post_type_support( WPURP_POST_TYPE, AMP_QUERY_VAR );
        }
    }

    public function metadata( $metadata, $post ) {
        if( $post->post_type == WPURP_POST_TYPE ) {
            $recipe = new WPURP_Recipe( $post );
            $metadata = array_merge( $metadata, WPUltimateRecipe::get()->helper( 'metadata' )->get_metadata_array( $recipe ) );
        }

        return $metadata;
    }

    public function content_filter( $content )
    {
        if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() && get_post_type() == 'recipe' ) {
            remove_filter( 'the_content', array( $this, 'content_filter' ), 10 );

            $recipe = new WPURP_Recipe( get_post() );
            $recipe_box = apply_filters( 'wpurp_output_recipe', $recipe->output_string( 'amp' ), $recipe );

            if( strpos( $content, '[recipe]' ) !== false ) {
                $content = str_replace( '[recipe]', $recipe_box, $content );
            } else {
                $content .= $recipe_box;
            }

            // Remove searchable part
            $content = preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content);

            add_filter( 'the_content', array( $this, 'content_filter' ), 10 );
        }

        return $content;
    }
}