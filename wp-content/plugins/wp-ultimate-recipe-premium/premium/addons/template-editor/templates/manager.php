<?php
$data = file_get_contents( 'php://input' );
$objData = json_decode( $data );

$wp_load_dir = isset( $objData->wp_load_dir ) ? $objData->wp_load_dir : false;
if( !$wp_load_dir || !file_exists( $wp_load_dir . 'wp-load.php' ) ) {
    $wp_load_dir = '../../../../../../../';
}
require_once( $wp_load_dir . 'wp-load.php' );

if( !current_user_can( 'manage_options' ) ) die( "You shouldn't be here" );

// Delete template
if( isset( $objData->template ) ) {
    WPUltimateRecipe::addon( 'custom-templates' )->delete_template( $objData->template );
}

// Load templates
$mapping = WPUltimateRecipe::addon( 'custom-templates' )->get_mapping();

$recipe_default = WPUltimateRecipe::option( 'recipe_template_recipe_template', 0 );
$print_default = WPUltimateRecipe::option( 'recipe_template_print_template', 1 );
$grid_default = WPUltimateRecipe::option( 'recipe_template_recipegrid_template', 2 );
$feed_default = WPUltimateRecipe::option( 'recipe_template_feed_template', 3 );
$user_menus_default = WPUltimateRecipe::option( 'user_menus_recipe_print_template', 1 );

$template_list = array();
foreach( $mapping as $index => $template )
{
    $active = array();

    if( $index == $recipe_default ) $active[] = 'Recipe Default';
    if( $index == $print_default ) $active[] = 'Print Default';
    if( $index == $grid_default ) $active[] = 'Recipe Grid Default';
    if( $index == $feed_default ) $active[] = 'RSS Feed Default';
    if( $index == $user_menus_default ) $active[] = 'User Menus Print Default';

    $template_list[] = array(
        'id' => $index,
        'name' => $template,
        'active' => implode( ', ', $active ),
    );
}

echo json_encode( $template_list );