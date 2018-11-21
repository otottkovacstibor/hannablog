<?php

class WPURP_Print_Shortcode {

    public function __construct()
    {
        add_shortcode( 'ultimate-recipe-print', array( $this, 'print_shortcode' ) );
    }

    function print_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'id' => '0',
            'text' => __( 'Print Recipe', 'wp-ultimate-recipe' ),
        ), $options );

        $recipe_post = null;

        if( $options['id'] ) {
            $recipe_post = get_post( intval( $options['id'] ) );
        } else {
            $recipe_post = get_post();
        }

        if( !is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' && !is_feed() )
        {
            $recipe = new WPURP_Recipe( $recipe_post );
            $output = '<a href="' . esc_attr( $recipe->link_print() ) . '" class="wpurp-print-recipe-shortcode" data-recipe-id="' . esc_attr( $recipe->ID() ) . '" rel="nofollow">' . esc_html( $options['text'] ) . '</a>';
        }
        else
        {
            $output = '';
        }

        return do_shortcode( $output );
    }
}