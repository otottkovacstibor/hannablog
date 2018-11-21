<?php
$premium_only = WPUltimatePostGrid::is_premium_active() ? '' : ' (' . __( 'Premium only', 'wp-ultimate-post-grid' ) . ')';
?>

<input type="hidden" name="wpupg_nonce" value="<?php echo wp_create_nonce( 'grid' ); ?>" />
<table id="wpupg_form_post" class="wpupg_form">
    <tr>
        <td><label for="wpupg_custom_link"><?php _e( 'Custom Link', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="text" name="wpupg_custom_link" id="wpupg_custom_link" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpupg_custom_link', true ) ); ?>"/>
        </td>
        <td><?php _e( 'Override the default link for this post.', 'wp-ultimate-post-grid' ); ?></td>
    </tr>
    <tr>
        <td><label for="wpupg_custom_image"><?php _e( 'Custom Image URL', 'wp-ultimate-post-grid' ); ?></label></td>
        <td>
            <input type="text" name="wpupg_custom_image" id="wpupg_custom_image" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpupg_custom_image', true ) ); ?>"/>
            <input type="hidden" name="wpupg_custom_image_id" id="wpupg_custom_image_id" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpupg_custom_image_id', true ) ); ?>"/>
        </td>
        <td><input type="button" id="wpupg_add_custom_image" class="button" value="<?php _e( 'Choose from Library', 'wp-ultimate-post-grid' )?>"></td>
    </tr>
</table>