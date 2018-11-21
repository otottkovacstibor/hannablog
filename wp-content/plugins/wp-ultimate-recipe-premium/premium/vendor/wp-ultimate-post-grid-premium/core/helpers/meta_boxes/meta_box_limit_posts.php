<?php
// Grid should never be null. Construct just allows easy access to WPUPG_Grid functions in IDE.
if( is_null( $grid ) ) $grid = new WPUPG_Grid(0);
?>

<table id="wpupg_form_limit_posts" class="wpupg_form">
    <thead>
    <tr>
        <td>Field/Term</td>
        <td>Values</td>
        <td>Type</td>
        <td>&nbsp;</td>
    </tr>
    </thead>
    <tbody id="wpupg_rule_placeholder">
    <tr id="wpupg_rule_0" data-rule="0">
        <td>
            <select name="wpupg_limit_posts_rule[0][field]" id="wpupg_limit_posts_rule_field_0" class="wpupg_rule_field">
                <optgroup label="<?php _e( 'General', 'wp-ultimate-post-grid' ); ?>">
                    <?php
                    $rule_field_options = array(
                        'wpupg_general|author' => __( 'Author', 'wp-ultimate-post-grid' ),
                        'wpupg_general|date' => __( 'Date', 'wp-ultimate-post-grid' ),
                        'wpupg_general|id' => __( 'Post ID', 'wp-ultimate-post-grid' ),
                    );

                    foreach( $rule_field_options as $rule_field => $rule_field_name ) {
                        $selected = '';//$rule_field == $grid->filter_type() ? ' selected="selected"' : '';
                        echo '<option value="' . esc_attr( $rule_field ) . '"' . $selected . '>' . $rule_field_name . '</option>';
                    }
                    ?>
                </optgroup>
                <?php
                $post_types = get_post_types( '', 'objects' );

                unset( $post_types[WPUPG_POST_TYPE] );
                unset( $post_types['revision'] );
                unset( $post_types['nav_menu_item'] );

                $rule_fields = array();
                $rule_field_terms = array();

                foreach( $post_types as $post_type => $options ) {
                    $taxonomies = get_object_taxonomies( $post_type, 'objects' );

                    if( count( $taxonomies ) > 0 ) {
                        echo '<optgroup label="' . __( 'Taxonomies', 'wp-ultimate-post-grid' ) . ' (' . $options->labels->name . ')">';

                        foreach( $taxonomies as $taxonomy => $tax_options ) {
                            $selected = '';//in_array( $taxonomy, $grid->filter_taxonomies() ) ? ' selected="selected"' : '';
                            echo '<option value="' . esc_attr( $post_type ) . '|' . esc_attr( $taxonomy ) . '"' . $selected . '>' . $tax_options->labels->name . '</option>';

                            $rule_fields[] = $post_type . '|' . $taxonomy;
                            if( !isset( $rule_field_terms[$taxonomy] ) ) {
                                $rule_field_terms[$taxonomy] = get_terms( $taxonomy );
                            }
                        }
                        echo '</optgroup>';
                    }
                }
                ?>
            </select>
        </td>
        <td>
            <div class="rule_container rule_container_wpupg_general_author">
                <select name="wpupg_limit_posts_rule[0][values_wpupg_general_author][]" id="wpupg_limit_posts_rule_values_wpupg_general_author_0" multiple>
                    <?php
                    $args = array(
                        'who' => 'authors',
                        'fields' => array(
                            'ID', 'display_name'
                        )
                    );
                    $authors = get_users( $args );

                    foreach( $authors as $author ) {
                        $selected = '';//$rule_type == $grid->filter_type() ? ' selected="selected"' : '';
                        echo '<option value="' . esc_attr( $author->ID ) . '"' . $selected . '>' . $author->ID . ' - ' . $author->display_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="rule_container rule_container_wpupg_general_date">
                <select name="wpupg_limit_posts_rule[0][values_wpupg_general_date_condition]" id="wpupg_limit_posts_rule_values_wpupg_general_date_condition_0">
                    <?php
                    $date_condition_options = array(
                        'before' => __( 'Before', 'wp-ultimate-post-grid' ),
                        'is' => __( 'Is', 'wp-ultimate-post-grid' ),
                        'after' => __( 'After', 'wp-ultimate-post-grid' ),
                    );

                    foreach( $date_condition_options as $date_condition => $date_condition_name ) {
                        $selected = '';//$rule_field == $grid->filter_type() ? ' selected="selected"' : '';
                        echo '<option value="' . esc_attr( $date_condition ) . '"' . $selected . '>' . $date_condition_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="rule_container rule_container_wpupg_general_date">
                <input type="text" name="wpupg_limit_posts_rule[0][values_wpupg_general_date_date]" class="wpupg-date" id="wpupg_limit_posts_rule_values_wpupg_general_date_date_0" placeholder="01/28/1988"/>
                <input type="hidden" name="wpupg_limit_posts_rule[0][values_wpupg_general_date]" id="wpupg_limit_posts_rule_values_wpupg_general_date_0"/>
            </div>
            <div class="rule_container rule_container_wpupg_general_id">
                <input type="text" name="wpupg_limit_posts_rule[0][values_wpupg_general_id]" id="wpupg_limit_posts_rule_values_wpupg_general_id_0" placeholder="<?php _e( 'Separate post IDs with a semicolon', 'wp-ultimate-post-grid' ); ?> (1;28;88)"/>
            </div>
            <?php
            foreach( $rule_fields as $rule_field ) {
                $rule_field_parts = explode( '|', $rule_field );
                $rule_field_name = $rule_field_parts[0] . '_' . $rule_field_parts[1];

                echo '<div class="rule_container rule_container_' . $rule_field_name . '">';
                echo '<select name="wpupg_limit_posts_rule[0][values_' . $rule_field_name . '][]" id="wpupg_limit_posts_rule_values_' . $rule_field_name . '_0" multiple>';

                $terms = $rule_field_terms[$rule_field_parts[1]];
                foreach( $terms as $term ) {
                    $selected = '';//$rule_type == $grid->filter_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $term->term_id ) . '"' . $selected . '>' . $term->name . '</option>';
                }

                echo '</select>';
                echo '</div>';
            }
            ?>
        </td>
        <td>
            <select name="wpupg_limit_posts_rule[0][type]" id="wpupg_limit_posts_rule_type_0">
                <?php
                $rule_type_options = array(
                    'restrict' => __( 'Only these posts', 'wp-ultimate-post-grid' ),
                    'exclude' => __( 'Exclude these posts', 'wp-ultimate-post-grid' ),
                );

                foreach( $rule_type_options as $rule_type => $rule_type_name ) {
                    $selected = '';//$rule_type == $grid->filter_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $rule_type ) . '"' . $selected . '>' . $rule_type_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td>
            <img src="<?php echo WPUltimatePostGrid::get()->coreUrl . '/img/trash.png'; ?>" class="wpupg_rule_delete" title="<?php _e( 'Delete Rule', 'wp-ultimate-post-grid' ); ?>"/>
        </td>
    </tr>
    </tbody>
    <tbody id="wpupg_rules">
    <?php
    $limit_rules = $grid->limit_rules();

    $rule_id = 1;
    foreach( $limit_rules as $rule ) {
        if( $rule['post_type'] == 'wpupg_general' || in_array( $rule_field, $rule_fields ) ) {
        ?>
        <tr id="wpupg_rule_<?php echo $rule_id; ?>" data-rule="<?php echo $rule_id; ?>">
            <td>
                <select name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][field]" id="wpupg_limit_posts_rule_field_<?php echo $rule_id; ?>" class="wpupg_rule_field">
                    <optgroup label="<?php _e( 'General', 'wp-ultimate-post-grid' ); ?>">
                        <?php
                        foreach( $rule_field_options as $rule_field => $rule_field_name ) {
                            $selected = $rule_field == $rule['field'] ? ' selected="selected"' : '';
                            echo '<option value="' . esc_attr( $rule_field ) . '"' . $selected . '>' . $rule_field_name . '</option>';
                        }
                        ?>
                    </optgroup>
                    <?php
                    foreach( $post_types as $post_type => $options ) {
                        $taxonomies = get_object_taxonomies( $post_type, 'objects' );

                        if( count( $taxonomies ) > 0 ) {
                            echo '<optgroup label="' . __( 'Taxonomies', 'wp-ultimate-post-grid' ) . ' (' . $options->labels->name . ')">';

                            foreach( $taxonomies as $taxonomy => $tax_options ) {
                                $selected = $post_type == $rule['post_type'] && $taxonomy == $rule['taxonomy'] ? ' selected="selected"' : '';
                                echo '<option value="' . esc_attr( $post_type ) . '|' . esc_attr( $taxonomy ) . '"' . $selected . '>' . $tax_options->labels->name . '</option>';
                            }
                            echo '</optgroup>';
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <div class="rule_container rule_container_wpupg_general_author">
                    <select name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][values_wpupg_general_author][]" id="wpupg_limit_posts_rule_values_wpupg_general_author_<?php echo $rule_id; ?>" multiple>
                        <?php
                        foreach( $authors as $author ) {
                            $selected = $rule['post_type'] == 'wpupg_general' && $rule['taxonomy'] == 'author' && in_array( $author->ID, $rule['values'] ) ? ' selected="selected"' : '';
                            echo '<option value="' . esc_attr( $author->ID ) . '"' . $selected . '>' . $author->ID . ' - ' . $author->display_name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                $rule_date_condition = $rule['post_type'] == 'wpupg_general' && $rule['taxonomy'] == 'date' && isset( $rule['values'][0] ) ? $rule['values'][0] : '';
                $rule_date_date = $rule['post_type'] == 'wpupg_general' && $rule['taxonomy'] == 'date' && isset( $rule['values'][1] ) ? $rule['values'][1] : '';
                $rule_date = $rule_date_condition . ';' . $rule_date_date;
                ?>
                <div class="rule_container rule_container_wpupg_general_date">
                    <select name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][values_wpupg_general_date_condition]" id="wpupg_limit_posts_rule_values_wpupg_general_date_condition_<?php echo $rule_id; ?>">
                        <?php
                        foreach( $date_condition_options as $date_condition => $date_condition_name ) {
                            $selected = $date_condition == $rule_date_condition ? ' selected="selected"' : '';
                            echo '<option value="' . esc_attr( $date_condition ) . '"' . $selected . '>' . $date_condition_name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="rule_container rule_container_wpupg_general_date">
                    <input type="text" name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][values_wpupg_general_date_date]" class="wpupg-date" id="wpupg_limit_posts_rule_values_wpupg_general_date_date_<?php echo $rule_id; ?>" value="<?php echo $rule_date_date; ?>" placeholder="01/28/1988"/>
                    <input type="hidden" name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][values_wpupg_general_date]" id="wpupg_limit_posts_rule_values_wpupg_general_date_<?php echo $rule_id; ?>" value="<?php echo $rule_date; ?>"/>
                </div>
                <div class="rule_container rule_container_wpupg_general_id">
                    <?php $id_value = $rule['post_type'] == 'wpupg_general' && $rule['taxonomy'] == 'id' ? implode( ';', $rule['values'] ) : ''; ?>
                    <input type="text" name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][values_wpupg_general_id]" id="wpupg_limit_posts_rule_values_wpupg_general_id_<?php echo $rule_id; ?>" value="<?php echo $id_value; ?>" placeholder="<?php _e( 'Separate post IDs with a semicolon', 'wp-ultimate-post-grid' ); ?> (1;28;88)"/>
                </div>
                <?php
                foreach( $rule_fields as $rule_field ) {
                    $rule_field_parts = explode( '|', $rule_field );
                    $rule_field_name = $rule_field_parts[0] . '_' . $rule_field_parts[1];

                    echo '<div class="rule_container rule_container_' . $rule_field_name . '">';
                    echo '<select name="wpupg_limit_posts_rule[' . $rule_id . '][values_' . $rule_field_name . '][]" id="wpupg_limit_posts_rule_values_' . $rule_field_name . '_' . $rule_id . '" multiple>';

                    $terms = $rule_field_terms[$rule_field_parts[1]];
                    foreach( $terms as $term ) {
                        $selected = $rule['post_type'] == $rule_field_parts[0] && $rule['taxonomy'] == $rule_field_parts[1] && in_array( $term->term_id, $rule['values'] ) ? ' selected="selected"' : '';
                        echo '<option value="' . esc_attr( $term->term_id ) . '"' . $selected . '>' . $term->name . '</option>';
                    }

                    echo '</select>';
                    echo '</div>';
                }
                ?>
            </td>
            <td>
                <select name="wpupg_limit_posts_rule[<?php echo $rule_id; ?>][type]" id="wpupg_limit_posts_rule_type_<?php echo $rule_id; ?>">
                    <?php
                    foreach( $rule_type_options as $rule_type => $rule_type_name ) {
                        $selected = $rule_type == $rule['type'] ? ' selected="selected"' : '';
                        echo '<option value="' . esc_attr( $rule_type ) . '"' . $selected . '>' . $rule_type_name . '</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                <img src="<?php echo WPUltimatePostGrid::get()->coreUrl . '/img/trash.png'; ?>" class="wpupg_rule_delete" title="<?php _e( 'Delete Rule', 'wp-ultimate-post-grid' ); ?>"/>
            </td>
        </tr>
    <?php
            $rule_id++;
        }
    } ?>
    </tbody>
</table>
<a href="#" id="wpupg_add_rule"><?php _e( 'Add Rule', 'wp-ultimate-post-grid' ); ?></a>