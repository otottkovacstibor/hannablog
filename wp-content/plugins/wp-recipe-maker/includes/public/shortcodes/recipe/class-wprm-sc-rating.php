<?php
/**
 * Handle the recipe rating shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe rating shortcode.
 *
 * @since      3.2.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Rating extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-recipe-rating';
	public static $attributes = array(
		'id' => array(
			'default' => '0',
		),
		'display' => array(
			'default' => 'stars',
			'type' => 'dropdown',
			'options' => array(
				'stars' => 'Stars',
				'stars-details' => 'Stars with Details',
				'details' => 'Details',
				'average' => 'Average',
			),
		),
		'text_style' => array(
			'default' => 'normal',
			'type' => 'dropdown',
			'options' => 'text_styles',
			'dependency' => array(
				'id' => 'display',
				'value' => 'stars',
				'type' => 'inverse',
			),
		),
		'voteable' => array(
			'default' => '1',
			'type' => 'toggle',
			'dependency' => array(
				array(
					'id' => 'display',
					'value' => 'details',
					'type' => 'inverse'
				),
				array(
					'id' => 'display',
					'value' => 'average',
					'type' => 'inverse'
				),
			),
		),
		'icon' => array(
			'default' => 'star-empty',
			'type' => 'icon',
		),
		'icon_color' => array(
			'default' => '#343434',
			'type' => 'color',
			'dependency' => array(
				'id' => 'icon',
				'value' => '',
				'type' => 'inverse',
			),
		),
	);

	/**
	 * Output for the shortcode.
	 *
	 * @since	3.2.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe ) {
			return '';
		}
		
		$rating = $recipe->rating();

		if ( 'stars' === $atts['display'] || 'stars-details' === $atts['display'] ){
			$voteable = (bool) $atts['voteable'];
			$output = self::get_stars( $recipe, $rating, $voteable, $atts['icon'], $atts['icon_color'] );

			if ( false === $output ) {
				return '';
			}
		} else {
			$output = '<div class="wprm-recipe-rating">';
		}

		if ( 'details' === $atts['display'] || 'stars-details' === $atts['display'] ) {
			$classes = array(
				'wprm-recipe-rating-details',
				'wprm-block-text-' . $atts['text_style'],
			);

			$output .= '<div class="' . implode( ' ', $classes ) . '"><span class="wprm-recipe-rating-average">' . $rating['average'] . '</span> ' . __( 'from', 'wp-recipe-maker' ) . ' <span class="wprm-recipe-rating-count">' . $rating['count'] . '</span> ' . _n( 'vote', 'votes', $rating['count'], 'wp-recipe-maker' ) . '</div>';
		} elseif ( 'average' === $atts['display'] ) {
			$classes = array(
				'wprm-recipe-rating-average',
				'wprm-block-text-' . $atts['text_style'],
			);

			$output .= '<div class="' . implode( ' ', $classes ) . '">' . $rating['average'] . '</div>';
		}

		$output .= '</div>';

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}

	/**
	 * Get the stars output for a rating.
	 *
	 * @since    3.2.0
	 * @param    mixed 	 $recipe   Recipe to display the rating for.
	 * @param    array 	 $rating   Rating to display.
	 * @param    mixed	 $icon 	   Icon to use for the rating.
	 * @param    boolean $voteabel Wether the user is allowed to vote.
	 */
	private static function get_stars( $recipe, $rating, $voteable, $icon, $color ) {
		$user_ratings = WPRM_Addons::is_active( 'premium' ) && WPRM_Settings::get( 'features_user_ratings' );
		$rating_value = ceil( $rating['average'] );

		// Only output when there is an actual rating or users can rate.
		if ( ! ( $user_ratings && $voteable ) && ! $rating_value ) {
			return false;
		}

		$output = '';

		// Output style for star color.
		$output .= '<style>';
		$output .= '.wprm-recipe-rating .wprm-rating-star.wprm-rating-star-full svg * { fill: ' . $color . '; }';
		$output .= '</style>';

		// Get correct class.
		if ( $user_ratings && $voteable && WPRMP_User_Rating::is_user_allowed_to_vote() ) {
			$class = ' wprm-user-rating wprm-user-rating-allowed';
			$data = ' data-recipe="' . $recipe->id() . '" data-average="' . $rating['average'] . '" data-count="' . $rating['count'] . '" data-total="' . $rating['total'] . '" data-user="' . $rating['user'] . '"';
		} elseif ( $user_ratings ) {
			$class = ' wprm-user-rating';
			$data = '';
		} else {
			$class = '';
			$data = '';
		}

		// Output stars.
		$output .= '<div class="wprm-recipe-rating' . $class . '"' . $data . '>';
		for ( $i = 1; $i <= 5; $i++ ) {
			$class = $i <= $rating_value ? 'wprm-rating-star-full' : 'wprm-rating-star-empty';
			$output .= '<span class="wprm-rating-star wprm-rating-star-' . $i . ' ' . $class . '" data-rating="' . $i . '" data-color="' . $color . '">';
			$output .= apply_filters( 'wprm_recipe_rating_star_icon', WPRM_Icon::get( $icon, $color) );
			$output .= '</span>';
		}

		return $output;
	}
}

WPRM_SC_Rating::init();