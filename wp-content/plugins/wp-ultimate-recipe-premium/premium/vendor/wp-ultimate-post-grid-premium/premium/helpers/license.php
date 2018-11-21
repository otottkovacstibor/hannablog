<?php

class WPUPG_License {

    public function __construct()
    {
        add_action( 'admin_notices', array( $this, 'license_notice' ) );
        add_action( 'admin_init', array( $this, 'plugin_updater' ) );
        add_action( 'admin_init', array( $this, 'register_option' ) );

        // Setup EDD Plugin Updater
        define( 'EDD_WPUPG_STORE_URL', 'http://bootstrapped.ventures' );
        define( 'EDD_WPUPG_PRODUCT', 'WP Ultimate Post Grid Premium' );

        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( WPUltimatePostGridPremium::get()->premiumDir . '/vendor/EDD/EDD_SL_Plugin_Updater.php' );
        }
    }

    public function license_notice()
    {
        $screen = get_current_screen();
        // License key form
        if( $screen->id == WPUPG_POST_TYPE . '_page_wpupg_admin' ) {
            $license 	= get_option( 'edd_wpupg_license_key' );
            $status 	= get_option( 'edd_wpupg_license_status' );

            include( WPUltimatePostGridPremium::get()->premiumDir . '/helpers/license_form.php' );
        }

        // Nag messesage on plugins page
        if( $screen->id == 'plugins' ) {
            $status = get_option( 'edd_wpupg_license_status' );

            if( $status !== 'valid' ) {
                echo '<div class="error">';
                echo '<p><strong>WP Ultimate Post Grid</strong><br/>';
                echo __( 'Activate your license key to receive automatic updates.', 'wp-ultimate-post-grid' );
                echo ' <a href="' . get_admin_url( null, 'edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_admin' ) . '">' .__( 'Activate now', 'wp-ultimate-post-grid' ) . '</a>';
                echo ' - <a href="http://bootstrapped.ventures/wp-ultimate-post-grid/" target="_blank">' .__( 'Get a License', 'wp-ultimate-post-grid' ) . '</a>';
                echo '</p>';
                echo '</div>';
            }
        }
    }

    public function plugin_updater() {

        // retrieve our license key from the DB
        $license_key = trim( get_option( 'edd_wpupg_license_key' ) );

        // setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( EDD_WPUPG_STORE_URL, WPUltimatePostGrid::get()->pluginFile, array(
                'version' 	=> WPUPG_PREMIUM_VERSION, 				// current version number
                'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
                'item_name' => EDD_WPUPG_PRODUCT, 	// name of this plugin
                'author' 	=> 'Bootstrapped Ventures'  // author of this plugin
            )
        );

    }

    public function register_option() {
        register_setting( 'edd_wpupg_license', 'edd_wpupg_license_key', array( $this, 'sanitize_license' ) );
    }

    public function sanitize_license( $new ) {
        $old = get_option( 'edd_wpupg_license_key', '' );
        $status = get_option( 'edd_wpupg_license_status', '' );

        if( $old != $new ) {
            delete_option( 'edd_wpupg_license_status' ); // new license has been entered, so must reactivate
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
            'item_name' => urlencode( EDD_WPUPG_PRODUCT ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, EDD_WPUPG_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "active" or "inactive"
        update_option( 'edd_wpupg_license_status', $license_data->license );
    }

    public function deactivate_license( $key ) {
        $license = trim( $key );

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license' 	=> $license,
            'item_name' => urlencode( EDD_WPUPG_PRODUCT ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, EDD_WPUPG_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

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

    public function edd_wpupg_check_license() {

        global $wp_version;

        $license = trim( get_option( 'edd_wpupg_license_key' ) );

        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode( EDD_WPUPG_PRODUCT ),
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, EDD_WPUPG_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );


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