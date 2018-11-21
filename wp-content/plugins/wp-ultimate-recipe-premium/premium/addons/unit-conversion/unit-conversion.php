<?php

class WPURP_Unit_Conversion extends WPURP_Premium_Addon {

    public function __construct( $name = 'unit-conversion' ) {
        parent::__construct( $name );

        add_action( 'init', array( $this, 'assets' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'name' => 'js-quantities',
                'file' => $this->addonPath . '/vendor/js-quantities.js',
                'premium' => true,
                'public' => true,
                'admin' => true,
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'name' => 'wpurp-unit-conversion',
                'file' => $this->addonPath . '/js/unit-conversion.js',
                'premium' => true,
                'public' => true,
                'admin' => true,
                'deps' => array(
                    'jquery',
                    'fraction',
                    'js-quantities',
                ),
                'data' => array(
                    'name' => 'wpurp_unit_conversion',
                    'alias_to_unit'         => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_alias_to_unit(),
                    'unit_to_type'          => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_to_type(),
                    'universal_units'       => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_universal_units(),
                    'systems'               => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_active_systems(),
                    'unit_abbreviations'    => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_abbreviations(),
                    'user_abbreviations'    => WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_user_abbreviations(),
                )
            )
        );
    }
}

WPUltimateRecipe::loaded_addon( 'unit-conversion', new WPURP_Unit_Conversion() );