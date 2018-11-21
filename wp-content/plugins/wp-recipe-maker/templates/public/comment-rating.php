<?php
/**
 * Template to be used for the rating in comments.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.1.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/public
 */

?>
<div class="wprm-comment-rating">
	<span class="wprm-rating-stars"><?php
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<span class="wprm-rating-star">';
			if ( $i <= $rating ) {
					ob_start();
					include( WPRM_DIR . 'assets/icons/star-full.svg' );
					$star_icon = ob_get_contents();
					ob_end_clean();

					echo apply_filters( 'wprm_comment_rating_star_full_icon', $star_icon );
			} else {
					ob_start();
					include( WPRM_DIR . 'assets/icons/star-empty.svg' );
					$star_icon = ob_get_contents();
					ob_end_clean();

					echo apply_filters( 'wprm_comment_rating_star_icon', $star_icon );
			}
			echo '</span>';
		}
	?></span>
</div>
