<?php
/**
 * Template for the Import from Text tab in the modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal/tabs
 */

?>

<div class="wprm-recipe-form wprm-recipe-import-text-form">
	<div class="import-text-buttons">
		<button type="button" class="button wprm-button wprm-button-import-text-reset" disabled="disabled"><?php esc_html_e( 'Start Over', 'wp-recipe-maker' ); ?></button>
		<button type="button" class="button wprm-button wprm-button-import-text-clear" disabled="disabled"><?php esc_html_e( 'Clear', 'wp-recipe-maker' ); ?></button>
		<button type="button" class="button button-primary wprm-button wprm-button-import-text-next" disabled="disabled"><?php esc_html_e( 'Next', 'wp-recipe-maker' ); ?></button>
	</div>
	<div id="import-text-step-input" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Paste the recipe you want to import in the textarea below to get started.', 'wp-recipe-maker' ); ?>
		</div>
		<div class="import-text-input">
			<textarea id="import-text-input-recipe" rows="20"></textarea>
		</div>
	</div>
	<div id="import-text-step-name" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Highlight this part of the recipe:', 'wp-recipe-maker' ); ?> <strong><?php esc_html_e( 'Name', 'wp-recipe-maker' ); ?></strong>
		</div>
	</div>
	<div id="import-text-step-summary" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Highlight this part of the recipe:', 'wp-recipe-maker' ); ?> <strong><?php esc_html_e( 'Summary', 'wp-recipe-maker' ); ?></strong>
		</div>
	</div>
	<div id="import-text-step-ingredients" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Highlight this part of the recipe:', 'wp-recipe-maker' ); ?> <strong><?php esc_html_e( 'Ingredients', 'wp-recipe-maker' ); ?></strong>
		</div>
	</div>
	<div id="import-text-step-ingredient-groups" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Check any ingredient group headers in this list:', 'wp-recipe-maker' ); ?>
		</div>
		<div id="import-text-ingredient-groups" class="import-text-input import-text-input-groups">
		</div>
		<div class="import-text-group-warning">
			<strong><?php esc_html_e( 'Warning', 'wp-recipe-maker' ); ?></strong>: <?php esc_html_e( 'Make sure to only select the ingredient group headers indicating a new ingredient group, not all ingredients.', 'wp-recipe-maker' ); ?>
		</div>
	</div>
	<div id="import-text-step-instructions" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Highlight this part of the recipe:', 'wp-recipe-maker' ); ?> <strong><?php esc_html_e( 'Instructions', 'wp-recipe-maker' ); ?></strong>
		</div>
	</div>
	<div id="import-text-step-instruction-groups" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Check any instruction group headers in this list:', 'wp-recipe-maker' ); ?>
		</div>
		<div id="import-text-instruction-groups" class="import-text-input import-text-input-groups">
		</div>
		<div class="import-text-group-warning">
			<strong><?php esc_html_e( 'Warning', 'wp-recipe-maker' ); ?></strong>: <?php esc_html_e( 'Make sure to only select the instruction group headers indicating a new instruction group, not all instructions.', 'wp-recipe-maker' ); ?>
		</div>
	</div>
	<div id="import-text-step-notes" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Highlight this part of the recipe:', 'wp-recipe-maker' ); ?> <strong><?php esc_html_e( 'Notes', 'wp-recipe-maker' ); ?></strong>
		</div>
	</div>
	<div id="import-text-step-waiting" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Please wait for text import to finish.', 'wp-recipe-maker' ); ?>
		</div>
	</div>
	<div id="import-text-step-finished" class="import-text-step">
		<div class="import-text-description">
			<?php esc_html_e( 'Finished the text import.', 'wp-recipe-maker' ); ?>
		</div>
	</div>
	<div id="import-text-highlight-sandbox">
	</div>
</div>
