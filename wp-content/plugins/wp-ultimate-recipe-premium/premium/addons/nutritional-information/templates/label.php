<?php
$nutritional = $recipe->nutritional();

$has_nutritional_information = false;
$main_info = false;
$sub_info = false;

$label_serving_size_default = isset( $nutritional['serving_size_display'] ) && floatval( $nutritional['serving_size_display'] ) > 0 ? $nutritional['serving_size_display'] : 0;
$label_serving_size = isset( $label_serving_size ) && floatval( $label_serving_size ) > 0 ? floatval( $label_serving_size ) : floatval( $label_serving_size_default );

$recalculated = $label_serving_size > 0 && isset( $nutritional['serving_size'] ) && floatval( $nutritional['serving_size'] ) > 0 ? true : false;

foreach( $this->fields as $field => $unit ) {
    if( isset( $nutritional[$field] ) && trim( $nutritional[$field] ) !== '' ) {
        $$field = $nutritional[$field];

        // Recalculate for new serving size
        if( $recalculated ) {
            $$field = strval( round( floatval( $$field ) / floatval( $nutritional['serving_size'] ) * $label_serving_size ) );
        }

        if( isset( $this->daily[$field] ) ) {
            $perc_field = $field . '_perc';
            $$perc_field = round( floatval( $$field ) / $this->daily[$field] * 100 );
        }

        // Flags to know what to output
        $has_nutritional_information = true;
        if( in_array( $field, array( 'fat', 'cholesterol', 'sodium', 'potassium', 'carbohydrate', 'protein' ) ) ) {
            $main_info = true;
        } else if( in_array( $field, array( 'vitamin_a', 'vitamin_c', 'calcium', 'iron' ) ) ) {
            $sub_info = true;
        }
    }
}

if( $has_nutritional_information ) {

    // Calculate calories if not set
    if( !isset( $calories ) ) {
        $proteins = isset( $protein ) ? $protein : 0;
        $carbs = isset( $carbohydrate ) ? $carbohydrate : 0;
        $fat_calories = isset( $fat ) ? round( floatval( $fat ) * 9 ) : 0;

        $calories = ( ( $proteins + $carbs ) * 4 ) + $fat_calories;
    }

    // Display Unit
    switch( WPUltimateRecipe::option( 'nutritional_information_unit', 'calories' ) ) {
        case 'kilojoules':
            $calories = round( 4.1868 * floatval( $calories ) );
            $display_unit = __( 'Energy', 'wp-ultimate-recipe' );
            $display_unit_unit = 'kJ';
            $display_unit_fat = __( 'Energy from Fat', 'wp-ultimate-recipe' );
            $display_unit_fat_value = isset( $fat ) ? round( 4.1868 * floatval( $fat ) * 9 ) : false;
            break;
        default:
            $display_unit = __( 'Calories', 'wp-ultimate-recipe' );
            $display_unit_unit = '';
            $display_unit_fat = __( 'Calories from Fat', 'wp-ultimate-recipe' );
            $display_unit_fat_value = isset( $fat ) ? round( floatval( $fat ) * 9 ) : false;
    }
?>
    <div class="wpurp-nutrition-label">
        <div class="nutrition-title"><?php _e( 'Nutrition Facts', 'wp-ultimate-recipe' ); ?></div>
        <div class="nutrition-recipe"><?php echo $recipe->title(); ?></div>
        <div class="nutrition-line nutrition-line-big"></div>
        <div class="nutrition-serving">
            <?php
            if( $recalculated ) {
                _e( 'Amount Per', 'wp-ultimate-recipe' );
                echo ' ' . $label_serving_size . 'g';
            } else {
                _e( 'Amount Per Serving', 'wp-ultimate-recipe' );
                if( isset( $serving_size ) ) echo ' (' . $serving_size . 'g)';
            }
            ?>
        </div>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php echo $display_unit; ?></strong> <?php echo $calories . $display_unit_unit; ?></span>
            <?php if( $display_unit_fat_value ) { ?>
            <span class="nutrition-percentage"><?php echo $display_unit_fat . ' ' . $display_unit_fat_value . $display_unit_unit; ?></span>
            <?php } ?>
        </div>
<?php if( $main_info ) { ?>
        <div class="nutrition-line"></div>
        <div class="nutrition-item">
            <span class="nutrition-percentage"><strong><?php _e( '% Daily Value', 'wp-ultimate-recipe' ); ?>*</strong></span>
        </div>
        <?php if( isset( $fat ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php _e( 'Total Fat', 'wp-ultimate-recipe' ); ?></strong> <?php echo $fat; ?>g</span>
            <span class="nutrition-percentage"><strong><?php echo $fat_perc; ?>%</strong></span>
        </div>
            <?php if( isset( $saturated_fat ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Saturated Fat', 'wp-ultimate-recipe' ); ?> <?php echo $saturated_fat; ?>g</span>
                <span class="nutrition-percentage"><strong><?php echo $saturated_fat_perc; ?>%</strong></span>
            </div>
            <?php } ?>
            <?php if( isset( $trans_fat ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Trans Fat', 'wp-ultimate-recipe' ); ?> <?php echo $trans_fat; ?>g</span>
            </div>
            <?php } ?>
            <?php if( isset( $polyunsaturated_fat ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Polyunsaturated Fat', 'wp-ultimate-recipe' ); ?> <?php echo $polyunsaturated_fat; ?>g</span>
            </div>
            <?php } ?>
            <?php if( isset( $monounsaturated_fat ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Monounsaturated Fat', 'wp-ultimate-recipe' ); ?> <?php echo $monounsaturated_fat; ?>g</span>
            </div>
            <?php } ?>
        <?php } ?>
        <?php if( isset( $cholesterol ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php _e( 'Cholesterol', 'wp-ultimate-recipe' ); ?></strong> <?php echo $cholesterol; ?>mg</span>
            <span class="nutrition-percentage"><strong><?php echo $cholesterol_perc; ?>%</strong></span>
        </div>
        <?php } ?>
        <?php if( isset( $sodium ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php _e( 'Sodium', 'wp-ultimate-recipe' ); ?></strong> <?php echo $sodium; ?>mg</span>
            <span class="nutrition-percentage"><strong><?php echo $sodium_perc; ?>%</strong></span>
        </div>
        <?php } ?>
        <?php if( isset( $potassium ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php _e( 'Potassium', 'wp-ultimate-recipe' ); ?></strong> <?php echo $potassium; ?>mg</span>
            <span class="nutrition-percentage"><strong><?php echo $potassium_perc; ?>%</strong></span>
        </div>
        <?php } ?>
        <?php if( isset( $carbohydrate ) ) { ?>
            <div class="nutrition-item">
                <span class="nutrition-main"><strong><?php _e( 'Total Carbohydrates', 'wp-ultimate-recipe' ); ?></strong> <?php echo $carbohydrate; ?>g</span>
                <span class="nutrition-percentage"><strong><?php echo $carbohydrate_perc; ?>%</strong></span>
            </div>
            <?php if( isset( $fiber ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Dietary Fiber', 'wp-ultimate-recipe' ); ?> <?php echo $fiber; ?>g</span>
                <span class="nutrition-percentage"><strong><?php echo $fiber_perc; ?>%</strong></span>
            </div>
            <?php } ?>
            <?php if( isset( $sugar ) ) { ?>
            <div class="nutrition-sub-item">
                <span class="nutrition-sub"><?php _e( 'Sugars', 'wp-ultimate-recipe' ); ?> <?php echo $sugar; ?>g</span>
            </div>
            <?php } ?>
        <?php } ?>
        <?php if( isset( $protein ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><strong><?php _e( 'Protein', 'wp-ultimate-recipe' ); ?></strong> <?php echo $protein; ?>g</span>
            <span class="nutrition-percentage"><strong><?php echo $protein_perc; ?>%</strong></span>
        </div>
        <?php } ?>
<?php } ?>
<?php if( $sub_info ) { ?>
        <div class="nutrition-line nutrition-line-big"></div>
        <?php if( isset( $vitamin_a ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><?php _e( 'Vitamin A', 'wp-ultimate-recipe' ); ?></span>
            <span class="nutrition-percentage"><?php echo $vitamin_a; ?>%</span>
        </div>
        <?php } ?>
        <?php if( isset( $vitamin_c ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><?php _e( 'Vitamin C', 'wp-ultimate-recipe' ); ?></span>
            <span class="nutrition-percentage"><?php echo $vitamin_c; ?>%</span>
        </div>
        <?php } ?>
        <?php if( isset( $calcium ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><?php _e( 'Calcium', 'wp-ultimate-recipe' ); ?></span>
            <span class="nutrition-percentage"><?php echo $calcium; ?>%</span>
        </div>
        <?php } ?>
        <?php if( isset( $iron ) ) { ?>
        <div class="nutrition-item">
            <span class="nutrition-main"><?php _e( 'Iron', 'wp-ultimate-recipe' ); ?></span>
            <span class="nutrition-percentage"><?php echo $iron; ?>%</span>
        </div>
        <?php } ?>
<?php } ?>
        <div class="nutrition-warning">
            * <?php _e( 'Percent Daily Values are based on a 2000 calorie diet. ', 'wp-ultimate-recipe' ); ?>
        </div>
    </div>
<?php } else {
    // Doesn't have nutritional information
    _e( 'There is no Nutrition Label for this recipe yet.', 'wp-ultimate-recipe' );
} ?>