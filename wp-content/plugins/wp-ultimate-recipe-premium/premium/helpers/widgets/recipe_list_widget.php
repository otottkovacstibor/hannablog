<?php

class WPURP_Recipe_List_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'wpurp_recipe_list_widget',
            __( 'WPURP Recipe List', 'wp-ultimate-recipe' ),
            array(
                'description' => __( 'Make a list of your latest, highest rated, ... recipes.', 'wp-ultimate-recipe' )
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

        $images = isset( $instance['images'] ) && $instance['images'] ? true : false;

        $query = WPUltimateRecipe::get()->query()
            ->order( $instance['order'] )
            ->order_by( $instance['order_by'] )
            ->limit( $instance['limit'] );

        $recipes = $query->get();

        echo '<ul>';

        foreach( $recipes as $recipe )
        {
            $output = '<li>';

            if( $images ) {
                $output .= get_the_post_thumbnail( $recipe->ID(), array( 50, 50) );
            }

            $output .= '<a href="' . $recipe->link() . '">' . $recipe->title() . '</a>';
            $output .= '</li>';
            echo apply_filters( 'wpurp_recipe_list_widget_recipe', $output, $recipe );
        }

        echo '</ul>';

        echo $args['after_widget'];
    }

    public function form( $instance )
    {
        // Parameters
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'Our Recipes', 'wp-ultimate-recipe' );
        $order_by = isset( $instance['order_by'] ) ? $instance['order_by'] : 'date';
        $order = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
        $limit = isset( $instance['limit'] ) ? $instance['limit'] : '10';
        $images = isset( $instance['images'] ) && $instance['images'] ? ' checked="checked"' : '';

        // Options
        $order_by_options = array(
            'date' => __( 'Date', 'wp-ultimate-recipe' ),
            'title' => __( 'Recipe Title', 'wp-ultimate-recipe' ),
            'rating' => __( 'Recipe Rating', 'wp-ultimate-recipe' ),
        );
        $order_options = array(
            'ASC' => __( 'ascending', 'wp-ultimate-recipe' ),
            'DESC' => __( 'descending', 'wp-ultimate-recipe' ),
        );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order By', 'wp-ultimate-recipe' ); ?>:</label>
            <select name="<?php echo $this->get_field_name( 'order_by' ); ?>" id="<?php echo $this->get_field_id( 'order_by' ); ?>" class="widefat">
                <?php
                    foreach ( $order_by_options as $value => $name ) {
                        $selected = $order_by == $value ? ' selected="selected"' : '';
                        echo '<option value="' . $value . '" id="' . $value . '"' . $selected . '>' . $name . '</option>';
                    }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order', 'wp-ultimate-recipe' ); ?>:</label>
            <select name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_id( 'order' ); ?>" class="widefat">
                <?php
                foreach ( $order_options as $value => $name ) {
                    $selected = $order == $value ? ' selected="selected"' : '';
                    echo '<option value="' . $value . '" id="' . $value . '"' . $selected . '>' . $name . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of recipes to show' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" size="3">
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>" value="1" <?php echo $images; ?>>
            <label for="<?php echo $this->get_field_id( 'images' ); ?>"><?php _e( 'Show Images', 'wp-ultimate-recipe' ); ?></label>
        </p>
    <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['order_by'] = ( !empty( $new_instance['order_by'] ) ) ? strip_tags( $new_instance['order_by'] ) : 'date';
        $instance['order'] = ( !empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order'] ) : 'DESC';
        $instance['limit'] = ( !empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '10';
        $instance['images'] = ( !empty( $new_instance['images'] ) ) ? $new_instance['limit'] : '';

        return $instance;
    }
}

add_action( 'widgets_init', create_function( '', 'return register_widget("WPURP_Recipe_List_Widget");' ) );