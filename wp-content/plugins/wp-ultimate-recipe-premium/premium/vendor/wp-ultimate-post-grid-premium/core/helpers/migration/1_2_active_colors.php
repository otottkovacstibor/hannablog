<?php
/*
 * -> 1.2
 *
 * Set active colors to hover colors
 */

$args = array(
    'post_type' => WPUPG_POST_TYPE,
    'post_status' => 'any',
    'posts_per_page' => -1,
    'nopaging' => true,
);

$query = new WP_Query( $args );
$posts = $query->have_posts() ? $query->posts : array();

foreach ( $posts as $grid_post )
{
    $grid = new WPUPG_Grid( $grid_post );

    $filter_type = $grid->filter_type();

    if( $filter_type == 'isotope' ) {
        $filter_style = $grid->filter_style();

        $filter_style['isotope']['background_active_color'] = $filter_style['isotope']['background_hover_color'];
        $filter_style['isotope']['text_active_color'] = $filter_style['isotope']['text_hover_color'];
        $filter_style['isotope']['border_active_color'] = $filter_style['isotope']['border_hover_color'];

        update_post_meta( $grid->ID(), 'wpupg_filter_style', $filter_style );
    }
}

// Successfully migrated to 1.2
$migrate_version = '1.2';
update_option( 'wpupg_migrate_version', $migrate_version );
if( $notices ) WPUltimatePostGrid::get()->helper( 'notices' )->add_admin_notice( '<strong>WP Ultimate Post Grid</strong> Successfully migrated to 1.2+' );