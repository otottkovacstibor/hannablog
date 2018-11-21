<?php

class WPUPG_Assets {

    private $assets = array();

    public function __construct()
    {
        add_action( 'init', array( $this, 'default_assets' ) );
        add_action( 'init', array( $this, 'default_public_assets' ) );
        add_action( 'admin_init', array( $this, 'default_admin_assets' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'color_picker' ) );
    }

    public function default_assets()
    {
        $this->add(
            array(
                'file' => '/css/filter.min.css',
                'public' => true,
                'admin' => true,
            ),
            array(
                'file' => '/css/pagination.min.css',
                'public' => true,
                'admin' => true,
            )
        );
    }

    public function default_public_assets()
    {
        if( !is_admin() ) {
            $base_layout = WPUltimatePostGrid::option( 'grid_template_force_style', '0' ) == '1' ? 'layout_base_forced.min.css' : 'layout_base.min.css';

            $this->add(
                array(
                    'file' => '/css/grid.min.css',
                    'public' => true
                ),
                array(
                    'file' => '/css/' . $base_layout,
                    'public' => true
                ),
                array(
                    'name' => 'isotope',
                    'file' => '/vendor/isotope/isotope.pkgd.min.js',
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    )
                ),
                array(
                    'name' => 'imagesloaded',
                    'file' => '/vendor/imagesloaded/imagesloaded.pkgd.min.js',
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    )
                ),
                array(
                    'file' => '/js/grid.min.js',
                    'name' => 'wpupg_grid',
                    'public' => true,
                    'deps' => array(
                        'jquery',
                        'isotope',
                        'imagesloaded',
                    ),
                    'data' => array(
                        'name' => 'wpupg_public',
                        'ajax_url' => WPUltimatePostGrid::get()->helper('ajax')->url(),
                        'animationSpeed' => WPUltimatePostGrid::option( 'grid_animation_speed', '0.8' ) . 's',
                        'nonce' => wp_create_nonce( 'wpupg_grid' ),
                        'rtl' => is_rtl(),
                        'dropdown_hide_search' => WPUltimatePostGrid::option( 'filters_dropdown_hide_search', '0' ) == '1' ? true : false,
                    ),
                )
            );
        }
    }

    public function default_admin_assets()
    {
        $this->add(
            array(
                'file' => '/css/admin.min.css',
                'admin' => true,
            ),
            array(
                'file' => '/css/admin_form.min.css',
                'admin' => true,
            ),
            array(
                'file' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.min.css',
                'direct' => true,
                'admin' => true,
                'page' => 'grid_form',
            ),
            array(
                'file' => WPUltimatePostGrid::get()->coreUrl . '/vendor/select2/css/select2.min.css',
                'direct' => true,
                'admin' => true,
                'page' => 'grid_form',
            ),
            array(
                'name' => 'select2wpupg',
                'file' => '/vendor/select2/js/select2.min.js',
                'admin' => true,
                'page' => 'grid_form',
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'file' => '/vendor/nouislider/jquery.nouislider.min.css',
                'public' => true,
                'admin' => true,
                'page' => 'grid_form',
            ),
            array(
                'name' => 'nouislider',
                'file' => '/vendor/nouislider/jquery.nouislider.all.min.js',
                'public' => true,
                'admin' => true,
                'page' => 'grid_form',
                'deps' => array(
                    'jquery',
                )
            ),
            array(
                'file' => '/js/admin_post.min.js',
                'admin' => true,
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'file' => '/js/admin_form.min.js',
                'admin' => true,
                'page' => 'grid_form',
                'deps' => array(
                    'jquery',
                    'jquery-ui-datepicker',
                    'select2wpupg',
                    'nouislider',
                    'wp-color-picker',
                ),
                'data' => array(
                    'name' => 'wpupg_admin',
                    'ajax_url' => WPUltimatePostGrid::get()->helper('ajax')->url(),
                ),
            )
        );
    }

    public function color_picker()
    {
        wp_enqueue_style( 'wp-color-picker' );
    }

    public function add()
    {
        $assets = func_get_args();

        foreach( $assets as $asset )
        {
            if( isset( $asset['file'] ) ) {

                if( !isset( $asset['type'] ) ) {
                    $asset['type'] = pathinfo( $asset['file'], PATHINFO_EXTENSION );
                }

                if( !isset( $asset['priority'] ) ) {
                    $asset['priority'] = 10;
                }

                // Set a URL and DIR variable
                if( isset( $asset['direct'] ) && $asset['direct'] ) {
                    $asset['url'] = $asset['file'];
                    $asset['dir'] = $asset['file'];
                } else {
                    $base_url = WPUltimatePostGrid::get()->coreUrl;
                    $base_dir = WPUltimatePostGrid::get()->coreDir;

                    if( isset( $asset['premium'] ) && $asset['premium'] ) {
                        $base_url = WPUltimatePostGridPremium::get()->premiumUrl;
                        $base_dir = WPUltimatePostGridPremium::get()->premiumDir;
                    }

                    $asset['url'] = $base_url . $asset['file'];
                    $asset['dir'] = $base_dir . $asset['file'];
                }

                $this->assets[] = $asset;
            }
        }
    }

    public function enqueue( $hook = '' )
    {
        $assets = $this->assets;

        // Check which assets to enqueue
        $css_to_enqueue = array();
        $js_to_enqueue = array();

        foreach( $assets as $asset )
        {
            // Check if asset is intended for admin or public side
            if( !is_admin() && ( !isset( $asset['public'] ) || !$asset['public'] ) ) continue;
            if( is_admin() && ( !isset( $asset['admin'] ) || !$asset['admin'] ) ) continue;

            // Check if we're on a certain page
            if( isset( $asset['page'] ) ) {
                $screen = get_current_screen();
                
                switch ( strtolower( $asset['page'] ) ) {

                    case 'grid_posts':
                        if( $hook != 'edit.php' || $screen->post_type != WPUPG_POST_TYPE ) continue 2; // Switch is consider a loop statement for continue
                        break;

                    case 'grid_form':
                        if( !in_array( $hook, array( 'post.php', 'post-new.php' ) ) || $screen->post_type != WPUPG_POST_TYPE ) continue 2;
                        break;

                    default:
                        if( $hook != strtolower( $asset['page'] ) ) continue 2;
                        break;
                }
            }

            // Check for shortcode
            if( isset( $asset['shortcode'] ) ) {
                if( !$this->check_for_shortcode( $asset['shortcode'] ) ) continue;
            }

            // Check if setting equals value
            if( isset( $asset['setting'] ) && count( $asset['setting'] ) == 2 ) {
                if( WPUltimatePostGrid::option( $asset['setting'][0], $asset['setting'][1] ) != $asset['setting'][1] ) continue;
            }

            // Check if setting does not equal value
            if( isset( $asset['setting_inverse'] ) && count( $asset['setting_inverse'] ) == 2 ) {
                if( WPUltimatePostGrid::option( $asset['setting_inverse'][0], $asset['setting_inverse'][1] ) == $asset['setting_inverse'][1] ) continue;
            }

            // If we've made it here, this asset should be included
            switch( strtolower( $asset['type'] ) ) {

                case 'css':
                    $css_to_enqueue[] = $asset;
                    break;
                case 'js':
                    $js_to_enqueue[] = $asset;
                    break;
            }
        }

        // We've got the assets we need, enqueue them
        if( count( $css_to_enqueue ) > 0 )   $this->enqueue_css( $css_to_enqueue );
        if( count( $js_to_enqueue ) > 0 )    $this->enqueue_js( $js_to_enqueue );
    }

    private function enqueue_css( $assets )
    {
        $i = 1;
        foreach( $assets as $asset ) {
            $version = WPUltimatePostGrid::option( 'assets_use_cache', '1' ) == '1' ? WPUPG_VERSION : time();

            wp_enqueue_style( 'wpupg_style' . $i, $asset['url'], false, $version, 'all' );
            $i++;
        }
    }

    private function enqueue_js( $assets )
    {
        $i = 1;
        foreach( $assets as $asset ) {
            $name = isset( $asset['name'] ) ? $asset['name'] : 'wpupg_script' . $i;
            $deps = isset( $asset['deps'] ) ? $asset['deps'] : '';

            $version = WPUltimatePostGrid::option( 'assets_use_cache', '1' ) == '1' ? WPUPG_VERSION : time();

            wp_enqueue_script( $name, $asset['url'], $deps, $version, true );

            if( isset( $asset['data'] ) && isset( $asset['data']['name'] ) ) {
                $data_name = $asset['data']['name'];
                unset( $asset['data']['name'] );

                wp_localize_script( $name, $data_name, $asset['data'] );
            }

            $i++;
        }
    }

    /**
     * Check if any of the shortcodes is used in post
     */
    public function check_for_shortcode( $shortcodes ) {
        if( !is_single() ) return apply_filters( 'wpupg_check_for_shortcode', true, $shortcodes ); // TODO Needs better solution

        global $post;

        if( function_exists( 'has_shortcode' ) ) {

            // Multiple shortcodes passed, if one shortcode is in the post, return true
            if( is_array( $shortcodes ) ) {
                $shortcode_used = false;

                foreach( $shortcodes as $shortcode ) {
                    if( isset( $post->post_content ) && has_shortcode( $post->post_content, $shortcode ) ) {
                        $shortcode_used = true;
                    }
                }

                return apply_filters( 'wpupg_check_for_shortcode', $shortcode_used, $shortcodes );
            }

            // Only one shortcode passed, true if that one is in the post
            if( isset( $post->post_content ) && has_shortcode( $post->post_content, $shortcodes ) ) {
                return apply_filters( 'wpupg_check_for_shortcode', true, $shortcodes );
            }

            return apply_filters( 'wpupg_check_for_shortcode', false, $shortcodes );
        }

        return apply_filters( 'wpupg_check_for_shortcode', true, $shortcodes ); // In older versions of WP just enqueue everything
    }
}