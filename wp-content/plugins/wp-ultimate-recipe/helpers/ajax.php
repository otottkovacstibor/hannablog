<?php

class WPURP_Ajax {

    public function __construct()
    {
    }

    public function url()
    {
        $scheme = ( !empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' || ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 ) ) ? 'https' : 'http';
        $ajaxurl = admin_url( 'admin-ajax.php', $scheme );
        $ajaxurl .= '?wpurp_ajax=1';

        // WPML AJAX Localization Fix
        global $sitepress;
        if( isset( $sitepress) ) {
            $ajaxurl .= '&lang='.$sitepress->get_current_language();
        }

        return $ajaxurl;
    }
}