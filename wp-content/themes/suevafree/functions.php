<?php

/**
 *
 * This is your standard WordPress
 * functions.php file.
 *
 *
*/

add_filter( 'wp_statistics_sanitize_user_ip', 'sanitize_user_ip' );
function sanitize_user_ip( $user_ip ) {
    $ip_list = explode( ",", $user_ip );
    $user_ip = trim( $ip_list[0] );

    return $user_ip;
}

require_once get_template_directory() . '/core/main.php';



?>
