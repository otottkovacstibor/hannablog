<?php
/**
 * Handle the recipe nutrition shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe nutrition shortcode.
 *
 * @since      3.2.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Nutrition extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-nutrition';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
		),
		'field' => array(
			'default' => '',
			'type' => 'dropdown',
			'options' => 'nutrition_fields',
		),
		'unit' => array(
			'default' => '0',
			'type' => 'toggle',
		),
		'daily' => array(
			'default' => '0',
			'type' => 'toggle',
		),
	);

	/**
	 * Output for the shortcode.
	 *
	 * @since	3.2.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe ) {
			return '';
		}
		
		$show_unit = (bool) $atts['unit'];
		$show_daily = (bool) $atts['daily'];

		$field = '';
		$unit = '';

		if ( ! WPRM_Addons::is_active( 'premium' ) ) {
			// We can only output calories in free version.
			if ( 'calories' === $atts['field'] ) {
				if ( false !== $recipe->calories() ) {
					$field = $recipe->calories();
					$unit = __( 'kcal', 'wp-recipe-maker' );
				}
			}
		} else {
			$nutrition = $recipe->nutrition();
			$value = isset( $nutrition[ $atts['field'] ] ) ? $nutrition[ $atts['field'] ] : false;

			if ( $value ) {
				if ( $show_daily ) {
					$daily = isset( WPRMP_Nutrition_Label::$daily_values[ $atts['field'] ] ) ? WPRMP_Nutrition_Label::$daily_values[ $atts['field'] ] : false;

					if ( $daily ) {
						$field = round( floatval( $value ) / $daily * 100 );
						$unit = '%';
					}
				} else {
					$field = $value;
					$unit = WPRMP_Nutrition_Label::$nutrition_fields[ $atts['field'] ]['unit'];
				}
			}
		}

		if ( ! $field ) {
			return '';
		}

		// Output.
		$classes = array(
			'wprm-recipe-details',
			'wprm-recipe-nutrition',
			'wprm-recipe-' . $atts['field'],
			'wprm-block-text-' . $atts['text_style'],
		);

		$output = '<span class="' . implode( ' ', $classes ) . '">' . $field .  '</span>';

		if ( $show_unit && $unit ) {
			$classes = array(
				'wprm-recipe-details-unit',
				'wprm-recipe-nutrition-unit',
				'wprm-recipe-' . $atts['field'] . '-unit',
				'wprm-block-text-' . $atts['text_style'],
			);

			$output .= '<span class="' . implode( ' ', $classes ) . '">' . $unit . '</span>';
		}

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Nutrition::init();