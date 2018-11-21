<?php

class WPURP_Privacy {

    public function __construct()
    {
        add_action('admin_init', array($this, 'privacy_policy') );
    }

    public function privacy_policy()
    {
        if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
            return;
        }
     
        ob_start();
        include('privacy_template.php');
        $content = ob_get_contents();
        ob_end_clean();
     
        wp_add_privacy_policy_content(
            'WP Ultimate Recipe',
            wp_kses_post( wpautop( $content, false ) )
        );
    }
}