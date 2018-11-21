<?php
/**
 * Template for the Jump to Video tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.2.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<p>
	<?php printf( esc_html__( 'The %s shortcode can be used to add a link that jumps your visitors to a recipe on the page.', 'wp-recipe-maker' ), esc_html( '[wprm-recipe-jump-video]' ) ); ?>
</p>
<h3><?php esc_html_e( 'Shortcode Examples' ); ?></h3>
<p>
	[wprm-recipe-jump-video]<br />
	<em><?php esc_html_e( 'Add a link that jumps to the first recipe video found on the page with "Jump to Video" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<p>
	[wprm-recipe-jump-video id="123"]<br />
	<em><?php esc_html_e( 'Add a link that jumps to the recipe video with ID 123 with "Jump to Video" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<p>
	[wprm-recipe-jump-video id="123" text="View Video"]<br />
	<em><?php esc_html_e( 'Add a link that jumps to the recipe video with ID 123 with "View Video" as the link text.', 'wp-recipe-maker' ); ?></em>
</p>
<h3><?php esc_html_e( 'Shortcode Builder' ); ?></h3>
<div class="wprm-shortcode-builder">
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-recipe-jump-video-id"><?php esc_html_e( 'Recipe', 'wp-recipe-maker' ); ?></label>
		<select id="wprm-recipe-jump-video-id" class="wprm-recipes-dropdown-with-first">
			<option value="0"><?php esc_html_e( 'First recipe on page', 'wp-recipe-maker' ); ?></option>
		</select>
	</div>
	<div class="wprm-shortcode-builder-container">
		<label for="wprm-recipe-jump-video-text"><?php esc_html_e( 'Text', 'wp-recipe-maker' ); ?></label>
		<input type="text" id="wprm-recipe-jump-video-text" placeholder="<?php esc_attr_e( 'Jump to Video', 'wp-recipe-maker' ); ?>" />
		<span class="wprm-shortcode-builder-helper"><?php esc_html_e( 'Leave blank to use default', 'wp-recipe-maker' ); ?></span>
	</div>
</div>
