<?php
/**
 * Template for the plugin settings structure.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/settings
 */

$premium_active = class_exists( 'WPRM_Addons' ) && WPRM_Addons::is_active( 'premium' );
$premium_only = $premium_active ? '' : ' (' . __( 'WP Recipe Maker Premium only', 'wp-recipe-maker' ) . ')';

// Appearance.
require_once( 'group-recipe-template.php' );
require_once( 'group-labels.php' );
require_once( 'group-recipe-print.php' );
require_once( 'group-recipe-times.php' );
require_once( 'group-nutrition-label.php' );
require_once( 'group-custom-style.php' );

// Interactivity.
require_once( 'group-recipe-snippets.php' );
require_once( 'group-recipe-ratings.php' );
require_once( 'group-adjustable-servings.php' );
require_once( 'group-social-sharing.php' );
require_once( 'group-ingredient-links.php' );
require_once( 'group-unit-conversion.php' );
require_once( 'group-recipe-submission.php' );

// Backend.
require_once( 'group-recipe-defaults.php' );
require_once( 'group-import.php' );

// Advanced.
require_once( 'group-metadata.php' );
require_once( 'group-permissions.php' );
require_once( 'group-settings-tools.php' );

$settings_structure = array(
	array( 'header' => __( 'Appearance', 'wp-recipe-maker' ) ),
	$recipe_template,
	$labels,
	$recipe_print,
	// $recipe_times,
	$nutrition_label,
	$custom_style,
	array( 'header' => __( 'Interactivity', 'wp-recipe-maker' ) ),
	$recipe_snippets,
	$recipe_ratings,
	$adjustable_servings,
	$social_sharing,
	$ingredient_links,
	$unit_conversion,
	$recipe_submission,
	array( 'header' => __( 'Backend', 'wp-recipe-maker' ) ),
	$recipe_defaults,
	$import,
	array( 'header' => __( 'Advanced', 'wp-recipe-maker' ) ),
	$metadata,
	$permissions,
	$settings_tools,
);
