<?php

class WPURP_Partners {

    public function __construct()
    {
        add_filter( 'init', array( $this, 'enable_mediavine' ) );
    }

    public function enable_mediavine()
    {
        if( WPUltimateRecipe::option( 'partners_integrations_mediavine_enable', '0' ) == '1' || WPUltimateRecipe::option( 'partners_integrations_adthrive_enable', '0' ) == '1' ) {
            add_filter( 'wpurp_output_recipe_block_recipe-instructions', array( $this, 'mediavine_instructions_ad' ), 10, 4 );
        }
    }

    public function mediavine_instructions_ad( $output, $recipe, $block, $args )
    {
        if( isset( $args['desktop'] ) && $args['desktop'] ) {
            // Desktop ad unit
            $ad = '<div id="recipe_desktop_target"></div>';
            $ad_container = '<div class="wpurp-mediavine-desktop">' . $ad . '</div>';

            $inline_style = WPUltimateRecipe::option( 'recipe_template_force_style', '1' ) == '1' ? 'display:inline !important;' : 'display:inline;';
            $output = str_replace( 'wpurp-recipe-instruction-group" style="', 'wpurp-recipe-instruction-group" style="' . $inline_style, $output );
            $output = str_replace( 'wpurp-recipe-instruction-text" style="', 'wpurp-recipe-instruction-group" style="' . $inline_style, $output );

            $output = $ad_container . $output;
        } else {
            // Mobile ad unit
            $ad = '<div id="recipe_mobile_target"></div>';
            $ad_container = '<div class="wpurp-mediavine-mobile" style="margin:10px 0;width:200px;height:200px;border:1px solid black;">' . $ad . '</div>';

            $output = $ad_container . $output;
        }


        return $output;
    }
}