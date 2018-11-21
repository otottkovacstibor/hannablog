<?php
/**
 * Responsible for parsing recipe (parts) into our recipe format.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Responsible for parsing recipe (parts) into our recipe format.
 *
 * @since      1.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Recipe_Parser {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.7.0
	 */
	public static function init() {
		add_action( 'wp_ajax_wprm_parse_ingredients', array( __CLASS__, 'ajax_parse_ingredients' ) );
	}

	/**
	 * Parse ingredients submitted through AJAX.
	 *
	 * @since    1.7.0
	 */
	public static function ajax_parse_ingredients() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$ingredients = isset( $_POST['ingredients'] ) ? wp_unslash( $_POST['ingredients'] ) : array(); // Input var okay.
			$parsed_ingredients = array();
			$parsed_ingredient_group = array();

			foreach ( $ingredients as $ingredient_or_group ) {
				if ( isset( $ingredient_or_group['raw'] ) ) {
					$parsed_ingredients[] = self::parse_ingredient( $ingredient_or_group['raw'] );
				} elseif ( isset( $ingredient_or_group['name'] ) ) {
					$parsed_group_ingredients = array();

					if ( isset( $ingredient_or_group['ingredients'] ) ) {
						foreach ( $ingredient_or_group['ingredients'] as $ingredient ) {
							$parsed_group_ingredients[] = self::parse_ingredient( $ingredient['raw'] );
						}
					}

					$parsed_ingredients[] = array(
						'name' => $ingredient_or_group['name'],
						'ingredients' => $parsed_group_ingredients,
					);
				}
			}

			wp_send_json_success( array(
				'ingredients' => $parsed_ingredients,
			) );
		}

		wp_die();
	}

	/**
	 * Parse text to ingredient fields.
	 *
	 * @since    1.0.0
	 * @param	mixed $raw Text to parse into an ingredient.
	 */
	public static function parse_ingredient( $raw ) {
		// Backup raw.
		$raw_original = $raw;

		// Amount.
		$amount = '';

		$unicode_map = array(
			'00BC' => ' 1/4', '00BD' => ' 1/2', '00BE' => ' 3/4', '2150' => ' 1/7',
			'2151' => ' 1/9', '2152' => ' 1/10', '2153' => ' 1/3', '2154' => ' 2/3',
			'2155' => ' 1/5', '2156' => ' 2/5', '2157' => ' 3/5', '2158' => ' 4/5',
			'2159' => ' 1/6', '215A' => ' 5/6', '215B' => ' 1/8', '215C' => ' 3/8',
			'215D' => ' 5/8', '215E' => ' 7/8'
		);

		$unicode_regex = '';
		foreach ( $unicode_map as $unicode => $normal ) {
			$unicode_regex .= '\x{' . $unicode . '}';
		}

		$range_keyword = WPRM_Settings::get( 'import_range_keyword' );
		$amount_regex = '/^\s*([\d' . $unicode_regex . ']([\s\/\-\d.,' . $unicode_regex . ']|' . $range_keyword . ')*)(.*)/u';

		preg_match( $amount_regex, $raw, $match );
		if ( isset( $match[0] ) ) {
			$amount = trim( $match[1] );
			$raw = trim( $match[3] );
		}

		// Units.
		$unit = '';

		$possible_units = WPRM_Settings::get( 'import_units' );

		// Can't use array_map because of parameter.
		foreach ( $possible_units as $index => $value ) {
			$possible_units[ $index ] = preg_quote( $value, '/' );
			$possible_units[ $index ] .= '\.?'; // With or without dot to cover abbreviations.
		}

		$pattern = '/^(\b' . implode( '\s|\b', $possible_units ) . '\s)/ui';
		preg_match( $pattern, $raw, $match );
		if ( isset( $match[0] ) ) {
			$unit = trim( $match[0] );
			$raw = trim( preg_replace( $pattern, '', $raw, 1 ) );
		}

		// Notes 2.0.
		$notes = '';
		$notes_start = array();

		// Check for comma.
		if ( in_array( WPRM_Settings::get( 'import_notes_identifier' ), array( 'comma', 'both' ), true ) ) {
			if ( strpos( $raw, ',' ) ) {
				$notes_start[] = strpos( $raw, ',' );
			}
		}

		// Check for ().
		if ( in_array( WPRM_Settings::get( 'import_notes_identifier' ), array( 'parentheses', 'both' ), true ) ) {
			preg_match( '/\((.*?)\)/i', $raw, $match );
			if ( ! empty( $match ) ) {
				$notes_start[] = strpos( $raw, '(' );
			}
		}

		// Take out notes.
		if ( ! empty( $notes_start ) ) {
			$start = min( array_map( 'intval', $notes_start ) );

			$notes = trim( substr( $raw, $start ) );
			$raw = trim( substr( $raw, 0, $start ) );

			if ( WPRM_Settings::get( 'import_notes_remove_identifier' ) ) {
				$type = substr( $notes, 0, 1 );
				$notes = substr( $notes, 1 );

				if ( '(' === $type ) {
					$notes = preg_replace( '/\)/', '', $notes, 1);
				}
			}

			// Make sure the name is not empty.
			if ( ! $raw ) {
				$raw = $notes;
				$notes = '';
			}
		}

		// Name.
		$name = trim( $raw );

		// Make sure name is always filled in.
		if ( ! $name ) {
			$amount = '';
			$unit = '';
			$name = $raw_original;
			$notes = '';
		}

		return array(
			'amount' => trim( $amount ),
			'unit' => trim( $unit ),
			'name' => trim( $name ),
			'notes' => trim( $notes ),
		);
	}

	/**
	 * Get an array of possible ingredient units.
	 *
	 * @since    1.0.0
	 */
	public static function parse_ingredient_units() {
		$units = array(
			// Weight.
			__( 'kilograms', 'wp-recipe-maker' ),
			__( 'kilogram', 'wp-recipe-maker' ),
			__( 'kg', 'wp-recipe-maker' ),
			__( 'grams', 'wp-recipe-maker' ),
			__( 'gram', 'wp-recipe-maker' ),
			__( 'gr', 'wp-recipe-maker' ),
			__( 'g', 'wp-recipe-maker' ),
			__( 'milligrams', 'wp-recipe-maker' ),
			__( 'milligram', 'wp-recipe-maker' ),
			__( 'mg', 'wp-recipe-maker' ),
			__( 'pounds', 'wp-recipe-maker' ),
			__( 'pound', 'wp-recipe-maker' ),
			__( 'lbs', 'wp-recipe-maker' ),
			__( 'lb', 'wp-recipe-maker' ),
			__( 'ounces', 'wp-recipe-maker' ),
			__( 'ounce', 'wp-recipe-maker' ),
			__( 'oz', 'wp-recipe-maker' ),
			// Volume.
			__( 'liters', 'wp-recipe-maker' ),
			__( 'liter', 'wp-recipe-maker' ),
			__( 'l', 'wp-recipe-maker' ),
			__( 'deciliters', 'wp-recipe-maker' ),
			__( 'deciliter', 'wp-recipe-maker' ),
			__( 'dl', 'wp-recipe-maker' ),
			__( 'centiliters', 'wp-recipe-maker' ),
			__( 'centiliter', 'wp-recipe-maker' ),
			__( 'cl', 'wp-recipe-maker' ),
			__( 'milliliters', 'wp-recipe-maker' ),
			__( 'milliliter', 'wp-recipe-maker' ),
			__( 'ml', 'wp-recipe-maker' ),
			__( 'gallons', 'wp-recipe-maker' ),
			__( 'gallon', 'wp-recipe-maker' ),
			__( 'gal', 'wp-recipe-maker' ),
			__( 'quarts', 'wp-recipe-maker' ),
			__( 'quart', 'wp-recipe-maker' ),
			__( 'qt', 'wp-recipe-maker' ),
			__( 'pints', 'wp-recipe-maker' ),
			__( 'pint', 'wp-recipe-maker' ),
			__( 'pt', 'wp-recipe-maker' ),
			__( 'cups', 'wp-recipe-maker' ),
			__( 'cup', 'wp-recipe-maker' ),
			__( 'cu', 'wp-recipe-maker' ),
			__( 'c', 'wp-recipe-maker' ),
			__( 'fluid ounces', 'wp-recipe-maker' ),
			__( 'fluid ounce', 'wp-recipe-maker' ),
			__( 'fl ounces', 'wp-recipe-maker' ),
			__( 'fl ounce', 'wp-recipe-maker' ),
			__( 'floz', 'wp-recipe-maker' ),
			__( 'tablespoons', 'wp-recipe-maker' ),
			__( 'tablespoon', 'wp-recipe-maker' ),
			__( 'tbsps', 'wp-recipe-maker' ),
			__( 'tbsp', 'wp-recipe-maker' ),
			__( 'tbls', 'wp-recipe-maker' ),
			__( 'tbs', 'wp-recipe-maker' ),
			__( 'tb', 'wp-recipe-maker' ),
			__( 'T', 'wp-recipe-maker' ),
			__( 'teaspoons', 'wp-recipe-maker' ),
			__( 'teaspoon', 'wp-recipe-maker' ),
			__( 'tsps', 'wp-recipe-maker' ),
			__( 'tsp', 'wp-recipe-maker' ),
			__( 'ts', 'wp-recipe-maker' ),
			__( 't', 'wp-recipe-maker' ),
			// Length.
			__( 'meters', 'wp-recipe-maker' ),
			__( 'meter', 'wp-recipe-maker' ),
			__( 'm', 'wp-recipe-maker' ),
			__( 'centimeters', 'wp-recipe-maker' ),
			__( 'centimeter', 'wp-recipe-maker' ),
			__( 'cm', 'wp-recipe-maker' ),
			__( 'millimeters', 'wp-recipe-maker' ),
			__( 'millimeter', 'wp-recipe-maker' ),
			__( 'mm', 'wp-recipe-maker' ),
			__( 'yards', 'wp-recipe-maker' ),
			__( 'yard', 'wp-recipe-maker' ),
			__( 'yd', 'wp-recipe-maker' ),
			__( 'feet', 'wp-recipe-maker' ),
			__( 'foot', 'wp-recipe-maker' ),
			__( 'ft', 'wp-recipe-maker' ),
			__( 'inches', 'wp-recipe-maker' ),
			__( 'inch', 'wp-recipe-maker' ),
			__( 'in', 'wp-recipe-maker' ),
			// General.
			__( 'cloves', 'wp-recipe-maker' ),
			__( 'clove', 'wp-recipe-maker' ),
			__( 'leaves', 'wp-recipe-maker' ),
			__( 'leave', 'wp-recipe-maker' ),
			__( 'slices', 'wp-recipe-maker' ),
			__( 'slice', 'wp-recipe-maker' ),
			__( 'pieces', 'wp-recipe-maker' ),
			__( 'piece', 'wp-recipe-maker' ),
			__( 'pinches', 'wp-recipe-maker' ),
			__( 'pinch', 'wp-recipe-maker' ),
		);

		$units = apply_filters( 'wprm_parse_ingredient_units', $units );

		return array_map( 'sanitize_text_field', $units );
	}
}

WPRM_Recipe_Parser::init();
