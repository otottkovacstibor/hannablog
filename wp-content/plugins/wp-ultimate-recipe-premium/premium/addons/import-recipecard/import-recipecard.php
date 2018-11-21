<?php

class WPURP_Import_Recipecard extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-recipecard' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_menu' ) );
        add_action( 'admin_menu', array( $this, 'import_manual_menu' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_recipecard.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_recipecard',
            )
        );
    }

    public function import_menu() {
        add_submenu_page( null, __( 'Import RecipeCard', 'wp-ultimate-recipe' ), __( 'Import RecipeCard', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_recipecard', array( $this, 'import_page' ) );
    }

    public function import_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function import_manual_menu() {
        add_submenu_page( null, __( 'Import RecipeCard', 'wp-ultimate-recipe' ), __( 'Import RecipeCard', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_recipecard_manual', array( $this, 'import_manual_page' ) );
    }

    public function import_manual_page() {
        if ( !wp_verify_nonce( $_POST['import_recipecard_manual'], 'import_recipecard_manual' ) ) {
            die( 'Invalid nonce.' );
        }

        // Actually import recipe
        if( isset( $_POST['import_recipecard_id'] ) && isset( $_POST['import_post_id'] )) {

            $post_id = intval( $_POST['import_post_id'] );
            $recipecard_id = intval( $_POST['import_recipecard_id'] );

            $this->import_recipecard_recipe( $post_id, $recipecard_id );
        }

        $this->custom_fields();
        require( $this->addonDir. '/templates/manual_import.php' );
    }

    private function import_recipecard_recipe( $post_id, $recipecard_id )
    {
        $recipecard = $this->get_recipecard_recipe( $recipecard_id );

        // Recipe image
        $recipe_image_url = $recipecard->image;

        if( $recipe_image_url ) {
            $recipe_image_id = $this->get_attachment_id_from_url( $recipe_image_url );

            if( $recipe_image_id ) {
                set_post_thumbnail( $post_id, $recipe_image_id );
            }
        }

        // Ingredient groups
        $ingredient_groups = array();
        foreach( $recipecard->ingredients as $ingredient_group ) {
            foreach( $ingredient_group->lines as $ingredient_line ) {
                $ingredient_groups[] = $ingredient_group->title;
            }
        }

        // Ingredients
        $ingredients = $_POST['recipe_ingredients'];
        $new_ingredients = array();
        $ingredient_terms = array();

        if( $ingredients )
        {
            $i = 0;
            foreach( $ingredients as $ingredient )
            {
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
                        $ingredient['group'] = $ingredient_groups[$i];
                        $i++;

                        $new_ingredients[] = $ingredient;
                        $ingredient_terms[] = $term_id;
                    }
                }
            }
            wp_set_post_terms( $post_id, $ingredient_terms, 'ingredient' );
        }
        update_post_meta( $post_id, 'recipe_ingredients', $new_ingredients );

        // Instructions
        if( isset( $recipecard->directions ) && is_array( $recipecard->directions ) ) {
            $instructions = array();
            foreach( $recipecard->directions as $instruction_group ) {
                foreach( $instruction_group->lines as $instruction_line ) {
                    $instructions[] = array(
                        'description' => $instruction_line,
                        'group' => $instruction_group->title,
                        'image' => '',
                    );
                }
            }
            update_post_meta( $post_id, 'recipe_instructions', $instructions );
        }

        // Cooking Times
        $prep_time = intval( $recipecard->prepTime );
        $cook_time = intval( $recipecard->cookTime );
        $total_time = intval( $recipecard->totalTime );

        if( $prep_time != 0 ) {
            update_post_meta( $post_id, 'recipe_prep_time', $prep_time );
            update_post_meta( $post_id, 'recipe_prep_time_text', __( 'minutes', 'wp-ultimate-recipe' ) );
        }

        if( $cook_time != 0 ) {
            update_post_meta( $post_id, 'recipe_cook_time', $cook_time );
            update_post_meta( $post_id, 'recipe_cook_time_text', __( 'minutes', 'wp-ultimate-recipe' ) );
        }

        if( $total_time != 0 ) {
            $passive_time = $total_time - ( $prep_time + $cook_time );

            if( $passive_time > 0 ) {
                update_post_meta( $post_id, 'recipe_passive_time', $passive_time );
                update_post_meta( $post_id, 'recipe_passive_time_text', __( 'minutes', 'wp-ultimate-recipe' ) );
            }
        }

        // Recipe Notes
        if( isset( $recipecard->notes ) && is_array( $recipecard->notes ) ) {
            $notes = '';

            foreach( $recipecard->notes as $notes_group ) {
                if( $notes_group->title ) {
                    if( $notes ) {
                        $notes .= '<br/>';
                    }
                    $notes .= $notes_group->title . ':<br/>';
                }

                foreach( $notes_group->lines as $notes_line ) {
                    $notes .= $notes_line . '<br/>';
                }
            }
            update_post_meta( $post_id, 'recipe_notes', $notes );
        }


        // Servings
        update_post_meta( $post_id, 'recipe_servings', $recipecard->servings );
        update_post_meta( $post_id, 'recipe_servings_type', '' );

        $normalized_servings = WPUltimateRecipe::get()->helper( 'recipe_save' )->normalize_servings( $recipecard->servings );
        update_post_meta( $post_id, 'recipe_servings_normalized', $normalized_servings );

        // Other metadata
        update_post_meta( $post_id, 'recipe_title', $recipecard->title );
        update_post_meta( $post_id, 'recipe_description', $recipecard->summary );

        update_post_meta( $post_id, 'recipecard_author', $recipecard->author );
        update_post_meta( $post_id, 'recipecard_adapted', $recipecard->adapted );
        update_post_meta( $post_id, 'recipecard_adapted_link', $recipecard->adaptedLink );
        update_post_meta( $post_id, 'recipecard_yield', $recipecard->yields );

        // Backup to remember which recipecard recipe this originated from
        update_post_meta( $post_id, 'recipe_recipecard_id', $recipecard_id );


        // Switch post type to recipe
        set_post_type( $post_id, 'recipe' );

        // Add [recipe] shortcode instead of recipecard one
        $post = get_post( $post_id );

        $update_content = array(
            'ID' => $post_id,
            'post_content' => preg_replace("/\[yumprint-recipe\s+id=\'(\d+)\'\s*]/i", "[recipe]", $post->post_content),
        );
        wp_update_post( $update_content );

        // Update recipe terms
        WPUltimateRecipe::get()->helper( 'recipe_save' )->update_recipe_terms( $post_id );
    }

    private function get_recipecard_recipes()
    {
        $import_recipecard = array(
            'total' => 0,
            'import' => array(

            ),
            'problem' => array(

            ),
        );

        // Loop through all posts
        $limit = 100;
        $offset = 0;
        $total = 0;

        while(true) {
            $args = array(
                'post_type' => array( 'post', 'page'),
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
                $recipes = $this->get_recipes_from_content( $post->post_content );

                if( count( $recipes ) == 1 ) {
                    $total++;
                    $import_recipecard['import'][$post->ID] = $recipes[0];
                } else if( count( $recipes ) != 0 ) {
                    $import_recipecard['problem'][$post->ID] = $recipes;
                }

                wp_cache_delete( $post->ID, 'posts' );
                wp_cache_delete( $post->ID, 'post_meta' );
            }

            $offset += $limit;
            wp_cache_flush();
        }

        $import_recipecard['total'] = $total;

        return $import_recipecard;
    }

    private function get_recipecard_recipe( $recipeId )
    {
        global $wpdb;

        $recipe_table_name = $wpdb->prefix . "yumprint_recipe_recipe";
        $recipe_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $recipe_table_name WHERE id=%d", intval( $recipeId ) ) );
        if( empty( $recipe_row ) ) {
            return false;
        }

        $recipe = json_decode( $recipe_row->recipe );

        return $recipe;
    }

    private function custom_fields()
    {
        $fields = array(
            'recipecard_author' => __( 'Author', 'wp-ultimate-recipe' ),
            'recipecard_adapted' => __( 'Adapted', 'wp-ultimate-recipe' ),
            'recipecard_adapted_link' => __( 'Adapted Link', 'wp-ultimate-recipe' ),
            'recipecard_yield' => __( 'Yield', 'wp-ultimate-recipe' ),
        );

        $custom_fields = WPUltimateRecipe::addon( 'custom-fields' )->get_custom_fields();

        foreach( $fields as $key => $name ) {
            if ( !array_key_exists( $key, $custom_fields ) ) {
                $custom_fields[$key] = array(
                    'key' => $key,
                    'name' => $name,
                );
            }
        }

        WPUltimateRecipe::addon( 'custom-fields' )->update_custom_fields( $custom_fields );
    }

    /*
     * Source: https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
     */
    function get_attachment_id_from_url( $attachment_url = '' ) {

        global $wpdb;
        $attachment_id = false;

        // If there is no url, return.
        if ( '' == $attachment_url )
            return;

        // Get the upload directory paths
        $upload_dir_paths = wp_upload_dir();

        // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
        if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

            // If this is the URL of an auto-generated thumbnail, get the URL of the original image
            $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

        }

        return $attachment_id;
    }

    /*
     * Helper functions
     */

    private function get_recipes_from_content( $content )
    {
        preg_match_all("/\[yumprint-recipe\s+id=\'(\d+)\'\s*]/i", $content, $output);

        return array_unique( $output[1] );
    }
}

WPUltimateRecipe::loaded_addon( 'import-recipecard', new WPURP_Import_Recipecard() );