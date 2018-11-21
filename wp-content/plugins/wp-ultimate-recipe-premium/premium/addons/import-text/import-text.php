<?php

class WPURP_Import_Text extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-text' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_text_menu' ) );

        // Filters
        add_filter( 'custom_menu_order', array( $this, 'menu_order' ) );
    }

    public function assets() {
        $units = array_keys( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_alias_to_unit() );
        $units = array_merge(
            $units,
            explode( ';', WPUltimateRecipe::option( 'import_recipes_generic_units' ) )
        );

        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_text.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
            ),
            array(
                'name' => 'rangy-core',
                'file' => $this->addonPath . '/vendor/rangy-core.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'jquery',
                )
            ),
            array(
                'name' => 'rangy-css',
                'file' => $this->addonPath . '/vendor/rangy-cssclassapplier.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'rangy-core',
                ),
            ),
            array(
                'file' => $this->addonPath . '/js/import_text.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_text',
                'deps' => array(
                    'jquery',
                    'rangy-core',
                    'rangy-css',
                ),
                'data' => array(
                    'name' => 'wpurp_import_text',
                    'units' => $units,
                )
            )
        );
    }

    public function generic_units()
    {

    }

    public function menu_order( $menu_ord ) {
        global $submenu;

        $wpurp_menu = isset( $submenu['edit.php?post_type=recipe'] ) ? $submenu['edit.php?post_type=recipe'] : false;

        if( is_array( $wpurp_menu ) ) {
            foreach( $wpurp_menu as $index => $menu_item ) {
                $type = $menu_item[2];

                if( $type == 'edit.php?post_type=recipe' ) {
                    $edit = $menu_item;
                    unset( $wpurp_menu[$index] );
                } else if( $type == 'post-new.php?post_type=recipe' ) {
                    $add = $menu_item;
                    unset( $wpurp_menu[$index] );
                } else if ( $type == 'wpurp_import_text' ) {
                    $import = $menu_item;
                    unset( $wpurp_menu[$index] );
                }
            }

            if( isset( $edit ) && isset( $add ) && isset( $import ) ) {

                $reordered = array_merge(
                    array( $edit, $add, $import ),
                    $wpurp_menu
                );

                $submenu['edit.php?post_type=recipe'] = $reordered;
            }
        }

        return $menu_ord;
    }

    public function import_text_menu() {
        add_submenu_page( 'edit.php?post_type=recipe', __( 'Add New from Text', 'wp-ultimate-recipe' ), __( 'Add New from Text', 'wp-ultimate-recipe' ), 'edit_posts', 'wpurp_import_text', array( $this, 'import_text_page' ) );
    }

    public function import_text_page() {
        if ( !current_user_can( 'edit_posts' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_POST['submitrecipe'] ) ) {
            $this->import_text_process();
        }

        require( $this->addonDir . '/templates/import_text.php' );
    }

    public function import_text_process() {
        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) ) {

            wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' );

            if( isset ( $_POST['recipe_title'] ) ) {
                $title = $_POST['recipe_title'];
            }

            if( $title == '' ) {
                $title = __( 'Untitled', 'wp-ultimate-recipe' );
            }

            $post = array(
                'post_title' => $title,
                'post_type'	=> 'recipe',
                'post_status' => 'draft',
                'post_author' => get_current_user_id(),
            );

            $post_id = wp_insert_post($post);

            // Instead of wp_redirect to prevent "Headers already sent" issue
            echo'<script> window.location="'.admin_url( 'post.php?post=' . $post_id . '&action=edit' ).'"; </script> ';
            exit();
        }

        do_action( 'wp_insert_post', 'wp_insert_post' );
    }

}

WPUltimateRecipe::loaded_addon( 'import-text', new WPURP_Import_Text() );