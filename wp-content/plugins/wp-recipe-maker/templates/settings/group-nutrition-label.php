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

$nutrition_label = array(
	'id' => 'nutritionLabel',
	'name' => __( 'Nutrition Label', 'wp-recipe-maker' ),
	'required' => 'premium',
	'subGroups' => array(
		array(
			'name' => __( 'Display Manually', 'wp-recipe-maker' ),
			'description' => __( 'Type the [wprm-nutrition-label] or [wprm-nutrition-label id="123" align="center"] shortcode where you want the label to appear.', 'wp-recipe-maker' ),
			'documentation' => 'https://help.bootstrapped.ventures/article/22-nutrition-label',
		),
		array(
			'name' => __( 'Display Automatically', 'wp-recipe-maker' ),
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'legacy',
			),
			'settings' => array(
				array(
					'id' => 'show_nutrition_label',
					'name' => __( 'Nutrition Label in template', 'wp-recipe-maker' ),
					'description' => __( 'Display the nutrition label at its default location in the recipe template.', 'wp-recipe-maker' ),
					'type' => 'dropdown',
					'options' => array(
						'disabled' => __( "Don't show", 'wp-recipe-maker' ),
						'left' => __( 'Show - Aligned Left', 'wp-recipe-maker' ),
						'center' => __( 'Show - Aligned Center', 'wp-recipe-maker' ),
						'right' => __( 'Show - Aligned Right', 'wp-recipe-maker' ),
					),
					'default' => 'disabled',
				),
			),
		),
		array(
			'name' => __( 'Appearance', 'wp-recipe-maker' ),
			'settings' => array(
				array(
					'id' => 'nutrition_label_zero_values',
					'name' => __( 'Show values when 0', 'wp-recipe-maker' ),
					'description' => __( 'Show nutrient in the nutrition label when it has a value of 0.', 'wp-recipe-maker' ),
					'type' => 'toggle',
					'default' => false,
				),
			),
		),
	),
);

if ( class_exists( 'WPRM_Addons' ) && ! WPRM_Addons::is_active( 'pro' ) ) {
	$nutrition_label['description'] = __( 'Get the Pro Bundle to have our Nutrition API integration help calculate the nutrition facts for you.', 'wp-recipe-maker' );
	$nutrition_label['documentation'] = 'https://help.bootstrapped.ventures/article/21-nutrition-facts-calculation';
}
