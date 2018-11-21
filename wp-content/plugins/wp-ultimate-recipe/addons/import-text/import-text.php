<?php

class WPURP_Import_Text extends WPURP_Addon {

    public function __construct( $name = 'import-text' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
    }

    public function assets() {
        $units = array_keys( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_alias_to_unit() );
        $units = array_merge(
            $units,
            explode( ';', WPUltimateRecipe::option( 'import_recipes_generic_units' ) )
        );

        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_text.css',
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
            ),
            array(
                'name' => 'rangy-core',
                'file' => $this->addonPath . '/vendor/rangy-core.js',
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'jquery',
                )
            ),
            array(
                'name' => 'rangy-css',
                'file' => $this->addonPath . '/vendor/rangy-cssclassapplier.js',
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'rangy-core',
                ),
            ),
            array(
                'file' => $this->addonPath . '/js/import_text.js',
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'jquery',
                    'rangy-core',
                    'rangy-css',
                ),
                'data' => array(
                    'name' => 'wpurp_import_text',
                    'units' => $units,
                )
            )
        );
    }

    public function meta_box()
    {
        require( $this->addonDir . '/templates/meta_box.php' );
    }
}

WPUltimateRecipe::loaded_addon( 'import-text', new WPURP_Import_Text() );