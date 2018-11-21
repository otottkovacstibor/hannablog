<?php
/**
 * Allow visitors to rate the recipe in the comment.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.1.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Allow visitors to rate the recipe in the comment.
 *
 * @since      1.1.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Comment_Rating {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.1.0
	 */
	public static function init() {
		add_filter( 'comment_text', array( __CLASS__, 'add_stars_to_comment' ), 10, 2 );

		add_action( 'init', array( __CLASS__, 'wpdiscuz_compatibility' ) );
		add_action( 'wpdiscuz_button', array( __CLASS__, 'add_rating_field_to_comments' ) );
		add_action( 'comment_form_after_fields', array( __CLASS__, 'add_rating_field_to_comments' ) );
		add_action( 'comment_form_logged_in_after', array( __CLASS__, 'add_rating_field_to_comments' ) );
		add_action( 'add_meta_boxes_comment', array( __CLASS__, 'add_rating_field_to_admin_comments' ) );

		add_action( 'comment_post', array( __CLASS__, 'save_comment_rating' ) );
		add_action( 'edit_comment', array( __CLASS__, 'save_admin_comment_rating' ) );

		add_action( 'trashed_comment', array( __CLASS__, 'update_comment_rating_on_change' ) );
		add_action( 'spammed_comment', array( __CLASS__, 'update_comment_rating_on_change' ) );
		add_action( 'unspammed_comment', array( __CLASS__, 'update_comment_rating_on_change' ) );
		add_action( 'comment_unapproved_', array( __CLASS__, 'update_comment_rating_on_change' ) );
		add_action( 'comment_approved_', array( __CLASS__, 'update_comment_rating_on_change' ) );
	}

	/**
	 * Get ratings for a specific recipe.
	 *
	 * @since	2.2.0
	 * @param	int $recipe_id ID of the recipe.
	 */
	public static function get_ratings_for( $recipe_id ) {
		$ratings = array();
		$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

		if ( $recipe ) {
			$query_where = '';

			$comments = get_approved_comments( $recipe->parent_post_id() );
			$comment_ids = array_map( 'intval', wp_list_pluck( $comments, 'comment_ID' ) );

			if ( count( $comment_ids ) ) {
				$comment_ratings = WPRM_Rating_Database::get_ratings(array(
					'where' => 'comment_id IN (' . implode( ',', $comment_ids ) . ')',
				));

				$ratings = $comment_ratings['ratings'];
			}
		}

		return $ratings;
	}

	/**
	 * Get rating for a specific comment.
	 *
	 * @since	2.2.0
	 * @param	int $comment_id ID of the comment.
	 */
	public static function get_rating_for( $comment_id ) {
		$rating = 0;
		$comment_id = intval( $comment_id );

		if ( $comment_id ) {
			$rating_found = get_comment_meta( $comment_id, 'wprm-comment-rating', true );

			// Cache rating for this comment if none can be found.
			if ( '' === $rating_found ) {
				$rating_found = WPRM_Rating_Database::get_rating(array(
					'where' => 'comment_id = ' . $comment_id,
				));
	
				if ( $rating_found ) {
					$rating = intval( $rating_found->rating );
				} else {
					$rating = 0;
				}

				self::update_cached_rating( $comment_id, $rating );
			} else {
				$rating = intval( $rating_found );
			}
		}

		return $rating;
	}

	/**
	 * Add or update rating for a specific comment.
	 *
	 * @since	2.2.0
	 * @param	int $comment_id ID of the comment.
	 * @param	int $comment_rating Rating to add for this comment.
	 */
	public static function add_or_update_rating_for( $comment_id, $comment_rating ) {
		$comment_id = intval( $comment_id );
		$comment_rating = intval( $comment_rating );

		if ( $comment_id ) {
			$comment = get_comment( $comment_id );

			if ( $comment ) {
				if ( $comment_rating ) {
					$rating = array(
						'date' => $comment->comment_date,
						'comment_id' => $comment->comment_ID,
						'user_id' => $comment->user_id,
						'ip' => $comment->comment_author_IP,
						'rating' => $comment_rating,
					);

					WPRM_Rating_Database::add_or_update_rating( $rating );
				} else {
					WPRM_Rating_Database::delete_ratings_for_comment( $comment_id );
				}
			} else {
				WPRM_Rating_Database::delete_ratings_for_comment( $comment_id );
			}
		}
	}

	/**
	 * Update the comment rating meta that is used as a cache.
	 *
	 * @since	3.1.0
	 * @param	int $comment_id ID of the comment.
	 * @param	int $comment_rating Rating to set for this comment.
	 */
	public static function update_cached_rating( $comment_id, $comment_rating ) {
		$comment_id = intval( $comment_id );
		$comment_rating = intval( $comment_rating );

		if ( $comment_id ) {
			$comment = get_comment( $comment_id );

			if ( $comment ) {
				update_comment_meta( $comment_id, 'wprm-comment-rating', $comment_rating );
			}
		}
	}

	/**
	 * Add field to the comment form.
	 *
	 * @since    1.1.0
	 * @param		 mixed  $text Comment text.
	 * @param		 object $comment Comment object.
	 */
	public static function add_stars_to_comment( $text, $comment = null ) {
		if ( null !== $comment ) {
			$rating = self::get_rating_for( $comment->comment_ID );

			$rating_html = '';
			if ( $rating ) {
				ob_start();
				$template = apply_filters( 'wprm_template_comment_rating', WPRM_DIR . 'templates/public/comment-rating.php' );
				require( $template );
				$rating_html = ob_get_contents();
				ob_end_clean();
			}

			$text = 'below' === WPRM_Settings::get( 'comment_rating_position' ) ? $text . $rating_html : $rating_html . $text;
		}

		return $text;
	}

	/**
	 * Compatibility with the wpDiscuz plugin.
	 *
	 * @since    1.3.0
	 */
	public static function wpdiscuz_compatibility() {
		if ( ! defined( 'WPDISCUZ_BOTTOM_TOOLBAR' ) ) {
			define( 'WPDISCUZ_BOTTOM_TOOLBAR', true );
		}
	}

	/**
	 * Add field to the comment form.
	 *
	 * @since    1.1.0
	 */
	public static function add_rating_field_to_comments() {
		$rating = 0;
		$template = apply_filters( 'wprm_template_comment_rating_form', WPRM_DIR . 'templates/public/comment-rating-form.php' );
		require( $template );
	}

	/**
	 * Add field to the admin comment form.
	 *
	 * @since    1.1.0
	 */
	public static function add_rating_field_to_admin_comments() {
		add_meta_box( 'wprm-comment-rating', __( 'Recipe Rating', 'wp-recipe-maker' ), array( __CLASS__, 'add_rating_field_to_admin_comments_form' ), 'comment', 'normal', 'high' );
	}

	/**
	 * Callback for the admin comments meta box.
	 *
	 * @since    1.1.0
	 * @param		 object $comment Comment being edited.
	 */
	public static function add_rating_field_to_admin_comments_form( $comment ) {
		$rating = self::get_rating_for( $comment->comment_ID );
		wp_nonce_field( 'wprm-comment-rating-nonce', 'wprm-comment-rating-nonce', false );
		$template = apply_filters( 'wprm_template_comment_rating_form', WPRM_DIR . 'templates/public/comment-rating-form.php' );
		require( $template );
	}

	/**
	 * Save the comment rating.
	 *
	 * @since    1.1.0
	 * @param		 int $comment_id ID of the comment being saved.
	 */
	public static function save_comment_rating( $comment_id ) {
		$rating = isset( $_POST['wprm-comment-rating'] ) ? intval( $_POST['wprm-comment-rating'] ) : 0; // Input var okay.
		self::add_or_update_rating_for( $comment_id, $rating );
	}

	/**
	 * Update recipe rating when comment changes.
	 *
	 * @since	3.2.0
	 * @param	int $comment_id ID of the comment being changed.
	 */
	public static function update_comment_rating_on_change( $comment_id ) {
		WPRM_Rating::update_recipe_rating_for_comment( $comment_id );
	}

	/**
	 * Save the admin comment rating.
	 *
	 * @since	1.1.0
	 * @param	int $comment_id ID of the comment being saved.
	 */
	public static function save_admin_comment_rating( $comment_id ) {
		if ( isset( $_POST['wprm-comment-rating-nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wprm-comment-rating-nonce'] ), 'wprm-comment-rating-nonce' ) ) { // Input var okay.
			$rating = isset( $_POST['wprm-comment-rating'] ) ? intval( $_POST['wprm-comment-rating'] ) : 0; // Input var okay.
			self::add_or_update_rating_for( $comment_id, $rating );
		}
	}
}

WPRM_Comment_Rating::init();
