<?php
/**
 * Template for the taxonomies manage page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.10.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/manage
 */

$sub = isset( $_GET['sub'] ) ? sanitize_key( wp_unslash( $_GET['sub'] ) ) : ''; // Input var okay.
$taxonomy = substr( $sub, 9 );

?>

<table id="wprm-manage-taxonomy-<?php echo esc_attr( $taxonomy ); ?>" class="wprm-manage-datatable wprm-manage-taxonomies" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th data-width="50px">ID</th>
			<th>Name</th>
			<th data-width="50px">Recipes</th>
			<th data-sortable="false" data-width="20px">&nbsp;</th>
		</tr>
	</thead>
</table>
