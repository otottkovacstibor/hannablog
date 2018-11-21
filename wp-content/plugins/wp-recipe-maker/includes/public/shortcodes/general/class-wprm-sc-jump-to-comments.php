<?php
/**
 * Handle the jump to comments shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the jump to comments shortcode.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Jump_To_Comments extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-jump-to-comments';
	public static $attributes = array(
		'style' => array(
			'default' => 'text',
			'type' => 'dropdown',
			'options' => array(
				'text' => 'Text',
				'button' => 'Button',
				'inline-button' => 'Inline Button',
				'wide-button' => 'Full Width Button',
			),
		),
		'icon' => array(
			'default' => '',
			'type' => 'icon',
		),
		'text' => array(
			'default' => 'Rate this Recipe',
			'type' => 'text',
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
		),
		'icon_color' => array(
			'default' => '#333333',
			'type' => 'color',
			'dependency' => array(
				'id' => 'icon',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'text_color' => array(
			'default' => '#333333',
			'type' => 'color',
			'dependency' => array(
				'id' => 'text',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'horizontal_padding' => array(
			'default' => '5px',
			'type' => 'size',
			'dependency' => array(
				'id' => 'style',
				'value' => 'text',
				'type' => 'inverse',
			),
		),
		'vertical_padding' => array(
			'default' => '5px',
			'type' => 'size',
			'dependency' => array(
				'id' => 'style',
				'value' => 'text',
				'type' => 'inverse',
			),
		),
		'button_color' => array(
			'default' => '#ffffff',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'text',
				'type' => 'inverse',
			),
		),
		'border_color' => array(
			'default' => '#333333',
			'type' => 'color',
			'dependency' => array(
				'id' => 'style',
				'value' => 'text',
				'type' => 'inverse',
			),
		),
		'border_radius' => array(
			'default' => '0px',
			'type' => 'size',
			'dependency' => array(
				'id' => 'style',
				'value' => 'text',
				'type' => 'inverse',
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

		if ( ! comments_open() && ! $atts['is_template_editor_preview'] ) {
			return '';
		}

		// Get optional icon.
		$icon = '';
		if ( $atts['icon'] ) {
			$icon = WPRM_Icon::get( $atts['icon'], $atts['icon_color'] );

			if ( $icon ) {
				$icon = '<span class="wprm-recipe-icon wprm-recipe-jump-to-comments-icon">' . $icon . '</span> ';
			}
		}

		// Output.
		$classes = array(
			'wprm-recipe-jump-to-comments',
			'wprm-recipe-link',
			'wprm-block-text-' . $atts['text_style'],
		);

		$style = 'color: ' . $atts['text_color'] . ';';
		if ( 'text' !== $atts['style'] ) {
			$classes[] = 'wprm-recipe-jump-to-comments-' . $atts['style'];
			$classes[] = 'wprm-recipe-link-' . $atts['style'];
			$classes[] = 'wprm-color-accent';

			$style .= 'background-color: ' . $atts['button_color'] . ';';
			$style .= 'border-color: ' . $atts['border_color'] . ';';
			$style .= 'border-radius: ' . $atts['border_radius'] . ';';
			$style .= 'padding: ' . $atts['vertical_padding'] . ' ' . $atts['horizontal_padding'] . ';';
		}

		$output = '<a href="#commentform" style="' . $style . '" class="' . implode( ' ', $classes ) . '">' . $icon . $atts['text'] . '</a>';
		return apply_filters( parent::get_hook(), $output, $atts );
	}
}

WPRM_SC_Jump_To_Comments::init();