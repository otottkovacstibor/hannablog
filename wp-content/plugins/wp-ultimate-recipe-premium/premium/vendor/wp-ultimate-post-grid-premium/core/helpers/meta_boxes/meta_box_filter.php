<?php
// Grid should never be null. Construct just allows easy access to WPUPG_Grid functions in IDE.
if( is_null( $grid ) ) $grid = new WPUPG_Grid(0);
$premium_only = WPUltimatePostGrid::is_premium_active() ? '' : ' (' . __( 'Premium only', 'wp-ultimate-post-grid' ) . ')';
?>
<div id="wpupg_no_taxonomies"><?php _e( 'There are no taxonomies associated with this post type', 'wp-ultimate-post-grid' ); ?></div>
<table id="wpupg_form_filter" class="wpupg_form">
    <tr class="wpupg_no_filter">
        <td><label for="wpupg_filter_type"><?php _e( 'Type', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_filter_type" id="wpupg_filter_type" class="wpupg-select2">
                <?php
                $filter_type_options = array(
                    'none' => __( 'No Filter', 'wp-ultimate-post-grid' ),
                    'isotope' => __( 'Isotope', 'wp-ultimate-post-grid' ),
                    'dropdown' => __( 'Dropdown', 'wp-ultimate-post-grid' ) . $premium_only,
                );

                foreach( $filter_type_options as $filter_type => $filter_type_name ) {
                    $selected = $filter_type == $grid->filter_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $filter_type ) . '"' . $selected . '>' . $filter_type_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Type of filter to be used for this grid.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr class="wpupg_divider">
        <td><label for="wpupg_filter_taxonomy_post"><?php _e( 'Taxonomy', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <?php
            $post_types = get_post_types( '', 'objects' );

            unset( $post_types[WPUPG_POST_TYPE] );
            unset( $post_types['revision'] );
            unset( $post_types['nav_menu_item'] );

            foreach( $post_types as $post_type => $options ) {
                $taxonomies = get_object_taxonomies( $post_type, 'objects' );

                if( count( $taxonomies ) > 0 ) {
                    $multiple = WPUltimatePostGrid::is_premium_active() ? ' multiple' : '';
                    echo '<div id="wpupg_filter_taxonomy_' . $post_type . '_container" class="wpupg_filter_taxonomy_container">';
                    echo '<select name="wpupg_filter_taxonomy_' . $post_type . '[]" id="wpupg_filter_taxonomy_' . $post_type . '" class="wpupg_filter_taxonomy wpupg-select2"' . $multiple . '>';

                    foreach( $taxonomies as $taxonomy => $tax_options ) {
                        if( !in_array( $taxonomy, $grid->filter_taxonomies() ) ) {
                            echo '<option value="' . esc_attr( $taxonomy ) . '">' . $tax_options->labels->name . '</option>';
                        }
                    }

                    // Selected taxonomies in correct order
                    foreach( $grid->filter_taxonomies() as $selected_taxonomy ) {
                        if( isset( $taxonomies[$selected_taxonomy] ) ) {
                            echo '<option value="' . esc_attr( $selected_taxonomy ) . '" selected="selected">' . $taxonomies[$selected_taxonomy]->labels->name . '</option>';
                        }
                    }

                    echo '</select>';
                    echo '</div>';
                }
            }
            ?>
        </td>
        <td><?php _e( 'Taxonomy to be used for filtering the grid.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_filter_match_parents"><?php _e( 'Selecting parent terms matches children', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_filter_match_parents" id="wpupg_filter_match_parents" <?php if( $grid->filter_match_parents() ) echo 'checked="true" '?>/>
        </td>
        <td><?php _e( 'Selecting a parent term will also match posts with one of its child terms.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_filter_inverse"><?php _e( 'Inverse Selection', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_filter_inverse" id="wpupg_filter_inverse" <?php if( $grid->filter_inverse() ) echo 'checked="true" '?>/>
        </td>
        <td><?php _e( 'Items that match the selection will be hidden.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr class="wpupg_divider">
        <td><label for="wpupg_filter_multiselect"><?php _e( 'Multi-select', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_filter_multiselect" id="wpupg_filter_multiselect" <?php if( $grid->filter_multiselect() ) echo 'checked="true" '?>/>
            <?php echo $premium_only; ?>
        </td>
        <td><?php _e( 'Allow users to select multiple terms.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tbody class="wpupg_multiselect">
    <tr>
        <td><label for="wpupg_filter_multiselect_type"><?php _e( 'Multi-select Behaviour', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_filter_multiselect_type" id="wpupg_filter_multiselect_type" class="wpupg-select2">
                <?php
                $filter_multiselect_type_options = array(
                    'match_all' => __( 'Only posts that match all selected terms', 'wp-ultimate-post-grid' ),
                    'match_one' => __( 'All posts that match any of the selected terms', 'wp-ultimate-post-grid' ),
                );

                foreach( $filter_multiselect_type_options as $filter_multiselect_type => $filter_multiselect_type_name ) {
                    $selected = $filter_multiselect_type == $grid->filter_multiselect_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $filter_multiselect_type ) . '"' . $selected . '>' . $filter_multiselect_type_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Type of filtering when selecting multiple terms.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    </tbody>
    <tr class="wpupg_divider">
        <td><label for="wpupg_filter_limit"><?php _e( 'Limit Terms', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_filter_limit" id="wpupg_filter_limit" <?php if( $grid->filter_limit() ) echo 'checked="true" '?>/>
        </td>
        <td><?php _e( 'Only show/hide selected terms in the filter.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tbody class="wpupg_limit_terms">
    <tr>
        <td><label for="wpupg_filter_limit_exclude"><?php _e( 'Exclude Selected Terms', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_filter_limit_exclude" id="wpupg_filter_limit_exclude" <?php if( $grid->filter_limit_exclude() ) echo 'checked="true" '?>/>
        </td>
        <td>&nbsp;</td>
    </tr>
    <?php
    $post_types = get_post_types( '', 'objects' );

    unset( $post_types[WPUPG_POST_TYPE] );
    unset( $post_types['revision'] );
    unset( $post_types['nav_menu_item'] );

    $taxonomies_in_form = array();
    foreach( $post_types as $post_type => $options ) {
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        $limit_terms = $grid->filter_limit_terms();

        foreach( $taxonomies as $taxonomy => $tax_options ) {
            if( !in_array( $taxonomy, $taxonomies_in_form ) ) {
                $taxonomies_in_form[] = $taxonomy;

                echo '<tr id="wpupg_filter_terms_taxonomy_' . $taxonomy . '" class="wpupg_filter_terms_taxonomy">';
                echo '<td><label for="wpupg_filter_limit_terms_' . $taxonomy . '">' . $tax_options->labels->name . '</label></td>';
                echo '<td>';
                echo '<select name="wpupg_filter_limit_terms_' . $taxonomy . '[]" id="wpupg_filter_limit_terms_' . $taxonomy . '" class="wpupg-select2" multiple>';
                foreach( get_terms( $taxonomy ) as $term ) {
                    $selected = isset( $limit_terms[$taxonomy] ) && in_array( $term->term_id, $limit_terms[$taxonomy] ) ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $term->term_id ) . '"' . $selected . '>' . $term->name . '</option>';
                }
                echo '</td>';
                echo '<td>&nbsp;</td>';
                echo '</tr>';
            }
        }
    }
    ?>
    </tbody>
</table>