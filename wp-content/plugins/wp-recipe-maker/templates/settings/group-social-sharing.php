<?php
/**
 * Template for the plugin settings structure.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/settings
 */



$social_sharing = array(
	'id' => 'socialSharing',
	'name' => __( 'Social Sharing', 'wp-recipe-maker' ),
	'subGroups' => array(
		array(
			'name' => __( 'Pinterest', 'wp-recipe-maker' ),
			'settings' => array(
				array(
					'id' => 'pinterest_use_for_image',
					'name' => __( 'Pin Image', 'wp-recipe-maker' ),
					'description' => __( 'Image to use for the Pin Recipe shortcode.', 'wp-recipe-maker' ),
					'documentation' => 'https://help.bootstrapped.ventures/article/49-pin-recipe-image',
					'type' => 'dropdown',
					'options' => array(
						'recipe_image' => __( 'Recipe Image', 'wp-recipe-maker' ),
						'custom' => __( 'Custom Image per Recipe', 'wp-recipe-maker' ) . $premium_only,
						'custom_or_recipe_image' => __( 'Custom Image if set, otherwise recipe image', 'wp-recipe-maker' ) . $premium_only,
					),
					'default' => 'recipe_image',
				),
				array(
					'description' => __( 'You can set the pin image when editing a recipe.', 'wp-recipe-maker' ),
					'required' => 'premium',
					'dependency' => array(
						'id' => 'pinterest_use_for_image',
						'value' => 'recipe_image',
						'type' => 'inverse',
					),
				),
				array(
					'id' => 'pinterest_use_for_description',
					'name' => __( 'Pin Description', 'wp-recipe-maker' ),
					'description' => __( 'What to use for the pin description.', 'wp-recipe-maker' ),
					'type' => 'dropdown',
					'options' => array(
						'recipe_name' => __( 'Recipe Name', 'wp-recipe-maker' ),
						'recipe_summary' => __( 'Recipe Summary', 'wp-recipe-maker' ),
						'image_title' => __( 'Image Title', 'wp-recipe-maker' ),
						'image_caption' => __( 'Image Caption', 'wp-recipe-maker' ),
						'image_description' => __( 'Image Description', 'wp-recipe-maker' ),
						'custom' => __( 'Custom Text', 'wp-recipe-maker' ),
					),
					'default' => 'recipe_name',
				),
				array(
					'id' => 'pinterest_custom_description',
					'name' => __( 'Custom Description', 'wp-recipe-maker' ),
					'description' => __( 'You can use the following placeholders: %recipe_name%', 'wp-recipe-maker' ),
					'type' => 'textarea',
					'default' => '',
					'dependency' => array(
						'id' => 'pinterest_use_for_description',
						'value' => 'custom',
					),
				),
			),
		),
	),
);
