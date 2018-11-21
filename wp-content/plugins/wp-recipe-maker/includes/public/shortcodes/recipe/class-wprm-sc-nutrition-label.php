<?php
/**
 * Handle the recipe nutrition label shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe nutrition label shortcode.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Nutrition_Label extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-nutrition-label';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'style' => array(
			'default' => 'label',
			'type' => 'dropdown',
			'options' => array(
				'label' => 'Label',
				'simple' => 'Simple Text',
			),
		),
		'label_background_color' => array(
			'default' => '#ffffff',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'label',
			),
		),
		'label_text_color' => array(
			'default' => '#000000',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'label',
			),
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
			'dependency' => array(
				'id' => 'style',
				'value' => 'label',
				'type' => 'inverse',
			),
		),
		'header' => array(
			'default' => '',
			'type' => 'text',
		),
		'header_tag' => array(
			'default' => 'h3',
			'type' => 'dropdown',
			'options' => 'header_tags',
			'dependency' => array(
				'id' => 'header',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'header_style' => array(
			'default' => 'bold',
			'type' => 'dropdown',
			'options' => 'text_styles',
			'dependency' => array(
				'id' => 'header',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'label_color' => array(
			'default' => '#777777',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'simple',
			),
		),
		'value_color' => array(
			'default' => '#333333',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'simple',
			),
		),
		'label_separator' => array(
			'default' => ': ',
			'type' => 'text',
			'dependency' => array(
				'id' => 'style',
				'value' => 'simple',
			),
		),
		'label_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
			'dependency' => array(
				'id' => 'style',
				'value' => 'simple',
			),
		),
		'nutrition_separator' => array(
			'default' => ' | ',
			'type' => 'text',
			'dependency' => array(
				'id' => 'style',
				'value' => 'simple',
			),
		),
		'align' => array(
			'default' => 'left',
			'type' => 'dropdown',
			'options' => array(
				'left' => 'Aligned left',
				'center' => 'Aligned center',
				'right' => 'Aligned right',
			),
		),
	);

	/**
	 * Output for the shortcode.
	 *
	 * @since	4.0.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe || 'disabled' === $atts['align'] ) {
			return '';
		}

		// Show teaser for Premium only shortcode in Template editor.
		if ( ! WPRM_Addons::is_active( 'premium' ) ) {
			if ( ! $atts['is_template_editor_preview'] ) {
				return '';
			} else {
				return '<div class="wprm-template-editor-premium-only">The Nutrition Label is only available in <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/">WP Recipe Maker Premium</a>.</div>';
			}
		}

		$output = '';
		$align = in_array( $atts['align'], array( 'center', 'right' ) ) ? $atts['align'] : 'left';

		if ( $atts['header'] ) {
			$classes = array(
				'wprm-recipe-header',
				'wprm-recipe-nutrition-header',
				'wprm-block-text-' . $atts['header_style'],
			);

			$tag = trim( $atts['header_tag'] );
			$output .= '<' . $tag . ' class="' . implode( ' ', $classes ) . '">' . $atts['header'] . '</' . $tag . '>';
		}

		// Output.
		$classes = array(
			'wprm-nutrition-label-container',
			'wprm-nutrition-label-container-' . $atts['style'],
		);

		if ( 'label' !== $atts['style'] ) {
			$classes[] = 'wprm-block-text-' . $atts['text_style'];
		}

		$output .= '<div class="' . implode( ' ', $classes ) . '" style="text-align: ' . $align . ';">';

		switch ( $atts['style'] ) {
			case 'simple':
				$nutrition = $recipe->nutrition();
				$nutrition_output = array();

				$nutrition_fields = WPRMP_Nutrition_Label::$nutrition_fields;
				$nutrition_fields['serving_size']['unit'] = isset( $nutrition['serving_unit'] ) && $nutrition['serving_unit'] ? $nutrition['serving_unit'] : 'g';

				foreach ( $nutrition_fields as $field => $options ) {
					if ( isset( $nutrition[ $field ] ) && false !== $nutrition[ $field ] && ( WPRM_Settings::get( 'nutrition_label_zero_values' ) || $nutrition[ $field ] ) ) {
						$field_output = '<span class="wprm-nutrition-label-text-nutrition-container">';
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-label  wprm-block-text-' . $atts['label_style'] . '" style="color: ' . $atts['label_color'] . '">' . $options['label']. $atts['label_separator'] . '</span>';
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-value" style="color: ' . $atts['value_color'] . '">' . $nutrition[ $field ] . '</span>';
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-unit" style="color: ' . $atts['value_color'] . '">' . $options['unit'] . '</span>';
						$field_output .= '</span>';

						$nutrition_output[] = $field_output;
					}
				}

				if ( ! count( $nutrition_output ) ) {
					return '';
				}
				
				$output .= implode( '<span style="color: ' . $atts['label_color'] . '">' . $atts['nutrition_separator'] . '</span>', $nutrition_output );
				break;
			default:
				$label = WPRMP_Nutrition_Label::nutrition_label( $recipe );
				if ( ! $label ) {
					return '';
				}

				$style = 'style="';
				$style .= 'background-color: ' . $atts['label_background_color'] . ';';
				$style .= 'color: ' . $atts['label_text_color'] . ';';
				$style .= '"';

				$label = str_replace( 'class="wprm-nutrition-label"', 'class="wprm-nutrition-label" ' . $style, $label );
				
				$output .= $label;
			}

		$output .= '</div>';

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Nutrition_Label::init();