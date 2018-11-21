<?php

class WPURP_Import_Cooked extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-cooked' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_menu' ) );
        add_action( 'admin_menu', array( $this, 'import_manual_menu' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_cooked.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_cooked',
            )
        );
    }

    public function import_menu() {
        add_submenu_page( null, __( 'Import Cooked', 'wp-ultimate-recipe' ), __( 'Import Cooked', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_cooked', array( $this, 'import_page' ) );
    }

    public function import_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function import_manual_menu() {
        add_submenu_page( null, __( 'Import Cooked', 'wp-ultimate-recipe' ), __( 'Import Cooked', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_cooked_manual', array( $this, 'import_manual_page' ) );
    }

    public function import_manual_page() {
        if ( !wp_verify_nonce( $_POST['import_cooked_manual'], 'import_cooked_manual' ) ) {
            die( 'Invalid nonce.' );
        }

        // Automatic import recipes
        $cooked_recipes = $this->get_cooked_recipes();

        if( count( $cooked_recipes['auto_import'] ) > 0 ) {
            foreach( $cooked_recipes['auto_import'] as $post_id ) {
                $this->import_cooked_recipe( $post_id );
            }
        }

        // Manual import recipe
        if( isset( $_POST['import_post_id'] )) {

            $post_id = intval( $_POST['import_post_id'] );

            $this->import_cooked_recipe( $post_id );
        }

        $this->custom_fields();
        require( $this->addonDir. '/templates/manual_import.php' );
    }

    private function import_cooked_recipe( $post_id )
    {
        // Ingredients
        $cooked_ingredients = get_post_meta( $post_id, '_cp_recipe_detailed_ingredients', true );
        $detailed_ingredients = true;
        if( empty( $cooked_ingredients ) ) {
            $cooked_ingredients = get_post_meta( $post_id, '_cp_recipe_ingredients', true );
            $detailed_ingredients = false;
        }

        $new_ingredients = array();
        $ingredient_terms = array();

        // Detailed Ingredients
        if( $detailed_ingredients ) {
            $ingredient_group = '';

            foreach( $cooked_ingredients as $cooked_ingredient ) {
                if( $cooked_ingredient['type'] == 'section' ) {
                    $ingredient_group = $cooked_ingredient['value'];
                } else {

                    $ingredient = array();
                    $ingredient['amount'] = $cooked_ingredient['amount'];
                    $ingredient['unit'] = $cooked_ingredient['measurement'];
                    $ingredient['ingredient'] = $cooked_ingredient['name'];
                    $ingredient['notes'] = '';
                    $ingredient['group'] = $ingredient_group;

                    if( trim( $ingredient['ingredient'] ) !== '' )
                    {
                        $term = term_exists( $ingredient['ingredient'], 'ingredient' );

                        if ( $term === 0 || $term === null ) {
                            $term = wp_insert_term( $ingredient['ingredient'], 'ingredient' );
                        }

                        $ingredient['amount_normalized'] = WPUltimateRecipe::get()->helper( 'recipe_save' )->normalize_amount( $ingredient['amount'] );

                        if( !is_wp_error( $term ) )
                        {
                            $term_id = intval( $term['term_id'] );

                            $ingredient['ingredient_id'] = $term_id;

                            $new_ingredients[] = $ingredient;
                            $ingredient_terms[] = $term_id;
                        }
                    }
                }
            }
        }

        // Simple Ingredients
        $ingredients = $_POST['recipe_ingredients'];

        if( !$detailed_ingredients && $ingredients ) {
            $cooked_ingredient_lines = explode( "\n", $cooked_ingredients );

            $ingredient_group = '';
            $ingredient_nbr = 0;

            foreach( $cooked_ingredient_lines as $cooked_ingredient_line ) {
                if( strpos( $cooked_ingredient_line, '--' ) === 0 ) {
                    $ingredient_group = substr( $cooked_ingredient_line, 2 );
                } else {
                    $ingredient = $ingredients[$ingredient_nbr];

                    if( trim( $ingredient['ingredient'] ) !== '' )
                    {
                        $term = term_exists( $ingredient['ingredient'], 'ingredient' );

                        if ( $term === 0 || $term === null ) {
                            $term = wp_insert_term( $ingredient['ingredient'], 'ingredient' );
                        }

                        $ingredient['amount_normalized'] = WPUltimateRecipe::get()->helper( 'recipe_save' )->normalize_amount( $ingredient['amount'] );

                        if( !is_wp_error( $term ) )
                        {
                            $term_id = intval( $term['term_id'] );

                            $ingredient['ingredient_id'] = $term_id;
                            $ingredient['group'] = $ingredient_group;

                            $new_ingredients[] = $ingredient;
                            $ingredient_terms[] = $term_id;
                        }
                    }

                    $ingredient_nbr++;
                }
            }
        }

        // Update Ingredients
        wp_set_post_terms( $post_id, $ingredient_terms, 'ingredient' );
        update_post_meta( $post_id, 'recipe_ingredients', $new_ingredients );


        // Instructions
        $cooked_instructions = get_post_meta( $post_id, '_cp_recipe_detailed_directions', true );
        $detailed_instructions = true;
        if( empty( $cooked_instructions ) ) {
            $cooked_instructions = get_post_meta( $post_id, '_cp_recipe_directions', true );
            $detailed_instructions = false;
        }

        $new_instructions = array();

        // Detailed instructions
        if( $detailed_instructions ) {
            $instruction_group = '';

            foreach( $cooked_instructions as $cooked_instruction ) {
                if( $cooked_instruction['type'] == 'section' ) {
                    $instruction_group = $cooked_instruction['value'];
                } else {
                    $instruction = preg_replace( "/\[timer[^\]]*]/i", "", $cooked_instruction['value'] );
                    $instruction = str_ireplace( '[/timer]', '', $instruction );

                    $new_instructions[] = array(
                        'description' => $instruction,
                        'group' => $instruction_group,
                        'image' => strval( $cooked_instruction['image_id'] ),
                    );
                }
            }
        }

        // Simple instructions
        if( !$detailed_instructions ) {
            $cooked_instruction_lines = explode( "\n", $cooked_instructions );

            $instruction_group = '';

            foreach( $cooked_instruction_lines as $cooked_instruction_line ) {
                if( strpos( $cooked_instruction_line, '--' ) === 0 ) {
                    $instruction_group = substr( $cooked_instruction_line, 2 );
                } else {
                    $instruction = preg_replace( "/\[timer[^\]]*]/i", "", $cooked_instruction_line );
                    $instruction = str_ireplace( '[/timer]', '', $instruction );

                    $new_instructions[] = array(
                        'description' => $instruction,
                        'group' => $instruction_group,
                        'image' => '',
                    );
                }
            }
        }

        // Update instructions
        update_post_meta( $post_id, 'recipe_instructions', $new_instructions );


        // Servings
        $cooked_servings = get_post_meta($post_id, '_cp_recipe_yields', true);

        $servings = '';
        $servings_type = '';
        if( $cooked_servings ) {
            $match = preg_match( "/^\s*\d+/", $cooked_servings, $servings_array );
            if( $match === 1 ) {
                $servings = str_replace( ' ','', $servings_array[0] );
            }

            $servings_type = preg_replace( "/^\s*\d+\s*/", "", $cooked_servings );
        }

        update_post_meta( $post_id, 'recipe_servings', $servings );
        update_post_meta( $post_id, 'recipe_servings_type', $servings_type );

        $normalized_servings = WPUltimateRecipe::get()->helper( 'recipe_save' )->normalize_servings( $servings );
        update_post_meta( $post_id, 'recipe_servings_normalized', $normalized_servings );


        // Cooking Times
        $prep_time = get_post_meta($post_id, '_cp_recipe_prep_time', true);
        $cook_time = get_post_meta($post_id, '_cp_recipe_cook_time', true);

        if( $prep_time && $prep_time != 0 ) {
            update_post_meta( $post_id, 'recipe_prep_time', $prep_time );
            update_post_meta( $post_id, 'recipe_prep_time_text', __( 'minutes', 'wp-ultimate-recipe' ) );
        }

        if( $cook_time && $cook_time != 0 ) {
            update_post_meta( $post_id, 'recipe_cook_time', $cook_time );
            update_post_meta( $post_id, 'recipe_cook_time_text', __( 'minutes', 'wp-ultimate-recipe' ) );
        }

        update_post_meta( $post_id, 'recipe_passive_time', '' );
        update_post_meta( $post_id, 'recipe_passive_time_text', '' );


        // Nutritional information
        $nutritional_mapping = array(
            '_cp_recipe_nutrition_servingsize'          => 'serving_size',
            '_cp_recipe_nutrition_calories'             => 'calories',
            '_cp_recipe_nutrition_carbs'         => 'carbohydrate',
            '_cp_recipe_nutrition_protein'               => 'protein',
            '_cp_recipe_nutrition_fat'                   => 'fat',
            '_cp_recipe_nutrition_satfat'          => 'saturated_fat',
            '_cp_recipe_nutrition_polyunsatfat'        => 'polyunsaturated_fat',
            '_cp_recipe_nutrition_monounsatfat'        => 'monounsaturated_fat',
            '_cp_recipe_nutrition_transfat'              => 'trans_fat',
            '_cp_recipe_nutrition_cholesterol'           => 'cholesterol',
            '_cp_recipe_nutrition_sodium'                => 'sodium',
            '_cp_recipe_nutrition_potassium'                => 'potassium',
            '_cp_recipe_nutrition_fiber'                 => 'fiber',
            '_cp_recipe_nutrition_sugar'                 => 'sugar',
        );

        $nutritional = array();
        foreach( $nutritional_mapping as $cooked_field => $wpurp_field ) {
            $cooked_value = get_post_meta( $post_id, $cooked_field, true );

            if( $cooked_value ) {
                $value = trim( $cooked_value );
                $nutritional[$wpurp_field] = floatval( $value ) > 0 ? strval( floatval( $value ) ) : '';
            }
        }
        add_post_meta( $post_id, 'recipe_nutritional', $nutritional );


        // Difficulty
        switch( get_post_meta( $post_id, '_cp_recipe_difficulty_level', true ) ) {
            case 1:
                $difficulty = __( 'Difficulty Level: Easy', 'cooked' );
                break;
            case 2:
                $difficulty = __( 'Difficulty Level: Intermediate', 'cooked' );
                break;
            case 3:
                $difficulty = __( 'Difficulty Level: Advanced', 'cooked' );
                break;
            default:
                $difficulty = '';
        }

        update_post_meta( $post_id, 'cooked_difficulty', $difficulty );


        // Tags
        $import_tags = array();
        if( isset( $_POST['cooked_tag_category'] ) && taxonomy_exists( $_POST['cooked_tag_category'] ) ) {
            $import_tags['cp_recipe_category'] = $_POST['cooked_tag_category'];
        }
        if( isset( $_POST['cooked_tag_tags'] ) && taxonomy_exists( $_POST['cooked_tag_tags'] ) ) {
            $import_tags['cp_recipe_tags'] = $_POST['cooked_tag_tags'];
        }
        if( isset( $_POST['cooked_tag_cuisine'] ) && taxonomy_exists( $_POST['cooked_tag_cuisine'] ) ) {
            $import_tags['cp_recipe_cuisine'] = $_POST['cooked_tag_cuisine'];
        }
        if( isset( $_POST['cooked_tag_method'] ) && taxonomy_exists( $_POST['cooked_tag_method'] ) ) {
            $import_tags['cp_recipe_cooking_method'] = $_POST['cooked_tag_method'];
        }

        foreach( $import_tags as $cooked_tag => $new_tag ) {
            $terms = get_the_terms( $post_id, $cooked_tag );

            if( $terms !== false && !is_wp_error( $terms ) )
            {
                $term_ids = array();
                foreach( $terms as $term )
                {
                    $existing_term = term_exists( $term->name, $new_tag );

                    if ( $existing_term == 0 || $existing_term == null ) {
                        $new_term = wp_insert_term(
                            $term->name,
                            $new_tag,
                            array(
                                'description' => $term->description,
                                'slug' => $term->slug,
                                'parent' => $term->parent,
                            )
                        );

                        $term_ids[] = (int)$new_term['term_id'];
                    } else {
                        $term_ids[] = (int)$existing_term['term_id'];
                    }
                }

                wp_set_object_terms( $post_id, $term_ids, $new_tag );
            }
        }


        // Other metadata
        update_post_meta( $post_id, 'recipe_title', get_the_title( $post_id ) );
        update_post_meta( $post_id, 'recipe_description', get_post_meta( $post_id, '_cp_recipe_short_description', true ) );
        update_post_meta( $post_id, 'recipe_rating', get_post_meta( $post_id, '_cp_recipe_admin_rating', true ) );
        update_post_meta( $post_id, 'recipe_notes', get_post_meta( $post_id, '_cp_recipe_additional_notes', true ) );

        update_post_meta( $post_id, 'cooked_video', get_post_meta( $post_id, '_cp_recipe_external_video', true ) );

        // Switch post type to recipe
        set_post_type( $post_id, 'recipe' );


        // Excerpt
        $cooked_excerpt = get_post_meta( $post_id, '_cp_recipe_excerpt', true );
        if ( !$cooked_excerpt ) {
            $cooked_excerpt = get_post_meta( $post_id, '_cp_recipe_short_description', true );
        }

        if( $cooked_excerpt ) {
            $update_excerpt = array(
                'ID' => $post_id,
                'post_excerpt' => $cooked_excerpt,
            );
            wp_update_post( $update_excerpt );
        }


        // Post Content
        $update_content = array(
            'ID' => $post_id,
            'post_content' => '[recipe]',
        );
        wp_update_post( $update_content );


        // Update recipe terms
        WPUltimateRecipe::get()->helper( 'recipe_save' )->update_recipe_terms( $post_id );
    }

    private function get_cooked_recipes()
    {
        $import_cooked = array(
            'total' => 0,
            'import' => array(

            ),
        );

        // Loop through all posts
        $limit = 100;
        $offset = 0;
        $total = 0;

        while(true) {
            $args = array(
                'post_type' => 'cp_recipe',
                'post_status' => 'any',
                'orderby' => 'ID',
                'order' => WPUltimateRecipe::option( 'import_recipes_order', 'ASC' ),
                'posts_per_page' => $limit,
                'offset' => $offset,
            );

            $query = new WP_Query( $args );

            if ( !$query->have_posts() ) break;

            $posts = $query->posts;

            foreach( $posts as $post ) {
                $cooked_ingredients = get_post_meta( $post->ID, '_cp_recipe_detailed_ingredients', true );

                if( empty( $cooked_ingredients ) ) {
                    $import_cooked['import'][] = $post->ID;
                } else {
                    $import_cooked['auto_import'][] = $post->ID;
                }

                $total++;
                wp_cache_delete( $post->ID, 'posts' );
                wp_cache_delete( $post->ID, 'post_meta' );
            }

            $offset += $limit;
            wp_cache_flush();
        }

        $import_cooked['total'] = $total;

        return $import_cooked;
    }

    private function custom_fields()
    {
        $key = 'cooked_difficulty';
        $name = __( 'Difficulty', 'wp-ultimate-recipe' );

        $custom_fields = WPUltimateRecipe::addon( 'custom-fields' )->get_custom_fields();

        if( !array_key_exists( $key, $custom_fields ) ) {
            $custom_fields[$key] = array(
                'key' => $key,
                'name' => $name,
            );

            WPUltimateRecipe::addon( 'custom-fields' )->update_custom_fields( $custom_fields );
        }

        $key = 'cooked_video';
        $name = __( 'Video', 'wp-ultimate-recipe' );

        if( !array_key_exists( $key, $custom_fields ) ) {
            $custom_fields[$key] = array(
                'key' => $key,
                'name' => $name,
            );

            WPUltimateRecipe::addon( 'custom-fields' )->update_custom_fields( $custom_fields );
        }
    }
}

WPUltimateRecipe::loaded_addon( 'import-cooked', new WPURP_Import_Cooked() );