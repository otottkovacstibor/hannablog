<?php
/**
 * Handle the recipe time container shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.3.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe time container shortcode.
 *
 * @since      3.3.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Time_Container extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-time-container';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'type' => array(
			'default' => '',
			'type' => 'dropdown',
			'options' => 'recipe_times',
		),
		'style' => array(
			'default' => 'separate',
			'type' => 'dropdown',
			'options' => array(
				'inline' => 'Inline',
				'separate' => 'On its own line',
				'separated' => 'On separate lines',
				'columns' => 'Columns',
			),
		),
		'table_border_width' => array(
			'default' => '',
		),
		'table_border_style' => array(
			'default' => '',
		),
		'table_border_color' => array(
			'default' => '',
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
		),
		'shorthand' => array(
			'default' => '0',
			'type' => 'toggle',
		),
		'icon' => array(
			'default' => '',
			'type' => 'icon',
		),
		'icon_color' => array(
			'default' => '#333333',
			'type' => 'color',
		),
		'label' => array(
			'default' => '',
			'type' => 'text',
		),
		'label_separator' => array(
			'default' => ' ',
			'type' => 'text',
		),
		'label_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
		),
	);

	/**
	 * Output for the shortcode.
	 *
	 * @since	3.3.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$time = WPRM_SC_Time::shortcode( $atts );
		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe || ! $time ) {
			return '';
		}

		// Get optional icon.
		$icon = '';
		if ( $atts['icon'] ) {
			$icon = WPRM_Icon::get( $atts['icon'], $atts['icon_color'] );

			if ( $icon ) {
				$icon = '<span class="wprm-recipe-icon wprm-recipe-time-icon wprm-recipe-' . $atts['type']. '-time-icon">' . $icon . '</span> ';
			}
		}

		// Get optional label.
		$label = '';
		if ( 'custom' === $atts['type'] && $recipe->custom_time_label() ) {
			$atts['label'] = $recipe->custom_time_label();
		}

		if ( $atts['label'] ) {
			$label = '<span class="wprm-recipe-details-label wprm-block-text-' . $atts['label_style'] . ' wprm-recipe-time-label wprm-recipe-' . $atts['type']. '-time-label">' . $atts['label'] . $atts['label_separator'] . '</span>';
		}

		// Border style.
		$style = '';
		if ( 'table' === $atts['style'] ) {
			$style .= 'border-width: ' . $atts['table_border_width'] . ';';
			$style .= 'border-style: ' . $atts['table_border_style'] . ';';
			$style .= 'border-color: ' . $atts['table_border_color'] . ';';
		}

		// Output.
		$classes = array(
			'wprm-recipe-time-container',
			'wprm-recipe-' . $atts['type']. '-time-container',
			'wprm-recipe-block-container',
			'wprm-recipe-block-container-' . $atts['style'],
			'wprm-block-text-' . $atts['text_style'],
		);

		$output = '<div class="' . implode( ' ', $classes ) . '" style="' . $style . '">';
		$output .= $icon;
		$output .= $label;
		$output .= $time;
		$output .= '</div>';

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Time_Container::init();