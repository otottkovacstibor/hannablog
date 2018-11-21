<?php

class WPUPG_Vafpress_Menu {

    public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'vafpress_menu_init' ), 11 );
    }

    public function vafpress_menu_init()
    {
        require_once( WPUltimatePostGrid::get()->coreDir . '/helpers/vafpress/vafpress_menu_whitelist.php');
        require_once( WPUltimatePostGrid::get()->coreDir . '/helpers/vafpress/vafpress_menu_options.php');

        new VP_Option(array(
            'is_dev_mode'           => false,
            'option_key'            => 'wpupg_option',
            'page_slug'             => 'wpupg_admin',
            'template'              => $admin_menu,
            'menu_page'             => 'edit.php?post_type=' . WPUPG_POST_TYPE,
            'use_auto_group_naming' => true,
            'use_exim_menu'         => true,
            'minimum_role'          => 'manage_options',
            'layout'                => 'fluid',
            'page_title'            => __( 'Settings', 'wp-ultimate-post-grid' ),
            'menu_label'            => __( 'Settings', 'wp-ultimate-post-grid' ),
        ));
    }
}