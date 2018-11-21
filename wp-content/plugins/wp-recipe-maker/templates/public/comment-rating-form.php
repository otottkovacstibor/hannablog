<?php
/**
 * Template to be used for the rating field in the comment form.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.1.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/public
 */

?>
<p class="comment-form-wprm-rating" data-color="<?php echo esc_attr( WPRM_Settings::get( 'template_color_comment_rating' ) ); ?>">
	<label for="wprm-rating"><?php echo WPRM_Template_Helper::label( 'comment_rating' ); ?></label>
	<span class="wprm-rating-stars">
		<?php
		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $i <= $rating ) {
					echo '<span class="wprm-rating-star rated" data-rating="' . esc_attr( $i ) . '">';
			} else {
					echo '<span class="wprm-rating-star" data-rating="' . esc_attr( $i ) . '">';
			}
			ob_start();
			include( WPRM_DIR . 'assets/icons/star-empty.svg' );
			$star_icon = ob_get_contents();
			ob_end_clean();

			echo apply_filters( 'wprm_comment_rating_star_icon', $star_icon );

			echo '</span>';
		}
		?>
	</span>
	<input id="wprm-comment-rating" name="wprm-comment-rating" type="hidden" value="<?php echo esc_attr( $rating ); ?>">
</p>
