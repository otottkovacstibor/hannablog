<?php
$meal_plan = $this->get_meal_plan( $meal_plan_id, $meal_plan_admin );
$time_in_day = 24*60*60;

if( !class_exists( 'Mobile_Detect' ) ) {
    include_once( WPUltimateRecipePremium::get()->premiumDir . '/vendor/Mobile_Detect.php' );
}
$detect = new Mobile_Detect;

if( $detect->isTablet() ) {
    $nbr_of_days = intval( WPUltimateRecipe::option( 'meal_planner_days_tablet', 3 ) );
} elseif( $detect->isMobile() ) {
    $nbr_of_days = intval( WPUltimateRecipe::option( 'meal_planner_days_mobile', 1 ) );
} else {
    $nbr_of_days = intval( WPUltimateRecipe::option( 'meal_planner_days_desktop', 5 ) );
}

$dates = array();
$courses = $meal_plan['courses'];
?>

<table class="wpurp-meal-plan-calendar" data-admin="<?php echo $meal_plan_admin ? 'true' : 'false'; ?>" data-meal-plan-id="<?php echo $meal_plan_id; ?>" data-start-date="<?php echo $start_date->format( 'Ymd' ); ?>" data-end-date="<?php echo $end_date ? $end_date->format( 'Ymd' ) : ''; ?>" data-nbr-days="<?php echo $nbr_of_days; ?>">
    <thead>
    <tr>
        <?php
        // Adjust columns shown if there's an end date
        if( $end_date ) {
            $date_diff = date_diff( $end_date, $start_date );
            $date_diff = $date_diff->days + 1;
            if( $nbr_of_days > $date_diff ) {
                $nbr_of_days = $date_diff;
            }
        }

        for( $i = 0; $i < $nbr_of_days; $i++ ) {
            // Get Recipes for Date
            $date_key = intval( $start_date->format( 'Ymd' ) );
            $dates[$i] = isset( $meal_plan['dates'][$date_key] ) ? $meal_plan['dates'][$date_key] : array();

            // Get Courses
            foreach( $dates[$i] as $course => $recipes ) {
                if( !in_array( $course, $courses ) ) {
                    $courses[] = $course;
                }
            }

            // Output Header
            $class = $start_date->format( 'N' ) >= 6 ? 'wpurp-meal-plan-date-weekend' : 'wpurp-meal-plan-date-weekday';

            echo '<th class="' . $class . '" width="' . (100.0/$nbr_of_days) . '%">' ;
            echo '<div class="wpurp-meal-plan-date">' . $start_date->format( WPUltimateRecipe::option( 'meal_planner_date_format', 'F j' ) ) . '</div>';
            echo '<div class="wpurp-meal-plan-date-readable">';
            if( $i == 0 && $start_date == new DateTime( 'today', WPUltimateRecipe::get()->timezone() ) ) {
                _e( 'Today', 'wp-ultimate-recipe' );
            } elseif( $i == 1 && $start_date == new DateTime( 'tomorrow', WPUltimateRecipe::get()->timezone() ) ) {
                _e( 'Tomorrow', 'wp-ultimate-recipe' );
            } else {
                $day_of_week = $start_date->format( 'N' );
                switch( $day_of_week ) {
                    case 1:
                        _e( 'Monday' );
                        break;
                    case 2:
                        _e( 'Tuesday' );
                        break;
                    case 3:
                        _e( 'Wednesday' );
                        break;
                    case 4:
                        _e( 'Thursday' );
                        break;
                    case 5:
                        _e( 'Friday' );
                        break;
                    case 6:
                        _e( 'Saturday' );
                        break;
                    case 7:
                        _e( 'Sunday' );
                        break;
                }
            }
            echo '</div>';

            // Header in day numbers from 2000
            $date_diff = date_diff( new DateTime( '2000-01-01', WPUltimateRecipe::get()->timezone() ), $start_date );
            $date_diff = $date_diff->days + 1;

            echo '<div class="wpurp-meal-plan-date-readable wpurp-meal-plan-date-readable-numbers">';
            echo __( 'Day', 'wp-ultimate-recipe' ) . ' ' . $date_diff;
            echo '</div>';

            if( $i == 0 ) {
                if( $meal_plan_id == 0 || $start_date != new DateTime( '2000-01-01', WPUltimateRecipe::get()->timezone() ) ) {
                    echo '<i class="fa fa-chevron-left wpurp-meal-plan-date-change wpurp-meal-plan-date-prev"></i>';
                }
            }
            if( $i == $nbr_of_days-1 ) {
                if( !$end_date || $end_date > $start_date ) {
                    echo '<i class="fa fa-chevron-right wpurp-meal-plan-date-change wpurp-meal-plan-date-next"></i>';
                }
            }

            echo '</th>';

            $start_date->modify( '+1 day' );
        }
        ?>
    </tr>
    </thead>
    <?php
    foreach( $courses as $index => $course ) {
        $up_disabled = $index == 0 ? ' wpurp-disabled' : '';
        $down_disabled = $index == count( $courses ) - 1 ? ' wpurp-disabled' : '';

        echo '<tbody class="wpurp-meal-plan-course">';
        echo '<tr class="wpurp-meal-plan-header">';
        echo '<td colspan="' . $nbr_of_days . '"><span class="wpurp-meal-plan-course-name">' . $course . '</span><span class="wpurp-meal-plan-actions"><i class="fa fa-arrow-down wpurp-course-move wpurp-course-down' . $down_disabled . '"></i><i class="fa fa-arrow-up wpurp-course-move wpurp-course-up' . $up_disabled . '"></i><i class="fa fa-pencil wpurp-course-edit"></i><i class="fa fa-trash wpurp-course-delete"></i></span></td>';
        echo '</tr>';

        echo '<tr class="wpurp-meal-plan-recipes">';
        foreach( $dates as $date => $recipes ) {
            echo '<td class="wpurp-meal-plan-recipe-list">';

            if( isset( $recipes[$course] ) ) {
                foreach( $recipes[$course] as $recipe ) {
                    $recipe_obj = new WPURP_Recipe( $recipe['id'] );

                    echo '<div class="wpurp-meal-plan-recipe" data-recipe="' . $recipe['id'] . '" data-servings="' . $recipe['servings'] . '">';
                    echo '<img src="' . $recipe_obj->image_url( 'thumbnail' ) .'">';
                    echo '<span class="wpurp-meal-plan-recipe-title">' . $recipe_obj->title() . '</span>  <span class="wpurp-meal-plan-recipe-servings"> (' . $recipe['servings'] . ')</span>';
                    echo '</div>';
                }
            }

            echo '</td>';
        }
        echo '</tr>';
        echo '</tbody>';
    }
    ?>
    <tbody class="wpurp-meal-plan-course-placeholder">
    <tr class="wpurp-meal-plan-header">
        <td colspan="<?php echo $nbr_of_days; ?>"><span class="wpurp-meal-plan-course-name"></span><span class="wpurp-meal-plan-actions"><i class="fa fa-arrow-down wpurp-course-move wpurp-course-down"></i><i class="fa fa-arrow-up wpurp-course-move wpurp-course-up"></i><i class="fa fa-pencil wpurp-course-edit"></i><i class="fa fa-trash wpurp-course-delete"></i></span></td>
    </tr>
    <tr class="wpurp-meal-plan-recipes">
        <?php
        for( $i = 0; $i < $nbr_of_days; $i++ ) {
            echo '<td class="wpurp-meal-plan-recipe-list"></td>';
        }
        ?>
    </tr>
    </tbody>
    <tbody class="wpurp-meal-plan-selected-recipe">
    <tr class="wpurp-meal-plan-header">
        <td colspan="<?php echo $nbr_of_days; ?>"><span class="recipe-not-selected"><?php _e( 'Click on a recipe for more details', 'wp-ultimate-recipe' ); ?></span><div class="recipe-details-loader wpurp-loader"><div></div><div></div><div></div></div><span class="recipe-selected"><?php _e( 'Selected Recipe:', 'wp-ultimate-recipe' ); ?> <span class="recipe-title"></span><span class="wpurp-meal-plan-actions"><i class="fa fa-close wpurp-recipe-close"></i><i class="fa fa-trash wpurp-recipe-delete"></i></span></span></td>
    </tr>
    <tr class="wpurp-meal-plan-selected-recipe-details">
        <td colspan="<?php echo $nbr_of_days; ?>">
            <div class="wpurp-meal-plan-selected-recipe-details-container"></div>
        </td>
    </tr>
    </tbody>
</table>
<div class="wpurp-meal-plan-footer-actions">
    <?php if( $meal_plan_id > 0 && WPUltimateRecipe::option( 'saved_meal_plan_add_to_meal_planner', '1' ) == '1' ) { ?>
    <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-add-meal-planner"><?php _e( 'Save to Meal Planner', 'wp-ultimate-recipe' ); ?></button>
    <?php } ?>
    <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-add-course"><?php _e( 'Add Course', 'wp-ultimate-recipe' ); ?></button>
    <div class="wpurp-meal-plan-footer-actions-right">
        <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-shopping-list"><?php _e( 'Generate Shopping List', 'wp-ultimate-recipe' ); ?></button>
    </div>
</div>