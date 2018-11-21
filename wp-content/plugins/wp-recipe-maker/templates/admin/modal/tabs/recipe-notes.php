<?php
/**
 * Template for the Ingredient Notes tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<div class="wprm-recipe-form wprm-recipe-notes-form">
	<div class="wprm-recipe-form-container wprm-recipe-video-container">
		<label for="wprm-recipe-video-id"><?php esc_html_e( 'Video', 'wp-recipe-maker' ); ?></label>
		<button type="button" class="button wprm-recipe-video-add"><?php esc_html_e( 'Upload Video', 'wp-recipe-maker' ); ?></button>
		<button type="button" class="button wprm-recipe-video-embed"><?php esc_html_e( 'Embed Video', 'wp-recipe-maker' ); ?></button>
		<button type="button" class="button wprm-recipe-video-edit hidden"><?php esc_html_e( 'Edit Video', 'wp-recipe-maker' ); ?></button>
		<button type="button" class="button wprm-recipe-video-remove hidden"><?php esc_html_e( 'Remove Video', 'wp-recipe-maker' ); ?></button>
		<input type="hidden" id="wprm-recipe-video-id" />
		<textarea rows="3" id="wprm-recipe-video-embed" class="hidden" placeholder="<?php esc_attr_e( 'Paste in the video embed code, URL or shortcode', 'wp-recipe-maker' ); ?>"></textarea>
		<div class="wprm-recipe-video-preview"></div>
	</div>
	<div class="wprm-recipe-form-container">
		<label for="wprm-recipe-notes"><?php esc_html_e( 'Notes', 'wp-recipe-maker' ); ?></label>
		<?php
		$editor_settings = array(
			'editor_height' => 300,
		);
		wp_editor( '', 'wprm_recipe_notes', $editor_settings );
		?>
	</div>
</div>
