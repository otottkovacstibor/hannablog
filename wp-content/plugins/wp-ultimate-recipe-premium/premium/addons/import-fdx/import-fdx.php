<?php

class WPURP_Import_Fdx extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-fdx' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_menu' ) );
        add_action( 'admin_menu', array( $this, 'import_manual_menu' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_fdx.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_fdx',
            )
        );
    }

    public function import_menu() {
        add_submenu_page( null, __( 'Import FDX', 'wp-ultimate-recipe' ), __( 'Import FDX', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_fdx', array( $this, 'import_page' ) );
    }

    public function import_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function import_manual_menu() {
        add_submenu_page( null, __( 'Import FDX', 'wp-ultimate-recipe' ), __( 'Import FDX', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_fdx_manual', array( $this, 'import_manual_page' ) );
    }

    public function import_manual_page() {
        if ( !wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' ) ) {
            die( 'Invalid nonce.' );
        }

        $fdx = simplexml_load_file( $_FILES['fdx']['tmp_name'] );

        echo '<h2>Recipes Imported</h2>';

        $i = 1;
        foreach( $fdx->Recipes->Recipe as $recipe ) {
            $this->import_fdx_recipe( $recipe, $i );
            $i++;
        }

        if( $i == 1 ) {
            echo 'No recipes found';
        }
    }

    public function import_fdx_recipe( $fdx_recipe, $recipe_number ) {
        $title = isset ( $fdx_recipe['Name'] ) ? (string) $fdx_recipe['Name'] : '';

        if( $title == '' ) {
            $title = __( 'Untitled', 'wp-ultimate-recipe' );
        }

        $post = array(
            'post_title' => $title,
            'post_type'	=> 'recipe',
            'post_status' => 'draft',
            'post_author' => get_current_user_id(),
        );

        $_POST['recipe_title']              = $title;
        $_POST['recipe_description']        = '';
        $_POST['recipe_rating']             = '';
        $_POST['recipe_servings']           = isset( $fdx_recipe['Servings'] )          ? (string) $fdx_recipe['Servings']          : '';
        $_POST['recipe_servings_type']      = '';
        $_POST['recipe_prep_time']          = isset( $fdx_recipe['PreparationTime'] )   ? (string) $fdx_recipe['PreparationTime']   : '';
        $_POST['recipe_prep_time_text']     = isset( $fdx_recipe['PreparationTime'] )   ? __( 'minutes', 'wp-ultimate-recipe' )     : '';
        $_POST['recipe_cook_time']          = isset( $fdx_recipe['CookingTime'] )       ? (string) $fdx_recipe['CookingTime']       : '';
        $_POST['recipe_cook_time_text']     = isset( $fdx_recipe['CookingTime'] )       ? __( 'minutes', 'wp-ultimate-recipe' )     : '';
        $_POST['recipe_passive_time']       = isset( $fdx_recipe['InactiveTime'] )      ? (string) $fdx_recipe['InactiveTime']      : '';
        $_POST['recipe_passive_time_text']  = isset( $fdx_recipe['InactiveTime'] )      ? __( 'minutes', 'wp-ultimate-recipe' )     : '';
        $_POST['recipe_notes']              = isset( $fdx_recipe['Comments'] )          ? (string) $fdx_recipe['Comments']          : '';

        $group = '';
        $ingredients = array();
        foreach( $fdx_recipe->RecipeIngredients->RecipeIngredient as $ingredient ) {
            if( isset( $ingredient['Heading'] ) && (string) $ingredient['Heading'] == 'Y' ) {
                $group = (string) $ingredient['Ingredient'];
            } else {
                $raw_ingredient = (string) $ingredient['Ingredient'];
                $raw_ingredient_parts = explode( ',', $raw_ingredient, 2);

                $ingredients[] = array(
                    'ingredient' => trim( $raw_ingredient_parts[0] ),
                    'amount' => (string) $ingredient['Quantity'],
                    'unit' => (string) $ingredient['Unit'],
                    'notes' => isset( $raw_ingredient_parts[1] ) ? trim( $raw_ingredient_parts[1] ) : '',
                    'group' => $group,
                );
            }
        }
        $_POST['recipe_ingredients'] = $ingredients;

        $group = '';
        $instructions = array();
        foreach( $fdx_recipe->RecipeProcedures->RecipeProcedure as $instruction ) {
            if( isset( $instruction['Heading'] ) && (string) $instruction['Heading'] == 'Y' ) {
                $group = (string) $instruction->ProcedureText;
            } else {

                $image = '';
                if( isset( $instruction->ProcedureImage ) ) {
                    $image = strval( $this->upload_base64_image( $title . ' - Step ' . ( count( $instructions ) + 1 ), $instruction->ProcedureImage ) );
                }

                $instructions[] = array(
                    'description' => (string) $instruction->ProcedureText,
                    'image' => $image,
                    'group' => $group,
                );
            }
        }
        $_POST['recipe_instructions'] = $instructions;

        $post_id = wp_insert_post($post);

        // Recipe image
        if( isset( $fdx_recipe->RecipeImage ) ) {
            $image = $this->upload_base64_image( $title, $fdx_recipe->RecipeImage );
            set_post_thumbnail( $post_id, $image );
        }

        //Nutrition
        if( isset( $fdx_recipe->RecipeNutrition ) ) {
            $nutrition_data = $fdx_recipe->RecipeNutrition;
            $serving_size = intval( (string) $nutrition_data['ServingSize'] );

            $nutritional_mapping = array(
                'Calories'              => 'calories',
                'TotalCarbohydrate'     => 'carbohydrate',
                'Protein'               => 'protein',
                'TotalFat'              => 'fat',
                'SaturatedFat'          => 'saturated_fat',
                'PolyunsaturatedFat'    => 'polyunsaturated_fat',
                'MonounsaturatedFat'    => 'monounsaturated_fat',
                'TransFattyAcids'       => 'trans_fat',
                'Cholesterol'           => 'cholesterol',
                'Sodium'                => 'sodium',
                'Fiber'                 => 'fiber',
                'Sugar'                 => 'sugar',
//                'VitaminA'              => 'vitamin_a',
//                'VitaminC'              => 'vitamin_c',
//                'Calcium'               => 'calcium',
//                'Iron'                  => 'iron',
            );

            $nutritional = array();
            foreach( $nutritional_mapping as $fdx_field => $wpurp_field ) {

                if( isset( $nutrition_data[$fdx_field] ) ) {
                    $amount = floatval( (string) $nutrition_data[$fdx_field] );
                    $amount = $amount / $serving_size;
                    $nutritional[$wpurp_field] = $amount > 0 ? strval( $amount ) : '';
                }
            }
            add_post_meta( $post_id, 'recipe_nutritional', $nutritional );
        }

        echo $recipe_number . '. <a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . $title . '</a><br/>';
    }

    /**
     * Helper functions
     */

    // Source: https://gist.github.com/tjhole/3ddfc6cbf6da01c7ce0f
    public function upload_base64_image( $title, $item ) {
        $upload_dir = wp_upload_dir();
        $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

        $decoded = base64_decode( (string) $item );
        $filename = $title . '.' . strtolower( $item['FileType'] );
        $image_upload = file_put_contents( $upload_path . $filename, $decoded );

        if( !function_exists( 'wp_handle_sideload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        if( !function_exists( 'wp_get_current_user' ) ) require_once( ABSPATH . 'wp-includes/pluggable.php' );

        $file             = array();
        $file['error']    = '';
        $file['tmp_name'] = $upload_path . $filename;
        $file['name']     = $filename;
        $file['type']     = 'image/' . strtolower( $item['FileType'] );
        $file['size']     = filesize( $upload_path . $filename );

        $file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );

        $filename = $file_return['file'];
        $attachment = array(
            'post_mime_type' => $file_return['type'],
            'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $upload_dir['url'] . '/' . basename($filename)
        );

        $attach_id = wp_insert_attachment( $attachment, $filename, 0 );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}

WPUltimateRecipe::loaded_addon( 'import-fdx', new WPURP_Import_Fdx() );