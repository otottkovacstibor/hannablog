<?php

class WPURP_Import_Xml extends WPURP_Premium_Addon {

    public function __construct( $name = 'import-xml' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'import_menu' ) );
        add_action( 'admin_menu', array( $this, 'import_manual_menu' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/import_xml.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_import_xml',
            )
        );
    }

    public function import_menu() {
        add_submenu_page( null, __( 'Import XML', 'wp-ultimate-recipe' ), __( 'Import XML', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_xml', array( $this, 'import_page' ) );
    }

    public function import_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function import_manual_menu() {
        add_submenu_page( null, __( 'Import XML', 'wp-ultimate-recipe' ), __( 'Import XML', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_import_xml_manual', array( $this, 'import_manual_page' ) );
    }

    public function import_manual_page() {
        if ( !wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' ) ) {
            die( 'Invalid nonce.' );
        }

        $recipes = simplexml_load_file( $_FILES['xml']['tmp_name'] );

        echo '<h2>Recipes Imported</h2>';

        $i = 1;
        foreach( $recipes as $recipe ) {
            $this->import_xml_recipe( $recipe, $i );
            $i++;
        }

        if( $i == 1 ) {
            echo 'No recipes found';
        }
    }

    public function import_xml_recipe( $xml_recipe, $recipe_number ) {
        $title = isset ( $xml_recipe->title ) ? (string) $xml_recipe->title : '';
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
        $_POST['recipe_description']        = (string) $xml_recipe->description;
        $_POST['recipe_rating']             = isset( $xml_recipe->rating ) ? (string) $xml_recipe->rating->attributes()->stars : '';
        $_POST['recipe_servings']           = isset( $xml_recipe->servings ) ? (string) $xml_recipe->servings->attributes()->quantity : '';
        $_POST['recipe_servings_type']      = isset( $xml_recipe->servings ) ? (string) $xml_recipe->servings->attributes()->unit : '';
        $_POST['recipe_prep_time']          = isset( $xml_recipe->prepTime ) ? (string) $xml_recipe->prepTime->attributes()->quantity : '';
        $_POST['recipe_prep_time_text']     = isset( $xml_recipe->prepTime ) ? (string) $xml_recipe->prepTime->attributes()->unit : '';
        $_POST['recipe_cook_time']          = isset( $xml_recipe->cookTime ) ? (string) $xml_recipe->cookTime->attributes()->quantity : '';
        $_POST['recipe_cook_time_text']     = isset( $xml_recipe->cookTime ) ? (string) $xml_recipe->cookTime->attributes()->unit : '';
        $_POST['recipe_passive_time']       = isset( $xml_recipe->passiveTime ) ? (string) $xml_recipe->passiveTime->attributes()->quantity : '';
        $_POST['recipe_passive_time_text']  = isset( $xml_recipe->passiveTime ) ? (string) $xml_recipe->passiveTime->attributes()->unit : '';
        $_POST['recipe_notes']              = (string) $xml_recipe->notes;

        $ingredients = array();
        foreach( $xml_recipe->ingredients as $ingredient_group ) {
            $group = (string) $ingredient_group->attributes()->group;

            foreach( $ingredient_group->ingredient as $ingredient ) {
                $ingredients[] = array(
                    'ingredient' => (string) $ingredient->attributes()->name,
                    'amount' => (string) $ingredient->attributes()->quantity,
                    'unit' => (string) $ingredient->attributes()->unit,
                    'notes' => (string) $ingredient->attributes()->notes,
                    'group' => $group,
                );
            }
        }
        $_POST['recipe_ingredients'] = $ingredients;

        $instructions = array();
        foreach( $xml_recipe->instructions as $instruction_group ) {
            $group = (string) $instruction_group->attributes()->group;

            foreach( $instruction_group->instruction as $instruction ) {
                $instructions[] = array(
                    'description' => (string) $instruction,
                    'image' => '',
                    'group' => $group,
                );
            }
        }
        $_POST['recipe_instructions'] = $instructions;

        $post_id = wp_insert_post($post);

        // Recipe image
        $recipe_image_url = (string) $xml_recipe->imageUrl;

        if( $recipe_image_url ) {
            $recipe_image_id = $this->get_or_upload_attachment( $post_id, $recipe_image_url );

            if( $recipe_image_id ) {
                set_post_thumbnail( $post_id, $recipe_image_id );
            }
        }

        // Alternate image
        $alternate_image_url = (string) $xml_recipe->alternateImageUrl;

        if( $alternate_image_url ) {
            $alternate_image_id = $this->get_or_upload_attachment( $post_id, $alternate_image_url );

            if( $alternate_image_id ) {
                $_POST['recipe_alternate_image'] = $alternate_image_id;
            }
        }

        // Taxonomies
        foreach( $xml_recipe->taxonomy as $xml_taxonomy ) {
            $taxonomy = (string) $xml_taxonomy->attributes()->name;

            if( taxonomy_exists( $taxonomy ) ) {
                $terms = array();
                foreach( $xml_taxonomy->term as $xml_term ) {
                    $term_string = (string) $xml_term;

                    if( $taxonomy !== 'post_tag' ) {
                        $term = term_exists( $term_string, $taxonomy );

                        if ( $term === 0 || $term === null ) {
                            $term = wp_insert_term( $term_string, $taxonomy );
                        }

                        $term_id = intval( $term['term_id'] );

                        $terms[] = $term_id;
                    } else {
                        $terms[] = $term_string;
                    }
                }

                wp_set_post_terms( $post_id, $terms, $taxonomy );
            }
        }

        // Custom Fields
        $fields = array();
        $custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
        if( $custom_fields_addon ) {
            $custom_fields = $custom_fields_addon->get_custom_fields();

            foreach( $custom_fields as $key => $custom_field ) {
                $fields[] = $key;
            }
        }

        foreach( $xml_recipe->customField as $custom_field ) {
            $key = (string) $custom_field->attributes()->key;

            if( in_array( $key, $fields ) ) {
                update_post_meta( $post_id, $key, (string) $custom_field->attributes()->value );
            }
        }

        //Nutrition
        $nutrition_addon = WPUltimateRecipe::addon( 'nutritional-information' );
        if( $nutrition_addon && isset( $xml_recipe->nutrition ) ) {
            $nutrition_fields = $nutrition_addon->fields;

            $recipe_nutritional = array();

            foreach( $nutrition_fields as $field => $unit ) {
                if( isset( $xml_recipe->nutrition->attributes()->$field ) ) {
                    $recipe_nutritional[$field] = (string) $xml_recipe->nutrition->attributes()->$field;
                }
            }

            update_post_meta( $post_id, 'recipe_nutritional', $recipe_nutritional );
        }

        echo $recipe_number . '. <a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . $title . '</a><br/>';
    }

    /**
     * Helper functions
     */
    private function get_or_upload_attachment( $post_id, $url ) {
        $image_id = $this->get_attachment_id_from_url( $url );

        if( $image_id ) {
            return $image_id;
        } else {
            $media = media_sideload_image( $url, $post_id );

            $attachments = get_posts( array(
                    'numberposts' => '1',
                    'post_parent' => $post_id,
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                )
            );

            if( sizeof( $attachments ) > 0 ) {
                return $attachments[0]->ID;
            }
        }

        return null;
    }

    /*
     * Source: https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
     */
    private function get_attachment_id_from_url( $attachment_url = '' ) {

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
}

WPUltimateRecipe::loaded_addon( 'import-xml', new WPURP_Import_Xml() );