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

$ingredient_links = array(
	'id' => 'ingredientLinks',
	'name' => __( 'Ingredient Links', 'wp-recipe-maker' ),
	'required' => 'premium',
	'description' => __( 'Ingredient links can be set when editing a recipe or through the WP Recipe Maker > Manage > Ingredients page.', 'wp-recipe-maker' ),
	'documentation' => 'https://help.bootstrapped.ventures/article/29-ingredient-links',
	'settings' => array(
		array(
			'id' => 'ingredient_links_open_in_new_tab',
			'name' => __( 'Open in New Tab', 'wp-recipe-maker' ),
			'description' => __( 'Open ingredient links in a new tab.', 'wp-recipe-maker' ),
			'type' => 'toggle',
			'default' => false,
		),
		array(
			'id' => 'ingredient_links_use_nofollow',
			'name' => __( 'Default Use Nofollow', 'wp-recipe-maker' ),
			'description' => __( 'Add the nofollow attribute to ingredient links by default.', 'wp-recipe-maker' ),
			'type' => 'toggle',
			'default' => false,
		),
	),
);
