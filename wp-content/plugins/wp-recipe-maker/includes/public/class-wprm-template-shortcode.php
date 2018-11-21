<?php
/**
 * Parent class for the template shortcodes.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Parent class for the template shortcodes.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Template_Shortcode {
	public static $attributes = array();
	public static $shortcode = '';

	public static function init() {
		$shortcode = static::$shortcode;

		if ( $shortcode ) {
			// Register shortcode in WordPress.
			add_shortcode( $shortcode, array( get_called_class(), 'shortcode' ) );

			// Add to list of all shortcodes.
			WPRM_Template_Shortcodes::$shortcodes[ $shortcode ] = static::$attributes;
		}
	}

	public static function clean_paragraphs( $text ) {

		// Remove blank lines.
		$text = str_ireplace( '<p></p>', '', $text );
		$text = str_ireplace( '<p><br></p>', '', $text );
		$text = str_ireplace( '<p><br/></p>', '', $text );

		// Remove last occurence of </p>.
		$pos = strripos( $text, '</p>' );
		if( false !== $pos ) {
			$text = substr_replace( $text, '', $pos, 4 );
		}

		// Remove <p>.
		$text = str_ireplace( '<p>', '', $text );

		// Replace remaining </p> with spacer.
		$text = str_ireplace( '</p>', '[wprm-spacer]', $text );

		return trim( do_shortcode( $text ) );
	}

	public static function get_hook() {
		return str_replace( '-', '_', static::$shortcode ) . '_shortcode';
	}

	protected static function get_attributes( $atts ) {
		$atts = shortcode_atts( WPRM_Template_Shortcodes::get_defaults( static::$shortcode ), $atts, str_replace( '-', '_', static::$shortcode ) );

		$atts['is_template_editor_preview'] = isset( $GLOBALS['wp']->query_vars['rest_route'] ) && '/wp-recipe-maker/v1/template/preview' === $GLOBALS['wp']->query_vars['rest_route'];

		return $atts;
	}
}
