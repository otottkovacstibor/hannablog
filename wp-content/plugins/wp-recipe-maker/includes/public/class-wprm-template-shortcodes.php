<?php
/**
 * Handle the recipe shortcodes.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Handle the recipe shortcodes.
 *
 * @since      3.2.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Template_Shortcodes {

	/**
	 * Current recipe ID.
	 *
	 * @since	3.2.0
	 * @access	private
	 * @var		array $current_recipe_id ID of the recipe we're currently outputting.
	 */
	private static $current_recipe_id = false;

	/**
	 * Array of shortcodes with their attributes.
	 *
	 * @since	4.0.0
	 * @access	private
	 * @var		array $shortcodes Array of shortcodes with their attributes.
	 */
	public static $shortcodes = array();

	/**
	 * Array of defaults for the shortcodes.
	 *
	 * @since	4.0.0
	 * @access	private
	 * @var		array $defaults Array of defaults for the shortcodes.
	 */
	public static $defaults = array();

	/**
	 * Wether or not the shortcodes have been parsed.
	 *
	 * @since	4.0.0
	 * @access	private
	 * @var		array $parsed Wether or not the shortcodes have been parsed.
	 */
	private static $parsed = false;

	/**
	 * Register actions and filters.
	 *
	 * @since    3.2.0
	 */
	public static function init() {
		self::load_shortcodes();
	}

	/**
	 * Get recipe for a specific ID.
	 *
	 * @since	3.2.0
	 * @param	int $id ID to get the recipe for.
	 */
	public static function get_recipe( $id ) {
		$recipe_id = intval( $id );

		// Get first recipe in post content if no ID is set.
		if ( ! $recipe_id ) {
			$recipe_id = self::get_current_recipe_id();
		}

		if ( $recipe_id ) {
			return WPRM_Recipe_Manager::get_recipe( $recipe_id );
		} else {
			return false;
		}
	}

	/**
	 * Get the current recipe ID.
	 *
	 * @since	3.2.0
	 */
	public static function get_current_recipe_id() {
		if ( ! self::$current_recipe_id ) {
			$parent_post = get_post();

			if ( $parent_post ) {
				$recipes = WPRM_Recipe_Manager::get_recipe_ids_from_content( $parent_post->post_content );

				if ( isset( $recipes[0] ) ) {
					self::set_current_recipe_id( $recipes[0] );
				} else {
					self::set_current_recipe_id( false );
				}
			}
		}

		return self::$current_recipe_id;
	}

	/**
	 * Set the current recipe ID.
	 *
	 * @since	3.2.0
	 * @param	int $id ID to set as the current recipe ID.
	 */
	public static function set_current_recipe_id( $id ) {
		self::$current_recipe_id = $id;
	}

	/**
	 * Load all available shortcodes from the /includes/public/recipe-shortcodes directory.
	 *
	 * @since    3.2.0
	 */
	private static function load_shortcodes() {
		$dirs = array(
			WPRM_DIR . 'includes/public/shortcodes/general',
			WPRM_DIR . 'includes/public/shortcodes/recipe',
		);

		foreach ( $dirs as $dir ) {
			if ( $handle = opendir( $dir ) ) {
				while ( false !== ( $file = readdir( $handle ) ) ) {
					preg_match( '/^class-wprm-sc-(.*?).php/', $file, $match );
					if ( isset( $match[1] ) ) {
						require_once( $dir . '/' . $match[0] );
					}
				}
			}
		}
	}

	/**
	 * Get all available shortcodes.
	 *
	 * @since	4.0.0
	 */
	public static function get_shortcodes() {
		if ( ! self::$parsed ) {
			self::parse_shortcodes();
		}

		return self::$shortcodes;
	}

	/**
	 * Get the defaults for a specific shortcode.
	 *
	 * @since	4.0.0
	 * @param	mixed $shortcode Shortcode to get the defaults for.
	 */
	public static function get_defaults( $shortcode ) {
		if ( ! self::$parsed ) {
			self::parse_shortcodes();
		}

		return isset( self::$defaults[ $shortcode ] ) ? self::$defaults[ $shortcode ] : array();
	}

	/**
	 * Parse all shortcodes.
	 *
	 * @since	4.0.0
	 */
	public static function parse_shortcodes() {
		$premium_only = class_exists( 'WPRM_Addons' ) && WPRM_Addons::is_active( 'premium' ) ? '' : ' (' . __( 'WP Recipe Maker Premium only', 'wp-recipe-maker' ) . ')';
		
		$shortcodes = self::$shortcodes;
		$defaults = array();

		foreach ( $shortcodes as $shortcode => $attributes ) {
			// Tags container.
			if ( 'wprm-recipe-tags-container' === $shortcode ) {
				$taxonomies = WPRM_Taxonomies::get_taxonomies();
	
				foreach ( $taxonomies as $taxonomy => $options ) {
					$key = substr( $taxonomy, 5 );
					$shortcodes[ $shortcode ]['label_' . $key] = array(
						'default' => $options['singular_name'],
						'type' => 'text',
					);
					$shortcodes[ $shortcode ]['icon_' . $key] = array(
						'default' => '',
						'type' => 'icon',
					);
				}
			}

			// Times container.
			if ( 'wprm-recipe-times-container' === $shortcode ) {
				$times = array(
					'prep' => __( 'Prep Time', 'wp-recipe-maker' ),
					'cook' => __( 'Cook Time', 'wp-recipe-maker' ),
					'custom' => __( 'Custom Time', 'wp-recipe-maker' ),
					'total' => __( 'Total Time', 'wp-recipe-maker' ),
				);
	
				foreach ( $times as $key => $label ) {
					if ( 'custom' !== $key ) {
						$shortcodes[ $shortcode ]['label_' . $key] = array(
							'default' => $label,
							'type' => 'text',
						);
					}
					$shortcodes[ $shortcode ]['icon_' . $key] = array(
						'default' => '',
						'type' => 'icon',
					);
				}
			}
			
			$defaults[ $shortcode ] = array();
			foreach ( $shortcodes[ $shortcode ] as $attribute => $options ) {
				// Save defaults separately for easy access.
				$defaults[ $shortcode ][ $attribute ] = isset( $options['default'] ) ? $options['default'] : '';

				// Resueable option arrays.
				if ( isset( $options['type'] ) && 'dropdown' === $options['type'] && ! is_array( $options['options'] ) ) {
					switch ( $options['options'] ) {
						case 'header_tags':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'span' => 'span',
								'span' => 'div',
								'h1' => 'h1',
								'h2' => 'h2',
								'h3' => 'h3',
								'h4' => 'h4',
								'h5' => 'h5',
								'h6' => 'h6',
							);
							break;
						case 'text_styles':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'normal' => 'Normal',
								'light' => 'Light',
								'bold' => 'Bold',
								'italic' => 'Italic',
								'uppercase' => 'Uppercase',
								'faded' => 'Faded',
								'uppercase-faded' => 'Uppercase & Faded',
							);
							break;
						case 'border_styles':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'solid' => 'Solid',
								'dashed' => 'Dashed',
								'dotted' => 'Dotted',
								'double' => 'Double',
								'groove' => 'Groove',
								'ridge' => 'Ridge',
								'inset' => 'Inset',
								'outset' => 'Outset'
							);
							break;
						case 'nutrition_fields':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'serving_size' => 'Serving Size',
								'calories' => 'Calories',
								'carbohydrates' => 'Carbohydrates',
								'protein' => 'Protein',
								'fat' => 'Fat',
								'saturated_fat' => 'Saturated Fat',
								'polyunsaturated_fat' => 'Polyunsaturated Fat',
								'monounsaturated_fat' => 'Monounsaturated Fat',
								'trans_fat' => 'Trans Fat',
								'cholesterol' => 'Cholesterol',
								'sodium' => 'Sodium',
								'potassium' => 'Potassium',
								'fiber' => 'Fiber',
								'sugar' => 'Sugar',
								'vitamin_a' => 'Vitamin A',
								'vitamin_c' => 'Vitamin C',
								'calcium' => 'Calcium',
								'iron' => 'Iron',
							);
							break;
						case 'recipe_tags':
							$keys = array();
							$taxonomies = WPRM_Taxonomies::get_taxonomies();
	
							foreach ( $taxonomies as $taxonomy => $options ) {
								$key = substr( $taxonomy, 5 );
								$keys[ $key ] = $options['singular_name'];
							}
	
							$shortcodes[ $shortcode ][ $attribute ]['options'] = $keys;
							break;
						case 'recipe_times':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'prep' => 'Prep Time',
								'cook' => 'Cook Time',
								'custom' => 'Custom Time',
								'total' => 'Total Time',
							);
							break;
						case 'list_style_types':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'none' => 'None',
								'checkbox' => 'Checkbox' . $premium_only,
								'circle' => 'Circle',
								'disc' => 'Disc',
								'square' => 'Square',
								'decimal' => 'Decimal',
								'decimal-leading-zero' => 'Decimal with leading zero',
								'lower-roman' => 'Lower Roman',
								'upper-roman' => 'Upper Roman',
								'lower-latin' => 'Lower Latin',
								'upper-latin' => 'Upper Latin',
								'lower-greek' => 'Lower Greek',
								'armenian' => 'Armenian',
								'georgian' => 'Georgian',
							);
							break;
						case 'adjustable_servings':
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array(
								'disabled' => 'Disabled',
								'tooltip' => 'Tooltip Slider' . $premium_only,
								'text' => 'Text Field' . $premium_only,
							);
							break;
						default:
							$shortcodes[ $shortcode ][ $attribute ]['options'] = array();
					}
				}
			}
		}

		self::$parsed = true;
		self::$defaults = $defaults;
		self::$shortcodes = $shortcodes;
	}
}

WPRM_Template_Shortcodes::init();
