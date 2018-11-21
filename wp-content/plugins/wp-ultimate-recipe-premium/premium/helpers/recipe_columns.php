<?php

class WPURP_Recipe_Columns {

    public function __construct()
    {
        add_filter( 'manage_edit-recipe_columns', array( $this, 'recipe_columns') );
        add_filter( 'manage_recipe_posts_custom_column' , array( $this, 'recipe_columns_content'), 10, 2 );
        add_filter( 'manage_recipe_posts_columns' , array( $this, 'recipe_columns_order' ) );
        add_filter( 'manage_edit-recipe_sortable_columns', array( $this, 'recipe_columns_sortable' ));
        add_filter( 'request', array( $this, 'recipe_columns_sort' ));
    }

    public function recipe_columns( $columns ) {
        // Thumbnails
        if( WPUltimateRecipe::option( 'overview_show_recipe_thumbnails', '1' ) == '1' ) {
            $columns['recipe_thumbnail'] = __( 'Featured image', 'wp-ultimate-recipe' );
        }

        // User submissions
        $columns['recipe_author'] = __( 'Author', 'wp-ultimate-recipe' );

        // User ratings
        if( WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' ) != 'disabled' && WPUltimateRecipe::option( 'overview_show_recipe_rating', '1' ) == '1' ) {
            $columns['recipe_rating'] = __( 'Rating', 'wp-ultimate-recipe' );
        }

        // Actions
        $columns['recipe_actions'] = __( 'Actions', 'wp-ultimate-recipe' );

        return $columns;
    }

    public function recipe_columns_content( $column, $post_ID ) {
        switch( $column ) {
            case 'recipe_thumbnail':
                echo the_post_thumbnail( array( 80, 80) );
                break;

            case 'recipe_author':
                $guestname = get_post_meta( $post_ID, 'recipe-author' );
                if( isset( $guestname['0'] ) ) {
                    echo $guestname['0'];
                } else {
                    echo get_the_author_meta( 'display_name' );
                }
                break;

            case 'recipe_rating':
                $rating = WPURP_User_Ratings::get_recipe_rating( $post_ID );

                echo __( 'Votes', 'wp-ultimate-recipe' ) . ': ' . $rating['votes'];

                if( $rating['votes'] > 0 ) {
                    echo ' (<a href="#" class="reset-recipe-rating" data-recipe="'. $post_ID.'">' . __( 'reset', 'wp-ultimate-recipe' ) . '</a>)';
                }
                echo '<br/>';
                echo __( 'Rating', 'wp-ultimate-recipe' ) . ': ' . $rating['rating'];
                break;

            case 'recipe_actions':
                echo '<a href="#" class="clone-recipe" data-recipe="' . $post_ID . '" data-nonce="' . wp_create_nonce( 'recipe' ) . '">' . __( 'Clone Recipe', 'wp-ultimate-recipe' ) . '</a>';
                break;
        }
    }

    public function recipe_columns_order( $columns ) {
        $reordered = array(
            'cb' => '<input type="checkbox" />',
            'recipe_thumbnail' => __( 'Featured image', 'wp-ultimate-recipe' ),
            'title' => __( 'Title', 'wp-ultimate-recipe' ),
            'recipe_author' =>__( 'Author', 'wp-ultimate-recipe' ),
        );

        if( WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' ) != 'disabled' ) {
            $reordered = array_merge( $reordered, array(
                'recipe_rating' =>__( 'Rating', 'wp-ultimate-recipe' ),
            ) );
        }

        if( WPUltimateRecipe::option( 'recipe_tags_use_wp_categories', '1' ) == '1' ) {
            $reordered = array_merge( $reordered, array(
                'categories' => __( 'Categories', 'wp-ultimate-recipe' ),
                'tags' => __( 'Tags', 'wp-ultimate-recipe' ),
            ) );
        }

        $reordered = array_merge( $reordered, array(
            'comments' => '<div title="Comments" class="comment-grey-bubble"</div>',
            'date' => __( 'Date', 'wp-ultimate-recipe' ),
        ) );

        $reordered = array_merge( $reordered, array(

            'recipe_actions' =>__( 'Actions', 'wp-ultimate-recipe' ),
        ) );

        if( WPUltimateRecipe::option( 'overview_show_recipe_thumbnails', '1' ) !== '1' ) {
            unset( $reordered['recipe_thumbnail'] );
        }
        if( WPUltimateRecipe::option( 'overview_show_recipe_rating', '1' ) !== '1' ) {
            unset( $reordered['recipe_rating'] );
        }

        return $reordered;
    }

    public function recipe_columns_sortable( $columns) {
        $columns['recipe_rating'] = 'recipe_rating';

        return $columns;
    }

    public function recipe_columns_sort( $vars ) {
        if ( is_admin() && isset( $vars['orderby'] ) && 'recipe_rating' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'recipe_user_ratings_rating',
                'orderby' => 'meta_value_num'
            ) );
        }

        return $vars;
    }
}