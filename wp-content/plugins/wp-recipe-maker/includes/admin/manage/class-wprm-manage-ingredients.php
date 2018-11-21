<?php
/**
 * Handle the manage ingredients page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 */

/**
 * Handle the manage ingredients page.
 *
 * @since      1.9.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/manage
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Manage_Ingredients {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.9.0
	 */
	public static function init() {
	}

	/**
	 * Get the data to display in the datatable.
	 *
	 * @since    1.9.0
	 * @param		 array $datatable Datatable request values.
	 */
	public static function get_datatable( $datatable ) {
		$data = array();

		$orderby_options = array(
			0 => 'id',
			1 => 'name',
			2 => 'count',
		);
		$orderby = isset( $orderby_options[ $datatable['orderby'] ] ) ? $orderby_options[ $datatable['orderby'] ] : $orderby_options[0];

		$args = array(
				'taxonomy' => 'wprm_ingredient',
				'hide_empty' => false,
				'orderby' => $orderby,
				'order' => $datatable['order'],
				'number' => $datatable['length'],
				'offset' => $datatable['start'],
				'search' => $datatable['search'],
		);

		$terms = get_terms( $args );

		foreach ( $terms as $term ) {
			// Bulk delete checkbox.
			$checkbox_disabled = $term->count > 0 ? ' disabled="disabled"' : '';
			$checkbox = '<input type="checkbox" class="wprm-manage-ingredients-bulk" id="wprm-manage-ingredients-bulk-' . esc_attr( $term->term_id ) . '" value="' . esc_attr( $term->term_id ) . '"' . $checkbox_disabled . '>';

			// Link to recipes.
			$recipes_url = add_query_arg( array(
				'sub' => 'recipes',
				'wprm_ingredient' => $term->term_id,
			), admin_url( 'admin.php?page=wprecipemaker' ) );

			// Ingredient link.
			$link = get_term_meta( $term->term_id, 'wprmp_ingredient_link', true );

			// Ingredient link options.
			$link_options = '';
			if ( $link ) {
				$link_nofollow = get_term_meta( $term->term_id, 'wprmp_ingredient_link_nofollow', true );
				$link_nofollow = in_array( $link_nofollow, array( 'default', 'nofollow', 'follow' ) ) ? $link_nofollow : 'default';

				$link_options = '<select class="wprm-manage-ingredients-link-nofollow" data-id="' . esc_attr( $term->term_id ) . '">';

				$default_nofollow = WPRM_Settings::get( 'ingredient_links_use_nofollow' ) ? __( 'Use Nofollow', 'wp-recipe-maker' ) : __( "Don't use Nofollow", 'wp-recipe-maker' );

				$options = array(
					'default' => __( 'Default', 'wp-recipe-maker' ) . ' (' . $default_nofollow . ')',
					'follow' => __( "Don't use Nofollow", 'wp-recipe-maker' ),
					'nofollow' => __( 'Use Nofollow', 'wp-recipe-maker' ),
				);

				foreach ( $options as $option => $label ) {
					$selected = $link_nofollow === $option ? ' selected="selected"' : '';
					$link_options .= '<option value="' . esc_attr( $option ) . '"' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
				}

				$link_options .= '</select>';
			}

			$data[] = array(
				$checkbox . ' <label for="wprm-manage-ingredients-bulk-' . esc_attr( $term->term_id ) . '">' . $term->term_id . '</label>',
				'<span id="wprm-manage-ingredients-name-' . esc_attr( $term->term_id ) . '">' . $term->name . '</span>',
				'<a href="' . $recipes_url . '">' . $term->count . '</a>',
				'<span id="wprm-manage-ingredients-link-' . esc_attr( $term->term_id ) . '"><a href="' . esc_url( $link ) . '" target="_blank">' . esc_url( $link ) . '</a></span>',
				$link_options,
				'<span class="dashicons dashicons-admin-tools wprm-icon wprm-manage-ingredients-actions" data-id="' . esc_attr( $term->term_id ) . '" data-count="' . esc_attr( $term->count ) . '"></span>',
			);
		}

		unset( $args['offset'] );
		unset( $args['number'] );
		$total = wp_count_terms( 'wprm_ingredient', $args );

		return array(
			'draw' => $datatable['draw'],
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
			'data' => $data,
		);
	}
}

WPRM_Manage_Ingredients::init();
