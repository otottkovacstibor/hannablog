<?php
/*
 * -> 3.4.0
 *
 * Performance improvements related to the cache and autoloading options
 */

delete_option( 'wpurp_custom_template_preview' );

// Don't autoload all templates
$custom_templates = WPUltimateRecipe::get()->addon( 'custom-templates' );

$mapping = $custom_templates->get_mapping();

foreach( $mapping as $id => $name ) {
    $template = $custom_templates->get_template_code( $id );

    update_option( 'wpurp_custom_template_' . $id, array(), false );
    update_option( 'wpurp_custom_template_' . $id, $template, false );
}

// Trigger cache reset
WPUltimateRecipe::get()->helper( 'cache' )->trigger_reset();