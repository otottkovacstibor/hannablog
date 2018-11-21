<?php
/**
 * FoodiePress importer.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.25.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/import
 */

/**
 * FoodiePress importer.
 *
 * @since      1.25.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/import
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Import_Foodiepress extends WPRM_Import {

	/**
	 * Get the UID of this import source.
	 *
	 * @since    1.25.0
	 */
	public function get_uid() {
		return 'foodiepress';
	}

	/**
	 * Wether or not this importer requires a manual search for recipes.
	 *
	 * @since    1.25.0
	 */
	public function requires_search() {
		return false;
	}

	/**
	 * Get the name of this import source.
	 *
	 * @since    1.25.0
	 */
	public function get_name() {
		return 'FoodiePress';
	}

	/**
	 * Get HTML for the import settings.
	 *
	 * @since    1.25.0
	 */
	public function get_settings_html() {
		return '';
	}

	/**
	 * Get the total number of recipes to import.
	 *
	 * @since    1.25.0
	 */
	public function get_recipe_count() {
		$args = array(
			'post_type' => 'post',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'meta_query' => array(
				array(
					'key'     => 'cookingpressingridients',
					'compare' => '!=',
					'value' => 'a:0:{}',
				),
			),
		);

		$query = new WP_Query( $args );
		return $query->found_posts;
	}

	/**
	 * Get a list of recipes that are available to import.
	 *
	 * @since    1.25.0
	 * @param	 int $page Page of recipes to get.
	 */
	public function get_recipes( $page = 0 ) {
		$recipes = array();

		$limit = 100;
		$offset = $limit * $page;

		$args = array(
				'post_type' => 'post',
				'post_status' => 'any',
				'meta_query' => array(
					array(
						'key'     => 'cookingpressingridients',
						'compare' => '!=',
						'value' => 'a:0:{}',
					),
				),
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
	 * @since    1.25.0
	 * @param	 mixed $id ID of the recipe we want to import.
	 * @param	 array $post_data POST data passed along when submitting the form.
	 */
	public function get_recipe( $id, $post_data ) {
		$recipe = array(
			'import_id' => 0,
			'import_backup' => array(
				'foodiepress_post_id' => intval( $id ),
			),
		);

		$post = get_post( $id );
		$post_meta = get_post_custom( $id );

		// Take over these fields.
		$recipe['name'] = $post_meta['cookingpresstitle'][0];
		$recipe['summary'] = $post_meta['cookingpresssummary'][0];
		$recipe['image_id'] = intval( $post_meta['cookingpressphoto'][0] );

		// Servings.
		$foodiepress_recipe_options = maybe_unserialize( $post_meta['cookingpressrecipeoptions'][0] );
		$foodiepress_yield = $foodiepress_recipe_options[2];

		$match = preg_match( '/^\s*\d+/', $foodiepress_yield, $servings_array );
		if ( 1 === $match ) {
				$servings = str_replace( ' ','', $servings_array[0] );
		} else {
				$servings = '';
		}

		$servings_unit = preg_replace( '/^\s*\d+\s*/', '', $foodiepress_yield );

		$recipe['servings'] = $servings;
		$recipe['servings_unit'] = $servings_unit;

		// Recipe Times.
		$recipe['prep_time'] = intval( $foodiepress_recipe_options[0] );
		$recipe['cook_time'] = intval( $foodiepress_recipe_options[1] );
		$recipe['total_time'] = $recipe['prep_time'] + $recipe['cook_time'];

		// Recipe Ingredients.
		$ingredients = maybe_unserialize( $post_meta['cookingpressingridients'][0] );
		$recipe['ingredients'] = array();

		$current_group = array(
			'name' => '',
			'ingredients' => array(),
		);
		foreach ( $ingredients as $ingredient ) {
			if ( isset( $ingredient['note'] ) && 'separator' === $ingredient['note'] ) {
				$recipe['ingredients'][] = $current_group;
				$current_group = array(
					'name' => $ingredient['name'],
					'ingredients' => array(),
				);
			} else {
				$current_group['ingredients'][] = array(
					'raw' => $ingredient['note'] . ' ' . $ingredient['name'],
				);
			}
		}
		$recipe['ingredients'][] = $current_group;

		// Instructions.
		$foodiepress_instructions = $this->parse_recipe_component_list( $post_meta['cookingpressinstructions'][0] );

		$instructions = array();

		foreach ( $foodiepress_instructions as $foodiepress_group ) {
			$group = array(
				'name' => $foodiepress_group['name'],
				'instructions' => array(),
			);

			foreach ( $foodiepress_group['items'] as $foodiepress_item ) {
				$text = trim( strip_tags( $foodiepress_item, '<a><strong><b><em><i><u><sub><sup>' ) );

				// Find any images.
				preg_match_all( '/<img[^>]+>/i', $foodiepress_item, $img_tags );

				foreach ( $img_tags[0] as $img_tag ) {
					if ( $img_tag ) {
						preg_match_all( '/src="([^"]*)"/i', $img_tag[0], $img );

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
		$nutrition = maybe_unserialize( $post_meta['cookingpressntfacts'][0] );

		$recipe['nutrition'] = array(
			'serving_size' => $nutrition['servingSize'],
			'calories' => $nutrition['calories'],
			'carbohydrates' => $nutrition['carbohydrateContent'],
			'protein' => $nutrition['proteinContent'],
			'fat' => $nutrition['fatContent'],
			'saturated_fat' => $nutrition['saturatedFatContent'],
			'polyunsaturated_fat' => $nutrition['unsaturatedFatContent'],
			'monounsaturated_fat' => '',
			'trans_fat' => $nutrition['transFatContent'],
			'cholesterol' => $nutrition['cholesterolContent'],
			'sodium' => $nutrition['sodiumContent'],
			'potassium' => '',
			'fiber' => $nutrition['fiberContent'],
			'sugar' => $nutrition['sugarContent'],
			'vitamin_a' => '',
			'vitamin_c' => '',
			'calcium' => '',
			'iron' => '',
		);

		return $recipe;
	}

	/**
	 * Replace the original recipe with the newly imported WPRM one.
	 *
	 * @since    1.25.0
	 * @param	 mixed $id ID of the recipe we want replace.
	 * @param	 mixed $wprm_id ID of the WPRM recipe to replace with.
	 * @param	 array $post_data POST data passed along when submitting the form.
	 */
	public function replace_recipe( $id, $wprm_id, $post_data ) {
		$post = get_post( $id );

		// Hide Foodiepress.
		$ingredients = get_post_meta( $id, 'cookingpressingridients', true );
		add_post_meta( $id, 'cookingpressingridients_bkp', $ingredients );
		delete_post_meta( $id, 'cookingpressingridients' );

		// Update or add shortcode.
		$content = $post->post_content;

		if ( 0 === substr_count( $content, '[foodiepress]' ) ) {
			$content .= ' [wprm-recipe id="' . $wprm_id . '"]';
		} else {
			$content = str_ireplace( '[foodiepress]', '[wprm-recipe id="' . $wprm_id . '"]', $content );
		}

		$update_content = array(
			'ID' => $id,
			'post_type' => 'post',
			'post_content' => $content,
		);
		wp_update_post( $update_content );
	}

	/**
	 * Blob to array.
	 *
	 * @since    1.25.0
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
	 * @since    1.25.0
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
