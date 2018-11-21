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

$recipe_template = array(
	'id' => 'recipeTemplate',
	'name' => __( 'Recipe Template', 'wp-recipe-maker' ),
	'settings' => array(
		array(
			'type' => 'button',
			'description' => __( 'The "Modern" template mode is highly recommended. Use "Legacy" for backwards compatibility only.', 'wp-recipe-maker' ),
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'legacy',
			),
			'button' => __( 'Learn more in the Migration Guide', 'wp-recipe-maker' ),
			'link' => 'https://help.bootstrapped.ventures/article/111-migrating-from-legacy-to-modern-mode',
		),
		array(
			'id' => 'recipe_template_mode',
			'name' => __( 'Template Mode', 'wp-recipe-maker' ),
			'type' => 'dropdown',
			'options' => array(
				'legacy' => __( 'Legacy', 'wp-recipe-maker' ),
				'modern' => __( 'Modern', 'wp-recipe-maker' ),
			),
			'default' => 'modern',
		),
		array(
			'id' => 'default_recipe_template_modern',
			'name' => __( 'Default Recipe Template', 'wp-recipe-maker' ),
			'description' => __( 'Default template to use for the recipes on your website.', 'wp-recipe-maker' ),
			'type' => 'dropdownTemplateModern',
			'default' => 'compact',
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'modern',
			),
		),
		array(
			'id' => 'default_recipe_archive_template',
			'name' => __( 'Default Recipe Archive Template', 'wp-recipe-maker' ),
			'description' => __( 'Default template to use in archives (like home and category pages).', 'wp-recipe-maker' ),
			'type' => 'dropdownTemplateModern',
			'default' => 'compact',
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'modern',
			),
		),
		array(
			'id' => 'default_recipe_amp_template',
			'name' => __( 'Default Recipe AMP Template', 'wp-recipe-maker' ),
			'description' => __( 'Default template to use for AMP pages.', 'wp-recipe-maker' ),
			'type' => 'dropdownTemplateModern',
			'default' => 'basic',
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'modern',
			),
		),
		array(
			'id' => 'default_recipe_template',
			'name' => __( 'Default Recipe Template', 'wp-recipe-maker' ),
			'description' => __( 'Default template to use for the recipes on your website.', 'wp-recipe-maker' ),
			'type' => 'dropdownTemplateLegacy',
			'default' => 'simple',
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'legacy',
			),
		),
	),
	'subGroups' => array(
		array(
			'name' => __( 'Template Editor', 'wp-recipe-maker' ),
			'description' => __( 'Use the Template Editor to manage and customize all modern templates on your website.', 'wp-recipe-maker' ),
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'modern',
			),
			'settings' => array(
				array(
					'name' => __( 'Template Editor', 'wp-recipe-maker' ),
					'documentation' => 'https://help.bootstrapped.ventures/article/53-template-editor',
					'type' => 'button',
					'button' => __( 'Open the Template Editor', 'wp-recipe-maker' ),
					'link' => admin_url( 'admin.php?page=wprm_template_editor' ),
				),
				array(
					'id' => 'template_editor_preview_recipe',
					'name' => __( 'Default Preview Recipe', 'wp-recipe-maker' ),
					'description' => __( 'Default recipe to use for the Template Editor preview.', 'wp-recipe-maker' ),
					'type' => 'dropdownRecipe',
					'default' => false,
				),
			),
		),
		array(
			'name' => __( 'Template Options', 'wp-recipe-maker' ),
			'description' => __( 'Note: not all options will affect every recipe template.', 'wp-recipe-maker' ),
			'dependency' => array(
				'id' => 'recipe_template_mode',
				'value' => 'legacy',
			),
			'settings' => array(
				array(
					'id' => 'template_font_size',
					'name' => __( 'Base Font Size', 'wp-recipe-maker' ),
					'description' => __( 'Leave blank to use the template default.', 'wp-recipe-maker' ),
					'type' => 'number',
					'suffix' => 'px',
					'default' => '',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
				array(
					'id' => 'template_font_header',
					'name' => __( 'Header Font', 'wp-recipe-maker' ),
					'description' => __( "Type the name of the font you'd like to use. Make sure the font is already loaded.", 'wp-recipe-maker' ) . ' ' . __( 'Leave blank to use the template default.', 'wp-recipe-maker' ),
					'type' => 'text',
					'default' => '',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
				array(
					'id' => 'template_font_regular',
					'name' => __( 'Regular Font', 'wp-recipe-maker' ),
					'description' => __( "Type the name of the font you'd like to use. Make sure the font is already loaded.", 'wp-recipe-maker' ) . ' ' . __( 'Leave blank to use the template default.', 'wp-recipe-maker' ),
					'type' => 'text',
					'default' => '',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
				array(
					'id' => 'template_recipe_image',
					'name' => __( 'Recipe Image Size', 'wp-recipe-maker' ),
					'description' => __( 'Leave blank to use the template default.', 'wp-recipe-maker' ) . ' ' . __( 'Type the name of a thumbnail size or the exact size you want.', 'wp-recipe-maker' ) . ' ' . __( 'For example:', 'wp-recipe-maker' ) . ' thumbnail or 200x200',
					'type' => 'text',
					'default' => '',
				),
				array(
					'id' => 'template_instruction_image',
					'name' => __( 'Instruction Image Size', 'wp-recipe-maker' ),
					'description' => __( 'Leave blank to use the template default.', 'wp-recipe-maker' ) . ' ' . __( 'Type the name of a thumbnail size or the exact size you want.', 'wp-recipe-maker' ) . ' ' . __( 'For example:', 'wp-recipe-maker' ) . ' thumbnail or 200x200',
					'type' => 'text',
					'default' => '',
				),
				array(
					'id' => 'template_instruction_image_alignment',
					'name' => __( 'Instruction Image Alignment', 'wp-recipe-maker' ),
					'type' => 'dropdown',
					'options' => array(
						'left' => __( 'Left', 'wp-recipe-maker' ),
						'center' => __( 'Center', 'wp-recipe-maker' ),
						'right' => __( 'Right', 'wp-recipe-maker' ),
					),
					'default' => 'left',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
				array(
					'id' => 'template_ingredient_list_style',
					'name' => __( 'Ingredient List Style', 'wp-recipe-maker' ),
					'type' => 'dropdown',
					'options' => array(
						'none' => __( 'None', 'wp-recipe-maker' ),
						'checkbox' => __( 'Checkbox', 'wp-recipe-maker' ) . $premium_only,
						'circle' => __( 'Circle', 'wp-recipe-maker' ),
						'disc' => __( 'Disc', 'wp-recipe-maker' ),
						'square' => __( 'Square', 'wp-recipe-maker' ),
						'decimal' => __( 'Decimal', 'wp-recipe-maker' ),
						'decimal-leading-zero' => __( 'Decimal with leading zero', 'wp-recipe-maker' ),
						'lower-roman' => __( 'Lower Roman', 'wp-recipe-maker' ),
						'upper-roman' => __( 'Upper Roman', 'wp-recipe-maker' ),
						'lower-latin' => __( 'Lower Latin', 'wp-recipe-maker' ),
						'upper-latin' => __( 'Upper Latin', 'wp-recipe-maker' ),
						'lower-greek' => __( 'Lower Greek', 'wp-recipe-maker' ),
						'armenian' => __( 'Armenian', 'wp-recipe-maker' ),
						'georgian' => __( 'Georgian', 'wp-recipe-maker' ),
					),
					'default' => 'disc',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
				array(
					'id' => 'template_instruction_list_style',
					'name' => __( 'Instruction List Style', 'wp-recipe-maker' ),
					'type' => 'dropdown',
					'options' => array(
						'none' => __( 'None', 'wp-recipe-maker' ),
						'checkbox' => __( 'Checkbox', 'wp-recipe-maker' ) . $premium_only,
						'circle' => __( 'Circle', 'wp-recipe-maker' ),
						'disc' => __( 'Disc', 'wp-recipe-maker' ),
						'square' => __( 'Square', 'wp-recipe-maker' ),
						'decimal' => __( 'Decimal', 'wp-recipe-maker' ),
						'decimal-leading-zero' => __( 'Decimal with leading zero', 'wp-recipe-maker' ),
						'lower-roman' => __( 'Lower Roman', 'wp-recipe-maker' ),
						'upper-roman' => __( 'Upper Roman', 'wp-recipe-maker' ),
						'lower-latin' => __( 'Lower Latin', 'wp-recipe-maker' ),
						'upper-latin' => __( 'Upper Latin', 'wp-recipe-maker' ),
						'lower-greek' => __( 'Lower Greek', 'wp-recipe-maker' ),
						'armenian' => __( 'Armenian', 'wp-recipe-maker' ),
						'georgian' => __( 'Georgian', 'wp-recipe-maker' ),
					),
					'default' => 'decimal',
					'dependency' => array(
						'id' => 'features_custom_style',
						'value' => true,
					),
				),
			),
		),
		array(
			'name' => __( 'Template Colors', 'wp-recipe-maker' ),
			'dependency' => array(
				array(
				'id' => 'features_custom_style',
				'value' => true,
			),
				array(
					'id' => 'recipe_template_mode',
					'value' => 'legacy',
				),
			),
			'settings' => array(
				array(
					'id' => 'template_color_background',
					'name' => __( 'Background Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#ffffff',
				),
				array(
					'id' => 'template_color_border',
					'name' => __( 'Border Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#aaaaaa',
				),
				array(
					'id' => 'template_color_text',
					'name' => __( 'Text Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#333333',
				),
				array(
					'id' => 'template_color_link',
					'name' => __( 'Link Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#3498db',
				),
				array(
					'id' => 'template_color_header',
					'name' => __( 'Header Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#000000',
				),
				array(
					'id' => 'template_color_icon',
					'name' => __( 'Icon Color', 'wp-recipe-maker' ),
					'description' => __( 'Used for the color of the star ratings and other icons.', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#343434',
				),
				array(
					'id' => 'template_color_accent',
					'name' => __( 'Accent Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#2c3e50',
				),
				array(
					'id' => 'template_color_accent_text',
					'name' => __( 'Accent Text Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#ffffff',
				),
				array(
					'id' => 'template_color_accent2',
					'name' => __( 'Accent 2 Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#3498db',
				),
				array(
					'id' => 'template_color_accent2_text',
					'name' => __( 'Accent 2 Text Color', 'wp-recipe-maker' ),
					'type' => 'color',
					'default' => '#ffffff',
				),
			),
		),
	),
);

if ( ! $premium_active ) {
	$recipe_template['description'] = __( 'Get access to more recipe templates with WP Recipe Maker Premium.', 'wp-recipe-maker' );
	$recipe_template['documentation'] = 'https://help.bootstrapped.ventures/article/53-template-editor';
}
