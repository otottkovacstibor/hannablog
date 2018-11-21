<div class="updated wpupg_notice">
    <div class="wpupg_notice_dismiss">
        <a href="<?php echo esc_url( add_query_arg( array('wpupg_hide_new_notice' => wp_create_nonce( 'wpupg_hide_new_notice' ) ) ) ); ?>"> <?php _e( 'Hide this message', 'wp-ultimate-post-grid' ); ?></a>
    </div>
    <h3>Hi there!</h3>
    <p>It looks like you're new to <strong>WP Ultimate Post Grid</strong>. Please check out our <a href="<?php echo admin_url( 'edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_faq&sub=getting_started' ); ?>"><strong>Getting Started page</strong>!</a></p>
</div>