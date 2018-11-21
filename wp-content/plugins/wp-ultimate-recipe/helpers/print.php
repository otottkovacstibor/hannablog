<?php

class WPURP_Print {

    public $keyword = 'print';

    public function __construct()
    {
        define( 'EP_RECIPE', 524288 ); // 2^19

        add_action( 'init', array( $this, 'endpoint' ) );
        add_action( 'init', array( $this, 'print_page' ) );
        add_action( 'template_redirect', array( $this, 'redirect' ) );
    }

    public function endpoint() {
        $keyword = urlencode( WPUltimateRecipe::option( 'print_template_keyword', 'print' ) );
        if( strlen( $keyword ) > 0 ) {
            $this->keyword = $keyword;
        }

        add_rewrite_endpoint( $this->keyword, EP_RECIPE );
    }

    public function redirect() {
        $print = get_query_var( $this->keyword, false );

        if( $print !== false ) {
            $post = get_post();
            $recipe = new WPURP_Recipe( $post );
            $this->print_recipe( $recipe, $print );
            exit();
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
            $fonts = 'https://fonts.googleapis.com/css?family=' . implode( '|', $template->fonts );
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

    public function print_page() {
		preg_match( '/\/wpurp_print\/(.+?)\/?$/', $_SERVER['REQUEST_URI'], $print_url ); // Input var okay.

        $print_function = isset( $print_url[1] ) ? $print_url[1] : false;

        if ( $print_function ) {
            // Styles.
            $styles = '';
            $styles .= '<style type="text/css">body { max-width: 1000px; margin: 0 auto; }</style>';

            // Scripts.
            $scripts = '';

            // Different print functions.
            switch ( $print_function ) {
                case 'meal_plan_recipes':
                    // TODO Clean up this mess.
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::get()->coreUrl . '/css/layout_base.css">';

                    // Change servings of recipes.
                    $scripts .= '<script src="' . includes_url( '/js/jquery/jquery.js' ) . '"></script>';
                    $scripts .= '<script src="' . WPUltimateRecipe::get()->coreUrl . '/vendor/fraction-js/index.js"></script>';
                    $scripts .= '<script src="' . WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js"></script>';
                    $scripts .= '<script>var wpurp_servings = {';
                    $scripts .= 'precision: ' . intval( WPUltimateRecipe::option( 'recipe_adjustable_servings_precision', 2 ) ) . ',';
                    $scripts .= 'decimal_character: "' . WPUltimateRecipe::option( 'recipe_decimal_character', '.' ) . '"';
                    $scripts .= '}</script>';

                    // TODO Use a JS file.
                    $scripts .= '<script>var mealPlannerPrintSetServings = function(servings) {';
                    $scripts .= 'for(var recipe_id in servings) {for(var i=0,l=servings[recipe_id].length; i<l; i++){';
                    $scripts .= 'var old_servings = parseInt(jQuery("[id=wpurp-container-recipe-" + recipe_id + "]:eq(0)").data("servings-original"));';
                    $scripts .= 'var new_servings = servings[recipe_id][i];';
                    $scripts .= 'if(old_servings !== new_servings) {';
                    $scripts .= 'var amounts = jQuery("[id=wpurp-container-recipe-" + recipe_id + "]:eq(" + i + ")").find(".wpurp-recipe-ingredient-quantity");';
                    $scripts .= 'wpurp_adjustable_servings.updateAmounts(amounts, old_servings, new_servings);';
                    $scripts .= 'wpurp_adjustable_servings.updateShortcode(jQuery("[id=wpurp-container-recipe-" + recipe_id + "]:eq(" + i + ")"), new_servings);';
                    $scripts .= 'jQuery("[id=wpurp-container-recipe-" + recipe_id + "]:eq(" + i + ")").find(".wpurp-recipe-servings").text(new_servings);';
                    $scripts .= '}}}};';
                    $scripts .= '</script>';

                    //var amounts = jQuery('.wpurp-recipe-ingredient-quantity');
                    // wpurp_adjustable_servings.updateAmounts(amounts, old_servings, new_servings);
                    // jQuery('.wpurp-recipe-servings').text(new_servings);

                    if( WPUltimateRecipe::is_premium_active() ) {
                        $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipePremium::get()->premiumUrl . '/addons/nutritional-information/css/nutrition-label.css">';
                        $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipePremium::get()->premiumUrl . '/addons/user-ratings/css/user-ratings.css">';
                    }
                    break;
                case 'meal_plan':
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::addon( 'meal-planner' )->addonUrl . '/css/meal-planner.css">';
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::addon( 'meal-planner' )->addonUrl . '/css/meal-plan.css">';
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::addon( 'meal-planner' )->addonUrl . '/css/meal_plan_print.css">';
                    break;
                case 'meal_plan_shopping_list':
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::addon( 'meal-planner' )->addonUrl . '/css/meal-planner.css">';
                    $styles .= '<link rel="stylesheet" type="text/css" href="' . WPUltimateRecipe::addon( 'meal-planner' )->addonUrl . '/css/print.css">';
                    break;
            }

            // Custom CSS.
            $styles .= '<style type="text/css">' . WPUltimateRecipe::option( 'custom_code_print_shoppinglist_css', '' ) . '</style>';

            // Fix for IE.
            header( 'HTTP/1.1 200 OK' );

            // Site icon.
			ob_start();
			wp_site_icon();
			$site_icon = ob_get_contents();
			ob_end_clean();

            $charset = get_bloginfo( 'charset' );
            $print_html = '<html><head><title>' . get_bloginfo('name') . '</title><meta http-equiv="Content-Type" content="text/html; charset=' . $charset . '" /><meta name="robots" content="noindex">' . $styles . $scripts . '</head><body class="wpurp-print">';
            $print_html .= $site_icon;
            $print_html .= $styles . $scripts;
            $print_html .='</head><body class="wpurp-print">';
            $print_html .= __( 'Loading...', 'wp-ultimate-recipe' );
            $print_html .= '</body></html>';
            echo $print_html;
            flush();
            exit;
        }
	}
}