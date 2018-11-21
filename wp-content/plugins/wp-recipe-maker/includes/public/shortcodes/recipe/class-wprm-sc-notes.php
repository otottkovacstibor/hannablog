<?php
/**
 * Handle the recipe notes shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe notes shortcode.
 *
 * @since      3.2.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Notes extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-notes';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
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
		if ( ! $recipe || ! $recipe->notes() ) {
			return '';
		}

		// Output.
		$classes = array(
			'wprm-recipe-notes-container',
			'wprm-block-text-' . $atts['text_style'],
		);

		$output = '<div class="' . implode( ' ', $classes ) . '">';

		if ( $atts['header'] ) {
			$classes = array(
				'wprm-recipe-header',
				'wprm-recipe-notes-header',
				'wprm-block-text-' . $atts['header_style'],
			);

			$tag = trim( $atts['header_tag'] );
			$output .= '<' . $tag . ' class="' . implode( ' ', $classes ) . '">' . $atts['header'] . '</' . $tag . '>';
		}

		$notes = parent::clean_paragraphs( $recipe->notes() );

		$output .= '<div class="wprm-recipe-notes">';
		$output .= do_shortcode( $notes );
		$output .= '</div>';
		$output .= '</div>';

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Notes::init();