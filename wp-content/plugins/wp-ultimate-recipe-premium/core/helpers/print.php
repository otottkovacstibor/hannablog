<?php

class WPURP_Print {

    public function __construct()
    {
        add_action( 'template_redirect', array( $this, 'redirect' ), 1 );
    }

    public function redirect() {
        // Keyword to check for in URL
        $keyword = urlencode( WPUltimateRecipe::option( 'print_template_keyword', 'print' ) );
        if( strlen( $keyword ) <= 0 ) {
            $keyword = 'print';
        }

        // Current URL
        $schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
        $url = $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Check if URL ends with /print
        preg_match( "/^(.*?)\/{$keyword}()$/", $url, $url_data );

        if( empty( $url_data ) ) {
            // Check if URL ends with /print/parameters
            preg_match( "/^(.*?)\/{$keyword}\/(.*?)$/", $url, $url_data );
        }

        if( isset( $url_data[1] ) ) {
            $post_id = url_to_postid( $url_data[1] );
            $post = get_post( $post_id );

            if( $post_id == 0 ) {
                // Check for plain permalinks
                $slug = substr( strrchr( $url_data[1], '=' ), 1 );
                if( $slug ) {
                    $post = get_page_by_path( $slug, OBJECT, WPURP_POST_TYPE );
                }
            }

            if ( $post && $post->post_type == WPURP_POST_TYPE ) {
                $recipe = new WPURP_Recipe( $post );
                $this->print_recipe( $recipe, $url_data[2] );
                exit();
            }
        }
    }

    public function print_recipe( $recipe, $parameters ) {
        // Get Serving Size
        preg_match("/[0-9\.,]+/", $parameters, $servings);
        $servings = empty( $servings ) ? 0.0 : floatval( str_replace( ',', '.', $servings[0] ) );

        if( $servings <= 0 ) {
            $servings = $recipe->servings_normalized();
        }

        if( WPUltimateRecipe::is_premium_active() ) {
            // Get Unit System
            $unit_system = false;
            $requested_systems = explode( '/', $parameters );
            $systems = WPUltimateRecipe::get()->helper( 'ingredient_units')->get_active_systems();

            foreach( $systems as $id => $options ) {
                foreach( $requested_systems as $requested_system ) {
                    if( $requested_system == $this->convertToSlug( $options['name'] ) ) {
                        $unit_system = $id;
                    }
                }
            }
        }

        // Get Template
        $template = WPUltimateRecipe::get()->template( 'print', 'default' );
        $fonts = false;
        if( isset( $template->fonts ) && count( $template->fonts ) > 0 ) {
            $fonts = 'http://fonts.googleapis.com/css?family=' . implode( '|', $template->fonts );
        }

        include( 'print_template.php' );
    }

    public function convertToSlug( $text )
    {
        $text = strtolower( $text );
        $text = str_replace( ' ', '-', $text );
        $text = preg_replace( "/-+/", "-", $text );
        $text = preg_replace( "/[^\w-]+/", "", $text );

        return $text;
    }
}