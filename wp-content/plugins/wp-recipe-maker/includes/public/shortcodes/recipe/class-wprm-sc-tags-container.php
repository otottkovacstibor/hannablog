<?php
/**
 * Handle the recipe tags container shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.3.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe tags container shortcode.
 *
 * @since      3.3.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Tags_Container extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-tags-container';
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
		'separator' => array(
			'default' => ', ',
			'type' => 'text',
		),
		'icon' => array(
			'default' => '',
			'type' => 'icon',
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
	 * @since	3.3.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe ) {
			return '';
		}

		// Loop over all taxonomies.
		$taxonomies = WPRM_Taxonomies::get_taxonomies();
		$tag_shortcodes = array();

		foreach ( $taxonomies as $taxonomy => $options ) {
			$key = substr( $taxonomy, 5 );
			$terms = $recipe->tags( $key );

			// Hide keywords from template.
			if ( 'keyword' === $key && ! WPRM_Settings::get( 'metadata_keywords_in_template' ) ) {
				$terms = array();
			}

			if ( count( $terms ) > 0 ) {
				$tag_shortcodes[ $key ] = array(
					'label' => $atts['label_' . $key],
					'icon' => $atts['icon_' . $key] ? $atts['icon_' . $key] : $atts['icon'],
				);
			}
		}

		if ( ! $tag_shortcodes ) {
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
			'wprm-recipe-tags-container',
			'wprm-recipe-details-container',
			'wprm-recipe-details-container-' . $atts['style'],
			'wprm-block-text-' . $atts['text_style'],
		);

		$output = $show_container ? '<div class="' . implode( ' ', $classes ) . '" style="' . $style . '">' : '';

		foreach ( $tag_shortcodes as $key => $options ) {
			$tag_atts = $atts;
			$tag_atts['key'] = $key;
			$tag_atts['label'] = $options['label'];
			$tag_atts['icon'] = $options['icon'];
			$output .= WPRM_SC_Tag_Container::shortcode( $tag_atts );
		}

		if ( $show_container ) {
			$output .= '</div>';
		}
		
		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Tags_Container::init();