<?php

class WPURP_Template_Recipe_Add_To_Meal_Plan extends WPURP_Template_Block {

    public $icon = 'fa-calendar';

    public $editorField = 'addToMealPlan';

    public function __construct( $type = 'recipe-add-to-meal-plan' )
    {
        parent::__construct( $type );
    }

    public function icon( $icon )
    {
        $this->icon = $icon;
        return $this;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        $icon = '<i class="fa ' . esc_attr( $this->icon ) . '"></i>';

        $classes = array();

        $tooltip_text = WPUltimateRecipe::option( 'add_to_meal_plan_tooltip_text', __('Add to Meal Plan:', 'wp-ultimate-recipe') );
        $tooltip_alt_text = WPUltimateRecipe::option( 'added_to_meal_plan_tooltip_text', __('This recipe has been added to your Meal Plan', 'wp-ultimate-recipe') );
        $classes[] = 'recipe-tooltip';

        $this->classes = $classes;

        $output = $this->before_output();
        ob_start();
?>
<a href="#"<?php echo $this->style(); ?> data-recipe-id="<?php echo $recipe->ID(); ?>"><?php echo $icon; ?></a>
<div class="recipe-tooltip-content">
    <div class="tooltip-shown">
        <span class="wpurp-meal-plan-button-text"><?php echo $tooltip_text; ?></span>
        <input type="text" class="wpurp-meal-plan-button-date" value="<?php $today = new DateTime( 'today', WPUltimateRecipe::get()->timezone() ); echo $today->format('Y-m-d'); ?>">
        <select class="wpurp-meal-plan-button-course">
            <option value="0"><?php _e( 'Select Course to Add:', 'wp-ultimate-recipe' ); ?></option>
            <?php
            $meal_plan = WPUltimateRecipe::addon( 'meal-planner' )->get_meal_plan();

            foreach( $meal_plan['courses'] as $course ) {
                echo '<option value="' . esc_attr( $course ) . '">' . $course . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="tooltip-alt"><?php echo $tooltip_alt_text; ?></div>
</div>
<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}