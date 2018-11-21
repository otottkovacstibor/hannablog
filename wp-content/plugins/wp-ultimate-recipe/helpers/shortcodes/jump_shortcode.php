<?php

class WPURP_Jump_Shortcode {

    public function __construct()
    {
        add_shortcode( 'ultimate-recipe-jump', array( $this, 'jump_shortcode' ) );
    }

    function jump_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'id' => '0',
            'text' => __( 'Jump to Recipe', 'wp-ultimate-recipe' ),
        ), $options );

        $recipe_post = null;

        if( $options['id'] ) {
            $recipe_post = get_post( intval( $options['id'] ) );
        } else {
            $recipe_post = get_post();
        }

        if( !is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' && !is_feed() )
        {
            $output = '<a href="#wpurp-container-recipe-' . esc_attr( $recipe_post->ID ) . '" class="wpurp-jump-to-recipe-shortcode">' . esc_html( $options['text'] ) . '</a>';
        }
        else
        {
            $output = '';
        }

        return do_shortcode( $output );
    }
}