<?php
/**
 * Handle the manage taxonomies pages.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.10.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 */

/**
 * Handle the manage taxonomies pages.
 *
 * @since      1.10.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Manage_Taxonomies {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.10.0
	 */
	public static function init() {
		add_filter( 'wprm_manage_tabs', array( __CLASS__, 'manage_tabs' ) );
	}

	/**
	 * Add tags to the manage tabs.
	 *
	 * @since    1.10.0
	 * @param 	 array $tabs Tags tabs.
	 */
	public static function manage_tabs( $tabs ) {
		$taxonomies = WPRM_Taxonomies::get_taxonomies();

		foreach ( $taxonomies as $taxonomy => $labels ) {
			$uid = 'taxonomy_' . substr( $taxonomy, 5 );
			$tabs[ $uid ] = $labels['name'];
		}
		return $tabs;
	}

	/**
	 * Get the data to display in the datatable.
	 *
	 * @since    1.10.0
	 * @param    array $datatable Datatable request values.
	 * @param    mixed $taxonomy  Taxonomy to get the datatable for.
	 */
	public static function get_datatable( $datatable, $taxonomy ) {
		$data = array();

		$orderby_options = array(
			0 => 'id',
			1 => 'name',
			2 => 'count',
		);
		$orderby = isset( $orderby_options[ $datatable['orderby'] ] ) ? $orderby_options[ $datatable['orderby'] ] : $orderby_options[0];

		$args = array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
				'orderby' => $orderby,
				'order' => $datatable['order'],
				'number' => $datatable['length'],
				'offset' => $datatable['start'],
				'search' => $datatable['search'],
		);

		$terms = get_terms( $args );

		foreach ( $terms as $term ) {
			$recipes_url = add_query_arg( array(
				'sub' => 'recipes',
				$taxonomy => $term->term_id,
			), admin_url( 'admin.php?page=wprecipemaker' ) );

			$data[] = array(
				$term->term_id,
				'<span id="wprm-manage-taxonomies-name-' . esc_attr( $term->term_id ) . '">' . $term->name . '</span>',
				'<a href="' . $recipes_url . '">' . $term->count . '</a>',
				'<span class="dashicons dashicons-admin-tools wprm-icon wprm-manage-taxonomies-actions" data-id="' . esc_attr( $term->term_id ) . '" data-count="' . esc_attr( $term->count ) . '"></span>',
			);
		}

		unset( $args['offset'] );
		unset( $args['number'] );
		$total = wp_count_terms( $taxonomy, $args );

		return array(
			'draw' => $datatable['draw'],
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
			'data' => $data,
		);
	}
}

WPRM_Manage_Taxonomies::init();
