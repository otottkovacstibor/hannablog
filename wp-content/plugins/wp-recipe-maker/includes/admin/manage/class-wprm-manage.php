<?php
/**
 * Handle the manage page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 */

/**
 * Handle the manage page.
 *
 * @since      1.9.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Manage {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.9.0
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu_page' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_action( 'wprm_manage_page', array( __CLASS__, 'manage_page' ) );

		add_action( 'wp_ajax_wprm_manage_datatable', array( __CLASS__, 'ajax_manage_datatable' ) );
		add_action( 'wp_ajax_wprm_update_term_metadata', array( __CLASS__, 'ajax_update_term_metadata' ) );
		add_action( 'wp_ajax_wprm_rename_term', array( __CLASS__, 'ajax_rename_term' ) );
		add_action( 'wp_ajax_wprm_delete_or_merge_term', array( __CLASS__, 'ajax_delete_or_merge_term' ) );
		add_action( 'wp_ajax_wprm_delete_terms', array( __CLASS__, 'ajax_delete_terms' ) );
		add_action( 'wp_ajax_wprm_delete_recipe', array( __CLASS__, 'ajax_delete_recipe' ) );
	}

	/**
	 * Update term metadata through AJAX.
	 *
	 * @since    1.9.0
	 */
	public static function ajax_update_term_metadata() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0; // Input var okay.
			$field = isset( $_POST['field'] ) ? sanitize_key( wp_unslash( $_POST['field'] ) ) : ''; // Input var okay.

			// Don't sanitize to make sure URL is identical.
			$value = isset( $_POST['value'] ) ? $_POST['value'] : ''; // Input var okay.

			// Check if valid field.
			if ( $term_id && in_array( $field, array( 'ingredient_link', 'ingredient_link_nofollow' ), true ) ) {

				if ( 'ingredient_link_nofollow' === $field ) {
					$value = in_array( $value, array( 'default', 'nofollow', 'follow' ) ) ? $value : 'default';
				}

				update_term_meta( $term_id, 'wprmp_' . $field, $value );
			}
		}

		wp_die();
	}

	/**
	 * Rename terms through AJAX.
	 *
	 * @since    1.21.0
	 */
	public static function ajax_rename_term() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0; // Input var okay.
			$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : ''; // Input var okay.
			$new_name = isset( $_POST['new_name'] ) ? sanitize_text_field( wp_unslash( $_POST['new_name'] ) ) : ''; // Input var okay.

			// This ensures were only chaning our own taxonomies.
			$taxonomy = 'wprm_' . $taxonomy;

			if ( $term_id ) {
				$term = get_term( $term_id, $taxonomy );

				// Check if this is one of our taxonomies.
				if ( $term && ! is_wp_error( $term ) ) {
					$term_update = wp_update_term(
						$term_id,
						$taxonomy,
						array(
							'name' => $new_name,
							'slug' => sanitize_title( $new_name ),
						)
					);

					$term = get_term( $term_id, $taxonomy );

					if ( ! is_wp_error( $term_update ) ) {
						self::rename_recipe_ingredients( $term );
					}
				}
			}
		}

		wp_die();
	}

	/**
	 * Delete or merge terms through AJAX.
	 *
	 * @since    1.9.0
	 */
	public static function ajax_delete_or_merge_term() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0; // Input var okay.
			$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : ''; // Input var okay.
			$new_term_id = isset( $_POST['new_term_id'] ) ? intval( $_POST['new_term_id'] ) : 0; // Input var okay.

			// This ensures were only chaning our own taxonomies.
			$taxonomy = 'wprm_' . $taxonomy;

			if ( $term_id ) {
				$term = get_term( $term_id, $taxonomy );

				// Check if this is one of our taxonomies.
				if ( $term && ! is_wp_error( $term ) ) {
					if ( ! $new_term_id ) {
						// Make sure this ingredient is not used anymore before deleting.
						if ( 'wprm_ingredient' !== $taxonomy || 0 === $term->count ) {
							wp_delete_term( $term->term_id, $taxonomy );
						}
					} else {
						// This ensures the term to merge into is in the same taxonomy.
						$new_term = get_term( $new_term_id, $taxonomy );

						if ( $new_term && ! is_wp_error( $new_term ) ) {
							self::merge_recipe_terms( $term, $new_term );
							wp_delete_term( $term->term_id, $taxonomy );
						}
					}
				}
			}
		}

		wp_die();
	}

	/**
	 * Delete nultiple terms at once through AJAX.
	 *
	 * @since    1.18.0
	 */
	public static function ajax_delete_terms() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$term_ids = isset( $_POST['term_ids'] ) ? array_map( 'intval', $_POST['term_ids'] ) : 0; // Input var okay.
			$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : ''; // Input var okay.

			// This ensures were only chaning our own taxonomies.
			$taxonomy = 'wprm_' . $taxonomy;

			if ( !empty( $term_ids ) ) {

				foreach ( $term_ids as $term_id ) {
					$term = get_term( $term_id, $taxonomy );

					// Check if this is one of our taxonomies.
					if ( $term && ! is_wp_error( $term ) ) {
						// Make sure this ingredient is not used anymore before deleting.
						if ( 'wprm_ingredient' !== $taxonomy || 0 === $term->count ) {
							wp_delete_term( $term->term_id, $taxonomy );
						}
					}
				}
			}
		}

		wp_die();
	}

	/**
	 * Rename ingredients for all recipes using them.
	 *
	 * @since    1.21.0
	 * @param    int $term Ingredient term to rename.
	 */
	public static function rename_recipe_ingredients( $term ) {
		$args = array(
			'post_type' => WPRM_POST_TYPE,
			'post_status' => 'any',
			'nopaging' => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'wprm_ingredient',
					'field' => 'id',
					'terms' => $term->term_id,
				),
			)
		);

		$query = new WP_Query( $args );
		$posts = $query->posts;
		foreach ( $posts as $post ) {
			$recipe = WPRM_Recipe_Manager::get_recipe( $post );

			$new_ingredients = array();
			foreach ( $recipe->ingredients() as $ingredient_group ) {
				$new_ingredient_group = $ingredient_group;
				$new_ingredient_group['ingredients'] = array();

				foreach ( $ingredient_group['ingredients'] as $ingredient ) {
					if ( intval( $ingredient['id'] ) === $term->term_id ) {
						$ingredient['name'] = $term->name;
					}
					$new_ingredient_group['ingredients'][] = $ingredient;
				}

				$new_ingredients[] = $new_ingredient_group;
			}

			update_post_meta( $recipe->id(), 'wprm_ingredients', $new_ingredients );
		}
	}

	/**
	 * Merge terms for all recipes using them.
	 *
	 * @since    1.9.0
	 * @param    object $term     Term to merge from.
	 * @param    object $new_term Term to merge to.
	 */
	public static function merge_recipe_terms( $term, $new_term ) {
		$args = array(
			'post_type' => WPRM_POST_TYPE,
			'post_status' => 'any',
			'nopaging' => true,
			'tax_query' => array(
				array(
					'taxonomy' => $term->taxonomy,
					'field' => 'id',
					'terms' => $term->term_id,
				),
			)
		);

		$query = new WP_Query( $args );
		$posts = $query->posts;
		foreach ( $posts as $post ) {
			if ( 'wprm_ingredient' === $term->taxonomy ) {
				$recipe = WPRM_Recipe_Manager::get_recipe( $post );

				$new_ingredients = array();
				$new_ingredient_ids = array();
				foreach ( $recipe->ingredients() as $ingredient_group ) {
					$new_ingredient_group = $ingredient_group;
					$new_ingredient_group['ingredients'] = array();

					foreach ( $ingredient_group['ingredients'] as $ingredient ) {
						if ( intval( $ingredient['id'] ) === $term->term_id ) {
							$ingredient['id'] = $new_term->term_id;
							$ingredient['name'] = $new_term->name;
						}
						$new_ingredient_ids[] = intval( $ingredient['id'] );
						$new_ingredient_group['ingredients'][] = $ingredient;
					}

					$new_ingredients[] = $new_ingredient_group;
				}

				$new_ingredient_ids = array_unique( $new_ingredient_ids );
				wp_set_object_terms( $recipe->id(), $new_ingredient_ids, 'wprm_ingredient', false );

				update_post_meta( $recipe->id(), 'wprm_ingredients', $new_ingredients );
			} else {
				// Append new term.
				wp_set_object_terms( $post->ID, $new_term->term_id, $term->taxonomy, true );
			}
		}
	}

	/**
	 * Delete recipes through AJAX.
	 *
	 * @since    1.9.0
	 */
	public static function ajax_delete_recipe() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$recipe_id = isset( $_POST['recipe_id'] ) ? intval( $_POST['recipe_id'] ) : 0; // Input var okay.

			if ( $recipe_id ) {
				$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

				if ( $recipe ) {
					wp_trash_post( $recipe_id );
				}
			}
		}

		wp_die();
	}

	/**
	 * Processor for the datatable on the manage page.
	 *
	 * @since    1.9.0
	 */
	public static function ajax_manage_datatable() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$table = isset( $_POST['table'] ) ? sanitize_key( $_POST['table'] ) : ''; // Input var okay.
			$datatable = self::sanitize_datatable( $_POST ); // Input var okay.

			$data = array();

			if ( 'wprm-manage-recipes' === $table ) {
				$data = WPRM_Manage_Recipes::get_datatable( $datatable );
			} elseif ( 'wprm-manage-ingredients' === $table ) {
				$data = WPRM_Manage_Ingredients::get_datatable( $datatable );
			} elseif ( 'wprm-manage-taxonomy-' === substr( $table, 0, 21 ) ) {
				$taxonomy = 'wprm_' . substr( $table, 21 );
				$data = WPRM_Manage_Taxonomies::get_datatable( $datatable, $taxonomy );
			}

			$data = apply_filters( 'wprm_manage_datatable_data', $data, $table, $datatable );

			echo wp_json_encode( $data );
		}

		wp_die();
	}

	/**
	 * Sanitize the datatable values that were passed along through AJAX.
	 *
	 * @since    1.9.0
	 * @param		 array $datatable Datatable request values.
	 */
	public static function sanitize_datatable( $datatable ) {
		return array(
			'draw' => isset( $datatable['draw'] ) ? intval( $datatable['draw'] ) : 1,
			'start' => isset( $datatable['start'] ) ? intval( $datatable['start'] ) : 0,
			'length' => isset( $datatable['length'] ) ? intval( $datatable['length'] ) : 10,
			'search' => isset( $datatable['search']['value'] ) ? sanitize_text_field( $datatable['search']['value'] ) : '',
			'orderby' => isset( $datatable['order'][0]['column'] ) ? intval( $datatable['order'][0]['column'] ) : 0,
			'order' => isset( $datatable['order'][0]['dir'] ) && 'desc' === $datatable['order'][0]['dir'] ? 'DESC' : 'ASC',
		);
	}

	/**
	 * Add the manage submenu to the WPRM menu.
	 *
	 * @since    1.9.0
	 */
	public static function add_submenu_page() {
		add_submenu_page( 'wprecipemaker', __( 'Manage', 'wp-recipe-maker-premium' ), __( 'Manage', 'wp-recipe-maker-premium' ), WPRM_Settings::get( 'features_manage_access' ), 'wprecipemaker', array( __CLASS__, 'page_template' ) );
	}

	/**
	 * Get the template for this submenu.
	 *
	 * @since    1.9.0
	 */
	public static function page_template() {
		require_once( WPRM_DIR . 'templates/admin/manage.php' );
	}

	/**
	 * Manage page to output.
	 *
	 * @since    1.10.0
	 * @param	 mixed $sub Sub manage page to display.
	 */
	public static function manage_page( $sub ) {
		if ( 'recipes' === $sub ) {
			require_once( WPRM_DIR . 'templates/admin/manage/recipes.php' );
		} elseif ( 'ingredients' === $sub ) {
			require_once( WPRM_DIR . 'templates/admin/manage/ingredients.php' );
		} elseif ( 'taxonomy_' === substr( $sub, 0, 9 ) ) {
			require_once( WPRM_DIR . 'templates/admin/manage/taxonomies.php' );
		}
	}

	/**
	 * Show notices on the manage pages.
	 *
	 * @since    1.9.0
	 */
	public static function notices() {
		$screen = get_current_screen();

		if ( 'toplevel_page_wprecipemaker' === $screen->id && ! WPRM_Addons::is_active( 'premium' ) ) {
			if ( isset( $_GET['sub'] ) && 'ingredients' === $_GET['sub'] ) {
				echo '<div class="notice notice-warning">';
				echo '<p><strong>WP Recipe Maker Premium</strong><br/>';
				echo esc_html__( 'These features are only available in', 'wp-recipe-maker-premium' );
				echo ' <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/" target="_blank">WP Recipe Maker Premium</a>:';
				echo '<ul>';
				echo '<li><a href="https://help.bootstrapped.ventures/article/29-ingredient-links" target="_blank">' . esc_html__( 'Ingredient Links', 'wp-recipe-maker-premium' ) . '</a></li>';
				echo '</ul>';
				echo '</p>';
				echo '</div>';
			}
		}
	}
}

WPRM_Manage::init();
