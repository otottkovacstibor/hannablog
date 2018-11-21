<?php

class WPUPG_Faq {

    public function __construct()
    {
        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'faq_menu' ), 20 );
    }

    public function assets()
    {
        WPUltimatePostGrid::get()->helper( 'assets' )->add(
            array(
                'file' => '/css/faq.css',
                'admin' => true,
                'page' => WPUPG_POST_TYPE . '_page_wpupg_faq',
            )
        );
    }

    public function faq_menu() {
        add_submenu_page( 'edit.php?post_type=' . WPUPG_POST_TYPE, 'WP Ultimate Post Grid ' . __( 'FAQ', 'wp-ultimate-post-grid' ), __( 'FAQ', 'wp-ultimate-post-grid' ), 'edit_posts', 'wpupg_faq', array( $this, 'faq_page' ) );
    }

    public function faq_page() {
        if ( !current_user_can( 'edit_posts' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        // Hide the new user notice
        update_user_meta( get_current_user_id(), '_wpupg_hide_new_notice', get_option( WPUltimatePostGrid::get()->pluginName . '_version') );
        include( WPUltimatePostGrid::get()->coreDir . '/static/faq.php' );
    }
}