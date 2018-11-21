<?php

class WPURP_Recipe_Cloner {

    private $fields_to_clone = array(
        'recipe_title',
        'recipe_description',
        'recipe_rating',
        'recipe_servings',
        'recipe_servings_normalized',
        'recipe_servings_type',
        'recipe_prep_time',
        'recipe_prep_time_text',
        'recipe_cook_time',
        'recipe_cook_time_text',
        'recipe_passive_time',
        'recipe_passive_time_text',
        'recipe_instructions',
        'recipe_notes',
    );

    public function __construct()
    {
        add_action( 'init', array( $this, 'assets' ) );

        add_action( 'wp_ajax_clone_recipe', array( $this, 'ajax_clone_recipe' ) );
        add_action( 'wp_ajax_nopriv_clone_recipe', array( $this, 'ajax_clone_recipe' ) );
    }

    public function assets()
    {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => '/js/recipe_cloner.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_posts',
                'deps' => array(
                    'jquery',
                ),
                'data' => array(
                    'name' => 'wpurp_recipe_cloner',
                    'ajax_url' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'clone_recipe' )
                )
            )
        );
    }

    public function ajax_clone_recipe()
    {
        $recipe_id = intval( $_POST['recipe'] );

        if( check_ajax_referer( 'clone_recipe', 'security', false ) && 'recipe' == get_post_type( $recipe_id ) )
        {
            $recipe = new WPURP_Recipe( $recipe_id );

            $post = array(
                'post_title' => $recipe->title(),
                'post_type'	=> 'recipe',
                'post_status' => 'draft',
                'post_author' => get_current_user_id(),
            );

            // Necessary to set the post terms correctly in recipe_save.
            $_POST['recipe_ingredients'] = get_post_meta( $recipe->ID(), 'recipe_ingredients', true );

            $post_id = wp_insert_post($post);

            foreach( $this->fields_to_clone as $field ) {
                $val = get_post_meta( $recipe->ID(), $field, true );
                update_post_meta( $post_id, $field, $val );
            }

            $url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
            echo json_encode( array( 'redirect' => $url ) );
        }
        die();
    }
}