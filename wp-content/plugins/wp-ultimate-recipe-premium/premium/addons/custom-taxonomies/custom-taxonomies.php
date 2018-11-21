<?php

class WPURP_Custom_Taxonomies extends WPURP_Premium_Addon {

    private $ignoreTaxonomies;

    public function __construct( $name = 'custom-taxonomies' ) {
        parent::__construct( $name );

        // Recipe taxonomies that users should not be able to delete
        $this->ignoreTaxonomies = array('rating', 'post_tag', 'category');
        $this->protectedTaxonomies = array('ingredient');

        //Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'admin_init', array( $this, 'custom_taxonomies_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
        add_action( 'admin_action_delete_taxonomy', array( $this, 'delete_taxonomy' ) );
        add_action( 'admin_action_add_taxonomy', array( $this, 'add_taxonomy' ) );
        add_action( 'admin_action_reset_taxonomies', array( $this, 'reset_taxonomies' ) );

        // Custom Taxonomies Metadata
        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );
        $taxonomies['category'] = array();
        $taxonomies['post_tag'] = array();

        foreach( $taxonomies as $taxonomy => $options ) {
            new WPURP_Taxonomy_MetaData( $taxonomy, array(
                'wpurp_link' => array(
                    'label'       => __( 'Link', 'wp-ultimate-recipe' ),
                    'desc'        => __( 'Send your visitors to a specific link when clicking on an term.', 'wp-ultimate-recipe' ),
                    'placeholder' => 'http://www.example.com',
                ),
            ) );
        }
    }

    public function assets()
    {
        WPUltimateRecipe::get()->helper('assets')->add(
            array(
                'file' => $this->addonPath . '/css/custom-taxonomies.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_taxonomies',
            ),
            array(
                'file' => $this->addonPath . '/js/custom-taxonomies.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_taxonomies',
                'deps' => array(
                    'jquery',
                ),
            )
        );
    }

    /*
     * Generate settings & addons pages
     */
    public function add_submenu_page() {
        add_submenu_page( 'edit.php?post_type=recipe', __( 'Custom Taxonomies', 'wp-ultimate-recipe' ), __( 'Custom Tags', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_taxonomies', array( $this, 'custom_taxonomies_page' ) );
    }

    public function custom_taxonomies_page() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        include( $this->addonDir . '/templates/page.php' );
    }

    public function custom_taxonomies_settings() {
        add_settings_section( 'wpurp_taxonomies_list_section', __('Current Recipe Tags', 'wp-ultimate-recipe' ), array( $this, 'page_list_taxonomies' ), 'wpurp_taxonomies_settings' );
        add_settings_section( 'wpurp_taxonomies_settings_section', __('Add new Recipe Tag', 'wp-ultimate-recipe' ), array( $this, 'page_taxonomy_form' ), 'wpurp_taxonomies_settings' );
    }

    public function page_list_taxonomies() {
        include( $this->addonDir . '/templates/page_list.php' );
    }

    public function page_taxonomy_form() {
        include( $this->addonDir . '/templates/page_form.php' );
    }

    // TODO - Not the nicest way of doing this.
    public function delete_taxonomy() {
        if ( !wp_verify_nonce( $_POST['delete_taxonomy_nonce'], 'delete_taxonomy' ) ) {
            die( 'Invalid nonce.');
        }

        foreach( $_POST as $name => $value ) {
            if( strpos($name, 'submit-delete-') === 0 ) { // Starts with submit-delete-
                $taxonomy =  substr( $name, 14 );

                global $wp_taxonomies;
                $taxonomies = WPUltimateRecipe::get()->tags();

                if( taxonomy_exists( $taxonomy ) && array_key_exists( $taxonomy, $taxonomies ) ) {
                    unset( $wp_taxonomies[$taxonomy] );
                    unset( $taxonomies[$taxonomy] );

                    WPUltimateRecipe::get()->helper( 'taxonomies' )->update( $taxonomies );
                }
            }
        }

        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit();
    }

    public function reset_taxonomies() {
        if ( !wp_verify_nonce( $_POST['reset_taxonomies_nonce'], 'reset_taxonomies' ) ) {
            die( 'Invalid nonce.');
        }

        WPUltimateRecipe::get()->helper( 'taxonomies' )->update( array() );

        WPUltimateRecipe::get()->helper( 'taxonomies' )->check_recipe_taxonomies();
        WPUltimateRecipe::get()->helper( 'permalinks_flusher' )->set_flush_needed();

        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit();
    }

    public function add_taxonomy() {
        if ( !wp_verify_nonce( $_POST['add_taxonomy_nonce'], 'add_taxonomy' ) ) {
            die( 'Invalid nonce.' . var_export( $_POST, true ) );
        }

        $taxonomies = WPUltimateRecipe::get()->tags();

        $name = $_POST['wpurp_custom_taxonomy_name'];
        $singular = $_POST['wpurp_custom_taxonomy_singular_name'];
        $slug = str_replace(' ', '-', strtolower($_POST['wpurp_custom_taxonomy_slug']));
        $tag_name = str_replace(' ', '-', strtolower($singular));
        $tag_name = preg_replace( '/[^a-z\-]/i', '', $tag_name );

        if( strlen($tag_name) == 0 ) {
            $tag_name = 'custom_tag' . (count( $taxonomies ) + 1);
        }

        $edit_tag_name = $_POST['wpurp_edit'];
        $editing = false;

        if( strlen($edit_tag_name) > 0 ) {
            $editing = true;
        }

        if( !$editing && taxonomy_exists( strtolower( $singular ) ) ) {
            die( 'This taxonomy already exists.' );
        }

        if( strlen($tag_name) > 1 && strlen($name) > 1 && strlen($singular) > 1 ) {
            $name_lower = strtolower( $name );

            if( $editing ) {
                $tag_name = $edit_tag_name;
            }

            $taxonomies[$tag_name] = apply_filters( 'wpurp_register_taxonomy',
                array(
                    'labels' => array(
                        'name'                       => $name,
                        'singular_name'              => $singular,
                        'search_items'               => __( 'Search', 'wp-ultimate-recipe' ) . ' ' . $name,
                        'popular_items'              => __( 'Popular', 'wp-ultimate-recipe' ) . ' ' . $name,
                        'all_items'                  => __( 'All', 'wp-ultimate-recipe' ) . ' ' . $name,
                        'edit_item'                  => __( 'Edit', 'wp-ultimate-recipe' ) . ' ' . $singular,
                        'update_item'                => __( 'Update', 'wp-ultimate-recipe' ) . ' ' . $singular,
                        'add_new_item'               => __( 'Add New', 'wp-ultimate-recipe' ) . ' ' . $singular,
                        'new_item_name'              => __( 'New', 'wp-ultimate-recipe' ) . ' ' . $singular . ' ' . __( 'Name', 'wp-ultimate-recipe' ),
                        'separate_items_with_commas' => __( 'Separate', 'wp-ultimate-recipe' ) . ' ' . $name_lower . ' ' . __( 'with commas', 'wp-ultimate-recipe' ),
                        'add_or_remove_items'        => __( 'Add or remove', 'wp-ultimate-recipe' ) . ' ' . $name_lower,
                        'choose_from_most_used'      => __( 'Choose from the most used', 'wp-ultimate-recipe' ) . ' ' . $name_lower,
                        'not_found'                  => __( 'No', 'wp-ultimate-recipe' ) . ' ' . $name_lower . ' ' . __( 'found.', 'wp-ultimate-recipe' ),
                        'menu_name'                  => $name
                    ),
                    'show_ui' => true,
                    'show_tagcloud' => true,
                    'hierarchical' => true,
                    'rewrite' => array(
                        'slug' => $slug,
                        'hierarchical' => true
                    )
                ),
                $tag_name
            );

            WPUltimateRecipe::get()->helper( 'taxonomies' )->update( $taxonomies );
            WPUltimateRecipe::get()->helper( 'taxonomies' )->check_recipe_taxonomies();
            WPUltimateRecipe::get()->helper( 'permalinks_flusher' )->set_flush_needed();
        }

        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit();
    }
}

WPUltimateRecipe::loaded_addon( 'custom-taxonomies', new WPURP_Custom_Taxonomies() );