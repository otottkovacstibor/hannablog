<?php
/*
 * -> 3.10.2
 *
 * Add keywords taxonomy
 */

function keywords_migration_add_taxonomy_to_array($arr, $tag, $name, $singular)
{
    $name = sanitize_text_field( $name );
    $singular = sanitize_text_field( $singular );

    $name_lower = strtolower($name);
    $singular_lower = strtolower($singular);

    $arr[$tag] = apply_filters( 'wpurp_register_taxonomy',
        array(
            'labels' => array(
                'name'                       => $name,
                'singular_name'              => $singular,
                'search_items'               => __( 'Search', 'wp-ultimate-recipe' ) . ' ' . $name,
                'popular_items'              => __( 'Popular', 'wp-ultimate-recipe' ) . ' ' . $name,
                'all_items'                  => __( 'All', 'wp-ultimate-recipe' ) . ' ' . $name,
                'edit_item'                  => __( 'Edit', 'wp-ultimate-recipe' ) . ' ' . $singular,
                'update_item'                => __( 'Update', 'wp-ultimate-recipe' ) . ' ' . $singular,
                'add_new_item'               => __( 'Add New', 'wp-ultimate-recipe' ) . ' ' . $singular,
                'new_item_name'              => __( 'New', 'wp-ultimate-recipe' ) . ' ' . $singular . ' ' . __( 'Name', 'wp-ultimate-recipe' ),
                'separate_items_with_commas' => __( 'Separate', 'wp-ultimate-recipe' ) . ' ' . $name_lower . ' ' . __( 'with commas', 'wp-ultimate-recipe' ),
                'add_or_remove_items'        => __( 'Add or remove', 'wp-ultimate-recipe' ) . ' ' . $name_lower,
                'choose_from_most_used'      => __( 'Choose from the most used', 'wp-ultimate-recipe' ) . ' ' . $name_lower,
                'not_found'                  => __( 'No', 'wp-ultimate-recipe' ) . ' ' . $name_lower . ' ' . __( 'found.', 'wp-ultimate-recipe' ),
                'menu_name'                  => $name
            ),
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => array(
                'slug' => sanitize_title( $singular_lower ),
            ),
            'show_in_rest' => true,
        ),
        $tag
    );

    if ( 'ingredient' !== $tag ) {
        $arr[$tag]['show_admin_column'] = true;
    }

    return $arr;
}

$taxonomies = maybe_unserialize( get_option( 'wpurp_taxonomies', array() ) );
if ( is_array( $taxonomies ) ) {
    $taxonomies = keywords_migration_add_taxonomy_to_array($taxonomies, 'wpurp_keyword', __( 'Keywords', 'wp-ultimate-recipe' ),     __( 'Keyword', 'wp-ultimate-recipe' ));
    update_option( 'wpurp_taxonomies', $taxonomies );
}
