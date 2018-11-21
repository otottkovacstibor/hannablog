<?php

class WPURP_Export_Xml extends WPURP_Premium_Addon {

    public function __construct( $name = 'export-xml' ) {
        parent::__construct( $name );

        // Actions
        add_action( 'admin_init', array( $this, 'assets' ) );
        add_action( 'admin_menu', array( $this, 'export_menu' ) );
        add_action( 'admin_menu', array( $this, 'export_manual_menu' ) );

        // Ajax
        add_action( 'wp_ajax_export_xml_author', array( $this, 'ajax_author' ) );
        add_action( 'wp_ajax_export_xml_date', array( $this, 'ajax_date' ) );
    }

    public function assets() {
        WPUltimateRecipe::get()->helper( 'assets' )->add(
            array(
                'file' => $this->addonPath . '/css/export_xml.css',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_export_xml',
            ),
            array(
                'file' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
                'direct' => true,
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_export_xml',
            ),
            array(
                'file' => $this->addonPath . '/js/export_xml.js',
                'premium' => true,
                'admin' => true,
                'page' => 'recipe_page_wpurp_export_xml',
                'deps' => array(
                    'jquery',
                    'jquery-ui-datepicker',
                ),
                'data' => array(
                    'name' => 'wpurp_export_xml',
                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
                    'nonce' => wp_create_nonce( 'wpurp_export_xml' ),
                ),
            )
        );
    }

    public function ajax_author()
    {
        if( check_ajax_referer( 'wpurp_export_xml', 'security', false ) )
        {
            $author = intval( $_POST['author'] );

            $recipes = WPUltimateRecipe::get()->query()->post_status( 'any' )->author( $author )->ids_only()->get();
            echo json_encode( $recipes );
        }

        die();
    }

    public function ajax_date()
    {
        if( check_ajax_referer( 'wpurp_export_xml', 'security', false ) )
        {
            $date_from = isset( $_POST['date_from'] ) ? $_POST['date_from'] : '';
            $date_to = isset( $_POST['date_to'] ) ? $_POST['date_to'] : '';

            $date_from = date_parse( $date_from );
            $date_to = date_parse( $date_to );

            if( $date_from['error_count'] == 0 && $date_to['error_count'] == 0 ) {
                $recipes = WPUltimateRecipe::get()->query()->post_status( 'any' )->date_from( $date_from )->date_to( $date_to )->ids_only()->get();
            } elseif( $date_from['error_count'] == 0 ) {
                $recipes = WPUltimateRecipe::get()->query()->post_status( 'any' )->date_from( $date_from )->ids_only()->get();
            } elseif( $date_to['error_count'] == 0 ) {
                $recipes = WPUltimateRecipe::get()->query()->post_status( 'any' )->date_to( $date_to )->ids_only()->get();
            } else {
                $recipes = array();
            }

            echo json_encode( $recipes );
        }

        die();
    }

    public function export_menu() {
        add_submenu_page( null, __( 'Export XML', 'wp-ultimate-recipe' ), __( 'Export XML', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_export_xml', array( $this, 'export_page' ) );
    }

    public function export_page() {
        if ( !current_user_can('manage_options') ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        require( $this->addonDir. '/templates/before_importing.php' );
    }

    public function export_manual_menu() {
        add_submenu_page( null, __( 'Export XML', 'wp-ultimate-recipe' ), __( 'Export XML', 'wp-ultimate-recipe' ), 'manage_options', 'wpurp_export_xml_manual', array( $this, 'export_manual_page' ) );
    }

    public function export_manual_page() {
        if ( !wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' ) ) {
            die( 'Invalid nonce.' );
        }
        echo '<h2>XML Export</h2>';

        $recipes = $_POST['recipes'];
        if( count( $recipes ) == 0 ) {
            _e( "You haven't selected any recipes to export.", 'wp-ultimate-recipe' );
        } else {
            $xml_data = array(
                'name' => 'recipes',
            );

            foreach( $recipes as $recipe ) {
                $xml_data[] = $this->export_xml_recipe( intval( $recipe ) );
            }

            $doc = new DOMDocument();
            $child = $this->generate_xml_element( $doc, $xml_data );
            if ( $child ) {
                $doc->appendChild( $child );
            }
            $doc->formatOutput = true; // Add whitespace to make easier to read XML
            $xml = $doc->saveXML();

            echo '<form id="exportRecipes" action="' . $this->addonUrl . '/templates/download.php" method="post">';
            echo '<input type="hidden" name="exportRecipes" value="' . base64_encode( $xml ) . '"/>';
            submit_button( __( 'Download XML', 'wp-ultimate-recipe' ) );
            echo '</form>';
        }
    }

    public function export_xml_recipe( $recipe_id ) {
        $recipe = new WPURP_Recipe( $recipe_id );

        $xml = array(
            'name' => 'recipe',
            array(
                'name' => 'title',
                'value' => $recipe->title(),
            ),
        );

        if( $recipe->is_present( 'recipe_description' ) ) {
            $xml[] = array(
                'name' => 'description',
                'value' => $recipe->description(),
            );
        }

        if( $recipe->is_present( 'recipe_featured_image' ) ) {
            $xml[] = array(
                'name' => 'imageUrl',
                'value' => $recipe->featured_image_url( 'full' ),
            );
        }

        if( $recipe->is_present( 'recipe_alternate_image' ) ) {
            $xml[] = array(
                'name' => 'alternateImageUrl',
                'value' => $recipe->alternate_image_url( 'full' ),
            );
        }

        if( $recipe->is_present( 'recipe_rating_author' ) ) {
            $xml[] = array(
                'name' => 'rating',
                'attributes' => array(
                    'stars' => $recipe->rating_author(),
                ),
            );
        }

        if( $recipe->is_present( 'recipe_servings' ) || $recipe->is_present( 'recipe_servings_type' ) ) {
            $xml[] = array(
                'name' => 'servings',
                'attributes' => array(
                    'quantity' => $recipe->servings(),
                    'unit' => $recipe->servings_type(),
                ),
            );
        }
        if( $recipe->is_present( 'recipe_prep_time' ) || $recipe->is_present( 'recipe_prep_time_text' ) ) {
            $xml[] = array(
                'name' => 'prepTime',
                'attributes' => array(
                    'quantity' => $recipe->prep_time(),
                    'unit' => $recipe->prep_time_text(),
                ),
            );
        }
        if( $recipe->is_present( 'recipe_cook_time' ) || $recipe->is_present( 'recipe_cook_time_text' ) ) {
            $xml[] = array(
                'name' => 'cookTime',
                'attributes' => array(
                    'quantity' => $recipe->cook_time(),
                    'unit' => $recipe->cook_time_text(),
                ),
            );
        }
        if( $recipe->is_present( 'recipe_passive_time' ) || $recipe->is_present( 'recipe_passive_time_text' ) ) {
            $xml[] = array(
                'name' => 'passiveTime',
                'attributes' => array(
                    'quantity' => $recipe->passive_time(),
                    'unit' => $recipe->passive_time_text(),
                ),
            );
        }

        if( $recipe->is_present( 'recipe_ingredients' ) ) {
            $previous_group = null;
            $group_ingredients = array();

            foreach( $recipe->ingredients() as $ingredient ) {
                $group = isset( $ingredient['group'] ) ? $ingredient['group'] : '';

                if( $group !== $previous_group ) {
                    if( count( $group_ingredients ) > 0 ) {
                        $group_xml = array(
                            'name' => 'ingredients',
                            'attributes' => array(
                                'group' => $previous_group,
                            ),
                            $group_ingredients,
                        );

                        $xml[] = array_merge( $group_xml, $group_ingredients );
                    }

                    $previous_group = $group;
                    $group_ingredients = array();
                }

                $group_ingredients[] = array(
                    'name' => 'ingredient',
                    'attributes' => array(
                        'name' => $ingredient['ingredient'],
                        'quantity' => $ingredient['amount'],
                        'unit' => $ingredient['unit'],
                        'notes' => $ingredient['notes'],
                    ),
                );
            }

            if( count( $group_ingredients ) > 0 ) {
                $group_xml = array(
                    'name' => 'ingredients',
                    'attributes' => array(
                        'group' => $group,
                    ),
                    $group_ingredients,
                );

                $xml[] = array_merge( $group_xml, $group_ingredients );
            }
        }

        if( $recipe->is_present( 'recipe_instructions' ) ) {
            $previous_group = null;
            $group_instructions = array();

            foreach( $recipe->instructions() as $instruction ) {
                $group = isset( $instruction['group'] ) ? $instruction['group'] : '';

                if( $group !== $previous_group ) {
                    if( count( $group_instructions ) > 0 ) {
                        $group_xml = array(
                            'name' => 'instructions',
                            'attributes' => array(
                                'group' => $previous_group,
                            ),
                            $group_instructions,
                        );

                        $xml[] = array_merge( $group_xml, $group_instructions );
                    }

                    $previous_group = $group;
                    $group_instructions = array();
                }

                $group_instructions[] = array(
                    'name' => 'instruction',
                    'value' => $instruction['description'],
                );
            }

            if( count( $group_instructions ) > 0 ) {
                $group_xml = array(
                    'name' => 'instructions',
                    'attributes' => array(
                        'group' => $group,
                    ),
                    $group_instructions,
                );

                $xml[] = array_merge( $group_xml, $group_instructions );
            }
        }

        if( $recipe->is_present( 'recipe_notes' ) ) {
            $xml[] = array(
                'name' => 'notes',
                'value' => $recipe->notes(),
            );
        }

        $recipe_nutritional = $recipe->nutritional();
        if( $recipe_nutritional ) {
            $xml[] = array(
                'name' => 'nutrition',
                'attributes' => $recipe_nutritional,
            );
        }

        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );
        $taxonomies['category'] = true;
        $taxonomies['post_tag'] = true;

        foreach( $taxonomies as $taxonomy => $options ) {
            $terms = get_the_terms($recipe_id, $taxonomy);
            $terms_xml = array();

            if (!is_wp_error($terms) && $terms) {
                foreach ($terms as $term) {
                    $terms_xml[] = array(
                        'name' => 'term',
                        'value' => $term->name,
                    );
                }
            }

            if (count($terms_xml) > 0) {
                $taxonomy_xml = array(
                    'name' => 'taxonomy',
                    'attributes' => array(
                        'name' => $taxonomy,
                    ),
                );

                $xml[] = array_merge($taxonomy_xml, $terms_xml);
            }
        }

        $custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
        if( $custom_fields_addon ) {
            $custom_fields = $custom_fields_addon->get_custom_fields();

            foreach( $custom_fields as $key => $custom_field ) {
                $value = $recipe->custom_field( $key );

                if( $value ) {
                    $xml[] = array(
                        'name' => 'customField',
                        'attributes' => array(
                            'key' => $key,
                            'value' => $recipe->custom_field( $key ),
                        ),
                    );
                }
            }
        }

        return $xml;
    }

    /**
     * Helper functions
     */
    // Source: http://www.viper007bond.com/2011/06/29/easily-create-xml-in-php-using-a-data-array/
    private function generate_xml_element( $dom, $data ) {
        if ( empty( $data['name'] ) )
            return false;

        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );

        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }

        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;

            $child = $this->generate_xml_element( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }

        return $element;
    }
}

WPUltimateRecipe::loaded_addon( 'export-xml', new WPURP_Export_Xml() );