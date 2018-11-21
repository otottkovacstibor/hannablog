<?php

class WPURP_License {

    public function __construct()
    {
        add_action( 'admin_notices', array( $this, 'license_notice' ) );
        add_action( 'admin_init', array( $this, 'plugin_updater' ) );
        add_action( 'admin_init', array( $this, 'register_option' ) );

        // Setup EDD Plugin Updater
        define( 'EDD_WPURP_STORE_URL', 'http://www.wpultimaterecipe.com' );
        define( 'EDD_WPURP_PRODUCT', 'WP Ultimate Recipe Premium' );

        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( WPUltimateRecipePremium::get()->premiumDir . '/vendor/EDD/EDD_SL_Plugin_Updater.php' );
        }
    }

    public function license_notice() {
        $screen = get_current_screen();
        // License key form
        if( $screen->id == 'recipe_page_wpurp_admin' ) {
            $license 	= get_option( 'edd_wpurp_license_key' );
            $status 	= get_option( 'edd_wpurp_license_status' );

            include( WPUltimateRecipePremium::get()->premiumDir . '/helpers/license_form.php' );
        }

        // Nag messesage on plugins page
        if( $screen->id == 'plugins' ) {
            $status = get_option( 'edd_wpurp_license_status' );

            if( $status !== 'valid' ) {
                echo '<div class="error">';
                echo '<p><strong>WP Ultimate Recipe Premium</strong><br/>';
                echo __( 'Activate your license key to receive automatic updates.', 'wp-ultimate-recipe' );
                echo ' <a href="' . get_admin_url( null, 'edit.php?post_type=recipe&page=wpurp_admin' ) . '">' .__( 'Activate now', 'wp-ultimate-recipe' ) . '</a>';
                echo ' - <a href="http://www.wpultimaterecipe.com/get-the-plugin/" target="_blank">' .__( 'Get a License', 'wp-ultimate-recipe' ) . '</a>';
                echo '</p>';
                echo '</div>';
            }
        }
    }

    public function plugin_updater() {

        // retrieve our license key from the DB
        $license_key = trim( get_option( 'edd_wpurp_license_key' ) );

        // setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( EDD_WPURP_STORE_URL, WPUltimateRecipe::get()->pluginFile, array(
                'version' 	=> WPURP_PREMIUM_VERSION, 				// current version number
                'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
                'item_name' => EDD_WPURP_PRODUCT, 	// name of this plugin
                'author' 	=> 'Bootstrapped Ventures'  // author of this plugin
            )
        );

    }

    public function register_option() {
        register_setting( 'edd_wpurp_license', 'edd_wpurp_license_key', array( $this, 'sanitize_license' ) );
    }

    public function sanitize_license( $new ) {
        $old = get_option( 'edd_wpurp_license_key', '' );
        $status = get_option( 'edd_wpurp_license_status', '' );

        if( $old != $new ) {
            delete_option( 'edd_wpurp_license_status' ); // new license has been entered, so must reactivate
            $this->activate_license( $new );
        }

        if( $status == 'valid' && $old != $new ) {
            $this->deactivate_license( $old ); // changing key, so deactivate the old one if that one was active
        }

        return $new;
    }

    public function activate_license( $key ) {
        $license = trim( $key );

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> $license,
            'item_name' => urlencode( EDD_WPURP_PRODUCT ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( EDD_WPURP_STORE_URL, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "active" or "inactive"
        update_option( 'edd_wpurp_license_status', $license_data->license );
    }

    public function deactivate_license( $key ) {
        $license = trim( $key );

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license' 	=> $license,
            'item_name' => urlencode( EDD_WPURP_PRODUCT ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( EDD_WPURP_STORE_URL, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        // $license_data->license will be either "deactivated" or "failed"
        if( $license_data->license == 'deactivated' )
            return true;
    }

    /************************************
     * this illustrates how to check if
     * a license key is still valid
     * the updater does this for you,
     * so this is only needed if you
     * want to do something custom
     *************************************/

    public function edd_wpurp_check_license() {

        global $wp_version;

        $license = trim( get_option( 'edd_wpurp_license_key' ) );

        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode( EDD_WPURP_PRODUCT ),
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( EDD_WPURP_STORE_URL, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );


        if ( is_wp_error( $response ) )
            return false;

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        if( $license_data->license == 'valid' ) {
            echo 'valid'; exit;
            // this license is still valid
        } else {
            echo 'invalid'; exit;
            // this license is no longer valid
        }
    }
}