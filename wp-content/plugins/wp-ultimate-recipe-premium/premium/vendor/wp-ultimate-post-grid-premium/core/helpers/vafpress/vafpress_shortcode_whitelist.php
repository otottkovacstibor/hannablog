<?php
function wpupg_shortcode_generator_grids_by_date()
{
    $args = array(
        'post_type' => WPUPG_POST_TYPE,
        'post_status' => 'any',
        'posts_per_page' => -1,
        'nopaging' => true,
    );

    $query = new WP_Query( $args );
    $posts = $query->have_posts() ? $query->posts : array();
    $grids = array();

    foreach ( $posts as $post )
    {
        $grids[] = array(
            'value' => $post->post_name,
            'label' => $post->post_title,
        );
    }

    return $grids;
}

VP_Security::instance()->whitelist_function('wpupg_shortcode_generator_grids_by_date');