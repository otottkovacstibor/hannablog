<?php
/**
 * Responsible for importing Tasty recipes.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.23.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/import
 */

/**
 * Responsible for importing Tasty recipes.
 *
 * @since      1.23.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/import
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Import_Tasty extends WPRM_Import {
	/**
	 * Get the UID of this import source.
	 *
	 * @since    1.23.0
	 */
	public function get_uid() {
		return 'tasty';
	}

	/**
	 * Wether or not this importer requires a manual search for recipes.
	 *
	 * @since    1.23.0
	 */
	public function requires_search() {
		return false;
	}

	/**
	 * Get the name of this import source.
	 *
	 * @since    1.23.0
	 */
	public function get_name() {
		return 'Tasty Recipes';
	}

	/**
	 * Get HTML for the import settings.
	 *
	 * @since    1.23.0
	 */
	public function get_settings_html() {
		return '';
	}

	/**
	 * Get the total number of recipes to import.
	 *
	 * @since    1.23.0
	 */
	public function get_recipe_count() {
		$args = array(
			'post_type' => 'tasty_recipe',
			'post_status' => 'any',
			'posts_per_page' => 1,
		);

		$query = new WP_Query( $args );
		return $query->found_posts;
	}

	/**
	 * Get a list of recipes that are available to import.
	 *
	 * @since    1.23.0
	 * @param	 int $page Page of recipes to get.
	 */
	public function get_recipes( $page = 0 ) {
		$recipes = array();

		$limit = 100;
		$offset = $limit * $page;

		$args = array(
			'post_type' => 'tasty_recipe',
			'post_status' => 'any',
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => $limit,
			'offset' => $offset,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			$posts = $query->posts;

			foreach ( $posts as $post ) {
				$recipes[ $post->ID ] = array(
					'name' => $post->post_title,
					'url' => get_edit_post_link( $post->ID ),
				);
			}
		}

		return $recipes;
	}

	/**
	 * Get recipe with the specified ID in the import format.
	 *
	 * @since    1.23.0
	 * @param	 mixed $id ID of the recipe we want to import.
	 * @param	 array $post_data POST data passed along when submitting the form.
	 */
	public function get_recipe( $id, $post_data ) {
		$recipe = array(
			'import_id' => $id,
			'import_backup' => array(),
		);

		$post = get_post( $id );

		// Featured Image.
		$recipe['image_id'] = get_post_thumbnail_id( $id );

		// Simple Matching.
		$recipe['name'] = $post->post_title;
		$recipe['summary'] = get_post_meta( $id, 'description', true );
		$recipe['notes'] = get_post_meta( $id, 'notes', true );
		$recipe['author_name'] = get_post_meta( $id, 'author_name', true );

		if ( $recipe['author_name'] ) {
			$recipe['author_display'] = 'custom';
		}

		// Servings.
		$tasty_yield = get_post_meta( $id, 'yield', true );
		$match = preg_match( '/^\s*\d+/', $tasty_yield, $servings_array );
		if ( 1 === $match ) {
				$servings = str_replace( ' ','', $servings_array[0] );
		} else {
				$servings = '';
		}

		$servings_unit = preg_replace( '/^\s*\d+\s*/', '', $tasty_yield );

		$recipe['servings'] = $servings;
		$recipe['servings_unit'] = $servings_unit;

		// Recipe times.
		$recipe['prep_time'] = $this->get_minutes_for_time( get_post_meta( $id, 'prep_time', true ) );
		$recipe['cook_time'] = $this->get_minutes_for_time( get_post_meta( $id, 'cook_time', true ) );

		// Recipe tags.
		$recipe['tags'] = array();
		$recipe['tags']['course'] = array_map( 'trim', explode( ',', get_post_meta( $id, 'category', true ) ) );
		$recipe['tags']['cuisine'] = array_map( 'trim', explode( ',', get_post_meta( $id, 'cuisine', true ) ) );

		// Ingredients.
		$tasty_ingredients = $this->parse_recipe_component_list( get_post_meta( $id, 'ingredients', true ) );

		$ingredients = array();

		foreach ( $tasty_ingredients as $tasty_group ) {
			$group = array(
				'name' => $tasty_group['name'],
				'ingredients' => array(),
			);

			foreach ( $tasty_group['items'] as $tasty_item ) {
				$text = trim( strip_tags( $tasty_item, '<a>' ) );

				if ( ! empty( $text ) ) {
					$group['ingredients'][] = array(
						'raw' => $text,
					);
				}
			}

			$ingredients[] = $group;
		}
		$recipe['ingredients'] = $ingredients;

		// Instructions.
		$tasty_instructions = $this->parse_recipe_component_list( get_post_meta( $id, 'instructions', true ) );

		$instructions = array();

		foreach ( $tasty_instructions as $tasty_group ) {
			$group = array(
				'name' => $tasty_group['name'],
				'instructions' => array(),
			);

			foreach ( $tasty_group['items'] as $tasty_item ) {
				$text = trim( strip_tags( $tasty_item, '<a><strong><b><em><i><u><sub><sup>' ) );

				// Find any images.
				preg_match_all( '/<img[^>]+>/i', $tasty_item, $img_tags );

				foreach ( $img_tags[0] as $img_tag ) {
					preg_match_all( '/src="([^"]*)"/i', $img_tag, $img );

					if ( $img[1] ) {
						$img_src = $img[1][0];
						$image_id = $this->get_or_upload_attachment( $id, $img_src );

						if ( $image_id ) {
							$group['instructions'][] = array(
								'text' => $text,
								'image' => $image_id,
							);
							$text = ''; // Only add same text once.
						}
					}
				}

				if ( ! empty( $text ) ) {
					$group['instructions'][] = array(
						'text' => $text,
					);
				}
			}

			$instructions[] = $group;
		}
		$recipe['instructions'] = $instructions;

		// Nutrition Facts.
		$recipe['nutrition'] = array();

		// Serving size.
		$tasty_serving_size = get_post_meta( $id, 'serving_size', true );
		$match = preg_match( '/^\s*\d+/', $tasty_serving_size, $servings_array );
		if ( 1 === $match ) {
				$servings = str_replace( ' ','', $servings_array[0] );
		} else {
				$servings = '';
		}

		$servings_unit = preg_replace( '/^\s*\d+\s*/', '', $tasty_serving_size );

		$recipe['nutrition']['serving_size'] = $servings;
		$recipe['nutrition']['serving_unit'] = $servings_unit;

		$recipe['nutrition']['calories'] = get_post_meta( $id, 'calories', true );
		$recipe['nutrition']['sugar'] = get_post_meta( $id, 'sugar', true );
		$recipe['nutrition']['sodium'] = get_post_meta( $id, 'sodium', true );
		$recipe['nutrition']['fat'] = get_post_meta( $id, 'fat', true );
		$recipe['nutrition']['saturated_fat'] = get_post_meta( $id, 'saturated_fat', true );
		$recipe['nutrition']['polyunsaturated_fat'] = get_post_meta( $id, 'unsaturated_fat', true );
		$recipe['nutrition']['trans_fat'] = get_post_meta( $id, 'trans_fat', true );
		$recipe['nutrition']['carbohydrates'] = get_post_meta( $id, 'carbohydrates', true );
		$recipe['nutrition']['fiber'] = get_post_meta( $id, 'fiber', true );
		$recipe['nutrition']['protein'] = get_post_meta( $id, 'protein', true );
		$recipe['nutrition']['cholesterol'] = get_post_meta( $id, 'cholesterol', true );

		return $recipe;
	}

	/**
	 * Replace the original recipe with the newly imported WPRM one.
	 *
	 * @since    1.23.0
	 * @param	 mixed $id ID of the recipe we want replace.
	 * @param	 mixed $wprm_id ID of the WPRM recipe to replace with.
	 * @param	 array $post_data POST data passed along when submitting the form.
	 */
	public function replace_recipe( $id, $wprm_id, $post_data ) {
		// We don't know which posts use this recipe so we rely on the fallback shortcode.
	}

	/**
	 * Custom strtotime function for Tasty format.
	 *
	 * @since    1.23.0
	 * @param	 mixed $time Time to get in minutes.
	 * @param	 mixed $now Time now.
	 */
	public static function strtotime( $time, $now = null ) {
		if ( null === $now ) {
			$now = time();
		}
		// Parse string to remove any info in parentheses.
		$time = preg_replace( '/\([^\)]+\)/' , '' , $time );
		return strtotime( $time, $now );
	}


	/**
	 * Get the time in minutes.
	 *
	 * @since    1.23.0
	 * @param	 mixed $time Time to get in minutes.
	 */
	private function get_minutes_for_time( $time ) {
		// Assume a number is minutes.
		if ( is_numeric( $time ) ) {
			$time = "{$time} minutes";
		}
		$now = time();
		$time = $this->strtotime( $time, $now );

		return ( $time - $now ) / 60;
	}

	/**
	 * Blob to array.
	 *
	 * @since    1.23.0
	 * @param	 mixed $component Component to parse.
	 */
	private function parse_recipe_component_list( $component ) {
		$component_list = array();
		$component_group = array(
			'name' => '',
			'items' => array(),
		);

		$bits = explode( PHP_EOL, $component );
		foreach ( $bits as $bit ) {

			$test_bit = trim( $bit );
			if ( empty( $test_bit ) ) {
				continue;
			}
			if ( $this->is_heading( $bit ) ) {
				$component_list[] = $component_group;

				$component_group = array(
					'name' => strip_tags( trim( $bit ) ),
					'items' => array(),
				);
			} else {
				$component_group['items'][] = trim( $bit );
			}
		}

		$component_list[] = $component_group;

		return $component_list;
	}

	/**
	 * Check if line is heading.
	 *
	 * @since    1.23.0
	 * @param	 mixed $string String to parse.
	 */
	private function is_heading( $string ) {
		$string = trim( $string );
		// For The Red Beans:.
		if ( ':' === substr( $string, -1, 1 ) ) {
			return true;
		}
		// <strong>For The Red Beans</strong>.
		if ( '<strong>' === substr( $string, 0, 8 ) && '</strong>' === substr( $string, -9, 9 ) ) {
			return true;
		}
		// <h3>For The Red Beans</h3>.
		if ( preg_match( '#^<h[1-6]>.+<\/h[1-6]>$#', $string ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get image attachment ID from a given URL or sideload the image if not on the website.
	 *
	 * @since    1.23.0
	 * @param		 int   $post_id Post to associate the image with.
	 * @param		 mixed $url Image URL.
	 */
	private function get_or_upload_attachment( $post_id, $url ) {
		$url = str_replace( array( "\n", "\t", "\r" ), '', $url );
		$image_id = $this->get_attachment_id_from_url( $url );

		if ( $image_id ) {
			return $image_id;
		} else {
			$media = media_sideload_image( $url, $post_id );

			$attachments = get_posts( array(
							'numberposts' => '1',
							'post_parent' => $post_id,
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'orderby' => 'post_date',
							'order' => 'DESC',
					)
			);

			if ( count( $attachments ) > 0 ) {
				return $attachments[0]->ID;
			}
		}

		return false;
	}

	/**
	 * Get image attachment ID from a given URL.
	 * Source: https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
	 *
	 * @since    1.23.0
	 * @param		 mixed $attachment_url Image URL.
	 */
	private function get_attachment_id_from_url( $attachment_url = '' ) {
		global $wpdb;
		$attachment_id = false;

		// If there is no url, return.
		if ( '' === $attachment_url ) {
			return;
		}

		// Get the upload directory paths.
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL.
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL.
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) ); // @codingStandardsIgnoreLine
		}

		return $attachment_id;
	}
}
