<?php

class WPURP_Template_Recipe_Ingredient_Name extends WPURP_Template_Block {

    public $editorField = 'recipeIngredientName';

    public function __construct( $type = 'recipe-ingredient-name' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) || !isset( $args['ingredient_name'] ) ) return '';

        $taxonomy = get_term_by('name', $args['ingredient_name'], 'ingredient');
        $taxonomy_slug = is_object( $taxonomy ) ? $taxonomy->slug : $args['ingredient_name'];
        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

        $output = $this->before_output();

        $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' );
        $plural = is_array( $plural ) ? false : $plural;
        $plural_data = $plural ? ' data-singular="' . esc_attr( $args['ingredient_name'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';

        $output .= '<span' . $this->style() . $plural_data . '>';

        $closing_tag = '';
        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' ) {

            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            } else {
                $custom_link = false;
            }

            if( WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) !== '1' ) {
                if( $custom_link !== false && $custom_link !== '' ) {
                    if( WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'nofollow_link' ) !== '1' ) {
                        $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';
                    } else {
                        $nofollow = ' rel="nofollow"';
                    }

                    $output .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
                    $closing_tag = '</a>';
                } else if( $ingredient_links != 'custom' ) {
                    $output .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
                    $closing_tag = '</a>';
                }
            }
        }

        $output .= $plural && ($args['ingredient_quantity_normalized'] > 1 || $args['ingredient_quantity_normalized'] == 0 || $args['ingredient_unit'] != '') ? $plural : $args['ingredient_name'];
        $output .= $closing_tag;
        $output .= '</span>';

        return $this->after_output( $output, $recipe, $args );
    }
}