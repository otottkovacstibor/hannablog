<?php
function wpupg_admin_premium_not_installed()
{
    return !WPUltimatePostGrid::is_premium_active();
}

function wpupg_admin_premium_installed()
{
    return WPUltimatePostGrid::is_premium_active();
}

function wpupg_admin_grids()
{
    $args = array(
        'post_type' => WPUPG_POST_TYPE,
        'post_status' => array( 'publish', 'private' ),
        'posts_per_page' => -1,
        'nopaging' => true,
        'orderby' => 'date',
        'order' => 'ASC',
    );

    $query = new WP_Query( $args );
    $posts = $query->have_posts() ? $query->posts : array();

    $grids = array();
    foreach( $posts as $post ) {
        $grids[] = array(
            'value' => $post->ID,
            'label' => $post->post_title,
        );
    }

    return $grids;
}

function wpupg_admin_template_editor()
{
    if( WPUltimatePostGrid::is_addon_active( 'template-editor' ) ) {
        $url = WPUltimatePostGrid::addon( 'template-editor' )->editor_url();
        $button = '<a href="' . $url . '" class="button button-primary" target="_blank">' . __('Open the Template Editor', 'wp-ultimate-post-grid') . '</a>';
    } else {
        $button = '<a href="#" class="button button-primary button-disabled" disabled>' . __('Open the Template Editor', 'wp-ultimate-post-grid') . '</a>';
    }

    return $button;
}

function wpupg_admin_post_types()
{
    $post_types = get_post_types( '', 'names' );
    $types = array();

    foreach( $post_types as $post_type ) {
        $types[] = array(
            'value' => $post_type,
            'label' => ucfirst( $post_type )
        );
    }

    return $types;
}

VP_Security::instance()->whitelist_function( 'wpupg_admin_premium_not_installed' );
VP_Security::instance()->whitelist_function( 'wpupg_admin_premium_installed' );
VP_Security::instance()->whitelist_function( 'wpupg_admin_grids' );
VP_Security::instance()->whitelist_function( 'wpupg_admin_template_editor' );
VP_Security::instance()->whitelist_function( 'wpupg_admin_post_types' );