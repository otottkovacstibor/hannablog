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

<div class="wpurp-meal-plan wpurp-meal-plan-shortcode <?php echo $class; ?>">
    <div class="wpurp-meal-plan-calendar-container">
        <?php
        $meal_plan_admin = is_admin();
        $meal_plan_id = $post->ID;
        $start_date = new DateTime( '2000-01-01', WPUltimateRecipe::get()->timezone() );

        // Get end date
        $meal_plan = $meal_plan = $this->get_meal_plan( $meal_plan_id );
        $max_date = max( array_keys( $meal_plan['dates'] ) );
        $end_date = DateTime::createFromFormat( 'Ymd', $max_date, WPUltimateRecipe::get()->timezone() );
        $end_date->setTime(0,0);

        include( $this->addonDir . '/templates/calendar.php' );
        ?>
    </div>
    <div class="wpurp-meal-plan-shopping-list-container">
        <?php include( $this->addonDir . '/templates/shopping-list.php' ); ?>
    </div>
</div>