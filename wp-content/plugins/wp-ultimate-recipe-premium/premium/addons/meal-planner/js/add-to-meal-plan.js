jQuery(document).ready(function() {

    if(jQuery('.wpurp-recipe-add-to-meal-plan').length > 0) {
        jQuery('.wpurp-meal-plan-button-date').datepicker({minDate: 0});

        jQuery(document).on('click', '.wpurp-recipe-add-to-meal-plan', function(e) {
            e.preventDefault();
            e.stopPropagation();

            jQuery(this).next().toggleClass('tooltip-force-display');
        });

        jQuery(document).on('change', '.wpurp-meal-plan-button-course', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var button = jQuery(this).parents('.recipe-tooltip-content').prev();

            if(!button.hasClass('in-meal-plan')) {
                var recipeId = button.data('recipe-id');
                var recipe = button.parents('.wpurp-container');

                var servings = 0;

                // Check if there is a servings changer (both free and Premium)
                var servings_input = recipe.find('input.adjust-recipe-servings');
                if(servings_input.length == 0) {
                    servings_input = recipe.find('input.advanced-adjust-recipe-servings');
                }

                // Take servings from serving changer if available
                if(servings_input.length != 0) {
                    servings = parseInt(servings_input.val());
                }

                // Date
                var date = button.next().find('.wpurp-meal-plan-button-date').val();
                var course = button.next().find('.wpurp-meal-plan-button-course option:selected').text();

                var data = {
                    action: 'meal_planner_button',
                    security: wpurp_add_to_meal_plan.nonce,
                    recipe_id: recipeId,
                    servings_wanted: servings,
                    date: date,
                    course: course
                };

                jQuery.post(wpurp_add_to_meal_plan.ajaxurl, data, function(html) {
                    button.addClass('in-meal-plan');
                    button.next().removeClass('tooltip-force-display');

                    if(button.next().hasClass('recipe-tooltip-content')) {
                        var tooltip = button.next().find('.tooltip-shown').first();
                        var tooltip_alt = button.next().find('.tooltip-alt').first();

                        var tooltip_text = tooltip.html();
                        var tooltip_alt_text = tooltip_alt.html();

                        tooltip.html(tooltip_alt_text);
                        tooltip_alt.html(tooltip_text);
                    }
                });
            }
        });
    }
});