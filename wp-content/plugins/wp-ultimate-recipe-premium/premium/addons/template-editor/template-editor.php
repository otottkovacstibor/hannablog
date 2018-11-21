<?php

class WPURP_Template_Editor extends WPURP_Premium_Addon {

    public function __construct( $name = 'template-editor' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'image_manager_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts_for_image_upload' ), -10 );

        // Filters
        add_filter( 'wpurp_output_recipe', array( $this, 'template_editor_preview' ) );
    }

    public function assets() {

        if( isset( $_GET['wpurp_template_editor_preview'] ) ) {
            WPUltimateRecipe::get()->helper( 'assets' )->add(
                array(
                    'file' => $this->addonUrl . '/css/preview.css',
                    'direct' => true,
                    'public' => true,
                ),
                array(
                    'file' => $this->addonUrl . '/js/preview.js',
                    'direct' => true,
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    ),
                    'data' => array(
                        'name' => 'wpurp_template_preview',
                        'ajax_url' => WPUltimateRecipe::get()->helper('ajax')->url(),
                        'nonce' => wp_create_nonce( 'template_editor_preview' ),
                        'recipe' => intval($_GET['wpurp_template_editor_preview']),
                    )
                )
            );

            $template = get_option( 'wpurp_custom_template_preview' );
            if( isset( $template->fonts ) && count( $template->fonts ) > 0 ) {

                WPUltimateRecipe::get()->helper( 'assets' )->add(
                    array(
                        'type' => 'css',
                        'file' => 'http://fonts.googleapis.com/css?family=' . implode( '|', $template->fonts ),
                        'direct' => true,
                        'public' => true,
                    )
                );
            }
        }

        WPUltimateRecipe::get()->helper( 'assets'  )->add(
            array(
                'file' => $this->addonPath . '/js/images.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_image_manager',
                'deps' => array(
                    'jquery',
                )
            )
        );
    }

    public function template_editor_preview( $output )
    {
        if( isset( $_GET['wpurp_template_editor_preview'] ) && 'recipe' == get_post_type( $_GET['wpurp_template_editor_preview'] ) )
        {
            $template = get_option( 'wpurp_custom_template_preview' );
            $output = $template->output_string( new WPURP_Recipe( $_GET['wpurp_template_editor_preview'] ) );
        }

        return $output;
    }

    public function editor_url()
    {
        return $this->addonUrl . '/templates/editor.php';
    }

    public function image_manager_menu()
    {
        add_submenu_page( null, 'WP Ultimate Recipe ' . __( 'Image Manager', 'wp-ultimate-recipe' ), __( 'Image Manager', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_image_manager', array( $this, 'image_manager_menu_page' ) );
    }

    public function image_manager_menu_page()
    {
        include( $this->addonDir . '/templates/images.php' );
    }

    public function scripts_for_image_upload()
    {
        $screen = get_current_screen();
        if( $screen->id == 'recipe_page_wpurp_image_manager' && function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
    }
}

WPUltimateRecipe::loaded_addon( 'template-editor', new WPURP_Template_Editor() );