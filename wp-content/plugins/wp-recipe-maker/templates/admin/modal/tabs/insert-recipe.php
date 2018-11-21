<?php
/**
 * Template for the Insert Recipe tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.12.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<p>
	<?php esc_html_e( 'Select the existing recipe you would like to insert.', 'wp-recipe-maker' ); ?>
</p>
<div class="wprm-shortcode-builder">
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-insert-recipe-id"><?php esc_html_e( 'Recipe', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-insert-recipe-id" class="wprm-recipes-dropdown">
			<option value="0"><?php esc_html_e( 'Select a recipe', 'wp-recipe-maker' ); ?></option>
		</select>
	</div>
</div>
