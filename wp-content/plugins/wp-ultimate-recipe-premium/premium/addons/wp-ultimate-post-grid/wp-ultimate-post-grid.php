<?php

class WPURP_Wp_Ultimate_Post_Grid extends WPURP_Premium_Addon {

    public function __construct( $name = 'wp-ultimate-post-grid' ) {
        parent::__construct( $name );

        add_filter( 'wpupg_meta_box_grid_templates', array( $this, 'meta_box_grid_templates' ) );

        if( !isset( $_GET['wpupg_template_editor_preview'] ) ) {
            add_filter( 'wpupg_output_grid_classes', array( $this, 'output_grid_classes' ), 10, 2 );
            add_filter( 'wpupg_output_grid_template', array( $this, 'output_grid_template' ), 10, 2 );
            add_filter( 'wpupg_output_grid_post', array( $this, 'output_grid_post' ), 10, 2 );
        }
    }

    public function meta_box_grid_templates( $templates ) {
        $mapping = WPUltimateRecipe::addon( 'custom-templates' )->get_mapping();

        foreach( $mapping as $index => $template ) {
            $templates['wpurp-' . $index] = 'WP Ultimate Recipe - ' . $template;
        }
        return $templates;
    }

    public function output_grid_classes( $classes, $grid ) {
        $template_id = $grid->template_id();

        if( substr( $template_id, 0, 6 ) == 'wpurp-' ) {
            $template_id = substr( $template_id, 6 );
            $mapping = WPUltimateRecipe::addon( 'custom-templates' )->get_mapping();

            if( isset( $mapping[$template_id] ) ) {
                $classes = array(
                    'wp-ultimate-post-grid' => true,
                    'template_type' => 'recipe_grid',
                    'classes' => $classes
                );
            }
        }

        return $classes;
    }

    public function output_grid_template( $template, $grid ) {
        $template_id = $grid->template_id();

        if( substr( $template_id, 0, 6 ) == 'wpurp-' ) {
            $template_id = substr( $template_id, 6 );
            $mapping = WPUltimateRecipe::addon( 'custom-templates' )->get_mapping();

            if( isset( $mapping[$template_id] ) ) {
                $template = WPUltimateRecipe::addon( 'custom-templates' )->get_template_code( $template_id );
            }

        }

        return $template;
    }

    public function output_grid_post( $post, $grid ) {
        $template_id = $grid->template_id();

        if( substr( $template_id, 0, 6 ) == 'wpurp-' ) {
            $template_id = substr( $template_id, 6 );
            $mapping = WPUltimateRecipe::addon( 'custom-templates' )->get_mapping();

            if( isset( $mapping[$template_id] ) ) {
                $post = new WPURP_Recipe( $post );
            }

        }

        return $post;
    }
}

WPUltimateRecipe::loaded_addon( 'wp-ultimate-post-grid', new WPURP_Wp_Ultimate_Post_Grid() );