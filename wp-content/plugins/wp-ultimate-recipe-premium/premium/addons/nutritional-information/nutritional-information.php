<?php

class WPURP_Nutritional_Information extends WPURP_Premium_Addon {

    public $fields = array(
        'serving_size'          => 'g',
        'serving_size_display'  => 'g',
        'calories'              => 'kcal',
        'carbohydrate'          => 'g',
        'protein'               => 'g',
        'fat'                   => 'g',
        'saturated_fat'         => 'g',
        'polyunsaturated_fat'   => 'g',
        'monounsaturated_fat'   => 'g',
        'trans_fat'             => 'g',
        'cholesterol'           => 'mg',
        'sodium'                => 'mg',
        'potassium'             => 'mg',
        'fiber'                 => 'g',
        'sugar'                 => 'g',
        'vitamin_a'             => '%',
        'vitamin_c'             => '%',
        'calcium'               => '%',
        'iron'                  => '%',
    );

    public $daily = array(
        'carbohydrate'  => 300,
        'protein' 		=> 50,
        'fat' 		    => 65,
        'saturated_fat' => 20,
        'cholesterol' 	=> 300,
        'sodium' 		=> 2400,
        'potassium' 	=> 3500,
        'fiber' 		=> 25,
    );

    private $nutritional;
    private $buttons_added = false;

    public function __construct( $name = 'nutritional-information' )
    {
        parent::__construct( $name );

        // Widgets
        require_once( $this->addonDir . '/widgets/nutrition_label_widget.php' );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
        add_action( 'delete_ingredient', array( $this, 'delete_ingredient_data' ), 10, 3 );

        if( is_admin() ) {
            add_action( 'media_buttons_context',  array( $this, 'add_shortcode_button' ) );
        }

        // Ajax
        add_action( 'wp_ajax_search_ingredients', array( $this, 'ajax_search_ingredients' ) );
        add_action( 'wp_ajax_get_nutritional', array( $this, 'ajax_get_nutritional' ) );
        add_action( 'wp_ajax_save_nutritional', array( $this, 'ajax_save_nutritional' ) );
        add_action( 'wp_ajax_save_nutritional_recipe', array( $this, 'ajax_save_nutritional_recipe' ) );

        // Shortcode
        add_shortcode( 'nutrition-label', array( $this, 'nutrition_label_shortcode' ) );
        add_shortcode( 'ultimate-nutrition-label', array( $this, 'nutrition_label_shortcode' ) );
    }

    public function assets()
    {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/nutrition-label.css',
                'premium' => true,
                'public' => true,
            ),
            array(
                'file' => WPUltimateRecipe::get()->coreUrl . '/vendor/select2/select2.min.css',
                'direct' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_nutritional_information',
            ),
            array(
                'name' => 'select2',
                'file' => '/vendor/select2/select2.min.js',
                'admin' => true,
                'page' => 'recipe_page_wpurp_nutritional_information',
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'file' => $this->addonPath . '/css/nutritional-information.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_nutritional_information',
            ),
            array(
                'file' => $this->addonPath . '/js/nutritional-information.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_nutritional_information',
                'deps' => array(
                    'jquery',
                    'select2',
                    'wpurp-unit-conversion',
                ),
                'data' => array(
                    'name' => 'wpurp_nutritional_information',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_nutritional_information' )
                )
            )
        );
    }

    /**
     * Nutritional Information page
     */
    public function add_submenu_page()
    {
        add_submenu_page( 'edit.php?post_type=recipe', __( 'Nutritional Information', 'wp-ultimate-recipe' ), __( 'Nutritional Information', 'wp-ultimate-recipe' ), WPUltimateRecipe::option( 'nutritional_information_capability', 'manage_options' ), 'wpurp_nutritional_information', array( $this, 'settings_page' ) );
    }

    public function settings_page()
    {
        if ( !current_user_can( WPUltimateRecipe::option( 'nutritional_information_capability', 'manage_options' ) ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        include( $this->addonDir . '/templates/settings.php' );
    }

    /**
     * Nutritional Information shortcode button on Recipe page
     */
    public function add_shortcode_button( $context )
    {
        $screen = get_current_screen();

        if( $screen->id == 'recipe' && !$this->buttons_added ) {
            $context .= '<a href="#" id="insert-nutrition-shortcode" class="button" data-editor="content" title="Add Nutrition Label">';
            $context .= __( 'Add Nutrition Label', 'wp-ultimate-recipe' );
            $context .= '</a>';

            // Prevent adding buttons to other TinyMCE instances on the recipe edit page
            $this->buttons_added = true;
        }

        return $context;
    }

    /**
     * Generate nutrition label
     */
    public function label( $recipe, $label_serving_size = 0 )
    {
        ob_start();
        include( $this->addonDir . '/templates/label.php' );
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Nutrition Label shortcode
     */

    function nutrition_label_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'id' => 'n/a',
            'align' => 'left',
            'serving_size' => '',
        ), $options );

        $align = in_array( $options['align'], array( 'left', 'center', 'right', 'inline' ) ) ? $options['align'] : 'left';
        $serving_size = floatval( $options['serving_size'] );
        $recipe_post = null;

        if( $options['id'] != 'n/a' ) {
            $recipe_post = get_post( intval( $options['id'] ) );
        } else {
            $recipe_post = get_post();
        }

        if( !is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' )
        {
            $recipe = new WPURP_Recipe( $recipe_post );

            if( $align != 'inline' ) {
                $output = '<div style="text-align: '.$align.';">';
                $output .= $this->label( $recipe, $serving_size );
                $output .= '</div>';
            } else {
                $output = $this->label( $recipe, $serving_size );
            }

        }
        else
        {
            $output = '';
        }

        return do_shortcode( $output );
    }

    /**
     * Get the nutritional data from the WP options
     */
    public function get( $ingredient = false )
    {
        if( !$this->nutritional ) {
            $this->nutritional = get_option( 'wpurp_nutritional_information', array() );
        }

        if( $ingredient ) {
            if( isset( $this->nutritional[$ingredient] ) ) {
                return $this->nutritional[$ingredient];
            } else {
                return false;
            }
        } else {
            return $this->nutritional;
        }
    }

    public function update( $nutritional )
    {
        $this->nutritional = $nutritional;
        update_option( 'wpurp_nutritional_information', $nutritional );
    }

    public function get_summary( $ingredient )
    {
        $nutritional = $this->get( $ingredient );

        if( $nutritional ) {
            $summary = array();

            if( isset( $nutritional['_meta']['serving'] ) && $nutritional['_meta']['serving'] != '' ) {
                preg_match("/(^\d[\d.,]*)(.*)/", ltrim( $nutritional['_meta']['serving'] ), $out);

                $ref_amount = isset( $out[1] ) ? floatval( $out[0] ) : '';
                $ref_unit = isset( $out[2] ) ? trim( $out[2] ) : '';

                $summary[] = '<span class="ref-amount-unit" data-amount="' . $ref_amount . '" data-unit="' . $ref_unit . '">' . $nutritional['_meta']['serving'] . '</span>';
            }

            if( isset( $nutritional['_meta']['serving_quantity'] ) && isset( $nutritional['_meta']['serving_unit'] ) ) {
                $summary[] = $nutritional['_meta']['serving_quantity'] . ' ' . $nutritional['_meta']['serving_unit'];
            }

            if( isset( $nutritional['calories'] ) && $nutritional['calories'] != '' ) {
                $summary[] = $nutritional['calories'] . ' kcal';
            }

            return implode( ' = ', $summary );
        } else {
            return false;
        }
    }

    /**
     * Clean up nutritional data when a term is deleted
     */
    public function delete_ingredient_data( $term, $tt_id, $deleted_term )
    {
        $nutritional = $this->get();
        unset( $nutritional[$term] );
        $this->update( $nutritional );
    }

    public function ajax_search_ingredients()
    {
        if( check_ajax_referer( 'wpurp_nutritional_information', 'security', false ) )
        {
            $name = $_POST['name'];

            $args = array(
                'method' => 'foods.search',
                'search_expression' => $name,
                'max_results' => 20,
            );

            echo $this->api_call( $args );
        }

        die();
    }

    public function ajax_get_nutritional()
    {
        if( check_ajax_referer( 'wpurp_nutritional_information', 'security', false ) )
        {
            $food_id = intval( $_POST['food'] );

            $args = array(
                'method' => 'food.get',
                'food_id' => $food_id,
            );

            echo $this->api_call( $args );
        }

        die();
    }

    public function ajax_save_nutritional()
    {
        if( check_ajax_referer( 'wpurp_nutritional_information', 'security', false ) )
        {
            $ingredient = intval( $_POST['ingredient'] );
            $nutritional = $_POST['nutritional'];
            $this->save_nutritional( $ingredient, $nutritional );

            $summary = $this->get_summary( $ingredient );
            if( $summary !== false ) {
                echo $summary;
            } else {
                echo 'Something went wrong. Please try again later.';
            }
        }

        die();
    }

    public function save_nutritional( $ingredient, $nutritional ) {
        $all_nutritional = $this->get();

        if( !isset( $all_nutritional[$ingredient] ) ) {
            $all_nutritional[$ingredient] = array(
                '_meta' => array(),
            );
        }

        $all_nutritional[$ingredient]['_meta']['serving'] = $nutritional['serving'];
        $all_nutritional[$ingredient]['_meta']['serving_quantity'] = $nutritional['serving_quantity'];
        $all_nutritional[$ingredient]['_meta']['serving_unit'] = $nutritional['serving_unit'];

        foreach( $this->fields as $field => $unit ) {
            if( isset( $nutritional[$field] ) ) {
                $all_nutritional[$ingredient][$field] = $nutritional[$field];
            }
        }

        $this->update( $all_nutritional );
    }

    public function ajax_save_nutritional_recipe()
    {
        if( check_ajax_referer( 'wpurp_nutritional_information', 'security', false ) )
        {
            $recipe_id = intval( $_POST['recipe'] );
            $nutritional = $_POST['nutritional'];

            $recipe_nutritional = array();

            foreach( $this->fields as $field => $unit ) {
                $recipe_nutritional[$field] = $nutritional[$field];
            }

            update_post_meta( $recipe_id, 'recipe_nutritional', $recipe_nutritional );
        }

        die();
    }

    public function api_call( $args )
    {
        // Personal Settings
        $api_key = 'd65fd507e4304218a600bb2bc823eea8';
        $api_secret = '6086d271cd314eeaa3492fb5ab755395';

        // Required arguments
        $args['format'] = 'json';
        $args['oauth_consumer_key'] = $api_key;
        $args['oauth_nonce'] = substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 32 );
        $args['oauth_signature_method'] = 'HMAC-SHA1';
        $args['oauth_timestamp'] = time();
        $args['oauth_version'] = '1.0';

        ksort( $args );
        $params = array();
        foreach( $args as $key => $val ) {
            $params[] = $key . '=' . rawurlencode( $val );
        }
        $params = implode( '&', $params );

        $base = 'POST&http%3A%2F%2Fplatform.fatsecret.com%2Frest%2Fserver.api&' . rawurlencode( $params );
        $signature = base64_encode( hash_hmac( 'sha1', $base, $api_secret . "&", true ) );

        $url = 'http://platform.fatsecret.com/rest/server.api?' . $params . '&oauth_signature=' . rawurlencode( $signature );

        $response = wp_remote_post( $url, array(
                'method' => 'POST',
                'timeout' => 45,
            )
        );

        if ( is_wp_error( $response ) ) {
            return json_encode( array() );
        } else {
            return $response['body'];
        }
    }
}

WPUltimateRecipe::loaded_addon( 'nutritional-information', new WPURP_Nutritional_Information() );