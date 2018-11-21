<?php
/**
 * Handle the recipe metadata.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Handle the recipe metadata.
 *
 * @since      1.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Metadata {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'metadata_in_head' ), 1 );
		add_action( 'after_setup_theme', array( __CLASS__, 'metadata_image_sizes' ) );
	}

	/**
	 * Output metadata in the HTML head.
	 *
	 * @since    1.25.0
	 */
	public static function metadata_in_head() {
		if ( WPRM_Settings::get( 'metadata_pinterest_optout' ) ) {
			echo '<meta name="pinterest-rich-pin" content="false" />';
		}
	}

	/**
	 * Register image sizes for the recipe metadata.
	 *
	 * @since    1.25.0
	 */
	public static function metadata_image_sizes() {
		if ( function_exists( 'fly_add_image_size' ) ) {
			fly_add_image_size( 'wprm-metadata-1_1', 500, 500, true );
			fly_add_image_size( 'wprm-metadata-4_3', 500, 375, true );
			fly_add_image_size( 'wprm-metadata-16_9', 480, 270, true );
		} else {
			add_image_size( 'wprm-metadata-1_1', 500, 500, true );
			add_image_size( 'wprm-metadata-4_3', 500, 375, true );
			add_image_size( 'wprm-metadata-16_9', 480, 270, true );
		}
	}

	/**
	 * Get the metadata to output for a recipe.
	 *
	 * @since    1.0.0
	 * @param		 object $recipe Recipe to get the metadata for.
	 */
	public static function get_metadata_output( $recipe ) {
		$output = '';

		$metadata = self::sanitize_metadata( self::get_metadata( $recipe ) );
		if ( $metadata ) {
			$output = '<script type="application/ld+json">' . wp_json_encode( $metadata ) . '</script>';
		}

		return $output;
	}

	/**
	 * Santize metadata before outputting.
	 *
	 * @since    1.5.0
	 * @param		 mixed $metadata Metadata to sanitize.
	 */
	public static function sanitize_metadata( $metadata ) {
		$sanitized = array();
		if ( is_array( $metadata ) ) {
			foreach ( $metadata as $key => $value ) {
				$sanitized[ $key ] = self::sanitize_metadata( $value );
			}
		} else {
			$sanitized = strip_shortcodes( wp_strip_all_tags( do_shortcode( $metadata ) ) );
		}
		return $sanitized;
	}

	/**
	 * Get the metadata for a recipe.
	 *
	 * @since    1.0.0
	 * @param		 object $recipe Recipe to get the metadata for.
	 */
	public static function get_metadata( $recipe ) {
		// Check if non-food recipe.
		if ( 'other' === $recipe->type() ) {
			return apply_filters( 'wprm_recipe_metadata', array(), $recipe );
		}

		// Essentials.
		$metadata = array(
			'@context' => 'http://schema.org/',
			'@type' => 'Recipe',
			'name' => $recipe->name(),
			'author' => array(
				'@type' => 'Person',
				'name' => $recipe->author_meta(),
			),
			'description' => wp_strip_all_tags( $recipe->summary() ),
		);

		// Dates.
		$date_published = date( 'c', strtotime( $recipe->date() ) );
		$metadata['datePublished'] = $date_published;

		$date_modified = date( 'c', strtotime( $recipe->date_modified() ) );
		if ( $date_modified !== $date_published ) {
			// Removed again on 2018-11-16 to see if this was causing rich snippet problems.
			// $metadata['dateModified'] = $date_modified;
		}

		// Recipe image.
		if ( $recipe->image_id() ) {
			$image_sizes = array(
				$recipe->image_url( 'full' ),
				$recipe->image_url( 'wprm-metadata-1_1' ),
				$recipe->image_url( 'wprm-metadata-4_3' ),
				$recipe->image_url( 'wprm-metadata-16_9' ),
			);

			$metadata['image'] = array_values( array_unique( $image_sizes ) );
		}

		// Recipe video.
		if ( $recipe->video_metadata() ) {
			$metadata['video'] = $recipe->video_metadata();
		}

		// Yield.
		if ( $recipe->servings() ) {
			$metadata['recipeYield'] = $recipe->servings() . ' ' . $recipe->servings_unit();
		}

		// Times.
		if ( $recipe->prep_time() ) {
			$metadata['prepTime'] = 'PT' . $recipe->prep_time() . 'M';
		}
		if ( $recipe->cook_time() ) {
			$metadata['cookTime'] = 'PT' . $recipe->cook_time() . 'M';
		}
		if ( $recipe->total_time() ) {
			$metadata['totalTime'] = 'PT' . $recipe->total_time() . 'M';
		}

		// Ingredients.
		$ingredients = $recipe->ingredients_without_groups();
		if ( count( $ingredients ) > 0 ) {
			$metadata_ingredients = array();

			foreach ( $ingredients as $ingredient ) {
				$metadata_ingredient = $ingredient['amount'] . ' ' . $ingredient['unit'] . ' ' . $ingredient['name'];
				if ( trim( $ingredient['notes'] ) !== '' ) {
					$metadata_ingredient .= ' (' . $ingredient['notes'] . ')';
				}

				$metadata_ingredients[] = $metadata_ingredient;
			}

			$metadata['recipeIngredient'] = $metadata_ingredients;
		}

		// Instructions.
		$instruction_groups = $recipe->instructions();
		if ( count( $instruction_groups ) > 0 ) {
			$metadata_instruction_groups = array();

			foreach ( $instruction_groups as $instruction_group ) {
				$metadata_instructions = array();

				foreach ( $instruction_group['instructions'] as $instruction ) {
					$metadata_instructions[] = array(
						'@type' => 'HowToStep',
						'text' => wp_strip_all_tags( $instruction['text'] ),
					);
				}

				if ( count( $metadata_instructions ) > 0 ) {
					if ( $instruction_group['name'] ) {
						$metadata_instruction_groups[] = array(
							'@type' => 'HowToSection',
							'name' => wp_strip_all_tags( $instruction_group['name'] ),
							'itemListElement' => $metadata_instructions,
						);
					} else {
						$metadata_instruction_groups = array_merge( $metadata_instruction_groups, $metadata_instructions );
					}
				}
			}

			if ( count( $metadata_instruction_groups ) > 0 ) {
				$metadata['recipeInstructions'] = $metadata_instruction_groups;
			}
		}

		// Category & Cuisine.
		$courses = $recipe->tags( 'course' );
		if ( count( $courses ) > 0 ) {
			$metadata['recipeCategory'] = wp_list_pluck( $courses, 'name' );
		}
		$cuisines = $recipe->tags( 'cuisine' );
		if ( count( $cuisines ) > 0 ) {
			$metadata['recipeCuisine'] = wp_list_pluck( $cuisines, 'name' );
		}

		// Keywords.
		$keywords = $recipe->tags( 'keyword' );
		if ( count( $keywords ) > 0 ) {
			$keyword_names = wp_list_pluck( $keywords, 'name' );
			$metadata['keywords'] = implode( ', ', $keyword_names );
		}

		// Nutrition.
		$nutrition_mapping = array(
			'serving_size' => 'servingSize',
			'calories' => 'calories',
			'fat' => 'fatContent',
			'saturated_fat' => 'saturatedFatContent',
			'unsaturated_fat' => 'unsaturatedFatContent',
			'trans_fat' => 'transFatContent',
			'carbohydrates' => 'carbohydrateContent',
			'sugar' => 'sugarContent',
			'fiber' => 'fiberContent',
			'protein' => 'proteinContent',
			'cholesterol' => 'cholesterolContent',
			'sodium' => 'sodiumContent',
		);
		$nutrition_metadata = array();
		$nutrition = $recipe->nutrition();

		// Calculate unsaturated fat.
		if ( isset( $nutrition['polyunsaturated_fat'] ) && isset( $nutrition['monounsaturated_fat'] ) ) {
			$nutrition['unsaturated_fat'] = $nutrition['polyunsaturated_fat'] + $nutrition['monounsaturated_fat'];
		} elseif ( isset( $nutrition['polyunsaturated_fat'] ) ) {
			$nutrition['unsaturated_fat'] = $nutrition['polyunsaturated_fat'];
		} elseif ( isset( $nutrition['monounsaturated_fat'] ) ) {
			$nutrition['unsaturated_fat'] = $nutrition['monounsaturated_fat'];
		}

		foreach ( $nutrition as $field => $value ) {
			if ( $value && array_key_exists( $field, $nutrition_mapping ) ) {
				$unit = esc_html__( 'g', 'wp-recipe-maker' );

				if ( 'serving_size' === $field && isset( $nutrition['serving_unit'] ) && $nutrition['serving_unit'] ) {
					$unit = $nutrition['serving_unit'];
				} elseif ( 'calories' === $field ) {
					$unit = esc_html__( 'kcal', 'wp-recipe-maker' );
				} elseif ( 'cholesterol' === $field || 'sodium' === $field ) {
					$unit = esc_html__( 'mg', 'wp-recipe-maker' );
				}

				$nutrition_metadata[ $nutrition_mapping[ $field ] ] = $value . ' ' . $unit;
			}
		}

		if ( count( $nutrition_metadata ) > 0 ) {
			if ( ! isset( $nutrition_metadata['servingSize'] ) ) {
				$nutrition_metadata['servingSize'] = esc_html__( '1 serving', 'wp-recipe-maker' );
			}

			$metadata['nutrition'] = array_merge( array(
				'@type' => 'NutritionInformation',
			), $nutrition_metadata );
		}

		// Rating.
		$rating = $recipe->rating();
		if ( $rating['count'] > 0 ) {
			$metadata['aggregateRating'] = array(
				'@type' => 'AggregateRating',
				'ratingValue' => $rating['average'],
				'ratingCount' => $rating['count'],
			);
		}

		// Allow external filtering of metadata.
		return apply_filters( 'wprm_recipe_metadata', $metadata, $recipe );
	}
}

WPRM_Metadata::init();
