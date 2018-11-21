<?php
/**
 * Template for the Recipe Details tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<div class="wprm-recipe-form wprm-recipe-details-form">
	<?php do_action( 'wprm_modal_notice' ); ?>
	<div class="wprm-easyrecipe-warning" style="display: none;">
		<strong>Warning!</strong>
		<p>We're detecting a compatibility problem, potentially breaking some of our functionality. You could try disabling all other plugins for a second to see if you can find the conflict.</p>
		<p>Please <a href="https://help.bootstrapped.ventures/article/41-how-can-i-contact-support" target="_blank">contact us</a> for more help!</p>
	</div>
	<?php if ( WPRM_Settings::get( 'metadata_nonfood_allowed' ) ) : ?>
	<div class="wprm-recipe-form-container wprm-recipe-form-container-halfs">
		<label for="wprm-recipe-type"><?php esc_html_e( 'Type', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-recipe-type">
			<?php
			$options = array(
				'food' => __( 'Food Recipe', 'wp-recipe-maker' ),
				// 'howto' => __( 'How-To', 'wp-recipe-maker' ),
				'other' => __( 'Other (no metadata)', 'wp-recipe-maker' ),
			);

			foreach ( $options as $option => $label ) {
				echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</option>';
			}
			?>
		</select>
	</div>
	<?php endif; // Non-Food Allowed. ?>
	<div id="wprm-recipe-image-container" class="wprm-recipe-form-container wprm-recipe-form-container-halfs wprm-recipe-image-container">
		<label for="wprm-recipe-image-id"><?php esc_html_e( 'Image', 'wp-recipe-maker' ); ?></label>
		<input type="hidden" id="wprm-recipe-image-id" />
		<div class="wprm-recipe-image-preview"></div>
		<button type="button" class="button wprm-recipe-image-add"><?php esc_html_e( 'Add Image', 'wp-recipe-maker' ); ?></button>
		<a href="#" class="wprm-recipe-image-remove hidden"><?php esc_html_e( 'Remove Image', 'wp-recipe-maker' ); ?></a>		
	</div><?php if ( WPRM_Addons::is_active( 'premium' ) && 'recipe_image' !== WPRM_Settings::get( 'pinterest_use_for_image' ) ) : ?><div id="wprm-recipe-pin-image-container" class="wprm-recipe-form-container wprm-recipe-form-container-halfs wprm-recipe-image-container">
		<label for="wprm-recipe-pin-image-id"><?php esc_html_e( 'Pin Image', 'wp-recipe-maker' ); ?></label>
		<input type="hidden" id="wprm-recipe-pin-image-id" />
		<div class="wprm-recipe-image-preview"></div>
		<button type="button" class="button wprm-recipe-image-add"><?php esc_html_e( 'Add Pin Image', 'wp-recipe-maker' ); ?></button>
		<a href="#" class="wprm-recipe-image-remove hidden"><?php esc_html_e( 'Remove Pin Image', 'wp-recipe-maker' ); ?></a>		
	</div>
	<?php endif; // Pin image. ?>
	<div class="wprm-recipe-form-container">
		<label for="wprm-recipe-name"><?php esc_html_e( 'Name', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-name" placeholder="<?php esc_attr_e( 'Recipe Name', 'wp-recipe-maker' ); ?>" />
	</div>
	<div class="wprm-recipe-form-container wprm-recipe-summary-container">
		<label for="wprm-recipe-summary"><?php esc_html_e( 'Summary', 'wp-recipe-maker' ); ?></label>
		<textarea id="wprm-recipe-summary" class="wprm-rich-editor" rows="4"></textarea>
	</div>
	<div class='wprm-modal-hint'>
		<span class="wprm-modal-hint-header"><?php esc_html_e( 'Hint', 'wp-recipe-maker' ); ?></span>
		<span class="wprm-modal-hint-text"><?php esc_html_e( 'Select text to add styling or links.', 'wp-recipe-maker' ); ?></span>
	</div>
	<div class="wprm-recipe-form-container wprm-recipe-form-container-halfs">
		<label for="wprm-recipe-author-display"><?php esc_html_e( 'Author', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-recipe-author-display">
			<?php
			$options = array(
				'disabled' => __( "Don't show", 'wp-recipe-maker' ),
				'post_author' => __( 'Name of post author', 'wp-recipe-maker' ),
				'custom' => __( 'Custom author name', 'wp-recipe-maker' ),
			);
			$setting = WPRM_Settings::get( 'recipe_author_display_default' );

			echo '<option value="default" data-default="' . esc_attr( $setting ) . '">' . esc_html__( 'Default', 'wp-recipe-maker' ) . ' (' . esc_html( $options[ $setting ] ) . ')</option>';

			foreach ( $options as $option => $label ) {
				echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</option>';
			}
			?>
		</select>
	</div><div class="wprm-recipe-form-container wprm-recipe-form-container-halfs" id="wprm-recipe-author-name-container">
		<label for="wprm-recipe-author-name"><?php esc_html_e( 'Custom Author Name', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-author-name" data-default="<?php echo esc_attr( WPRM_Settings::get( 'recipe_author_custom_default' ) ); ?>" placeholder="<?php esc_attr_e( 'Author Name', 'wp-recipe-maker' ); ?>" />
	</div>
	<?php if ( WPRM_Addons::is_active( 'premium' ) ) : ?>
	<div class="wprm-recipe-form-container" id="wprm-recipe-author-link-container">
		<label for="wprm-recipe-author-link"><?php esc_html_e( 'Custom Author Link', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-author-link" placeholder="https://bootstrapped.ventures" />
	</div>
	<?php endif; // Author Link. ?>
	<div class="wprm-recipe-form-container">
		<label for="wprm-recipe-servings"><?php esc_html_e( 'Servings', 'wp-recipe-maker' ); ?></label>
		<input type="number" min="0" id="wprm-recipe-servings" placeholder="4" /> <input type="text" id="wprm-recipe-servings-unit" placeholder="<?php esc_attr_e( 'people', 'wp-recipe-maker' ); ?>" />
	</div>
	<?php if ( ! WPRM_Addons::is_active( 'premium' ) ) : ?>
	<div class="wprm-recipe-form-container">
		<label for="wprm-recipe-calories"><?php esc_html_e( 'Calories', 'wp-recipe-maker' ); ?></label>
		<input type="number" min="0" id="wprm-recipe-calories" placeholder="280" /> <?php esc_html_e( 'kcal', 'wp-recipe-maker' ); ?>
	</div>
	<?php endif; // Calories. ?>
	<div class="wprm-recipe-form-container wprm-recipe-form-container-thirds">
		<label for="wprm-recipe-prep-time"><?php esc_html_e( 'Prep Time', 'wp-recipe-maker' ); ?></label>
		<input type="number" id="wprm-recipe-prep-time" class="wprm-recipe-time" placeholder="10" min="0" /> <?php esc_html_e( 'minutes', 'wp-recipe-maker' ); ?>
	</div><div class="wprm-recipe-form-container wprm-recipe-form-container-thirds">
		<label for="wprm-recipe-cook-time"><?php esc_html_e( 'Cook Time', 'wp-recipe-maker' ); ?></label>
		<input type="number" id="wprm-recipe-cook-time" class="wprm-recipe-time" placeholder="20" min="0" /> <?php esc_html_e( 'minutes', 'wp-recipe-maker' ); ?>
	</div><div class="wprm-recipe-form-container wprm-recipe-form-container-thirds">
		<label for="wprm-recipe-total-time"><?php esc_html_e( 'Total Time', 'wp-recipe-maker' ); ?></label>
		<input type="number" id="wprm-recipe-total-time" class="wprm-recipe-time" placeholder="30" min="0" /> <?php esc_html_e( 'minutes', 'wp-recipe-maker' ); ?>
	</div>
	<div class="wprm-recipe-form-container">
		<label for="wprm-recipe-custom-time"><?php esc_html_e( 'Custom Time', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-custom-time-label" placeholder="<?php esc_attr_e( 'Resting Time', 'wp-recipe-maker' ); ?>" /> <input type="number" id="wprm-recipe-custom-time" class="wprm-recipe-time" placeholder="15" min="0" /> <?php esc_html_e( 'minutes', 'wp-recipe-maker' ); ?>
	</div>
	<div class='wprm-modal-hint'>
		<span class="wprm-modal-hint-header"><?php esc_html_e( 'Hint', 'wp-recipe-maker' ); ?></span>
		<span class="wprm-modal-hint-text"><?php esc_html_e( 'Your recipe template will automatically use hours.', 'wp-recipe-maker' ); ?></span>
	</div>
	<?php
	$taxonomies = WPRM_Taxonomies::get_taxonomies();

	foreach ( $taxonomies as $taxonomy => $options ) :
		$key = substr( $taxonomy, 5 );
	?><div class="wprm-recipe-form-container wprm-recipe-form-container-halfs">
		<label for="wprm-recipe-tag-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $options['singular_name'] ); ?></label>
		<select id="wprm-recipe-tag-<?php echo esc_attr( $key ); ?>" class="wprm-recipe-tags" data-key="<?php echo esc_attr( $key ); ?>" data-placeholder="<?php _e( 'Select from list or type and press ENTER', 'wp-recipe-maker' ); ?>" multiple>
			<?php
			$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
			) );

			foreach ( $terms as $term ) {
				echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
			}
			?>
		</select>
	</div><?php endforeach; // Taxonomies. ?>
	<div class='wprm-modal-hint'>
		<span class="wprm-modal-hint-header"><?php esc_html_e( 'Hint', 'wp-recipe-maker' ); ?></span>
		<span class="wprm-modal-hint-text"><?php esc_html_e( 'You can type in any term you want and press ENTER.', 'wp-recipe-maker' ); ?></span>
	</div>
</div>
