<?php
/**
 * Template for the Print Recipe tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<p>
	<?php printf( esc_html__( 'The %s shortcode can be used to add a link for printing a recipe.', 'wp-recipe-maker' ), esc_html( '[wprm-recipe-print]' ) ); ?>
</p>
<h3><?php esc_html_e( 'Shortcode Examples' ); ?></h3>
<p>
	[wprm-recipe-print]<br />
	<em><?php esc_html_e( 'Add a link that prints the first recipe found on the page with "Print Recipe" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<p>
	[wprm-recipe-print id="123"]<br />
	<em><?php esc_html_e( 'Add a link that prints the recipe with ID 123 with "Print Recipe" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<p>
	[wprm-recipe-print id="123" text="Print my new Recipe"]<br />
	<em><?php esc_html_e( 'Add a link that prints the recipe with ID 123 with "Print my new Recipe" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<h3><?php esc_html_e( 'Shortcode Builder' ); ?></h3>
<div class="wprm-shortcode-builder">
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-recipe-print-id"><?php esc_html_e( 'Recipe', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-recipe-print-id" class="wprm-recipes-dropdown-with-first">
			<option value="0"><?php esc_html_e( 'First recipe on page', 'wp-recipe-maker' ); ?></option>
		</select>
	</div>
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-recipe-print-text"><?php esc_html_e( 'Text', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-print-text" placeholder="<?php esc_attr_e( 'Print Recipe', 'wp-recipe-maker' ); ?>" />
		<span class="wprm-shortcode-builder-helper"><?php esc_html_e( 'Leave blank to use default', 'wp-recipe-maker' ); ?></span>
	</div>
</div>
