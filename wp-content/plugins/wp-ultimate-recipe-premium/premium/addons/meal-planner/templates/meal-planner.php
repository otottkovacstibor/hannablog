<?php
$posts_from_grid = isset( $posts_from_grid ) ? $posts_from_grid : false;

if( !class_exists( 'Mobile_Detect' ) ) {
    include_once( WPUltimateRecipePremium::get()->premiumDir . '/vendor/Mobile_Detect.php' );
}
$detect = new Mobile_Detect;

if( $detect->isMobile() ) {
    $class = 'wpurp-meal-plan-mobile';
} else {
    $class = 'wpurp-meal-plan-desktop';
}
?>

<div class="wpurp-meal-plan <?php echo $class; ?>">
    <div class="wpurp-meal-plan-add-recipe-container">
        <?php
        $groupby_options = array(
            'a-z' => __( 'alphabet', 'wp-ultimate-recipe' ),
        );

        if( WPUltimateRecipe::option( 'recipe_tags_filter_categories', '0' ) == '1' ) {
            $groupby_options['category'] =  __( 'Category', 'wp-ultimate-recipe' );
        }
        if( WPUltimateRecipe::option( 'recipe_tags_filter_tags', '0' ) == '1' ) {
            $groupby_options['post_tag'] = __( 'Tag', 'wp-ultimate-recipe' );
        }

        $taxonomies = WPUltimateRecipe::get()->tags();
        unset($taxonomies['ingredient']);

        foreach( $taxonomies as $taxonomy => $options ) {
            if ( count( get_terms( $taxonomy ) ) > 0 ) {
                $groupby_options[$taxonomy] = $options['labels']['singular_name'];
            }
        }

        // Default group by option
        $default_group_by = 0;
        $default_group_by_taxonomy = WPUltimateRecipe::option( 'meal_planner_default_group_by', 'a-z' );
        $i = 0;

        // Hide group by options
        $hide_group_by = WPUltimateRecipe::option( 'meal_planner_hide_group_by', array() );

        foreach( $groupby_options as $taxonomy => $name ) {
            if( in_array( $taxonomy, $hide_group_by ) ) {
                unset( $groupby_options[$taxonomy] );
            } elseif( $taxonomy == $default_group_by_taxonomy ) {
                $default_group_by = $i;
            }
            $i++;
        }

        $i = 0;
        $groupby_output = array();
        foreach( $groupby_options as $id => $name ) {
            $selected = '';
            if( $i == $default_group_by ) {
                $selected = ' wpurp-meal-plan-group-by-selected';
                $default_group_by_taxonomy = $id;
            }

            $groupby_output[] = '<a href="#" class="wpurp-meal-plan-group-by'.$selected.'" data-groupby="'.$id.'">'.strtolower($name).'</a>';
            $i++;
        }
        ?>
        <select class="wpurp-meal-plan-add-recipe" data-placeholder="<?php _e( 'Add Recipe', 'wp-ultimate-recipe' ); ?>">
            <?php
            $groups = $this->get_recipes_grouped_by( $default_group_by_taxonomy, $posts_from_grid );
            echo $this->get_select_options( $groups );
            ?>
        </select>
        <?php if( count( $groupby_output ) > 1 ) { ?>
        <div class="wpurp-meal-plan-group-by-container">
            <?php _e( 'Group by', 'wp-ultimate-recipe' ); ?>: <?php echo implode( ', ', $groupby_output ); ?>
        </div>
        <?php } ?>
        <div class="wpurp-meal-plan-recipe-container">
            <?php _e( 'Drag recipe to your menu:', 'wp-ultimate-recipe' ); ?>
        </div>
    </div>
    <div class="wpurp-meal-plan-calendar-container">
        <?php
        $meal_plan_admin = is_admin();
        $meal_plan_id = is_admin() ? get_the_ID() : 0;
        $start_date = is_admin() ? new DateTime( '2000-01-01', WPUltimateRecipe::get()->timezone() ) : new DateTime( 'today', WPUltimateRecipe::get()->timezone() );
        $end_date = false;
        include( $this->addonDir . '/templates/calendar.php' );
        ?>
    </div>
    <div class="wpurp-meal-plan-shopping-list-container">
        <?php include( $this->addonDir . '/templates/shopping-list.php' ); ?>
    </div>
</div>