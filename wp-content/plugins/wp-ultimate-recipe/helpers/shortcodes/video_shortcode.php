<?php

class WPURP_Video_Shortcode {

    public function __construct()
    {
        add_shortcode( 'recipe-video', array( $this, 'video_shortcode' ) );
    }

    function video_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'id' => ''
        ), $options );

        if( $options['id'] ) {
            $recipe_post = get_post( intval( $options['id'] ) );
        } else {
            $recipe_post = get_post();
        }

        if( ! is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' )
        {
            $recipe = new WPURP_Recipe( $recipe_post );
            $output = $recipe->video();
        }
        else
        {
            $output = '';
        }

        return $output;
    }
}