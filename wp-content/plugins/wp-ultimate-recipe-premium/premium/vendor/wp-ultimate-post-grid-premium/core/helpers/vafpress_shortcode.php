<?php

class WPUPG_Vafpress_Shortcode {

    public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'vafpress_shortcode_init' ), 11 );
    }

    public function vafpress_shortcode_init()
    {
        require_once( WPUltimatePostGrid::get()->coreDir . '/helpers/vafpress/vafpress_shortcode_whitelist.php');
        require_once( WPUltimatePostGrid::get()->coreDir . '/helpers/vafpress/vafpress_shortcode_options.php');

        new VP_ShortcodeGenerator(array(
            'name'           => 'wpupg_shortcode_generator',
            'template'       => $shortcode_generator,
            'modal_title'    => 'WP Ultimate Post Grid ' . __( 'Shortcodes', 'wp-ultimate-post-grid' ),
            'button_title'   => 'WP Ultimate Post Grid',
            'types'          => WPUltimatePostGrid::option( 'shortcode_editor_post_types', array( 'post', 'page' ) ),
            'main_image'     => WPUltimatePostGrid::get()->coreUrl . '/img/icon_20.png',
            'sprite_image'   => WPUltimatePostGrid::get()->coreUrl . '/img/icon_sprite.png',
        ));
    }
}