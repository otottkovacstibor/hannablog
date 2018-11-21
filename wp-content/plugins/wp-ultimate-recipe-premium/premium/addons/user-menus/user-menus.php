<?php

class WPURP_User_Menus extends WPURP_Premium_Addon {

    public function __construct( $name = 'user-menus' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'init', array( $this, 'assets' ) );
        add_action( 'init', array( $this, 'menus_init' ));
        add_action( 'admin_init', array( $this, 'user_menus_admin_init' ));
        add_action( 'admin_menu', array( $this, 'ingredient_groups_menu' ), 5 );

        // Ajax
        add_action( 'wp_ajax_user_menus_groupby', array( $this, 'ajax_user_menus_groupby' ) );
        add_action( 'wp_ajax_nopriv_user_menus_groupby', array( $this, 'ajax_user_menus_groupby' ) );
        add_action( 'wp_ajax_user_menus_get_ingredients', array( $this, 'ajax_user_menus_get_ingredients' ) );
        add_action( 'wp_ajax_nopriv_user_menus_get_ingredients', array( $this, 'ajax_user_menus_get_ingredients' ) );
        add_action( 'wp_ajax_user_menus_delete', array( $this, 'ajax_user_menus_delete' ) );
        add_action( 'wp_ajax_nopriv_user_menus_delete', array( $this, 'ajax_user_menus_delete' ) );
        add_action( 'wp_ajax_user_menus_save', array( $this, 'ajax_user_menus_save' ) );
        add_action( 'wp_ajax_nopriv_user_menus_save', array( $this, 'ajax_user_menus_save' ) );
        add_action( 'wp_ajax_ingredient_groups_save', array( $this, 'ajax_ingredient_groups_save' ) );
        add_action( 'wp_ajax_nopriv_ingredient_groups_save', array( $this, 'ajax_ingredient_groups_save' ) );
        add_action( 'wp_ajax_add_to_shopping_list', array( $this, 'ajax_add_to_shopping_list' ) );
        add_action( 'wp_ajax_nopriv_add_to_shopping_list', array( $this, 'ajax_add_to_shopping_list' ) );
        add_action( 'wp_ajax_update_shopping_list', array( $this, 'ajax_update_shopping_list' ) );
        add_action( 'wp_ajax_nopriv_update_shopping_list', array( $this, 'ajax_update_shopping_list' ) );

        //Filters
        add_filter( 'the_content', array( $this, 'user_menus_content' ), 10 );

        // Shortcode
        add_shortcode( 'wpurp_user_menus', array( $this, 'user_menus_shortcode' ) ); // For backwards compatibility
        add_shortcode( 'ultimate-recipe-user-menus', array( $this, 'user_menus_shortcode' ) );
        add_shortcode( 'ultimate-recipe-menu', array( $this, 'display_menu_shortcode' ) );
        add_shortcode( 'ultimate-recipe-user-menus-by', array( $this, 'display_user_menus_by_shortcode' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            // User menus shortcode
            array(
                'file' => WPUltimateRecipe::get()->coreUrl . '/vendor/select2/select2.css',
                'direct' => true,
                'public' => true,
            ),
            array(
                'file' => $this->addonPath . '/css/user-menus.css',
                'premium' => true,
                'public' => true,
            ),
            array(
                'name' => 'select2',
                'file' => '/vendor/select2/select2.min.js',
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
            ),
            array(
                'name' => 'wpurp-user-menus',
                'file' => $this->addonPath . '/js/user-menus.js',
                'premium' => true,
                'public' => true,
                'deps' => array(
                    'jquery',
                    'wpurp-unit-conversion',
                    'js-quantities',
                    'jquery-ui-sortable',
                    'jquery-ui-droppable',
                    'select2',
                ),
                'data' => array(
                    'name' => 'wpurp_user_menus',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'addonUrl' => $this->addonUrl,
                    'nonce' => wp_create_nonce( 'wpurp_user_menus' ),
                    'consolidate_ingredients' => WPUltimateRecipe::option( 'user_menus_consolidate_ingredients', '1' ),
                    'adjustable_system' => WPUltimateRecipe::option( 'user_menus_dynamic_unit_system', '1' ),
                    'default_system' => WPUltimateRecipe::option( 'user_menus_default_unit_system', '0' ),
                    'static_systems' => $this->get_static_unit_systems(),
                    'checkboxes' => WPUltimateRecipe::option( 'user_menus_checkboxes', '1' ),
                    'ingredient_notes' => WPUltimateRecipe::option( 'user_menus_ingredient_notes', '0' ) == '1' ? true : false,
                    'fractions' => WPUltimateRecipe::option( 'recipe_adjustable_servings_fractions', '0' ) == '1' ? true : false,
                    'print_recipe_list' => WPUltimateRecipe::option( 'user_menus_print_with_menu', '0' ) == '1' ? true : false,
                    'print_recipe_list_header' => '<tr><th>' . __( 'Recipe', 'wp-ultimate-recipe' ) . '</th><th>' . __( 'Servings', 'wp-ultimate-recipe' ) . '</th></tr>',
                    'custom_print_shoppinglist_css' => WPUltimateRecipe::option( 'custom_code_print_shoppinglist_css', '' ),
                )
            ),
            array(
                'file' => $this->addonPath . '/js/add-to-shopping-list.js',
                'premium' => true,
                'public' => true,
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_add_to_shopping_list',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
                )
            ),
            // Ingredient groups page
            array(
                'file' => $this->addonPath . '/css/ingredient-groups.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_ingredient_groups',
            ),
            array(
                'file' => $this->addonPath . '/js/ingredient-groups.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_ingredient_groups',
                'deps' => array(
                    'jquery',
                    'jquery-ui-sortable',
                    'jquery-ui-droppable',
                ),
                'data' => array(
                    'name' => 'wpurp_ingredient_groups',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_ingredient_groups' )
                )
            )
        );
    }

    public function ingredient_groups_menu()
    {
        add_submenu_page( 'edit.php?post_type=recipe', 'WP Ultimate Recipe ' . __( 'Ingredient Groups', 'wp-ultimate-recipe' ), __( 'Ingredient Groups', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_ingredient_groups', array( $this, 'ingredient_groups_menu_page' ) );
    }

    public function ingredient_groups_menu_page() {
        include( $this->addonDir . '/templates/ingredient-groups.php' );
    }

    public function ajax_ingredient_groups_save()
    {
        if(check_ajax_referer( 'wpurp_ingredient_groups', 'security', false ) )
        {
            $ingredients = $_POST['ingredients'];
            $group = $_POST['group'];

            foreach( $ingredients as $slug) {
                WPURP_Taxonomy_MetaData::set( 'ingredient', $slug, 'group', $group );
            }
        }

        die();
    }

    public function ajax_add_to_shopping_list()
    {
        if(check_ajax_referer( 'wpurp_add_to_shopping_list', 'security', false ) )
        {
            $recipe_id = intval( $_POST['recipe_id'] );
            $servings_wanted = intval( $_POST['servings_wanted'] );

            $recipe = new WPURP_Recipe( $recipe_id );
            $servings_wanted = $servings_wanted < 1 ? $recipe->servings_normalized() : $servings_wanted;


            $shopping_list_recipes = array();
            if( isset( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) ) {
                $shopping_list_recipes = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) );
            }
            $shopping_list_recipes[] = $recipe_id;

            $shopping_list_servings = array();
            if( isset( $_COOKIE['WPURP_Shopping_List_Servings_v2'] ) ) {
                $shopping_list_servings = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Servings_v2'] ) );
            }
            $shopping_list_servings[] = $servings_wanted;

            $shopping_list_order = array();
            if( isset( $_COOKIE['WPURP_Shopping_List_Order_v2'] ) ) {
                $shopping_list_order = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Order_v2'] ) );
            }
            $shopping_list_order[] = ( count( $shopping_list_recipes ) - 1 );

            // Set or update cookies, expires in 30 days
            setcookie( 'WPURP_Shopping_List_Recipes_v2', implode( ';', $shopping_list_recipes ), time()+60*60*24*30, '/' );
            setcookie( 'WPURP_Shopping_List_Servings_v2', implode( ';', $shopping_list_servings ), time()+60*60*24*30, '/' );
            setcookie( 'WPURP_Shopping_List_Order_v2', implode( ';', $shopping_list_order ), time()+60*60*24*30, '/' );
        }

        die();
    }

    public function ajax_update_shopping_list()
    {
        if(check_ajax_referer( 'wpurp_user_menus', 'security', false ) )
        {
            $full_recipes = $_POST['recipes'];
            $full_order = $_POST['order'];

            $recipes = array();
            $servings = array();
            $order = array();
            foreach( $full_order as $key => $order_id ) {
                $recipes[] = $full_recipes[$order_id]['id'];
                $servings[] = $full_recipes[$order_id]['servings_wanted'];
                $order[] = $key;
            }

            // Set or update cookies, expires in 30 days
            setcookie( 'WPURP_Shopping_List_Recipes_v2', implode( ';', $recipes ), time()+60*60*24*30, '/' );
            setcookie( 'WPURP_Shopping_List_Servings_v2', implode( ';', $servings ), time()+60*60*24*30, '/' );
            setcookie( 'WPURP_Shopping_List_Order_v2', implode( ';', $order ), time()+60*60*24*30, '/' );
        }

        die();
    }

    public function menus_init()
    {
        $slug = WPUltimateRecipe::option( 'user_menus_slug', 'menu' );

        $name = __( 'Menus', 'wp-ultimate-recipe' );
        $singular = __( 'Menu', 'wp-ultimate-recipe' );

        $args = apply_filters( 'wpurp_register_menu_post_type',
            array(
                'labels' => array(
                    'name' => $name,
                    'singular_name' => $singular,
                    'add_new' => __( 'Add New', 'wp-ultimate-recipe' ),
                    'add_new_item' => __( 'Add New', 'wp-ultimate-recipe' ) . ' ' . $singular,
                    'edit' => __( 'Edit', 'wp-ultimate-recipe' ),
                    'edit_item' => __( 'Edit', 'wp-ultimate-recipe' ) . ' ' . $singular,
                    'new_item' => __( 'New', 'wp-ultimate-recipe' ) . ' ' . $singular,
                    'view' => __( 'View', 'wp-ultimate-recipe' ),
                    'view_item' => __( 'View', 'wp-ultimate-recipe' ) . ' ' . $singular,
                    'search_items' => __( 'Search', 'wp-ultimate-recipe' ) . ' ' . $name,
                    'not_found' => __( 'No', 'wp-ultimate-recipe' ) . ' ' . $name . ' ' . __( 'found.', 'wp-ultimate-recipe' ),
                    'not_found_in_trash' => __( 'No', 'wp-ultimate-recipe' ) . ' ' . $name . ' ' . __( 'found in trash.', 'wp-ultimate-recipe' ),
                    'parent' => __( 'Parent', 'wp-ultimate-recipe' ) . ' ' . $singular,
                ),
                'public' => true,
                'menu_position' => 5,
                'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'author' ),
                'taxonomies' => array( '' ),
                'menu_icon' =>  WPUltimateRecipe::get()->coreUrl . '/img/icon_16.png',
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => $slug
                ),
                'show_in_menu' => 'edit.php?post_type=recipe',
            )
        );

        register_post_type( 'menu', $args );
    }

    public function user_menus_admin_init() {
        add_meta_box(
            'user_menus_meta_box',
            __( 'Menu', 'wp-ultimate-recipe' ),
            array( $this, 'user_menus_meta_box' ),
            'menu',
            'normal',
            'high'
        );
    }

    public function user_menus_meta_box( $menu, $menu_id = '' )
    {
        _e( 'The menu can be edited from the front end:', 'wp-ultimate-recipe' );
        echo '<br/>';
        echo '<a href="'.get_permalink( $menu->ID ).'">';
        echo get_the_title( $menu->ID );
        echo '</a>';
    }

    public function user_menus_content( $content ) {

        if ( is_single() && get_post_type() == 'menu' ) {
            remove_filter( 'the_content', array( $this, 'user_menus_content' ), 10 );

            $menu = get_post();

            $recipes = get_post_meta( $menu->ID, 'user-menus-recipes' );
            $order = get_post_meta( $menu->ID, 'user-menus-order' );

            $script_name = WPUltimateRecipe::option( 'assets_use_minified', '1' ) == '1' ? 'wpurp_script_minified' : 'wpurp-user-menus';
            wp_localize_script( $script_name, 'wpurp_user_menu',
                array(
                    'recipes' => $recipes[0],
                    'order' => $order[0],
                    'nbrRecipes' => get_post_meta( $menu->ID, 'user-menus-nbrRecipes', true ),
                    'unitSystem' => get_post_meta( $menu->ID, 'user-menus-unitSystem', true ),
                    'menuId' => $menu->ID,
                )
            );

            ob_start();
            include( $this->addonDir . '/templates/user-menus.php');
            $menu_content = ob_get_contents();
            ob_end_clean();

            $content .= apply_filters( 'wpurp_user_menus_form', $menu_content, $menu );

            add_filter('the_content', array( $this, 'user_menus_content' ), 10);
        }

        return $content;
    }

    public function ajax_user_menus_delete()
    {
        if( check_ajax_referer( 'wpurp_user_menus', 'security', false ) )
        {
            global $user_ID;

            $menu_id = intval( $_POST['menuId'] );
            $menu = get_post( $menu_id );

            if( $menu->post_type == 'menu' && ( current_user_can( 'manage_options' ) || $menu->post_author == $user_ID ) ) {
                wp_delete_post( $menu_id );
            }

            die( apply_filters( 'wpurp_user_menus_delete_button_redirect', get_home_url() ) );
        }

        die();
    }

    public function ajax_user_menus_save()
    {
        if( check_ajax_referer( 'wpurp_user_menus', 'security', false ) )
        {
            global $user_ID;

            $menu_id = intval( $_POST['menuId'] );
            $title = $_POST['title'];
            $recipes = $_POST['recipes'];
            $order = $_POST['order'];
            $nbrRecipes = $_POST['nbrRecipes'];
            $unitSystem = $_POST['unitSystem'];

            // Create new menu
            if( $menu_id === 0 ) {
                $post = array(
                    'post_status' => 'publish',
                    'post_author' => $user_ID,
                    'post_type' => 'menu',
                    'post_title' => $title,
                );

                // Save post
                $menu_id = wp_insert_post( $post );

                // Blank slate for User Menus
                setcookie( 'WPURP_Shopping_List_Recipes_v2', '', time()+60*60*24*30, '/' );
                setcookie( 'WPURP_Shopping_List_Servings_v2', '', time()+60*60*24*30, '/' );
                setcookie( 'WPURP_Shopping_List_Order_v2', '', time()+60*60*24*30, '/' );
            } else {
                $post = array(
                    'ID' => $menu_id,
                    'post_title' => $title
                );

                wp_update_post( $post );
            }

            update_post_meta( $menu_id, 'user-menus-recipes', $recipes );
            update_post_meta( $menu_id, 'user-menus-order', $order );
            update_post_meta( $menu_id, 'user-menus-nbrRecipes', $nbrRecipes );
            update_post_meta( $menu_id, 'user-menus-unitSystem', $unitSystem );

            die( apply_filters( 'wpurp_user_menus_save_button_redirect', get_permalink( $menu_id ) ) );
        }

        die();
    }

    public function ajax_user_menus_get_ingredients()
    {
        if( check_ajax_referer( 'wpurp_user_menus', 'security', false ) )
        {
            $recipe_id = intval( $_POST['recipe_id'] );

            $ingredients = get_post_meta( $recipe_id, 'recipe_ingredients' );
            $ingredients = $ingredients[0];

            $ingredients_with_groups = array();

            if( is_array( $ingredients ) ) {
                foreach( $ingredients as $ingredient )
                {
                    $term = get_term( $ingredient['ingredient_id'], 'ingredient' );

                    if ( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && $term !== null && !is_wp_error( $term ) ) {
                        $ingredient['ingredient'] = $term->name;
                    }

                    $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $term->slug, 'plural' );
                    if( $plural && !is_array( $plural ) ) {
                        $ingredient['plural'] = $plural;
                    }

                    $ingredient['group'] = WPURP_Taxonomy_MetaData::get( 'ingredient', $term->slug, 'group' );
                    if( !$ingredient['group'] ) {
                        $ingredient['group'] = __( 'Other', 'wp-ultimate-recipe' );
                    }

                    $ingredients_with_groups[] = $ingredient;
                }
            }

            echo json_encode( $ingredients_with_groups );
        }

        die();
    }

    public function ajax_user_menus_groupby()
    {
        if( check_ajax_referer( 'wpurp_user_menus', 'security', false ) )
        {
            $groupby = $_POST['groupby'];
            $slug = $_POST['grid'];

            $groups = $this->get_recipes_grouped_by( $groupby, $slug );
            echo $this->get_select_options( $groups );
        }

        die();
    }

    public function get_recipes_grouped_by( $groupby, $slug = false )
    {
        $post_ids = false;
        if( $slug ) {
            $post = get_page_by_path( $slug, OBJECT, WPUPG_POST_TYPE );

            if ( !is_null( $post ) ) {
                $grid = new WPUPG_Grid( $post );
                $posts = $grid->posts();
                $post_ids = $posts['all'];
            }
        }

        $recipes_grouped = array();
        switch( $groupby ) {

            case 'a-z':
                $recipes = WPUltimateRecipe::get()->query()->ids( $post_ids )->order_by( 'title' )->order( 'ASC' )->get();

                $current_letter = '';
                $current_recipes = array();

                foreach( $recipes as $recipe ) {
                    $letter = strtoupper( mb_substr( $recipe->title(), 0, 1 ) );

                    if( $letter != $current_letter ) {
                        if($current_letter != '') {
                            $recipes_grouped[] = array(
                                'group_name' => $current_letter,
                                'recipes' => $current_recipes
                            );
                        }

                        $current_letter = $letter;
                        $current_recipes = array();
                    }

                    $current_recipes[] = $recipe;
                }

                if( count( $current_recipes ) > 0 ) {
                    $recipes_grouped[] = array(
                        'group_name' => $current_letter,
                        'recipes' => $current_recipes
                    );
                }

                break;

            default:
                $terms = get_terms( $groupby );

                foreach( $terms as $term ) {
                    $recipes_grouped[] = array(
                        'group_name'    => $term->name,
                        'recipes' => WPUltimateRecipe::get()->query()->ids( $post_ids )->order_by( 'title' )->order( 'ASC' )->taxonomy( $groupby )->term( $term->slug )->get(),
                    );
                }
                break;
        }

        return $recipes_grouped;
    }

    public function get_select_options($recipe_groups)
    {
        $out = '<option></option>';

        foreach( $recipe_groups as $group )
        {
            $out .= '<optgroup label="' . esc_attr( $group['group_name'] ) . '">';

            foreach( $group['recipes'] as $recipe )
            {
                $servings = $recipe->servings_normalized();
                if( $servings < 1 ) {
                    $servings = 1;
                }

                $out .= '<option value="' . $recipe->ID() . '" data-servings="' . $servings . '" data-link="' . $recipe->link() . '">' . $recipe->title() . '</option>';
            }

            $out .= '</optgroup>';
        }

        return $out;
    }

    public function user_menus_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'posts_from_grid' => '',
        ), $options );

        switch( WPUltimateRecipe::option( 'user_menus_enable', 'guests' ) ) {

            case 'off':
                return '<p class="errorbox">' . __( 'Sorry, the site administrator has disabled user menus.', 'wp-ultimate-recipe' ) . '</p>';
                break;

            case 'registered':
                if( !is_user_logged_in() ) {
                    return '<p class="errorbox">' . __( 'Sorry, only registered users may create menus.', 'wp-ultimate-recipe' ) . '</p>';
                }
            // Logged in? Fall through!
            case 'guests':
                // Posts from Grid
                $posts_from_grid = strtolower( trim( $options['posts_from_grid'] ) );

                $script_name = WPUltimateRecipe::option( 'assets_use_minified', '1' ) == '1' ? 'wpurp_script_minified' : 'wpurp-user-menus';
                wp_localize_script( $script_name, 'wpurp_user_menu_grid',
                    array(
                        'slug' => $posts_from_grid,
                    )
                );

                // Check for cookie
                if( isset( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) && isset( $_COOKIE['WPURP_Shopping_List_Servings_v2'] ) && isset( $_COOKIE['WPURP_Shopping_List_Order_v2'] ) ) {
                    $recipe_ids = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) );
                    $servings = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Servings_v2'] ) );
                    $order = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Order_v2'] ) );

                    $recipes = array();
                    foreach( $recipe_ids as $index => $recipe_id ) {
                        $recipe = new WPURP_Recipe( $recipe_id );

                        if( $recipe ) {
                            $recipes[] = array(
                                'id' => $recipe->ID(),
                                'name' => $recipe->title(),
                                'link' => $recipe->link(),
                                'servings_original' => $recipe->servings_normalized(),
                                'servings_wanted' => isset($servings[$index]) ? $servings[$index] : $recipe->servings_normalized(),
                            );
                        }
                    }

                    if( is_array( $recipes ) && is_array( $order ) ) {
                        $script_name = WPUltimateRecipe::option( 'assets_use_minified', '1' ) == '1' ? 'wpurp_script_minified' : 'wpurp-user-menus';
                        wp_localize_script( $script_name, 'wpurp_user_menu',
                            array(
                                'recipes' => $recipes,
                                'order' => $order,
                                'nbrRecipes' => count( $order ),
                                'unitSystem' => WPUltimateRecipe::option( 'user_menus_default_unit_system', '0' ),
                                'menuId' => 0,
                            )
                        );
                    }
                }

                // Include template
                ob_start();
                include( $this->addonDir . '/templates/user-menus.php' );
                return ob_get_clean();
                break;
        }

    }

    public function display_menu_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'id' => 'random', // If no ID given, show a random menu
            'template' => 'default'
        ), $options );

        $menu = null;

        if( $options['id'] == 'random' ) {

            $posts = get_posts(array(
                'post_type' => 'menu',
                'nopaging' => true
            ));

            $menu = $posts[ array_rand( $posts ) ];
        } else {
            $menu = get_post( intval( $options['id'] ) );
        }

        if( !is_null( $menu ) && $menu->post_type == 'menu' )
        {
            $recipes = get_post_meta( $menu->ID, 'user-menus-recipes' );
            $order = get_post_meta( $menu->ID, 'user-menus-order' );

            $script_name = WPUltimateRecipe::option( 'assets_use_minified', '1' ) == '1' ? 'wpurp_script_minified' : 'wpurp-user-menus';
            wp_localize_script( $script_name, 'wpurp_user_menu',
                array(
                    'recipes' => $recipes[0],
                    'order' => $order[0],
                    'nbrRecipes' => get_post_meta( $menu->ID, 'user-menus-nbrRecipes', true ),
                    'unitSystem' => get_post_meta( $menu->ID, 'user-menus-unitSystem', true ),
                    'menuId' => $menu->ID,
                )
            );

            $menu_display_only = true;

            ob_start();
            include( $this->addonDir . '/templates/user-menus.php');
            $output = ob_get_contents();
            ob_end_clean();
        }
        else
        {
            $output = '';
        }

        return $output;
    }

    public function display_user_menus_by_shortcode( $options )
    {
        $options = shortcode_atts( array(
            'author' => 'current_user',
            'sort_by' => 'title',
            'sort_order' => 'ASC',
        ), $options );

        $author = strtolower( $options['author'] );
        $sort_by = strtolower( $options['sort_by'] );
        $sort_order = strtoupper( $options['sort_order'] );

        $sort_by = in_array( $sort_by, array( 'name', 'title', 'date' ) ) ? $sort_by : 'title';
        $sort_order = in_array( $sort_order, array( 'ASC', 'DESC' ) ) ? $sort_order : 'ASC';

        if( $author == 'current_user' ) {
            $author = get_current_user_id();
        } else {
            $author = intval( $author );
        }

        $output = '';

        if( $author !== 0 ) {
            $args = array(
                'post_type' => 'menu',
                'post_status' => 'publish',
                'author' => $author,
                'orderby' => $sort_by,
                'order' => $sort_order,
                'no-paging' => true,
                'posts_per_page' => -1,
            );

            $menus = get_posts( $args );

            if( count( $menus ) !== 0 ) {
                $output .= '<ul class="wpurp-user-menus-by">';
                foreach ( $menus as $menu ) {
                    $item = '<li><a href="' . get_permalink( $menu->ID ) . '">' . $menu->post_title . '</a></li>';
                    $output .= apply_filters( 'wpurp_user_menus_by_item', $item, $menu );
                }
                $output .= '</ul>';
            }
        }

        return $output;
    }

    public function get_static_unit_systems()
    {
        $nbr_of_systems = intval( WPUltimateRecipe::option( 'user_menus_static_nbr_systems', '1' ) );
        $systems = array();

        for( $i = 1; $i <= $nbr_of_systems; $i++ ) {
            $systems[] = intval( WPUltimateRecipe::option( 'user_menus_static_system_' . $i, '0' ) );
        }

        return $systems;
    }
}

WPUltimateRecipe::loaded_addon( 'user-menus', new WPURP_User_Menus() );