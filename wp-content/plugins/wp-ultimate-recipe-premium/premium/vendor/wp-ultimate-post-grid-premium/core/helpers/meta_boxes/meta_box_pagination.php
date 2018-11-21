<?php
// Grid should never be null. Construct just allows easy access to WPUPG_Grid functions in IDE.
if( is_null( $grid ) ) $grid = new WPUPG_Grid(0);
$premium_only = WPUltimatePostGrid::is_premium_active() ? '' : ' (' . __( 'Premium only', 'wp-ultimate-post-grid' ) . ')';

$pagination = $grid->pagination();
?>
<table id="wpupg_form_pagination" class="wpupg_form">
    <tbody class="wpupg_pagination_none">
    <tr>
        <td><label for="wpupg_pagination_type"><?php _e( 'Type', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <select name="wpupg_pagination_type" id="wpupg_pagination_type" class="wpupg-select2">
                <?php
                $pagination_type_options = array(
                    'none' => __( 'No pagination (all posts visible at once)', 'wp-ultimate-post-grid' ),
                    'pages' => __( 'Divide posts in pages', 'wp-ultimate-post-grid' ),
                    'infinite_load' => __( 'Infinite Scroll Load', 'wp-ultimate-post-grid' ) . $premium_only,
                    'load_more' => __( 'Use a "Load More" button', 'wp-ultimate-post-grid' ) . $premium_only,
                    'load_filter' => __( 'Load more posts on filter', 'wp-ultimate-post-grid' ) . $premium_only,
                    'load_more_filter' => __( 'Use a "Load More" button', 'wp-ultimate-post-grid' ) . ' & '. __( 'Load more posts on filter', 'wp-ultimate-post-grid' ) . $premium_only,
                );

                foreach( $pagination_type_options as $pagination_type => $pagination_type_name ) {
                    $selected = $pagination_type == $grid->pagination_type() ? ' selected="selected"' : '';
                    echo '<option value="' . esc_attr( $pagination_type ) . '"' . $selected . '>' . $pagination_type_name . '</option>';
                }
                ?>
            </select>
        </td>
        <td><?php _e( 'Type of pagination to be used for this grid.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    </tbody>
    <tbody class="wpupg_pagination_pages">
    <tr class="wpupg_divider">
        <td><label for="wpupg_pagination_pages_posts_per_page"><?php _e( 'Posts per page', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_pages_posts_per_page_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_pages_posts_per_page" id="wpupg_pagination_pages_posts_per_page" value="<?php echo $pagination['pages']['posts_per_page']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    </tbody>
    <tbody class="wpupg_pagination_infinite_load">
    <tr class="wpupg_divider">
        <td><label for="wpupg_pagination_infinite_load_initial_posts"><?php _e( 'Initial posts', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_infinite_load_initial_posts_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_infinite_load_initial_posts" id="wpupg_pagination_infinite_load_initial_posts" value="<?php echo $pagination['infinite_load']['initial_posts']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_pagination_infinite_load_posts_on_scroll"><?php _e( 'Posts loaded on scroll', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_infinite_load_posts_on_scroll_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_infinite_load_posts_on_scroll" id="wpupg_pagination_infinite_load_posts_on_scroll" value="<?php echo $pagination['infinite_load']['posts_on_scroll']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    </tbody>
    <tbody class="wpupg_pagination_load_more wpupg_pagination_load_more_filter">
    <tr class="wpupg_divider">
        <td><label for="wpupg_pagination_load_more_initial_posts"><?php _e( 'Initial posts', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_load_more_initial_posts_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_load_more_initial_posts" id="wpupg_pagination_load_more_initial_posts" value="<?php echo $pagination['load_more']['initial_posts']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_pagination_load_more_posts_on_click"><?php _e( 'Posts loaded on click', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_load_more_posts_on_click_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_load_more_posts_on_click" id="wpupg_pagination_load_more_posts_on_click" value="<?php echo $pagination['load_more']['posts_on_click']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_pagination_load_more_button_text"><?php _e( 'Button text', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="text" name="wpupg_pagination_load_more_button_text" id="wpupg_pagination_load_more_button_text" value="<?php echo $pagination['load_more']['button_text']; ?>" />
        </td>
        <td><?php _e( 'Text shown on the "Load More" button.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    </tbody>
    <tbody class="wpupg_pagination_load_filter">
    <tr class="wpupg_divider">
        <td><label for="wpupg_pagination_load_filter_initial_posts"><?php _e( 'Initial posts', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <div id="wpupg_pagination_load_filter_initial_posts_slider"></div>
        </td>
        <td><input type="text" name="wpupg_pagination_load_filter_initial_posts" id="wpupg_pagination_load_filter_initial_posts" value="<?php echo $pagination['load_filter']['initial_posts']; ?>" /><?php _e( 'posts', 'wp-ultimate-posts-grid' ); ?></td>
    </tr>
    </tbody>
</table>