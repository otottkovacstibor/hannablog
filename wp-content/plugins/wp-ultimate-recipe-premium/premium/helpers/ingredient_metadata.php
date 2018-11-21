<?php

class WPURP_Ingredient_Metadata {

    public function __construct()
    {
        new WPURP_Taxonomy_MetaData( 'ingredient', array(
            'plural' => array(
                'label'       => __( 'Plural', 'wp-ultimate-recipe' ),
                'desc'        => __( 'Optional plural version of this ingredient.', 'wp-ultimate-recipe' ),
                'placeholder' => '',
            ),
            'link' => array(
                'label'       => __( 'Link', 'wp-ultimate-recipe' ),
                'desc'        => __( 'Send your visitors to a specific link when clicking on an ingredient.', 'wp-ultimate-recipe' ),
                'placeholder' => 'http://www.example.com',
            ),
            'hide_link' => array(
                'label'       => __( 'Hide link', 'wp-ultimate-recipe' ),
                'desc'        => __( "Don't use a link in the ingredients list for this ingredient.", 'wp-ultimate-recipe' ),
                'type'        => 'checkbox',
            ),
            'group' => array(
                'label'       => __( 'Group', 'wp-ultimate-recipe' ),
                'desc'        => __( 'Use this to group ingredients in the shopping list.', 'wp-ultimate-recipe' ),
                'placeholder' => __( 'Vegetables', 'wp-ultimate-recipe' ),
            ),
        ) );

        add_filter( 'manage_edit-ingredient_columns', array( $this, 'add_metadata_column_to_ingredients' ) );
        add_filter( 'manage_ingredient_custom_column', array( $this, 'add_metadata_column_content' ), 10, 3 );
    }

    public function add_metadata_column_to_ingredients($columns)
    {
        $columns['plural'] = __( 'Plural', 'wp-ultimate-recipe' );
        $columns['link'] = __( 'Link', 'wp-ultimate-recipe' );
        return $columns;
    }

    public function add_metadata_column_content($content, $column_name, $term_id)
    {
        $term = get_term( $term_id, 'ingredient' );

        if( $column_name == 'link' ) {
            $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $term->slug, 'link' );

            if( $custom_link ) {
                $content = $custom_link;
            }
        } else if( $column_name == 'plural' ) {
            $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $term->slug, 'plural' );

            if( $plural ) {
                $content = $plural;
            }
        }

        return $content;
    }
}