<!DOCTYPE HTML>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo WPUltimateRecipe::option( 'print_template_title_text', get_bloginfo('name') ); ?></title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/vendor/fraction-js/index.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/css/layout_base.css">

<?php if( WPUltimateRecipe::is_premium_active() ) { ?>
    <script src="<?php echo WPUltimateRecipePremium::get()->premiumUrl; ?>/addons/unit-conversion/vendor/js-quantities.js"></script>
    <script src="<?php echo WPUltimateRecipePremium::get()->premiumUrl; ?>/addons/unit-conversion/js/unit-conversion.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo WPUltimateRecipePremium::get()->premiumUrl; ?>/addons/nutritional-information/css/nutrition-label.css">
    <link rel="stylesheet" type="text/css" href="<?php echo WPUltimateRecipePremium::get()->premiumUrl; ?>/addons/user-ratings/css/user-ratings.css">
<?php } else { ?>
    <script src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/js/adjustable_servings.js"></script>
<?php } ?>

<?php if( $fonts ) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $fonts; ?>">
<?php } ?>

    <style><?php echo WPUltimateRecipe::option( 'custom_code_print_css', '' ); ?></style>


    <script>
        wpurp_servings = {
            precision: <?php echo intval( WPUltimateRecipe::option( 'recipe_adjustable_servings_precision', 2 ) ); ?>
        };

        jQuery(document).ready(function() {
            var old_servings = <?php echo $recipe->servings_normalized(); ?>;
            var new_servings = <?php echo $servings; ?>;

<?php if( WPUltimateRecipe::is_premium_active() ) { ?>
            wpurp_unit_conversion = {
                alias_to_unit:         <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_alias_to_unit() ); ?>,
                unit_to_type:          <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_to_type() ); ?>,
                universal_units:       <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_universal_units() ); ?>,
                systems:               <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_active_systems() ); ?>,
                unit_abbreviations:    <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_abbreviations() ); ?>,
                user_abbreviations:    <?php echo json_encode( WPUltimateRecipe::get()->helper( 'ingredient_units')->get_unit_user_abbreviations() ); ?>
            };

            var ingredientList = jQuery('.wpurp-recipe-ingredients');
            var old_system = RecipeUnitConversion.determineIngredientListSystem(ingredientList);
            var new_system = <?php echo $unit_system !== false ? $unit_system : 'old_system'; ?>;

            if(old_servings != new_servings) {
                RecipeUnitConversion.adjustServings(ingredientList, old_servings, new_servings)
                jQuery('.wpurp-recipe-servings').text(new_servings);
            }

            if(old_system != new_system) {
                RecipeUnitConversion.updateIngredients(ingredientList, old_system, new_system);
            }
<?php } else { ?>
            if(old_servings != new_servings) {
                var amounts = jQuery('.wpurp-recipe-ingredient-quantity');
                wpurp_adjustable_servings.updateAmounts(amounts, old_servings, new_servings);
                jQuery('.wpurp-recipe-servings').text(new_servings);
            }
<?php } ?>

            window.print();
        });
    </script>
</head>
<body class="<?php echo is_rtl() ? 'rtl' : ''; ?>">
<?php echo do_shortcode( $recipe->output_string( 'print' ) ); ?>
</body>
</html>