<?php
// Grid should never be null. Construct just allows easy access to WPUPG_Grid functions in IDE.
if( is_null( $grid ) ) $grid = new WPUPG_Grid(0);
?>

<table id="wpupg_form_grid" class="wpupg_form">
    <tr>
        <td><label for="wpupg_link_type"><?php _e( 'Links', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_link_type" id="wpupg_link_type" class="wpupg-select2">
                <?php
                $link_type_options = array(
                    '_self' => __( 'Open in same tab', 'wp-ultimate-post-grid' ),
                    '_blank' => __( 'Open in new tab', 'wp-ultimate-post-grid' ),
                    'none' => __( "Don't use links", 'wp-ultimate-post-grid' ),
                );

                foreach( $link_type_options as $link_type => $link_type_name ) {
                    $selected = $link_type == $grid->link_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $link_type ) . '"' . $selected . '>' . $link_type_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Options for links surrounding the grid items.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_link_target"><?php _e( 'Link to', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_link_target" id="wpupg_link_target" class="wpupg-select2">
                <?php
                $link_target_options = array(
                    'post' => __( 'Post', 'wp-ultimate-post-grid' ),
                    'image' => __( 'Featured Image', 'wp-ultimate-post-grid' ),
                );

                foreach( $link_target_options as $link_target => $link_target_name ) {
                    $selected = $link_target == $grid->link_target() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $link_target ) . '"' . $selected . '>' . $link_target_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Options for links surrounding the grid items.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr class="wpupg_divider">
        <td><label for="wpupg_template"><?php _e( 'Template', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_template" id="wpupg_template" class="wpupg-select2">
                <?php
                $templates = WPUltimatePostGrid::addon( 'custom-templates' )->get_mapping();
                $templates = apply_filters( 'wpupg_meta_box_grid_templates', $templates );

                foreach ( $templates as $index => $template ) {
                    $selected = $index == $grid->template_id() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $index ) . '"' . $selected . '>' . $template . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Template to be used for grid items.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_layout_mode"><?php _e( 'Layout Mode', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_layout_mode" id="wpupg_layout_mode" class="wpupg-select2">
                <?php
                $layout_mode_options = array(
                    'masonry' => __( 'Masonry (Pinterest like)', 'wp-ultimate-post-grid' ),
                    'fitRows' => __( 'Items in rows', 'wp-ultimate-post-grid' ),
                );

                foreach( $layout_mode_options as $layout_mode => $layout_mode_name ) {
                    $selected = $layout_mode == $grid->layout_mode() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $layout_mode ) . '"' . $selected . '>' . $layout_mode_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Options for links surrounding the grid items.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr class="wpupg_masonry">
        <td><label for="wpupg_centered"><?php _e( 'Center Grid', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="checkbox" name="wpupg_centered" id="wpupg_centered" <?php if( $grid->centered() ) echo 'checked="true" '?>/>
        </td>
        <td><?php _e( 'Center the entire grid.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
</table>