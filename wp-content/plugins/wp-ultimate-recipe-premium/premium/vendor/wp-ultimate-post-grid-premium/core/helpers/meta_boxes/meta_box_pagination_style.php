<?php
// Grid should never be null. Construct just allows easy access to WPUPG_Grid functions in IDE.
if( is_null( $grid ) ) $grid = new WPUPG_Grid(0);

$pagination_style = $grid->pagination_style();
?>

<table id="wpupg_form_pagination_style" class="wpupg_form">
    <tr>
        <td>&nbsp;</td>
        <td><span class="wpupg_label_prefix"><?php _e( 'Background Color', 'wp-ultimate-post-grid' ); ?></span></td>
        <td><span class="wpupg_label_prefix"><?php _e( 'Text Color', 'wp-ultimate-post-grid' ); ?></span></td>
        <td><span class="wpupg_label_prefix"><?php _e( 'Border Color', 'wp-ultimate-post-grid' ); ?></span></td>
    </tr>
    <tr>
        <td><span class="wpupg_label_prefix"><?php _e( 'Default', 'wp-ultimate-post-grid' ); ?></span></td>
        <td>
            <input type="color" id="wpupg_pagination_style_background_color" name="wpupg_pagination_style_background_color" value="<?php echo $pagination_style['background_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_text_color" name="wpupg_pagination_style_text_color" value="<?php echo $pagination_style['text_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_border_color" name="wpupg_pagination_style_border_color" value="<?php echo $pagination_style['border_color']; ?>">
        </td>
    </tr>
    <tr>
        <td><span class="wpupg_label_prefix"><?php _e( 'Active', 'wp-ultimate-post-grid' ); ?></span></td>
        <td>
            <input type="color" id="wpupg_pagination_style_background_active_color" name="wpupg_pagination_style_background_active_color" value="<?php echo $pagination_style['background_active_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_text_active_color" name="wpupg_pagination_style_text_active_color" value="<?php echo $pagination_style['text_active_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_border_active_color" name="wpupg_pagination_style_border_active_color" value="<?php echo $pagination_style['border_active_color']; ?>">
        </td>
    </tr>
    <tr>
        <td><span class="wpupg_label_prefix"><?php _e( 'Hover', 'wp-ultimate-post-grid' ); ?></span></td>
        <td>
            <input type="color" id="wpupg_pagination_style_background_hover_color" name="wpupg_pagination_style_background_hover_color" value="<?php echo $pagination_style['background_hover_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_text_hover_color" name="wpupg_pagination_style_text_hover_color" value="<?php echo $pagination_style['text_hover_color']; ?>">
        </td>
        <td>
            <input type="color" id="wpupg_pagination_style_border_hover_color" name="wpupg_pagination_style_border_hover_color" value="<?php echo $pagination_style['border_hover_color']; ?>">
        </td>
    </tr>
</table>
<table id="wpupg_form_pagination_style_2" class="wpupg_form">
    <tr class="wpupg_divider">
        <td><span class="wpupg_label_prefix"><?php _e( 'Border', 'wp-ultimate-post-grid' ); ?></span><label for="wpupg_pagination_style_border_width"><?php _e( 'Width', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_style_border_width_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_style_border_width" id="wpupg_pagination_style_border_width" value="<?php echo $pagination_style['border_width']; ?>" />px</td>
    </tr>
    <tr class="wpupg_divider">
        <td><span class="wpupg_label_prefix"><?php _e( 'Margin', 'wp-ultimate-post-grid' ); ?></span><label for="wpupg_pagination_style_margin_vertical"><?php _e( 'Vertical', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_style_margin_vertical_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_style_margin_vertical" id="wpupg_pagination_style_margin_vertical" value="<?php echo $pagination_style['margin_vertical']; ?>" />px</td>
    </tr>
    <tr>
        <td><label for="wpupg_pagination_style_margin_horizontal"><?php _e( 'Horizontal', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_style_margin_horizontal_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_style_margin_horizontal" id="wpupg_pagination_style_margin_horizontal" value="<?php echo $pagination_style['margin_horizontal']; ?>" />px</td>
    </tr>
    <tr>
        <td><span class="wpupg_label_prefix"><?php _e( 'Padding', 'wp-ultimate-post-grid' ); ?></span><label for="wpupg_pagination_style_padding_vertical"><?php _e( 'Vertical', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_style_padding_vertical_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_style_padding_vertical" id="wpupg_pagination_style_padding_vertical" value="<?php echo $pagination_style['padding_vertical']; ?>" />px</td>
    </tr>
    <tr>
        <td><label for="wpupg_pagination_style_padding_horizontal"><?php _e( 'Horizontal', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_style_padding_horizontal_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_style_padding_horizontal" id="wpupg_pagination_style_padding_horizontal" value="<?php echo $pagination_style['padding_horizontal']; ?>" />px</td>
    </tr>
    <tr class="wpupg_divider">
        <td><label for="wpupg_pagination_style_alignment"><?php _e( 'Alignment', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_pagination_style_alignment" id="wpupg_pagination_style_alignment" class="wpupg-select2">
                <?php
                $alignment_options = array(
                    'left' => __( 'Left', 'wp-ultimate-post-grid' ),
                    'center' => __( 'Center', 'wp-ultimate-post-grid' ),
                    'right' => __( 'Right', 'wp-ultimate-post-grid' ),
                );

                foreach( $alignment_options as $alignment_option => $alignment_option_name ) {
                    $selected = $alignment_option == $pagination_style['alignment'] ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $alignment_option ) . '"' . $selected . '>' . $alignment_option_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'How to align the filters.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
</table>

<div id="wpupg_filter_preview_pagination_style_pages" class="wpupg_filter_preview wpupg_filter_preview_pagination_style">
    <div class="wpupg-pagination-term">1</div>
    <div class="wpupg-pagination-term active">2</div>
    <div class="wpupg-pagination-term">3</div>
    <div class="wpupg-pagination-term">4</div>
</div>

<div id="wpupg_filter_preview_pagination_style_load_more" class="wpupg_filter_preview wpupg_filter_preview_pagination_style">
    <div class="wpupg-pagination-term"><?php _e( 'Load More', 'wp-ultimate-post-grid' ); ?></div>
</div>