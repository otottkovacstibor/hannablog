<?php

class WPURP_Search {

    public function __construct()
    {
        add_filter( 'wp_insert_post_data', array( $this, 'save' ), 10, 2 );
        add_filter( 'content_edit_pre', array( $this, 'post_content_edit_page' ) );
        add_filter( 'rest_prepare_recipe', array( $this, 'post_content_gutenberg_edit_page' ), 10, 3 );
        add_filter( 'the_content', array( $this, 'youtube_fix' ), 1 );
        add_filter( 'mce_external_plugins', array( $this, 'tinymce_shortcode_plugin' ) );

        add_shortcode( 'wpurp-searchable-recipe', array( $this, 'shortcode' ) );
    }

    public function youtube_fix( $content ) {
        $content = str_replace( '[wpurp-searchable-recipe]', "\n[wpurp-searchable-recipe]", $content );
        return $content;
    }

    /**
     * Handles saving of recipes
     */
    public function save( $data, $postarr )
    {
        if ( 'recipe' === $data['post_type'] && 'auto-draft' !== $data['post_status'] ) {
            if ( !isset( $postarr['recipe_meta_box_nonce'] ) || !wp_verify_nonce( $postarr['recipe_meta_box_nonce'], 'recipe' ) )
            {
                return $data;
            }

            $searchable_recipe = $postarr['recipe_title'];

            $searchable_recipe .= ' - ';
            $searchable_recipe .= $postarr['recipe_description'];
            $searchable_recipe .= ' - ';

            // Ingredients.
            $previous_group = null;
            foreach( $postarr['recipe_ingredients'] as $ingredient ) {
                $group = isset( $ingredient['group'] ) ? $ingredient['group'] : '';

                if( $group !== $previous_group && $group ) {
                    $searchable_recipe .= $group . ': ';
                    $previous_group = $group;
                }

                $searchable_recipe .= $ingredient['ingredient'];
                if( trim( $ingredient['notes'] ) !== '' ) {
                    $searchable_recipe .= ' (' . $ingredient['notes'] . ')';
                }
                $searchable_recipe .= ', ';
            }

            // Instructions.
            $previous_group = null;
            foreach( $postarr['recipe_instructions'] as $instruction ) {
                $group = isset( $instruction['group'] ) ? $instruction['group'] : '';

                if( $group !== $previous_group && $group ) {
                    $searchable_recipe .= $group . ': ';
                    $previous_group = $group;
                }

                $searchable_recipe .= $instruction['description'] . '; ';
            }

            $searchable_recipe .= ' - ';
            $searchable_recipe .= $postarr['recipe_notes'];

            // Taxonomies searchable.
            $taxonomies = WPUltimateRecipe::get()->tags();
            unset( $taxonomies['ingredient'] );
            $taxonomies['category'] = true;
            $taxonomies['post_tag'] = true;

            foreach( $taxonomies as $taxonomy => $options ) {
                $terms = get_the_terms( $postarr['post_ID'], $taxonomy );

                if (!is_wp_error($terms) && $terms) {
                    foreach ($terms as $term) {
                        $searchable_recipe .= ' - ';
                        $searchable_recipe .= $term->name;
                    }
                }
            }

            // Custom fields searchable.
            $custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
            if ( $custom_fields_addon ) {
                $custom_fields = $custom_fields_addon->get_custom_fields();

                foreach( $custom_fields as $key => $custom_field ) {
                    $searchable_recipe .= ' - ';
                    $searchable_recipe .= $postarr[ $key ];
                }
            }

            // Prevent shortcodes
            $searchable_recipe = str_replace( '[', '(', $searchable_recipe );
            $searchable_recipe = str_replace( ']', ')', $searchable_recipe );

            $post_content = preg_replace("/<div class=\"wpurp-searchable-recipe\"[^<]*<\/div>/", "", $data['post_content']); // Backwards compatibility
            $post_content = preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $post_content);
            $post_content .= '[wpurp-searchable-recipe]';
            $post_content .= esc_attr( $searchable_recipe );
            $post_content .= '[/wpurp-searchable-recipe]';

            $data['post_content'] = $post_content;

            update_post_meta( $postarr['post_ID'], 'wpurp_text_search_3', time() );
        }

        return $data;
    }

    public function post_content_edit_page( $content )
    {
        // Remove searchable recipe part
        $content = preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content);

        return $content;
    }

    public function post_content_gutenberg_edit_page( $response, $post, $request )
    {
        $params = $request->get_params();

		if ( isset( $params['context'] ) && 'edit' === $params['context'] ) {
			if ( isset( $response->data['content']['raw'] ) ) {
				$response->data['content']['raw'] = $this->post_content_edit_page( $response->data['content']['raw'] );
			}
		}
        return $response;
    }

    public function tinymce_shortcode_plugin( $plugin_array ) {
        $plugin_array['wpultimaterecipe'] = WPUltimateRecipe::get()->coreUrl . '/js/tinymce.js';
        return $plugin_array;
    }

    public function shortcode( $options )
    {
        // This is just to make sure the searchable part is not being output
    }
}