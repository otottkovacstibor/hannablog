<?php
/**
 * Calculate and store the recipe rating.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.22.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Calculate and store the recipe rating.
 *
 * @since      1.22.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Rating {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.22.0
	 */
	public static function init() {
	}

	/**
	 * Update the rating for the recipes affected by a specific comment.
	 *
	 * @since    1.22.0
	 * @param	 int $comment_id Comment ID to update the rating for.
	 */
	public static function update_recipe_rating_for_comment( $comment_id ) {
		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;
		$post_content = get_post_field( 'post_content', $post_id );

		$recipe_ids = WPRM_Recipe_Manager::get_recipe_ids_from_content( $post_content );

		if ( count( $recipe_ids ) > 0 ) {
			foreach ( $recipe_ids as $recipe_id ) {
				self::update_recipe_rating( $recipe_id );
			}
		}
	}

	/**
	 * Update the rating for a specific recipe.
	 *
	 * @since    1.22.0
	 * @param	 int $recipe_id Recipe ID to to update the rating for.
	 */
	public static function update_recipe_rating( $recipe_id ) {
		$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

		$recipe_rating = array(
			'count' => 0,
			'total' => 0,
			'average' => 0,
		);

		$ratings = self::get_ratings_for( $recipe_id );

		foreach ( $ratings['ratings'] as $rating ) {
			$recipe_rating['count']++;
			$recipe_rating['total'] += intval( $rating->rating );
		}

		// Calculate average.
		if ( $recipe_rating['count'] > 0 ) {
			$recipe_rating['average'] = ceil( $recipe_rating['total'] / $recipe_rating['count'] * 100 ) / 100;
		}

		// Update recipe rating and average (to sort by).
		update_post_meta( $recipe_id, 'wprm_rating', $recipe_rating );
		update_post_meta( $recipe_id, 'wprm_rating_average', $recipe_rating['average'] );

		// Update parent post with rating data (TODO account for multiple recipes in a post).
		update_post_meta( $recipe->parent_post_id(), 'wprm_rating', $recipe_rating );
		update_post_meta( $recipe->parent_post_id(), 'wprm_rating_average', $recipe_rating['average'] );

		return $recipe_rating;
	}

	/**
	 * Get the ratings for a specific recipe.
	 *
	 * @since    2.2.0
	 * @param	 int $recipe_id Recipe ID to to get the ratings for.
	 */
	public static function get_ratings_for( $recipe_id ) {
		$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

		$ratings = array(
			'total' => 0,
			'ratings' => array(),
		);
		$query_where = '';

		// Get comment ratings.
		if ( WPRM_Settings::get( 'features_comment_ratings' ) ) {
			$comments = get_approved_comments( $recipe->parent_post_id() );
			$comment_ids = array_map( 'intval', wp_list_pluck( $comments, 'comment_ID' ) );

			if ( count( $comment_ids ) ) {
				$where_comments = 'comment_id IN (' . implode( ',', $comment_ids ) . ')';
				$query_where .= $query_where ? ' OR ' . $where_comments : $where_comments;
			}
		}

		// Get user ratings.
		if ( WPRM_Addons::is_active( 'premium' ) && WPRM_Settings::get( 'features_user_ratings' ) ) {
			$where_recipe = 'recipe_id = ' . intval( $recipe_id );
			$query_where .= $query_where ? ' OR ' . $where_recipe : $where_recipe;
		}

		if ( $query_where ) {
			$rating_args = array(
				'where' => $query_where,
			);
			$ratings = WPRM_Rating_Database::get_ratings( $rating_args );
		}

		return $ratings;
	}
}

WPRM_Rating::init();
