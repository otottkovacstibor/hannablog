<?php

class WPUPG_Notices {

    public function __construct()
    {
        add_action( 'admin_init',       array( $this, 'hide_notice' ) );
        add_action( 'admin_notices',    array( $this, 'admin_notices' ) );
    }

    public function admin_notices()
    {
        // New to WP Ultimate Post Grid
        if( current_user_can( 'edit_posts' ) && get_user_meta( get_current_user_id(), '_wpupg_hide_new_notice', true ) == '' ) {
            include( WPUltimatePostGrid::get()->coreDir . '/static/getting_started_notice.php' );
        }

        if( $notices = get_option( 'wpupg_deferred_admin_notices' ) ) {
            foreach( $notices as $notice ) {
                echo '<div class="updated"><p>'.$notice.'</p></div>';
            }

            delete_option('wpupg_deferred_admin_notices');
        }
    }

    public function add_admin_notice( $notice )
    {
        $notices = get_option( 'wpupg_deferred_admin_notices', array() );
        $notices[] = $notice;
        update_option( 'wpupg_deferred_admin_notices', $notices );
    }

    function hide_notice()
    {
        if ( isset( $_GET['wpupg_hide_new_notice'] ) ) {
            check_admin_referer( 'wpupg_hide_new_notice', 'wpupg_hide_new_notice' );
            update_user_meta( get_current_user_id(), '_wpupg_hide_new_notice', get_option( WPUltimatePostGrid::get()->pluginName . '_version') );
        }
    }
}