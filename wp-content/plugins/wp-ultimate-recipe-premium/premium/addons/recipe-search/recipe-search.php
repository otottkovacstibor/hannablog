<?php

class WPURP_Recipe_Search extends WPURP_Premium_Addon {

    public function __construct( $name = 'recipe-search' ) {
        parent::__construct( $name );

        require_once( $this->addonDir . '/widgets/recipe_search_widget.php' );

        //Actions
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts_search' ), 20 );
    }

    public function pre_get_posts_search( $query )
    {
        if( $query->is_search && isset( $_GET['wpurp-search'] ) )
        {
            // Only search for recipes
            $query->set( 'post_type', 'recipe' );

            // Check taxonomy filters
            $tax_query = array();
            foreach( $_GET as $tag => $term )
            {
                if( substr( $tag, 0, 7 ) == 'recipe-' && $term != '0') {
                    $tax_query[] = array(
                        'taxonomy' => substr( $tag, 7 ),
                        'field' => 'id',
                        'terms' => array( intval( $term ) ),
                    );
                }
            }

            if( !empty( $tax_query ) ) {
                $query->tax_query->queries = $tax_query;
                $query->query_vars['tax_query'] = $query->tax_query->queries;
            }
        }

        return $query;
    }
}

WPUltimateRecipe::loaded_addon( 'recipe-search', new WPURP_Recipe_Search() );