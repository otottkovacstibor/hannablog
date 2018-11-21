<?php

class WPUPG_Grid_Shortcode {

    public function __construct()
    {
        add_shortcode( 'wpupg-grid', array( $this, 'shortcode' ) );

//        add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugin' ) );
    }

    public function shortcode( $options )
    {
        $output = '';

        $slug = strtolower( trim( $options['id'] ) );

        if( $slug ) {
            unset( $options['id'] );
            $post = get_page_by_path( $slug, OBJECT, WPUPG_POST_TYPE );

            if( !is_null( $post ) ) {
                $grid = new WPUPG_Grid( $post );

                // Check if we need to filter the grid dynamically
                $dynamic_rules = array();
                if( count( $options ) > 0 && WPUltimatePostGrid::is_premium_active() ) {
                    foreach( $options as $taxonomy => $terms ) {
                        if( taxonomy_exists( $taxonomy ) ) {
                            $dynamic_rules[] = array(
                                'post_type' => 'wpupg_dynamic',
                                'taxonomy' => $taxonomy,
                                'values' => explode( ';', str_replace( ',', ';', $terms ) ),
                                'type' => 'restrict',
                            );
                        }
                    }
                }
                if( count( $dynamic_rules ) > 0 ) {
                    $grid->set_dynamic_rules( $dynamic_rules );
                }

                $link_type = $grid->link_type();
                $link_target = $grid->link_target();
                $layout_mode = $grid->layout_mode();
                $centered = $grid->centered() ? 'true' : 'false';

                $posts = '<div id="wpupg-grid-' . esc_attr( $slug ) . '" class="wpupg-grid" data-grid="' . esc_attr( $slug ) . '" data-grid-id="' . $grid->ID() . '" data-link-type="' . $link_type . '" data-link-target="' . $link_target . '" data-layout-mode="' . $layout_mode . '" data-centered="' . $centered . '">';
                $posts .= $grid->draw_posts();
                $posts .= '</div>';

                $output = apply_filters( 'wpupg_posts_shortcode', $posts, $grid );

                $pagination = '';
                if( $grid->pagination_type() == 'pages' ) {
                    $pagination_type = $grid->pagination_type();
                    $pagination_style = $grid->pagination_style();

                    $style_data = ' data-margin-vertical="' . $pagination_style['margin_vertical'] . '"';
                    $style_data .= ' data-margin-horizontal="' . $pagination_style['margin_horizontal'] . '"';
                    $style_data .= ' data-padding-vertical="' . $pagination_style['padding_vertical'] . '"';
                    $style_data .= ' data-padding-horizontal="' . $pagination_style['padding_horizontal'] . '"';
                    $style_data .= ' data-border-width="' . $pagination_style['border_width'] . '"';

                    $style_data .= ' data-background-color="' . $pagination_style['background_color'] . '"';
                    $style_data .= ' data-text-color="' . $pagination_style['text_color'] . '"';
                    $style_data .= ' data-border-color="' . $pagination_style['border_color'] . '"';

                    $style_data .= ' data-active-background-color="' . $pagination_style['background_active_color'] . '"';
                    $style_data .= ' data-active-text-color="' . $pagination_style['text_active_color'] . '"';
                    $style_data .= ' data-active-border-color="' . $pagination_style['border_active_color'] . '"';

                    $style_data .= ' data-hover-background-color="' . $pagination_style['background_hover_color'] . '"';
                    $style_data .= ' data-hover-text-color="' . $pagination_style['text_hover_color'] . '"';
                    $style_data .= ' data-hover-border-color="' . $pagination_style['border_hover_color'] . '"';
                    
                    $pagination .= '<div id="wpupg-grid-' . esc_attr( $slug ) . '-pagination" class="wpupg-pagination wpupg-pagination-' . $pagination_type . '" style="text-align: ' . $pagination_style['alignment'] . ';" data-grid="' . esc_attr( $slug ) . '" data-type="' . $pagination_type . '"' . $style_data . '>';
                    $pagination .= $grid->draw_pagination();
                    $pagination .= '</div>';
                }

                $output .= apply_filters( 'wpupg_pagination_shortcode', $pagination, $grid );

                wp_localize_script( 'wpupg_grid', 'wpupg_grid_' . $grid->ID(), array(
                    'posts' => $grid->posts(),
                ));
            }
        }

        return $output;
    }

    public function tinymce_plugin( $plugin_array )
    {
        $plugin_array['wpupg_grid_shortcode'] = WPUltimatePostGrid::get()->coreUrl . '/js/tinymce_shortcode.js';
        return $plugin_array;
    }
}