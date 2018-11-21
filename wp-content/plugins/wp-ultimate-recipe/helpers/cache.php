<?php

class WPURP_Cache {

    private $cache;

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'check_manual_reset' ) );
        add_action( 'admin_init', array( $this, 'check_if_present' ) );

        // Check if reset is needed
        add_action( 'save_post', array( $this, 'check_save_post' ), 11, 2 );
        add_action( 'admin_init', array( $this, 'check_reset' ) );
    }

    public function check_manual_reset()
    {
        if( isset( $_GET['wpurp_reset_cache'] ) ) {
            $this->trigger_reset();
            WPUltimateRecipe::get()->helper( 'notices' )->add_admin_notice( '<strong>WP Ultimate Recipe</strong> The cache is being reset' );
        }
    }

    public function check_if_present()
    {
        $this->cache = get_option( 'wpurp_cache', false );

        if( !$this->cache ) {
            $resetting = intval( get_option( 'wpurp_cache_resetting', 0 ) );

            if( $resetting == 0 ) {
                $this->trigger_reset();
            }
        }
    }

    public function check_save_post( $id, $post )
    {
        if( $post->post_type == 'recipe' ) {
            $this->trigger_reset();
        }
    }

    public function trigger_reset()
    {
        $empty_cache = array(
            'recipes_by_date' => array(),
            'recipe_authors' => array(),
        );

        update_option( 'wpurp_cache_temp', $empty_cache, false );
        update_option( 'wpurp_cache_resetting', 1 );
    }

    public function check_reset()
    {
        $resetting = intval( get_option( 'wpurp_cache_resetting', 0 ) );

        if( $resetting > 0 ) {
            $this->reset( $resetting, 100 );
        }
    }

    public function reset( $stage = 1, $limit = 100 )
    {
        $recipes_by_date = array();
        $recipe_authors = array();
        $recipe_author_ids = array();

        $offset = ($stage-1) * $limit;

        $args = array(
            'post_type' => 'recipe',
            'post_status' => 'any',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $limit,
            'offset' => $offset,
        );

        $query = new WP_Query( $args );

        if( $query->have_posts() ) {
            $posts = $query->posts;

            foreach( $posts as $post ) {
                $recipe = new WPURP_Recipe( $post );
                $id = $recipe->ID();
                $author = $post->post_author;
                $title = $recipe->title();

                if( $post->post_status != 'publish' ) {
                    $title .= ' (' . $post->post_status . ')';
                }

                $recipes_by_date[] = array(
                    'value' => $id,
                    'label' => $title
                );

                if( !in_array( $author, $recipe_author_ids ) )
                {
                    $recipe_author_ids[] = $author;

                    $user = get_userdata( $author );

                    $name = $user ? $user->display_name : __( 'n/a', 'wp-ultimate-recipe' );

                    $recipe_authors[] = array(
                        'value' => $author,
                        'label' => $author . ' - ' . $name,
                    );
                }
            }

            // Update current temp cache with new recipes
            $temp_cache = get_option( 'wpurp_cache_temp' );

            $temp_recipes_by_date = isset( $temp_cache['recipes_by_date'] ) && is_array( $temp_cache['recipes_by_date'] ) ? $temp_cache['recipes_by_date'] : array();
            $temp_recipe_authors = isset( $temp_cache['recipe_authors'] ) && is_array( $temp_cache['recipe_authors'] ) ? $temp_cache['recipe_authors'] : array();

            $recipes_by_date = array_merge( $temp_recipes_by_date, $recipes_by_date );
            $recipe_authors = array_merge( $temp_recipe_authors, $recipe_authors );

            $updated_temp_cache = array(
                'recipes_by_date' => $this->array_unique_multidimensional( $recipes_by_date ),
                'recipe_authors' => $this->array_unique_multidimensional( $recipe_authors ),
            );

            update_option( 'wpurp_cache_temp', $updated_temp_cache, false );

            // Move on to next set of recipe next time
            update_option( 'wpurp_cache_resetting', $stage+1 );
        } else {
            // No posts left, finished resetting
            update_option( 'wpurp_cache_resetting', 0 );

            // Sort temp cache
            $temp_cache = get_option( 'wpurp_cache_temp' );

            $recipes_by_date = $temp_cache['recipes_by_date'];
            $recipe_authors = $temp_cache['recipe_authors'];

            usort( $recipe_authors, array( $this, 'sort_by_label' ) );

            // Put sorted temp cache as new cache
            $cache = array(
                'recipes_by_date' => $recipes_by_date,
                'recipe_authors' => $recipe_authors,
            );

            update_option( 'wpurp_cache', $cache, false );
            $this->cache = $cache;
        }
    }

    // Source: http://stackoverflow.com/questions/307674/how-to-remove-duplicate-values-from-a-multi-dimensional-array-in-php
    public function array_unique_multidimensional( $input )
    {
        $serialized = array_map('serialize', $input);
        $unique = array_unique($serialized);
        return array_intersect_key($input, $unique);
    }

    public function sort_by_label( $a, $b )
    {
        return strcmp( $a['label'], $b['label'] );
    }

    public function get( $item )
    {
        // Don't store recipes by title anymore, sort as needed.
        if( $item == 'recipes_by_title' ) {
            $recipes_by_title = $this->get( 'recipes_by_date' );
            usort( $recipes_by_title, array( $this, 'sort_by_label' ) );

            return $recipes_by_title;
        }
 
        // Don't store recipes with data anymore.
        if( $item == 'recipes_with_data' ) {
            $recipes_with_data = array();
            $recipes_by_date = $this->get( 'recipes_by_date' );

            foreach( $recipes_by_date as $recipe ) {
                $id = intval( $recipe['value'] );
                $recipes_with_data[$id] = array(
                    'title' => $recipe['label'],
                );
            }

            return $recipes_with_data;
        }

        if( !$this->cache ) {
            $this->cache = get_option( 'wpurp_cache', array() );
        }

        if( isset( $this->cache[$item] ) ) {
            return $this->cache[$item];
        }

        return array();
    }
}