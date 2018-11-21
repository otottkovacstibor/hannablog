<?php

class WPUPG_Filter_Dropdown extends WPUPG_Premium_Addon {

    public function __construct( $name = 'filter-dropdown' ) {
        parent::__construct( $name );

        add_action( 'init', array( $this, 'assets' ) );

        add_filter( 'wpupg_grid_cache_filter', array( $this, 'grid_cache_filter' ), 10, 3 );
        add_filter( 'wpupg_filter_shortcode', array( $this, 'filter_shortcode' ), 10, 2 );
    }

    public function assets()
    {
        if( !is_admin() ) {
            WPUltimatePostGrid::get()->helper( 'assets' )->add(
                array(
                    'file' => $this->addonPath . '/css/filter-dropdown.css',
                    'premium' => true,
                    'public' => true,
                ),
                array(
                    'file' => WPUltimatePostGrid::get()->coreUrl . '/vendor/select2/css/select2.min.css',
                    'direct' => true,
                    'public' => true,
                ),
                array(
                    'name' => 'select2wpupg',
                    'file' => '/vendor/select2/js/select2.min.js',
                    'public' => true,
                    'deps' => array(
                        'jquery',
                    ),
                ),
                array(
                    'name' => 'filter-dropdown',
                    'file' => $this->addonPath . '/js/filter-dropdown.js',
                    'premium' => true,
                    'public' => true,
                    'deps' => array(
                        'jquery',
                        'select2wpupg',
                    )
                )
            );
        }
    }

    public function grid_cache_filter( $filter, $cache, $grid ) {
        if( $grid->filter_type() == 'dropdown' ) {
            $taxonomies = $grid->filter_taxonomies();

            foreach( $taxonomies as $taxonomy_key ) {
                if( isset( $cache['taxonomies'][$taxonomy_key] ) ) {
                    $taxonomy = get_taxonomy( $taxonomy_key );

                    if( $taxonomy ) {
                        $category_args = array(
                            'show_option_none' => 'none',
                            'taxonomy' => $taxonomy_key,
                            'echo' => 0,
                            'hide_empty' => 1,
                            'class' => 'wpupg-filter-dropdown-item',
                            'show_count' => 0,
                            'orderby' => 'name',
                            'hierarchical' => true,
                            'hide_if_empty' => true,
                            'parent' => 0,
                        );

                        $options = get_categories( $category_args );

                        if( $grid->filter_multiselect() ) {
                            $empty_option = '';
                            $multiple = ' multiple';
                        } else {
                            $empty_option = '<option></option>';
                            $multiple = '';
                        }
                        $placeholder = $taxonomy->labels->name;

                        $select = '<select name="wpupg-filter-dropdown-'.$taxonomy_key.'" id="wpupg-filter-dropdown-'.$taxonomy_key.'" class="wpupg-filter-dropdown-item" data-taxonomy="' . $taxonomy_key . '" data-placeholder="'.$placeholder.'"'. $multiple .'>';
                        $select .= $empty_option;

                        $select_options = $this->generate_hierarchical_select( $category_args, $cache, $taxonomy_key, $options );
                        $select .= $select_options;

                        $select .= '</select>';

                        if( $select_options ) {
                            $filter .= $select;
                        }
                    }
                }
            }
        }

        return $filter;
    }

    private function generate_hierarchical_select( $args, $cache, $taxonomy_key, $options, $level = 0 ) {
        $select = '';

        foreach( $options as $option ) {
            if( is_object( $option ) ) {
                $slug = urldecode( $option->slug );
                if( array_key_exists( $slug, $cache['taxonomies'][$taxonomy_key] ) ) {
                    $indent = str_repeat( '&nbsp;&nbsp;', $level );
                    $select .= '<option value="'.$slug.'">'.$indent.$option->name.'</option>';
                }

                $args['parent'] = $option->term_id;
                $children = get_categories( $args );
                $select .= $this->generate_hierarchical_select( $args, $cache, $taxonomy_key, $children, $level+1);
            }
        }

        return $select;
    }

    public function filter_shortcode( $output, $grid) {
        if( $grid->filter_type() == 'dropdown' ) {
            $inverse = $grid->filter_inverse() ? 'true' : 'false';
            $output = '<div id="wpupg-grid-' . $grid->slug() . '-filter" class="wpupg-filter wpupg-filter-dropdown" data-grid="' . $grid->slug() . '" data-type="dropdown" data-multiselect-type="' . $grid->filter_multiselect_type() . '" data-inverse="' . $inverse . '">';
            $output .= $grid->filter();
            $output .= '</div>';
        }

        return $output;
    }
}

WPUltimatePostGrid::loaded_addon( 'filter-dropdown', new WPUPG_Filter_Dropdown() );