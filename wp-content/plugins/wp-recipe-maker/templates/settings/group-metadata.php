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

$metadata = array(
	'id' => 'metadata',
	'name' => __( 'Recipe Metadata', 'wp-recipe-maker' ),
	'settings' => array(
		array(
			'id' => 'metadata_keywords_in_template',
			'name' => __( 'Show keywords in template', 'wp-recipe-maker' ),
			'description' => __( 'Show keywords in the recipe template as well as the metadata.', 'wp-recipe-maker' ),
			'documentation' => 'https://developers.google.com/search/docs/data-types/recipe',
			'type' => 'toggle',
			'default' => true,
		),
		array(
			'id' => 'metadata_pinterest_optout',
			'name' => __( 'Opt out of Rich Pins', 'wp-recipe-maker' ),
			'description' => __( 'Tell Pinterest NOT to display my pins as rich pins. This will affect your entire website.', 'wp-recipe-maker' ),
			'type' => 'toggle',
			'default' => false,
		),
		array(
			'id' => 'metadata_nonfood_allowed',
			'name' => __( 'Allow non-food recipes', 'wp-recipe-maker' ),
			'description' => __( 'Get the option to set the recipe type as "Non-Food" for individual recipes. When you set a recipe as "Non-Food" we will NOT output the recipe metadata as per Google\'s guidelines.', 'wp-recipe-maker' ),
			'documentation' => 'https://help.bootstrapped.ventures/article/75-non-food-recipes',
			'type' => 'toggle',
			'default' => false,
		),
	),
);
