<?php

$shortcode_generator = array(
//=-=-=-=-=-=-= GRID =-=-=-=-=-=-=
    __( 'Grid', 'wp-ultimate-recipe' ) => array(
        'elements' => array(
            'by_date' => array(
                'title'   => __('Select a grid to display', 'wp-ultimate-post-grid') . ' - ' . __('Ordered by date added', 'wp-ultimate-post-grid'),
                'code'    => '[wpupg-grid]',
                'attributes' => array(
                    array(
                        'type' => 'select',
                        'name' => 'id',
                        'label' => __('Grid', 'wp-ultimate-post-grid'),
                        'items' => array(
                            'data' => array(
                                array(
                                    'source' => 'function',
                                    'value' => 'wpupg_shortcode_generator_grids_by_date',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
//=-=-=-=-=-=-= FILTER =-=-=-=-=-=-=
    __( 'Filter', 'wp-ultimate-recipe' ) => array(
        'elements' => array(
            'by_date' => array(
                'title'   => __('Select a filter to display', 'wp-ultimate-post-grid') . ' - ' . __('Ordered by date added', 'wp-ultimate-post-grid'),
                'code'    => '[wpupg-filter]',
                'attributes' => array(
                    array(
                        'type' => 'select',
                        'name' => 'id',
                        'label' => __('Grid', 'wp-ultimate-post-grid'),
                        'items' => array(
                            'data' => array(
                                array(
                                    'source' => 'function',
                                    'value' => 'wpupg_shortcode_generator_grids_by_date',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);