<?php
/**
 * Template for the Ingredients & Instructions tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<div class="wprm-recipe-ingredients-instructions-form">
	<div class="wprm-recipe-ingredients-form">
		<table class="wprm-recipe-ingredients-container">
			<thead>
				<th><span class="dashicons dashicons-sort"></span></th>
				<th><?php esc_html_e( 'Amount', 'wp-recipe-maker' ); ?></th>
				<th><?php esc_html_e( 'Unit', 'wp-recipe-maker' ); ?></th>
				<th><?php esc_html_e( 'Name', 'wp-recipe-maker' ); ?></th>
				<th><?php esc_html_e( 'Notes', 'wp-recipe-maker' ); ?></th>
				<th>&nbsp;</th>
			</thead>
			<tbody class="wprm-recipe-ingredients-placeholder">
				<tr class="wprm-recipe-ingredient-group">
					<td><span class="dashicons dashicons-menu wprm-recipe-ingredients-instructions-sort"></span></td>
					<td colspan="4"><input type="text" class="wprm-recipe-ingredient-group-name" placeholder="<?php esc_attr_e( 'Ingredient Group', 'wp-recipe-maker' );?>" /></td>
					<td><span class="dashicons dashicons-trash wprm-recipe-ingredients-instructions-delete"></span></td>
				</tr>
				<tr class="wprm-recipe-ingredient">
					<td><span class="dashicons dashicons-menu wprm-recipe-ingredients-instructions-sort"></span></td>
					<td><input type="text" class="wprm-recipe-ingredient-amount" placeholder="1" /></td>
					<td><input type="text" class="wprm-recipe-ingredient-unit" placeholder="<?php esc_attr_e( 'tbsp', 'wp-recipe-maker' );?>" /></td>
					<td><input type="text" class="wprm-recipe-ingredient-name" placeholder="<?php esc_attr_e( 'olive oil', 'wp-recipe-maker' );?>" /></td>
					<td><input type="text" class="wprm-recipe-ingredient-notes" placeholder="<?php esc_attr_e( 'extra virgin', 'wp-recipe-maker' );?>" /></td>
					<td><span class="dashicons dashicons-trash wprm-recipe-ingredients-instructions-delete"></span></td>
				</tr>
			</tbody>
			<tbody class="wprm-recipe-ingredients">
			</tbody>
		</table>
		<div class="wprm-recipe-ingredients-actions">
			<button type="button" class="button wprm-recipe-ingredients-add"><?php esc_html_e( 'Add Ingredient', 'wp-recipe-maker' ); ?></button>
			<button type="button" class="button wprm-recipe-ingredients-add-group"><?php esc_html_e( 'Add Ingredient Group', 'wp-recipe-maker' ); ?></button>
		</div>
	</div>

	<div class="wprm-recipe-instructions-form">
		<table class="wprm-recipe-instructions-container">
			<thead>
				<th><span class="dashicons dashicons-sort"></span></th>
				<th><?php esc_html_e( 'Instruction', 'wp-recipe-maker' ); ?></th>
				<th><?php esc_html_e( 'Image', 'wp-recipe-maker' ); ?></th>
				<th>&nbsp;</th>
			</thead>
			<tbody class="wprm-recipe-instructions-placeholder">
				<tr class="wprm-recipe-instruction-group">
					<td><span class="dashicons dashicons-menu wprm-recipe-ingredients-instructions-sort"></span></td>
					<td colspan="2"><input type="text" class="wprm-recipe-instruction-group-name" placeholder="<?php esc_attr_e( 'Instruction Group', 'wp-recipe-maker' );?>" /></td>
					<td><span class="dashicons dashicons-trash wprm-recipe-ingredients-instructions-delete"></span></td>
				</tr>
				<tr class="wprm-recipe-instruction">
					<td><span class="dashicons dashicons-menu wprm-recipe-ingredients-instructions-sort"></span></td>
					<td>
						<textarea class="wprm-recipe-instruction-text" rows="3"></textarea>
					</td>
					<td class="wprm-recipe-image-container">
						<div class="wprm-recipe-image-preview"></div>
						<button type="button" class="button wprm-recipe-image-add" tabindex="-1"><?php esc_html_e( 'Add Image', 'wp-recipe-maker' ); ?></button>
						<button type="button" class="button wprm-recipe-image-remove hidden" tabindex="-1"><?php esc_html_e( 'Remove Image', 'wp-recipe-maker' ); ?></button>
						<input type="hidden" class="wprm-recipe-instruction-image" />
					</td>
					<td><span class="dashicons dashicons-trash wprm-recipe-ingredients-instructions-delete"></span></td>
				</tr>
			</tbody>
			<tbody class="wprm-recipe-instructions">
			</tbody>
		</table>
		<div class="wprm-recipe-instructions-actions">
			<button type="button" class="button wprm-recipe-instructions-add"><?php esc_html_e( 'Add Instruction', 'wp-recipe-maker' ); ?></button>
			<button type="button" class="button wprm-recipe-instructions-add-group"><?php esc_html_e( 'Add Instruction Group', 'wp-recipe-maker' ); ?></button>
		</div>
	</div>
</div>
<div class='wprm-modal-hint'>
	<span class="wprm-modal-hint-header"><?php esc_html_e( 'Hint', 'wp-recipe-maker' ); ?></span>
	<span class="wprm-modal-hint-text"><?php esc_html_e( 'Use the TAB key to easily move from field to field and add ingredients/instructions without having to click the button.', 'wp-recipe-maker' ); ?></span>
</div>
<div class='wprm-modal-hint'>
	<span class="wprm-modal-hint-header"><?php esc_html_e( 'Hint', 'wp-recipe-maker' ); ?></span>
	<span class="wprm-modal-hint-text"><?php esc_html_e( 'Select text to add styling or links.', 'wp-recipe-maker' ); ?></span>
</div>