<?php
/**
 * Template for the ingredient manage page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/manage
 */

if ( ! function_exists( 'get_term_meta' ) ) {
	 echo '<br/><strong>Warning:</strong> You need at least WordPress 4.4 to manage the ingredients.';
}

?>

<div class="wprm-manage-header">
	<button type="button" class="button button-primary wprm-manage-ingredients-bulk-delete"><?php esc_html_e( 'Delete selected Ingredients', 'wp-recipe-maker' ); ?></button>
</div>

<table id="wprm-manage-ingredients" class="wprm-manage-datatable" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th data-width="50px">ID</th>
			<th>Name</th>
			<th data-width="50px">Recipes</th>
			<th data-sortable="false">Link</th>
			<th data-sortable="false">Link Options</th>
			<th data-sortable="false" data-width="20px">&nbsp;</th>
		</tr>
	</thead>
</table>
