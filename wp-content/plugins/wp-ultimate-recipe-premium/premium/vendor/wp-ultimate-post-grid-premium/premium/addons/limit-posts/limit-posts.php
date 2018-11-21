<?php

class WPUPG_Limit_Posts extends WPUPG_Premium_Addon {

    public function __construct( $name = 'limit-posts' ) {
        parent::__construct( $name );

        add_filter( 'wpupg_grid_cache_post_ids', array( $this, 'grid_cache_post_ids'), 10, 2 );
    }

    public function grid_cache_post_ids( $post_ids, $grid ) {
        if( $grid->limit_posts() ) {
            $rules = $grid->limit_rules();

            foreach( $rules as $rule ) {
                $args = array(
                    'post_type' => $grid->post_types(),
                    'post_status' => $grid->post_status(),
                    'posts_per_page' => -1,
                    'nopaging' => true,
                    'fields' => 'ids',
                );

                if( $rule['post_type'] == 'wpupg_general' ) {
                     if( $rule['taxonomy'] == 'id' ) {
                         $args['post__in'] = $rule['values'];
                     } elseif( $rule['taxonomy'] == 'author' ) {
                         $args['author__in'] = $rule['values'];
                     } elseif( $rule['taxonomy'] == 'date' ) {
                         $date_condition = $rule['values'][0];
                         $date = date_parse( $rule['values'][1] );

                         if( $date['error_count'] == 0 ) {
                             if( $date_condition == 'is' ) {
                                 $args['date_query'] = array(
                                     array(
                                         'year' => $date['year'],
                                         'month' => $date['month'],
                                         'day' => $date['day'],
                                     ),
                                 );
                             } else {
                                 $args['date_query'] = array(
                                     array(
                                         $date_condition => array(
                                             'year' => $date['year'],
                                             'month' => $date['month'],
                                             'day' => $date['day'],
                                         ),
                                         'inclusive' => false,
                                     ),
                                 );
                             }
                         }
                     }
                } else {
                    if( $rule['taxonomy'] == 'category' ) {
                        $args['category__in'] = $rule['values'];
                    } else if ( $rule['taxonomy'] == 'post_tag' ) {
                        $args['tag__in'] = $rule['values'];
                    } else {
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => $rule['taxonomy'],
                                'terms' => $rule['values'],
                            ),
                        );
                    }
                }

                $query = new WP_Query( $args );
                $posts = $query->have_posts() ? $query->posts : array();
                $rule_post_ids = array_map( 'intval', $posts );

                if( $rule['type'] == 'restrict' ) {
                    $post_ids = array_intersect( $post_ids, $rule_post_ids );
                } elseif( $rule['type'] == 'exclude' ) {
                    $post_ids = array_diff( $post_ids, $rule_post_ids );
                }
            }
        }

        return $post_ids;
    }
}

WPUltimatePostGrid::loaded_addon( 'limit-posts', new WPUPG_Limit_Posts() );