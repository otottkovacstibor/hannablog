<?php

class WPURP_Recipe_Search_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'wpurp_recipe_search_widget',
            __( 'WPURP Recipe Search', 'wp-ultimate-recipe' ),
            array(
                'description' => __( 'A customizable Recipe Search widget.', 'wp-ultimate-recipe' )
            )
        );
    }

    public function widget( $args, $instance )
    {
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if ( !empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<form role="search" method="get" action="' . get_home_url() . '">';
        echo '<input type="hidden" value="" name="wpurp-search" id="wpurp-search">';

        if(false) {
            // TODO Keyword search
        } else {
            echo '<input type="hidden" value="" name="s" id="s">';
        }

        $tags = $instance['filter_tags'];

        foreach( $tags as $id => $name )
        {
            // Get name at this point in time to have correct WPML translation
            $taxonomy = get_taxonomy( $id );
            $label = __( $taxonomy->labels->singular_name, 'wp-ultimate-recipe' );

            $dropdown_args = apply_filters( 'wpurp_search_widget_dropdown_args', array(
                'show_option_all' => $label,
                'orderby' => 'NAME',
                'hierarchical' => 1,
                'name' => 'recipe-' . $id,
                'taxonomy' => $id,
            ), $taxonomy );

            wp_dropdown_categories( $dropdown_args );

            echo '<br/>';
        }

        echo '<input type="submit" value="' . __( 'Search', 'wp-ultimate-recipe' ) . '">';
        echo '</form>';

        echo $args['after_widget'];
    }

    public function form( $instance )
    {
        // Parameters
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'Recipe Search', 'wp-ultimate-recipe' );
        $filter_tags = isset( $instance['filter_tags'] ) ? $instance['filter_tags'] : array();

        // Get tags that can be used to filter
        $tags = WPUltimateRecipe::get()->tags();

        $recipe_tags = array();
        foreach( $tags as $id => $tag ) {
            $recipe_tags[$id] = $tag['labels']['singular_name'];
        }

        if( WPUltimateRecipe::option( 'recipe_tags_use_wp_categories', '1' ) == '1' ) {
            $recipe_tags['post_tag'] = __( 'Tag', 'wp-ultimate-recipe' );
            $recipe_tags['category'] = __( 'Category', 'wp-ultimate-recipe' );
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php foreach( $recipe_tags as $id => $name ) {
            $checked = isset( $filter_tags[$id] ) ? ' checked="checked"' : '';
        ?>
            <p>
                <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'filter_tags' ); ?>[<?php echo $id; ?>]" name="<?php echo $this->get_field_name( 'filter_tags' ); ?>[<?php echo $id; ?>]" value="<?php echo esc_attr( $name ); ?>" <?php echo $checked; ?>>
                <label for="<?php echo $this->get_field_id( 'filter_tags' ); ?>[<?php echo $id; ?>]"><?php _e( 'Display', 'wp-ultimate-recipe' ); ?> <?php echo strtolower( $name ); ?> <?php _e( 'filter', 'wp-ultimate-recipe' ); ?>?</label>
            </p>
        <?php } ?>
    <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['filter_tags'] = array();

        if( isset( $new_instance['filter_tags'] ) ) {
            foreach( $new_instance['filter_tags'] as $id => $value ) {
                $instance['filter_tags'][$id] = $value;
            }
        }

        return $instance;
    }
}

add_action( 'widgets_init', create_function( '', 'return register_widget("WPURP_Recipe_Search_Widget");' ) );