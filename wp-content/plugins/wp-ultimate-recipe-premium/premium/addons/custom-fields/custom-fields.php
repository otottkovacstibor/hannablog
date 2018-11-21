<?php

class WPURP_Custom_Fields extends WPURP_Premium_Addon {

    private $fields;

    public function __construct( $name = 'custom-fields' ) {
        parent::__construct( $name );

        //Actions
        add_action( 'admin_init', array( $this, 'custom_fields_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
        add_action( 'admin_action_delete_custom_field', array( $this, 'delete_custom_field' ) );
        add_action( 'admin_action_add_custom_field', array( $this, 'add_custom_field' ) );
    }

    public function add_submenu_page() {
        add_submenu_page( 'edit.php?post_type=recipe', __( 'Custom Fields', 'wp-ultimate-recipe' ), __( 'Custom Fields', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_custom_fields', array( $this, 'custom_fields_page' ) );
    }

    public function custom_fields_page() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        include( $this->addonDir . '/templates/page.php' );
    }

    public function custom_fields_settings() {
        add_settings_section( 'wpurp_custom_fields_list_section', __('Current Custom Fields', 'wp-ultimate-recipe' ), array( $this, 'page_list_taxonomies' ), 'wpurp_custom_fields_settings' );
        add_settings_section( 'wpurp_custom_fields_settings_section', __('Add new Custom Field', 'wp-ultimate-recipe' ), array( $this, 'page_taxonomy_form' ), 'wpurp_custom_fields_settings' );
    }

    public function page_list_taxonomies() {
        include( $this->addonDir . '/templates/page_list.php' );
    }

    public function page_taxonomy_form() {
        include( $this->addonDir . '/templates/page_form.php' );
    }

    // TODO - Not the nicest way of doing this.
    public function delete_custom_field() {
        if ( !wp_verify_nonce( $_POST['delete_custom_field_nonce'], 'delete_custom_field' ) ) {
            die( 'Invalid nonce.');
        }

        foreach( $_POST as $name => $value ) {
            if( strpos($name, 'submit-delete-') === 0 ) { // Starts with submit-delete-
                $key =  substr( $name, 14 );

                $custom_fields = $this->get_custom_fields();
                unset( $custom_fields[$key] );
                $this->update_custom_fields( $custom_fields );
            }
        }

        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit();
    }

    public function add_custom_field() {
        if ( !wp_verify_nonce( $_POST['add_custom_field_nonce'], 'add_custom_field' ) ) {
            die( 'Invalid nonce.' . var_export( $_POST, true ) );
        }

        $name = $_POST['wpurp_custom_field_name'];
        $key = $_POST['wpurp_custom_field_key'];

        $key = str_replace(' ', '_', strtolower($key));
        $key = preg_replace( "/[^a-z\-_]/i", "", $key );

        $custom_fields = $this->get_custom_fields();

        if( array_key_exists( $key, $custom_fields ) ) {
            die( 'This custom field already exists.' );
        }

        if( strlen($name) > 1 && strlen($key) > 1 ) {

            $custom_fields[$key] = array(
                'key' => $key,
                'name' => $name,
            );

            $this->update_custom_fields( $custom_fields );
        }

        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit();
    }

    public function get_custom_fields()
    {
        if( !$this->fields ) {
            $custom_fields = get_option( 'wpurp_custom_fields', array() );

            if( !is_array( $custom_fields ) ) {
                $custom_fields = array();
            }
            $this->fields = $custom_fields;
        }

        return $this->fields;
    }

    public function update_custom_fields( $fields )
    {
        $this->fields = $fields;
        update_option( 'wpurp_custom_fields', $fields );
    }
}

WPUltimateRecipe::loaded_addon( 'custom-fields', new WPURP_Custom_Fields() );