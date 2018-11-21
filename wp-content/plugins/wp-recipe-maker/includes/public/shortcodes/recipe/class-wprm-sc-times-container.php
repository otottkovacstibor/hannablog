<?php
/**
 * Handle the recipe times container shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe times container shortcode.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Times_Container extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-times-container';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'style' => array(
			'default' => 'separate',
			'type' => 'dropdown',
			'options' => array(
				'inline' => 'Inline',
				'separate' => 'On its own line',
				'separated' => 'On separate lines',
				'columns' => 'Columns',
				'table' => 'Table',
			),
		),
		'table_border_width' => array(
			'default' => '1px',
			'type' => 'size',
			'dependency' => array(
				'id' => 'style',
				'value' => 'table',
			),
		),
		'table_border_style' => array(
			'default' => 'dotted',
			'type' => 'dropdown',
			'options' => 'border_styles',
			'dependency' => array(
				'id' => 'style',
				'value' => 'table',
			),
		),
		'table_border_color' => array(
			'default' => '#666666',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'table',
			),
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
			'dependency' => array(
				'id' => 'container',
				'value' => '1',
			),
		),
		'container' => array(
			'default' => '1',
		),
		'shorthand' => array(
			'default' => '0',
			'type' => 'toggle',
		),
		'icon' => array(
			'default' => '',
			'type' => 'icon',
			'dependency' => array(
				'id' => 'icon',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'icon_color' => array(
			'default' => '#333333',
			'type' => 'color',
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
	 * @since	4.0.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe ) {
			return '';
		}

		// Loop over all times.
		$times = array(
			'prep' => __( 'Prep Time', 'wp-recipe-maker' ),
			'cook' => __( 'Cook Time', 'wp-recipe-maker' ),
			'custom' => __( 'Custom Time', 'wp-recipe-maker' ),
			'total' => __( 'Total Time', 'wp-recipe-maker' ),
		);
		$time_shortcodes = array();

		foreach ( $times as $key => $label ) {
			$tag_atts = $atts;
			$tag_atts['type'] = $key;
			$tag_atts['label'] = isset( $atts['label_' . $key] ) ? $atts['label_' . $key] : '';
			$tag_atts['icon'] = $atts['icon_' . $key] ? $atts['icon_' . $key] : $atts['icon'];

			$time_shortcode = WPRM_SC_Time_Container::shortcode( $tag_atts );

			if ( $time_shortcode ) {
				$time_shortcodes[ $key ] = $time_shortcode;
			}
		}

		if ( ! $time_shortcodes ) {
			return '';
		}

		$show_container = (bool) $atts['container'];

		// Border style.
		$style = '';
		if ( 'table' === $atts['style'] ) {
			$style .= 'border-width: ' . $atts['table_border_width'] . ';';
			$style .= 'border-style: ' . $atts['table_border_style'] . ';';
			$style .= 'border-color: ' . $atts['table_border_color'] . ';';
		}

		// Output.
		$classes = array(
			'wprm-recipe-times-container',
			'wprm-recipe-details-container',
			'wprm-recipe-details-container-' . $atts['style'],
			'wprm-block-text-' . $atts['text_style'],
		);

		$output = $show_container ? '<div class="' . implode( ' ', $classes ) . '" style="' . $style . '">' : '';

		foreach ( $time_shortcodes as $key => $shortcode ) {
			$output .= $shortcode;
		}

		if ( $show_container ) {
			$output .= '</div>';
		}
		
		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Times_Container::init();