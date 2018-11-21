<?php
/**
 * Template for the Edit Recipe tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<p>
	<?php esc_html_e( 'Select the recipe you would like to edit and click the "Edit" button.', 'wp-recipe-maker' ); ?>
</p>
<div class="wprm-shortcode-builder">
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-edit-recipe-id"><?php esc_html_e( 'Recipe', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-edit-recipe-id" class="wprm-recipes-dropdown">
			<option value="0"><?php esc_html_e( 'Select a recipe', 'wp-recipe-maker' ); ?></option>
		</select>
	</div>
</div>
