<?php
/**
 * Template for the recipe manage page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/manage
 */

?>
<div class="wprm-manage-header wprm-manage-recipes-filters">
<?php
$count = array_sum( (array) wp_count_posts( WPRM_POST_TYPE ) );
if ( 0 === $count ) : ?>
<p style="font-weight:bold">Just getting started with WP Recipe Maker? Make sure to <a href="https://help.bootstrapped.ventures/category/4-getting-started" target="_blank">check out our documentation</a>!</p>
<?php endif; ?>
<button type="button" class="button button-primary wprm-modal-button" title="<?php esc_attr_e( 'Create Recipe', 'wp-recipe-maker' ); ?>"><?php esc_html_e( 'Create Recipe', 'wp-recipe-maker' ); ?></button><br/><br/>
<?php
esc_html_e( 'Filter', 'wp-recipe-maker' );
$taxonomies = WPRM_Taxonomies::get_taxonomies( true );

foreach ( $taxonomies as $taxonomy => $labels ) {
	$terms = get_terms(array(
		'taxonomy' => $taxonomy,
		'fields' => 'id=>name',
	));

	if ( count( $terms ) > 0 ) {
		echo '<select id="wprm-manage-recipes-filter-' . esc_attr( $taxonomy ) . '" class="wprm-manage-recipes-filter" data-taxonomy="' . esc_attr( $taxonomy ) . '">';
		echo '<option value="0">' . esc_html__( 'All', 'wp-recipe-maker' ) . ' ' . esc_html( $labels['name'] ) . '</option>';
		foreach ( $terms as $term_id => $term_name ) {
			echo '<option value="' . esc_attr( $term_id ) . '">' . esc_html( $term_name ) . '</option>';
		}
		echo '</select>';
	}
}
?>
</div>

<table id="wprm-manage-recipes" class="wprm-manage-datatable" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th data-width="50px">ID</th>
			<th>Date</th>
			<th>Name</th>
			<th data-sortable="false">Parent Post</th>
			<th>Rating</th>
			<th data-sortable="false" data-width="20px">SEO</th>
			<th data-sortable="false" data-width="20px">&nbsp;</th>
		</tr>
	</thead>
</table>
