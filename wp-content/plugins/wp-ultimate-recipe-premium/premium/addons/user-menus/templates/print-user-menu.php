<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WP Ultimate Recipe Plugin</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
        var wpurp = window.opener.wpurp_print;
        var user_menu = window.opener.wpurp_user_menus;

        document.title = wpurp.title;

        // Include CSS files
        document.write('<link rel="stylesheet" type="text/css" href="' + user_menu.addonUrl + '/css/list-print.css">');
        document.write('<link rel="stylesheet" type="text/css" href="' + wpurp.coreUrl + '/css/layout_base.css">');
        if(wpurp.premiumUrl) {
            document.write('<link rel="stylesheet" type="text/css" href="' + wpurp.premiumUrl + '/addons/nutritional-information/css/nutrition-label.css">');
            document.write('<link rel="stylesheet" type="text/css" href="' + wpurp.premiumUrl + '/addons/user-ratings/css/user-ratings.css">');
        }
        document.write('<style>' + wpurp.custom_print_css + '</style>');
        document.write('<style>' + user_menu.custom_print_shoppinglist_css + '</style>');

        jQuery(document).ready(function() {

            // Set RTL if opener was in RTL
            if(wpurp.rtl) {
                jQuery('html').attr('dir', 'rtl')
                    .find('body').addClass('rtl');
            }

            var wpurp_printed = false;

            function startChecking()
            {
                checkForAjax()
                setTimeout(function(){
                    checkForAjax();
                }, 50);
            }

            function checkForAjax() {
                user_menu = window.opener.wpurp_user_menus;

                if(user_menu.recipes.length != 0) {
                    var html = '';

                    // Shopping List
                    html += user_menu.shoppingListTitle;
                    html += user_menu.shoppingList;

                    // Recipe Templates
                    if(user_menu.recipes.fonts) {
                        html += '<link rel="stylesheet" type="text/css" href="' + user_menu.recipes.fonts + '">';
                    }

                    for(var i = 0; i < user_menu.recipes.templates.length; i++) {
                        html += '<hr>';
                        html += user_menu.recipes.templates[i];
                    }

                    jQuery('body').html(html);
                    adjustServings();

                    if( !wpurp_printed ) {
                        setTimeout(function() {
                            window.print();
                        }, 1000); // TODO Check if everything is actually loaded
                        wpurp_printed = true;
                    }
                } else {
                    setTimeout(function() {
                        checkForAjax();
                    }, 50);
                }
            }

            function adjustServings()
            {
                for(var i = 0; i < user_menu.recipes.templates.length; i++) {
                    var recipe_container = jQuery('.wpurp-container').eq(i);
                    var ingredientList = recipe_container.find('.wpurp-recipe-ingredients');

                    // Adjust Servings
                    var old_servings = user_menu.print_servings_original[i];
                    var new_servings = user_menu.print_servings_wanted[i];

                    if(old_servings != new_servings) {
                        window.opener.RecipeUnitConversion.adjustServings(ingredientList, old_servings, new_servings);
                        recipe_container.find('.wpurp-recipe-servings').text(new_servings);
                    }

                    // Adjust System
                    var new_system = user_menu.print_unit_system;
                    var old_system = window.opener.RecipeUnitConversion.determineIngredientListSystem(ingredientList);

                    if(old_system != new_system) {
                        window.opener.RecipeUnitConversion.updateIngredients(ingredientList, old_system, new_system);
                    }
                }
            }

            startChecking();
        });
    </script>
</head>
<body>
</body>
</html>