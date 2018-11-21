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

$recipe_defaults = array(
	'id' => 'recipeDefaults',
	'name' => __( 'Recipe Defaults', 'wp-recipe-maker' ),
	'settings' => array(
		array(
			'id' => 'recipe_image_use_featured',
			'name' => __( 'Use image from parent post', 'wp-recipe-maker' ),
			'description' => __( 'Use featured image of parent post if no recipe image is set.', 'wp-recipe-maker' ),
			'type' => 'toggle',
			'default' => false,
		),
		array(
			'id' => 'recipe_author_display_default',
			'name' => __( 'Default Author', 'wp-recipe-maker' ),
			'description' => __( 'Default value for the Recipe Author field when creating a new recipe.', 'wp-recipe-maker' ),
			'type' => 'dropdown',
			'options' => array(
				'disabled' => __( "Don't show", 'wp-recipe-maker' ),
				'post_author' => __( 'Name of post author', 'wp-recipe-maker' ),
				'custom' => __( 'Custom author name', 'wp-recipe-maker' ),
			),
			'default' => 'disabled',
		),
		array(
			'id' => 'recipe_author_custom_default',
			'name' => __( 'Default Custom Author Name', 'wp-recipe-maker' ),
			'type' => 'text',
			'dependency' => array(
				'id' => 'recipe_author_display_default',
				'value' => 'custom',
			),
			'default' => '',
		),
	),
);
