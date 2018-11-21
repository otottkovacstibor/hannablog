var RecipeUserMenus = RecipeUserMenus || {};

RecipeUserMenus.recipes = [];
RecipeUserMenus.order = [];
RecipeUserMenus.recipeIngredients = [];
RecipeUserMenus.nbrRecipes = 0;
RecipeUserMenus.ajaxGettingIngredients = 0;
RecipeUserMenus.generalServings = 4;
RecipeUserMenus.menuId = 0;
RecipeUserMenus.unitSystem = 0

RecipeUserMenus.init = function() {
    RecipeUserMenus.initSelect();

    // Set default servings and unit system
    RecipeUserMenus.unitSystem = parseInt(wpurp_user_menus.default_system);
    RecipeUserMenus.changeServings(jQuery('.user-menus-servings-general'), true);

    if(typeof wpurp_user_menu !== 'undefined') {
        RecipeUserMenus.setSavedValues(wpurp_user_menu);
        RecipeUserMenus.redrawRecipes();
        RecipeUserMenus.updateIngredients();
    }

    jQuery('.user-menus-group-by').on('click', function(e) {
        e.preventDefault();

        var link = jQuery(this);
        if(!link.hasClass('user-menus-group-by-selected'))
        {
            RecipeUserMenus.groupBy(jQuery(this).data('groupby'));
            link.siblings('.user-menus-group-by-selected').removeClass('user-menus-group-by-selected');
            link.addClass('user-menus-group-by-selected');
        }
    });

    jQuery('.user-menus-selected-recipes').sortable({
        opacity: 0.5,
        start: function(ev, ui) {
            jQuery('.user-menus-recipes-delete').slideDown(500);
        },
        stop: function(ev, ui) {
            jQuery('.user-menus-recipes-delete').slideUp(500);
        },
        update: function(ev, ui) {
            RecipeUserMenus.updateRecipeOrder(jQuery(this));
            RecipeUserMenus.updateCookies();
        }
    });

    jQuery('.user-menus-servings-general').on('keyup change', function() {
        RecipeUserMenus.changeServings(jQuery(this), true);
    });

    jQuery('.user-menus-selected-recipes').on('keyup change', '.user-menus-servings-recipe', function() {
        RecipeUserMenus.changeServings(jQuery(this), false);
    });

    jQuery('.user-menus-servings-general').on('blur', function() {
        var servings_input = jQuery(this);
        var servings_new = servings_input.val();

        if(isNaN(servings_new) || servings_new <= 0){
            servings_input.val(1);
        }
    });

    jQuery('.user-menus-selected-recipes').on('blur', '.user-menus-servings-recipe', function() {
        var servings_input = jQuery(this);
        var servings_new = servings_input.val();

        if(isNaN(servings_new) || servings_new <= 0){
            servings_input.val(1);
        }
    });

    jQuery('.wpurp-user-menus').on('click', '.delete-recipe-button', function() {
        jQuery(this).parent('.user-menus-recipe').remove();
        RecipeUserMenus.deleteRecipe();
    });

    jQuery('.user-menus-ingredients').on('change', '.shopping-list-ingredient', function() {
        var checkbox = jQuery(this);
        if(checkbox.is(':checked')) {
            checkbox.closest('tr').addClass('ingredient-checked');
        } else {
            checkbox.closest('tr').removeClass('ingredient-checked');
        }
    });
};

RecipeUserMenus.deleteRecipe = function() {
    RecipeUserMenus.nbrRecipes -= 1;
    RecipeUserMenus.checkIfEmpty();

    RecipeUserMenus.updateRecipeOrder(jQuery('.user-menus-selected-recipes'));
    RecipeUserMenus.updateIngredients();
    RecipeUserMenus.updateCookies();
}

RecipeUserMenus.setSavedValues = function(val)
{
    if(val.recipes !== null) {
        RecipeUserMenus.recipes = val.recipes;
        RecipeUserMenus.order = val.order;
    }

    if(val.nbrRecipes !== '') {
        RecipeUserMenus.nbrRecipes = parseInt(val.nbrRecipes);
    }
    if(val.unitSystem !== '') {
        RecipeUserMenus.unitSystem = parseInt(val.unitSystem);
    }
    if(val.menuId !== '') {
        RecipeUserMenus.menuId = parseInt(val.menuId);
    }
}

RecipeUserMenus.deleteMenu = function()
{
    var data = {
        action: 'user_menus_delete',
        security: wpurp_user_menus.nonce,
        menuId: RecipeUserMenus.menuId
    };

    jQuery.post(wpurp_user_menus.ajaxurl, data, function(url) {
        window.location.href = url;
    }, 'html');
}

RecipeUserMenus.saveMenu = function()
{
    var data = {
        action: 'user_menus_save',
        security: wpurp_user_menus.nonce,
        menuId: RecipeUserMenus.menuId,
        title: jQuery('.user-menus-title').val(),
        recipes: RecipeUserMenus.recipes,
        order: RecipeUserMenus.order,
        nbrRecipes: RecipeUserMenus.nbrRecipes,
        unitSystem: RecipeUserMenus.unitSystem
    };

    jQuery.post(wpurp_user_menus.ajaxurl, data, function(url) {
        if(RecipeUserMenus.menuId == 0) {
            window.location.href = url;
        }
    }, 'html');
}

RecipeUserMenus.printShoppingList = function()
{
    var title = jQuery('.user-menus-title').val();
    if(title != undefined) {
        wpurp_user_menus.shoppingListTitle = '<h2 class="wpurp-shoppinglist-title">' + title + '</h2>';
    } else {
        wpurp_user_menus.shoppingListTitle = '<h2 class="wpurp-shoppinglist-title">Shopping List</h2>';
    }

    wpurp_user_menus.recipeList = '';
    if(wpurp_user_menus.print_recipe_list) {
        console.log(wpurp_user_menus.recipes);
        wpurp_user_menus.recipeList = '<table class="wpurp-recipelist">';

        wpurp_user_menus.recipeList += wpurp_user_menus.print_recipe_list_header;

        jQuery('.user-menus-selected-recipes .user-menus-recipe').each(function() {
            wpurp_user_menus.recipeList += '<tr>';
            wpurp_user_menus.recipeList += '<td>' + jQuery(this).find('a').text() + '</td>';
            wpurp_user_menus.recipeList += '<td>' + jQuery(this).find('input').val() + '</td>';
            wpurp_user_menus.recipeList += '</tr>';
        });
        wpurp_user_menus.recipeList += '</table>';
    }

    wpurp_user_menus.shoppingList = '<table class="wpurp-shoppinglist">' + jQuery('.user-menus-ingredients').html() + '</table>';

    window.open(wpurp_user_menus.addonUrl + '/templates/print-shopping-list.php');
}

RecipeUserMenus.printUserMenu = function()
{
    // Recipes
    wpurp_user_menus.recipe_ids = [];
    wpurp_user_menus.recipes = [];
    wpurp_user_menus.print_servings_original = [];
    wpurp_user_menus.print_servings_wanted = [];
    wpurp_user_menus.print_unit_system = RecipeUserMenus.unitSystem;

    for(var i = 0, l = RecipeUserMenus.order.length; i < l; i++)
    {
        var order_id = RecipeUserMenus.order[i];
        var recipe = RecipeUserMenus.recipes[order_id];

        wpurp_user_menus.recipe_ids.push(recipe.id);
        wpurp_user_menus.print_servings_original.push(recipe.servings_original);
        wpurp_user_menus.print_servings_wanted.push(recipe.servings_wanted);
    }

    var data = {
        action: 'get_recipe_template',
        security: wpurp_user_menus.nonce,
        recipe_ids: wpurp_user_menus.recipe_ids
    };

    jQuery.post(wpurp_user_menus.ajaxurl, data, function(recipes) {
        wpurp_user_menus.recipes = recipes;
    }, 'json');

    // Shopping List
    var title = jQuery('.user-menus-title').val();
    if(title != undefined) {
        wpurp_user_menus.shoppingListTitle = '<h2>' + title + '</h2>';
    } else {
        wpurp_user_menus.shoppingListTitle = '<h2>Shopping List</h2>';
    }
    wpurp_user_menus.shoppingList = '<table class="wpurp-shoppinglist">' + jQuery('.user-menus-ingredients').html() + '</table>';

    window.open(wpurp_user_menus.addonUrl + '/templates/print-user-menu.php');
}

RecipeUserMenus.updateRecipeOrder = function(list)
{
    RecipeUserMenus.order = list.sortable('toArray', { attribute: 'data-index'} );
}


RecipeUserMenus.changeServings = function(input, general) {
    var servings_new = input.val();

    if(isNaN(servings_new) || servings_new <= 0) {
        servings_new = 1;
    }

    if(general) {
        RecipeUserMenus.generalServings = servings_new;
    } else {
        var index = input.parent('.user-menus-recipe').data('index');
        RecipeUserMenus.recipes[index].servings_wanted = servings_new;

        RecipeUserMenus.updateIngredientsTable();
        RecipeUserMenus.updateCookies();
    }
}

RecipeUserMenus.initSelect = function() {
    jQuery('.user-menus-select').select2({
        width: 'off'
    }).on('change', function() {
            // Add the selected recipe
            RecipeUserMenus.addRecipe(jQuery(this).select2('data'));

            // Clear the selection
            jQuery(this).select2('val', '');
        });
}

RecipeUserMenus.addRecipe = function(recipe) {
    if(recipe.id !== '')
    {
        RecipeUserMenus.recipes.push({
            id: recipe.id,
            name: recipe.text,
            link: recipe.element[0].dataset.link,
            servings_original: recipe.element[0].dataset.servings,
            servings_wanted: RecipeUserMenus.generalServings
        });

        RecipeUserMenus.order.push((RecipeUserMenus.recipes.length - 1).toString());
        RecipeUserMenus.redrawRecipes();
        RecipeUserMenus.updateCookies();
        RecipeUserMenus.updateIngredients();
    }
}

RecipeUserMenus.redrawRecipes = function() {
    var container = jQuery('.user-menus-selected-recipes');
    container.empty();

    var recipes = RecipeUserMenus.recipes;
    var order = RecipeUserMenus.order;

    RecipeUserMenus.nbrRecipes = 0;
    for(var i = 0, l = order.length; i < l; i++)
    {
        var recipe = recipes[order[i]];
        container.append(
            '<div class="user-menus-recipe" data-recipe="'+recipe.id+'" data-index="'+order[i]+'">' +
                '<i class="fa fa-trash delete-recipe-button"></i> ' +
                '<a href="'+recipe.link+'" target="_blank">'+recipe.name+'</a>' +
                '<input type="number" class="user-menus-servings-recipe" value="'+recipe.servings_wanted+'">' +
            '</div>');
        RecipeUserMenus.nbrRecipes += 1;
    }

    RecipeUserMenus.checkIfEmpty();
}

RecipeUserMenus.updateCookies = function() {
    if(RecipeUserMenus.menuId == 0) {
        var data = {
            action: 'update_shopping_list',
            security: wpurp_user_menus.nonce,
            recipes: RecipeUserMenus.recipes,
            order: RecipeUserMenus.order
        };

        jQuery.post(wpurp_user_menus.ajaxurl, data);
    }
}

RecipeUserMenus.checkIfEmpty = function() {
    if(RecipeUserMenus.nbrRecipes === 0) {
        jQuery('.user-menus-no-recipes').show();
    } else {
        jQuery('.user-menus-no-recipes').hide();
    }
}

RecipeUserMenus.groupBy = function(groupby) {
    var data = {
        action: 'user_menus_groupby',
        security: wpurp_user_menus.nonce,
        groupby: groupby,
        grid: wpurp_user_menu_grid.slug
    };

    jQuery.post(wpurp_user_menus.ajaxurl, data, function(html) {
        jQuery('.user-menus-select').select2('destroy').off().html(html);
        RecipeUserMenus.initSelect();
    });
}

RecipeUserMenus.updateIngredients = function()
{
    var order = RecipeUserMenus.order;
    var recipes = RecipeUserMenus.recipes;
    var ajaxCalls = 0;

    for(var i = 0, l = order.length; i < l; i++)
    {
        var recipe_id = recipes[order[i]].id;

        if(RecipeUserMenus.recipeIngredients[recipe_id] === undefined) {
            ajaxCalls++;

            RecipeUserMenus.recipeIngredients[recipe_id] = [];
            RecipeUserMenus.getIngredients(recipe_id);
        }
    }

    // No need to wait for non-existent ajax calls
    if(ajaxCalls === 0) {
        RecipeUserMenus.updateIngredientsTable();
    }
}

/**
 * Get recipe ingredients through ajax and put in cache
 *
 * @param recipe_id
 */
RecipeUserMenus.getIngredients = function(recipe_id)
{
    var data = {
        action: 'user_menus_get_ingredients',
        security: wpurp_user_menus.nonce,
        recipe_id: recipe_id
    };

    RecipeUserMenus.ajaxGettingIngredients++;

    jQuery.post(wpurp_user_menus.ajaxurl, data, function(ingredients) {
        RecipeUserMenus.ajaxGettingIngredients--;

        RecipeUserMenus.recipeIngredients[recipe_id] = ingredients;

        if(RecipeUserMenus.ajaxGettingIngredients === 0) {
            RecipeUserMenus.updateIngredientsTable();
        }

    }, 'json');
}

RecipeUserMenus.updateIngredientsTable = function()
{
    var ingredient_table = jQuery('table.user-menus-ingredients');
    ingredient_table.find('tbody').empty();

    var recipe_ingredients = RecipeUserMenus.recipeIngredients;
    var order = RecipeUserMenus.order;
    var recipes = RecipeUserMenus.recipes;
    var ingredients = [];
    var ingredients_plural = [];

    // Choose last cup type in systems. Not a perfect system.
    var cup_type = 236.6;
    for(var i = 0, l = wpurp_unit_conversion.systems.length; i < l; i++)
    {
        var system = wpurp_unit_conversion.systems[i];
        if(jQuery.inArray('cup', system.units_volume) != -1) {
            cup_type = parseFloat(system.cup_type);
        }
    }

    for(var i = 0, l = order.length; i < l; i++)
    {
        var recipe_id = recipes[order[i]].id;
        var servings = recipes[order[i]].servings_wanted / parseFloat(recipes[order[i]].servings_original);

        for(var j = 0, m = recipe_ingredients[recipe_id].length; j < m; j++)
        {
            var ingredient = recipe_ingredients[recipe_id][j];
            var name = wpurp_user_menus.ingredient_notes && ingredient.notes ? ingredient.ingredient + ' (' + ingredient.notes + ')' : ingredient.ingredient;
            var group = ingredient.group;
            var plural = ingredient.plural || name;

            if(ingredients_plural[name] === undefined) {
                ingredients_plural[name] = plural;
            }

            if(ingredients[group] === undefined) {
                ingredients[group] = [];
            }

            if(ingredients[group][name] === undefined) {
                ingredients[group][name] = [];
            }

            var amount = ingredient.amount_normalized * servings;

            var unit = ingredient.unit;
            var type = RecipeUnitConversion.getUnitFromAlias(unit);

            if(type !== undefined && wpurp_user_menus.consolidate_ingredients == '1') {
                var abbreviation = RecipeUnitConversion.getAbbreviation(type);

                // Adjust for cup type
                if(abbreviation == 'cup') {
                    var qty_cup = new Qty('1 cup').to('ml').scalar;

                    if(Math.abs(cup_type - qty_cup) > 0.1) { // 236.6 == 236.588238
                        amount = amount * (cup_type / qty_cup);
                    }
                }

                var quantity = new Qty('' + amount + ' ' + abbreviation);
                var base_quantity = quantity.toBase();

                if(base_quantity.units() == 'm3') {
                    base_quantity = base_quantity.to('l');
                }

                unit = base_quantity.units();
                amount = base_quantity.scalar;

            }

            if(unit == '') {
                unit = 'wpurp_nounit';
            }

            if(ingredients[group][name][unit] === undefined) {
                ingredients[group][name][unit] = 0.0;
            }

            ingredients[group][name][unit] += parseFloat(amount);
        }
    }

    // Sort ingredients by name
    var group_keys = Object.keys(ingredients);
    group_keys.sort(function (a, b) { // Case insensitive sort
        return a.toLowerCase().localeCompare(b.toLowerCase());
    });

    for(i = 0, l = group_keys.length; i < l; i++)
    {
        var group_key = group_keys[i];
        var group = ingredients[group_key];

        var group_row = jQuery('<tr><td colspan="2"><strong>'+group_key+'</strong></td></tr>');
        ingredient_table.append(group_row);

        var ingredient_keys = Object.keys(group);
        ingredient_keys.sort(function (a, b) { // Case insensitive sort
            return a.toLowerCase().localeCompare(b.toLowerCase());
        });

        for(j = 0, m = ingredient_keys.length; j < m; j++)
        {
            var ingredient = ingredient_keys[j];

            var units = group[ingredient];
            for(var unit in units)
            {
                var amount = units[unit];

                if(isFinite(amount))
                {
                    var ingredient_row = jQuery('<tr></tr>');

                    var actual_unit = RecipeUnitConversion.getUnitFromAlias(unit);

                    // Get the Unit Systems we need to generate
                    if(wpurp_user_menus.adjustable_system == '1') {
                        var systems = [RecipeUserMenus.unitSystem];
                    } else {
                        var systems = wpurp_user_menus.static_systems;
                    }

                    var plural = false;

                    for(var s = 0; s < systems.length; s++) {
                        var converted_amount = amount;
                        var converted_unit = unit;

                        if(unit == 'wpurp_nounit') {
                            converted_unit = '';
                        } else if(actual_unit !== undefined) {
                            if(wpurp_user_menus.consolidate_ingredients == '1' || (!RecipeUnitConversion.isUniversal(actual_unit) && jQuery.inArray(systems[s], RecipeUnitConversion.getUnitSystems(actual_unit)) == -1)) {
                                var quantity = RecipeUnitConversion.convertUnitToSystem(amount, actual_unit, 0, systems[s]);
                                var converted_amount = quantity.amount;
                                converted_unit = RecipeUnitConversion.getUserAbbreviation(quantity.unit, converted_amount);
                            }
                        }

                        converted_amount = RecipeUnitConversion.formatNumber(converted_amount, wpurp_user_menus.fractions);
                        if(converted_amount !== '1') {
                            plural = true;
                        }

                        ingredient_row.append('<td>'+converted_amount+' '+ converted_unit +'</td>');
                    }

                    var ingredient_name = plural ? ingredients_plural[ingredient] : ingredient;
                    var ingredient_name = ingredient_name.charAt(0).toUpperCase() + ingredient_name.slice(1);
                    var checkbox = '';

                    if(wpurp_user_menus.checkboxes == '1') {
                        checkbox = '<input type="checkbox" class="shopping-list-ingredient"> ';
                    }
                    ingredient_row.prepend('<td>'+checkbox+ingredient_name+'</td>');
                    
                    ingredient_table.append(ingredient_row);
                }
            }
        }
    }
}

RecipeUserMenus.changeUnits = function(dropdown)
{
    RecipeUserMenus.unitSystem = parseInt(jQuery(dropdown).val());
    RecipeUserMenus.updateIngredientsTable();
}

jQuery(document).ready(function(){
    if(jQuery('.wpurp-user-menus').length > 0) {
        RecipeUserMenus.init();
    }
});