<?php

class WPURP_User_Ratings extends WPURP_Premium_Addon {

    public function __construct( $name = 'user-ratings' ) {
        parent::__construct( $name );

        if( WPUltimateRecipe::option( 'user_ratings_enable', 'everyone') != 'disabled' ) {
            add_action( 'init', array( $this, 'assets' ) );

            add_action( 'wp_ajax_rate_recipe', array( $this, 'ajax_rate_recipe' ) );
            add_action( 'wp_ajax_nopriv_rate_recipe', array( $this, 'ajax_rate_recipe' ) );
            add_action( 'wp_ajax_reset_recipe_rating', array( $this, 'ajax_reset_recipe_rating' ) );
            add_action( 'wp_ajax_nopriv_reset_recipe_rating', array( $this, 'ajax_reset_recipe_rating' ) );
        }
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/user-ratings.css',
                'premium' => true,
                'public' => true,
            ),
            array(
                'file' => $this->addonPath . '/js/user-ratings.js',
                'premium' => true,
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_user_ratings',
                    'ajax_url' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'rate_recipe' )
                )
            ),
            array(
                'file' => $this->addonPath . '/js/admin.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_posts',
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_user_ratings',
                    'ajax_url' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'reset_recipe_rating' ),
                    'confirm' => __( 'Are you sure you want to reset the rating for this recipe? This will set the votes and rating to 0.', 'wp-ultimate-recipe' ),
                )
            )
        );
    }

    public function output( $recipe, $icon_full = 'fa-star', $icon_half = 'fa-star-half-o', $icon_empty = 'fa-star-o' )
    {
        ob_start();
        include( $this->addonDir . '/templates/user_rating.php' );
        $stars = ob_get_contents();
        ob_end_clean();

        return $stars;
    }

    public function ajax_rate_recipe()
    {
        $stars = floor( intval( $_POST['stars'] ) );
        $recipe_id = intval( $_POST['recipe'] );

        if( $stars < 1 ) $stars = 1;
        if( $stars > 5 ) $stars = 5;

        $ip = self::get_user_ip();
        $user = get_current_user_id();

        if( check_ajax_referer( 'rate_recipe', 'security', false ) && 'recipe' == get_post_type( $recipe_id ) && self::is_current_user_allowed_to_vote() )
        {
            if( self::get_current_user_rating_for( $recipe_id ) ) {
                // User has already voted for this recipe
                $user_ratings = get_post_meta( $recipe_id, 'recipe_user_ratings' );
                delete_post_meta( $recipe_id, 'recipe_user_ratings' );

                foreach( $user_ratings as $user_rating )
                {
                    if( $user != 0 && $user_rating['user'] === $user ) {
                        $user_rating['rating'] = $stars;
                    } elseif( $user == 0 && $user_rating['ip'] === $ip ) {
                        $user_rating['rating'] = $stars;
                    }

                    add_post_meta( $recipe_id, 'recipe_user_ratings', $user_rating );
                }
            } else {
                // First vote by this user
                $user_rating = array(
                    'user' => get_current_user_id(),
                    'ip' => $ip,
                    'rating' => $stars,
                );

                add_post_meta( $recipe_id, 'recipe_user_ratings', $user_rating );
            }

            // Set or update cookie, expires in 30 days
            setcookie( 'WPURP_User_Voted_' . $recipe_id, $stars, time()+60*60*24*30, '/' );
        }

        echo json_encode( self::get_recipe_rating( $recipe_id ) );

        die();
    }

    public function ajax_reset_recipe_rating()
    {
        $recipe_id = intval( $_POST['recipe'] );

        if( check_ajax_referer( 'reset_recipe_rating', 'security', false ) && 'recipe' == get_post_type( $recipe_id ) )
        {
            delete_post_meta( $recipe_id, 'recipe_user_ratings' );
            update_post_meta( $recipe_id, 'recipe_user_ratings_rating', 0 );
        }

        die();
    }

    public static function get_recipe_rating( $recipe_id )
    {
        $user_ratings = get_post_meta( $recipe_id, 'recipe_user_ratings' );

        $votes = count( $user_ratings );
        $total = 0;
        $rating = 0;
        $stars = 0;
        $half_star = false;

        foreach( $user_ratings as $user_rating )
        {
            $total += $user_rating['rating'];
        }

        if( $votes !== 0 ) {
            $rating = $total / $votes; // TODO Just an average for now, implement some more functions later

            $stars = floor( $rating );

            if( $rating - $stars >= 0.5 ) {
                $half_star = true;
            }

            $rating = round( $rating, 2 );
        }

        // Save numeric value of rating to allow sort by
        if( $rating != get_post_meta( $recipe_id, 'recipe_user_ratings_rating', true ) ) {
            update_post_meta( $recipe_id, 'recipe_user_ratings_rating', $rating );
        }

        return array(
            'votes' => $votes,
            'rating' => $rating,
            'stars' => $stars,
            'half_star' => $half_star,
        );
    }

    public static function is_current_user_allowed_to_vote()
    {
        $ip = self::get_user_ip();
        $user = get_current_user_id();
        $allowed = WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' );

        if( $allowed == 'disabled' ) return false;
        if( $allowed == 'users_only' && $user == 0 ) return false;

        if( $ip === 'unknown' && $user == 0 ) return false; // Y U have no valid IP?

        return true;
    }

    public static function get_current_user_rating_for( $recipe_id )
    {
        if( isset( $_COOKIE['WPURP_User_Voted_' . $recipe_id] ) ) return $_COOKIE['WPURP_User_Voted_' . $recipe_id];

        $ip = self::get_user_ip();
        $user = get_current_user_id();

        if( $ip === 'unknown' ) return false;

        $user_ratings = get_post_meta( $recipe_id, 'recipe_user_ratings' );

        if( count( $user_ratings ) == 0 ) return false; // No votes yet

        foreach( $user_ratings as $user_rating )
        {
            if( $user == 0 && $user_rating['ip'] === $ip ) return $user_rating['rating'];
            if( $user != 0 && $user_rating['user'] === $user ) return $user_rating['rating'];
        }

        return false;
    }

    // Source: http://stackoverflow.com/questions/6717926/function-to-get-user-ip-address
    private static function get_user_ip()
    {
        foreach( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key )
        {
            if( array_key_exists( $key, $_SERVER ) === true )
            {
                foreach( array_map( 'trim', explode( ',', $_SERVER[$key] ) ) as $ip )
                {
                    if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false )
                    {
                        return $ip;
                    }
                }
            }
        }

        return 'unknown';
    }
}

WPUltimateRecipe::loaded_addon( 'user-ratings', new WPURP_User_Ratings() );