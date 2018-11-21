<?php

class WPURP_Import_Xml_Ingredients extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-xml-ingredients' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_menu' ) );
        add_action( 'admin_menu', array( $this, 'import_manual_menu' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_xml_ingredients.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_xml_ingredients',
            )
        );
    }

    public function import_menu() {
        add_submenu_page( null, __( 'Import XML Ingredients', 'wp-ultimate-recipe' ), __( 'Import XML Ingredients', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_xml_ingredients', array( $this, 'import_page' ) );
    }

    public function import_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function import_manual_menu() {
        add_submenu_page( null, __( 'Import XML Ingredients', 'wp-ultimate-recipe' ), __( 'Import XML Ingredients', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_xml_ingredients_manual', array( $this, 'import_manual_page' ) );
    }

    public function import_manual_page() {
        if ( !wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' ) ) {
            die( 'Invalid nonce.' );
        }

        $ingredients = simplexml_load_file( $_FILES['xml']['tmp_name'] );

        echo '<h2>Ingredients Imported</h2>';

        $i = 1;
        foreach( $ingredients as $ingredient ) {
            $this->import_xml_ingredient( $ingredient, $i );
            $i++;
        }

        if( $i == 1 ) {
            echo 'No ingredients found';
        }
    }

    public function import_xml_ingredient( $xml_ingredient, $ingredient_number ) {
        $name = isset( $xml_ingredient->attributes()->name ) ? trim( (string) $xml_ingredient->attributes()->name ) : '';

        if( $name ) {
            // Check for existing ingredient
            $term = term_exists( $name, 'ingredient' );

            if ( $term === 0 || $term === null ) {
                $term = wp_insert_term( $name, 'ingredient' );
            }

            if( is_wp_error( $term ) ) {
                if( isset( $term->error_data['term_exists'] ) ) {
                    $ingredient_id = intval( $term->error_data['term_exists'] );
                } else {
                    echo 'Problem: ';
                    var_dump( $term );
                    die();
                }
            } else {
                $ingredient_id = intval( $term['term_id'] );
            }

            $ingredient = get_term( $ingredient_id, 'ingredient' );

            // Metadata
            $plural = isset( $xml_ingredient->attributes()->plural ) ? trim( (string) $xml_ingredient->attributes()->plural ) : '';
            $link = isset( $xml_ingredient->attributes()->link ) ? trim( (string) $xml_ingredient->attributes()->link ) : '';
            $group = isset( $xml_ingredient->attributes()->group ) ? trim( (string) $xml_ingredient->attributes()->group ) : '';

            WPURP_Taxonomy_MetaData::set( 'ingredient', $ingredient->slug, 'plural', $plural );
            WPURP_Taxonomy_MetaData::set( 'ingredient', $ingredient->slug, 'link', $link );
            WPURP_Taxonomy_MetaData::set( 'ingredient', $ingredient->slug, 'group', $group );

            if( isset( $xml_ingredient->attributes()->hide_link ) && $xml_ingredient->attributes()->hide_link == 'true' ) {
                WPURP_Taxonomy_MetaData::set( 'ingredient', $ingredient->slug, 'hide_link', true );
            } else {
                WPURP_Taxonomy_MetaData::delete( 'ingredient', $ingredient->slug, 'hide_link' );
            }

            // Nutritional information
            $nutrition_addon = WPUltimateRecipe::addon( 'nutritional-information' );
            if( $nutrition_addon ) {
                $nutrition = array();

                $nutrition['serving'] = isset( $xml_ingredient->attributes()->reference_serving ) ? trim( (string) $xml_ingredient->attributes()->reference_serving ) : '';
                $nutrition['serving_quantity'] = isset( $xml_ingredient->attributes()->metric_quantity ) ? trim( (string) $xml_ingredient->attributes()->metric_quantity ) : '';
                $nutrition['serving_unit'] = isset( $xml_ingredient->attributes()->metric_quantity_unit ) && $xml_ingredient->attributes()->metric_quantity_unit == 'ml' ? 'ml' : 'g';

                $nutrition_fields = $nutrition_addon->fields;

                foreach( $nutrition_fields as $field => $unit ) {
                    if( isset( $xml_ingredient->attributes()->$field ) ) {
                        $nutrition[$field] = trim( (string) $xml_ingredient->attributes()->$field );
                    }
                }

                $nutrition_addon->save_nutritional( $ingredient_id, $nutrition );
            }
        }

        echo $ingredient_number . '. <a href="' . admin_url( 'edit-tags.php?action=edit&taxonomy=ingredient&tag_ID=' . $ingredient_id . '&post_type=recipe' ) . '">' . $name . '</a><br/>';
    }
}

WPUltimateRecipe::loaded_addon( 'import-xml-ingredients', new WPURP_Import_Xml_Ingredients() );