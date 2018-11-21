<?php

class WPURP_Nutrition_Label_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'wpurp_nutrition_label_widget',
            __( 'WPURP Nutrition Label', 'wp-ultimate-recipe' ),
            array(
                'description' => __( 'Display the Nutrition Label of a recipe.', 'wp-ultimate-recipe' )
            )
        );
    }

    public function widget( $args, $instance )
    {
        $title = apply_filters( 'widget_title', $instance['title'] );

        // Only display on recipe pages if no Recipe ID set
        if( $instance['recipe'] == 0 && !is_singular( 'recipe' ) ) return;

        echo $args['before_widget'];
        if ( !empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if( $instance['recipe'] == 0 ) {
            $recipe = new WPURP_Recipe( get_post() );
        } else {
            $recipe = new WPURP_Recipe( $instance['recipe'] );
        }

        $serving_size = isset( $instance['serving_size'] ) ? $instance['serving_size'] : '';
        echo WPUltimateRecipe::addon( 'nutritional-information' )->label( $recipe, $serving_size );

        echo $args['after_widget'];
    }

    public function form( $instance )
    {
        // Parameters
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'Nutrition Label', 'wp-ultimate-recipe' );
        $recipe_id = isset( $instance['recipe'] ) ? $instance['recipe'] : 0;
        $serving_size = isset( $instance['serving_size'] ) ? $instance['serving_size'] : '';

        // All published recipes
        $recipes = WPUltimateRecipe::get()->helper( 'cache' )->get( 'recipes_by_title' );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'recipe' ); ?>"><?php _e( 'Recipe', 'wp-ultimate-recipe' ); ?>:</label>
            <select name="<?php echo $this->get_field_name( 'recipe' ); ?>" id="<?php echo $this->get_field_id( 'recipe' ); ?>" class="widefat">
                <option value="0" id="0"<?php if( $recipe_id == 0) echo ' selected="selected"'; ?>><?php _e( 'Recipe shown on recipe page', 'wp-ultimate-recipe' );?></option>
                <?php
                foreach ( $recipes as $recipe ) {
                    $selected = $recipe['value'] == $recipe_id ? ' selected="selected"' : '';
                    echo '<option value="' . $recipe['value'] . '" id="' . $recipe['value'] . '"' . $selected . '>' . $recipe['label'] . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'serving_size' ); ?>"><?php _e( 'Serving Size', 'wp-ultimate-recipe' ); ?> (g):</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'serving_size' ); ?>" name="<?php echo $this->get_field_name( 'serving_size' ); ?>" type="text" value="<?php echo esc_attr( $serving_size ); ?>">
        </p>
    <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['recipe'] = ( !empty( $new_instance['recipe'] ) ) ? intval( $new_instance['recipe'] ) : 0;
        $instance['serving_size'] = ( !empty( $new_instance['serving_size'] ) ) ? strip_tags( $new_instance['serving_size'] ) : '';

        return $instance;
    }
}

add_action( 'widgets_init', create_function( '', 'return register_widget("WPURP_Nutrition_Label_Widget");' ) );