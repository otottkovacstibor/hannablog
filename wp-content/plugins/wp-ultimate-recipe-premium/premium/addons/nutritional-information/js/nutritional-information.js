var NutritionalInformation = NutritionalInformation || {};

jQuery(document).ready(function() {
    NutritionalInformation.init();
});

// Variables
NutritionalInformation.loader = '<div class="wpurp-loader"><div></div><div></div><div></div></div>';
NutritionalInformation.nutritional_results = [];

// Functions
NutritionalInformation.init = function() {
    // Get Nutritional Data button
    jQuery('.wpurp-get-nutritional-panel').on('click', function() {
        var ingredient = jQuery(this).data('ingredient');

        NutritionalInformation.closePanel(ingredient, function() {
            jQuery('#nutritional-panel-' + ingredient + ' .get-nutritional').show();
            jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional').hide();
            NutritionalInformation.openPanel(ingredient);

            NutritionalInformation.searchIngredient(ingredient);
        });
    });

    // Edit Nutritional Data button
    jQuery('.wpurp-edit-nutritional-panel').on('click', function() {
        var ingredient = jQuery(this).data('ingredient');

        NutritionalInformation.closePanel(ingredient, function() {
            jQuery('#nutritional-panel-' + ingredient + ' .get-nutritional').hide();
            jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional').show();
            NutritionalInformation.openPanel(ingredient);
        });
    });

    // Limit ingredients shown by recipe
    jQuery('#limit-recipe').select2({
        width: 'element'
    }).on('change', function() {
        window.location = window.location.href + '&limit_by_recipe=' + jQuery(this).val();
    });
}

NutritionalInformation.closePanel = function(ingredient, callback) {
    jQuery('#nutritional-panel-' + ingredient).slideUp(250, callback);
}

NutritionalInformation.openPanel = function(ingredient, callback) {
    jQuery('#nutritional-panel-' + ingredient).slideDown(500, callback);
}

NutritionalInformation.searchIngredient = function(ingredient) {
    var name = jQuery('#search-ingredient-' + ingredient).val();

    if( name.length > 0 ) {
        var loader = jQuery(NutritionalInformation.loader);
        var results = jQuery('#get-nutritional-results-' + ingredient);

        jQuery('#nutritional-panel-' + ingredient + ' .get-nutritional-search').append(loader);
        results.slideUp(500);

        var data = {
            action: 'search_ingredients',
            security: wpurp_nutritional_information.nonce,
            name: name
        };

        jQuery.post(wpurp_nutritional_information.ajaxurl, data, function(ingredients) {
            loader.remove();
            results.empty();
            var contents = "No results found. Try a manual search."

            if(ingredients.foods != undefined && ingredients.foods.total_results > 0) {
                contents = jQuery('<table></table>');

                for(var i = 0; i < ingredients.foods.food.length; i++) {
                    var food = ingredients.foods.food[i];

                    var row = '<tr>';
                    row += '<td><button type="button" class="button" onclick="NutritionalInformation.getNutritional('+ingredient+', '+food.food_id+')">Use</button></td>';
                    row += '<td>'+food.food_name+'</td>';

                    if(food.food_type == 'Brand') {
                        row += '<td>'+food.brand_name+'</td>';
                    } else {
                        row += '<td>Generic</td>';
                    }

                    row += '<td>'+food.food_description+'</td>';
                    row += '</tr>';
                    contents.append(row);
                }
            }

            results.append(contents);
            results.slideDown(500);
        }, 'json');
    }
}

NutritionalInformation.getNutritional = function(ingredient, food_id) {
    var loader = jQuery(NutritionalInformation.loader);
    var results = jQuery('#get-nutritional-results-' + ingredient);

    jQuery('#nutritional-panel-' + ingredient + ' .get-nutritional-search').append(loader);
    results.slideUp(500);

    var data = {
        action: 'get_nutritional',
        security: wpurp_nutritional_information.nonce,
        food: food_id
    };

    jQuery.post(wpurp_nutritional_information.ajaxurl, data, function(nutritional) {
        loader.remove();
        results.empty();
        var contents = "Something went wrong. Please try again later."

        if(nutritional.food != undefined && nutritional.food.servings.serving != undefined) {
            // Store nutritional data
            NutritionalInformation.nutritional_results[ingredient] = nutritional.food;

            if(nutritional.food.servings.serving.length == undefined) {
                // Only 1 result, use it
                contents = false;
                NutritionalInformation.useNutritional(ingredient, false);
            } else {
                // Multiple results, let them choose
                contents = jQuery('<table></table>');

                for(var i = 0; i < nutritional.food.servings.serving.length; i++) {
                    var serving = nutritional.food.servings.serving[i];

                    var row = '<tr>';
                    row += '<td><button type="button" class="button" onclick="NutritionalInformation.useNutritional('+ingredient+', '+i+')">Use</button></td>';
                    row += '<td>'+serving.serving_description+'</td>';
                    row += '<td>'+serving.metric_serving_amount+' '+serving.metric_serving_unit+'</td>';
                    row += '<td>&nbsp;</td>';
                    row += '</tr>';
                    contents.append(row);
                }
            }
        }

        if(contents) {
            results.append(contents);
            results.slideDown(500);
        }
    }, 'json');
}

NutritionalInformation.useNutritional = function(ingredient, servings) {
    jQuery('#nutritional-panel-' + ingredient + ' .get-nutritional').slideUp(250);
    var food = NutritionalInformation.nutritional_results[ingredient];

    if(food != undefined) {
        if(servings === false) {
            var nutritional = food.servings.serving;
        } else {
            var nutritional = food.servings.serving[servings];
        }

        // Check metric serving unit
        if(nutritional.metric_serving_unit != 'g') {
            var select = jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional select[name="serving_unit"]');

            // It's not g or ml?! Add it to the select and hope for the best
            if(nutritional.metric_serving_unit != 'ml') {
                select.append(jQuery('<option>', {
                    value: nutritional.metric_serving_unit,
                    text: nutritional.metric_serving_unit
                }));
            }

            // Select the new value
            select.val(nutritional.metric_serving_unit);
        }

        // Update all nutritional fields
        jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional input').each(function() {
            var input = jQuery(this);
            var name = input.attr('name');

            switch(name) {
                case 'serving':
                    input.val(nutritional.serving_description);
                    break;
                case 'serving_quantity':
                    input.val(parseFloat(nutritional.metric_serving_amount));
                    break;
                default:
                    input.val(nutritional[name]);
                    break;
            }
        });

        jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional').slideDown(500);
    }
}

NutritionalInformation.saveNutritional = function(ingredient) {
    var loader = jQuery(NutritionalInformation.loader);
    var summary = jQuery('#nutritional-summary-' + ingredient);

    summary.html(loader);
    NutritionalInformation.closePanel(ingredient);

    var nutritional = {};

    jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional input').each(function() {
        var input = jQuery(this);
        var name = input.attr('name');

        nutritional[name] = input.val();
    });

    nutritional['serving_unit'] = jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional select[name="serving_unit"]').val();

    var data = {
        action: 'save_nutritional',
        security: wpurp_nutritional_information.nonce,
        ingredient: ingredient,
        nutritional: nutritional
    };

    jQuery.post(wpurp_nutritional_information.ajaxurl, data, function(html) {
        loader.remove();
        summary.html(html);
    }, 'html');
}

NutritionalInformation.calculateRecipe = function(recipe) {
    var system = RecipeUnitConversion.determineIngredientListSystem(jQuery('.calculate-recipe-panel'));

    jQuery('.calculate-recipe-panel tr.ingredient-nutritional-match').each(function() {
        var row = jQuery(this);
        var ingredient = row.data('ingredient');
        var data = jQuery('#nutritional-panel-' + ingredient + ' .edit-nutritional');

        var base_amount = parseFloat(data.find('input[name="serving_quantity"]').val());

        if(isNaN(base_amount)) {
            row.find('td:nth-child(2)').html('n/a');
            row.find('td:nth-child(3)').html('&nbsp;');
        } else {
            // Input variables
            var amount = row.data('amount');
            var alias = row.data('alias');
            var unit = RecipeUnitConversion.getUnitFromAlias(alias);

            var base_alias = data.find('select[name="serving_unit"]').val();
            var base_unit = RecipeUnitConversion.getUnitFromAlias(base_alias);

            var summary = jQuery('#nutritional-summary-' +ingredient);
            var ref_amount = summary.find('.ref-amount-unit').data('amount');
            var ref_unit = summary.find('.ref-amount-unit').data('unit');

            // Output
            var converted = '';

            // Calculate converted amount
            if(amount && ref_amount && (alias == ref_unit || alias == (ref_unit + 's'))) {
                converted = amount * base_amount / ref_amount;
            } else if(unit !== undefined && amount) {
                converted = NutritionalInformation.getConvertedAmount(system, amount, unit, base_unit);
            }

            // Set calculated amount
            var input = '= <input type="text" value="' + converted + '" data-base="'+base_amount+'"/> ' + base_alias;

            row.find('td:nth-child(2)').html(input);
            row.find('td:nth-child(3)').html(summary.html());
        }
    });

    jQuery('.calculate-recipe-panel').slideUp(250, function() {
        jQuery('.calculate-recipe-panel').slideDown(500);
    });
}

NutritionalInformation.getConvertedAmount = function(old_system, amount, unit, new_unit) {
    var systems = wpurp_unit_conversion.systems;

    // Adjust for cup type
    if(unit == 'cup') {
        var old_cup = parseFloat(systems[old_system].cup_type);
        var qty_cup = new Qty('1 cup').to('ml').scalar;

        if(Math.abs(old_cup - qty_cup) > 0.1) { // 236.6 == 236.588238
            amount = amount * (old_cup / qty_cup);
        }
    }

    // Old quantity
    var quantity = new Qty(amount + ' ' + RecipeUnitConversion.getAbbreviation(unit));

    // Check if unit types match
    var old_unit_type = RecipeUnitConversion.getUnitType(unit);
    var new_unit_type = RecipeUnitConversion.getUnitType(new_unit);

    // Do a simple conversion (1 g = 1 ml) if types don't match
    if(old_unit_type !== new_unit_type) {
        if(old_unit_type == 'volume' && new_unit_type == 'weight') {
            var base_amount = quantity.to('ml').scalar;
            quantity = new Qty(base_amount + ' g');
        } else if(old_unit_type == 'weight' && new_unit_type == 'volume') {
            var base_amount = quantity.to('g').scalar;
            quantity = new Qty('' + base_amount + ' ml');
        }
    }

    // Calculate converted amount
    try {
        var new_amount = quantity.to(RecipeUnitConversion.getAbbreviation(new_unit)).scalar;
    } catch (err) {
        var new_amount = '';
    }

    return new_amount;
}

NutritionalInformation.useCalculatedRecipe = function(recipe) {
    var ingredients = [];

    var servings = parseInt(jQuery('.calculate-recipe-panel').data('servings'));

    jQuery('.calculate-recipe-panel tr.ingredient-nutritional-match').each(function() {
        var row = jQuery(this);
        var ingredient = row.data('ingredient');
        var amount = row.find('td:nth-child(2) input').val();
        var base_amount = row.find('td:nth-child(2) input').data('base');
        var rate = amount / servings / base_amount;

        if(!isNaN(rate)) {
            ingredients.push({
                id: ingredient,
                rate: rate
            });
        }
    });

    jQuery('.wpurp-recipe-nutritional .nutritional-data input').each(function() {
        var input = jQuery(this);
        var total = 0;

        for(var i = 0; i < ingredients.length; i++) {
            var ingredient = ingredients[i];

            var data = parseFloat(jQuery('#nutritional-panel-' + ingredient.id + ' .edit-nutritional input[name="'+input.attr('name')+'"]').val());

            if(!isNaN(data)) {
                total += data * ingredient.rate;
            }
        }

        input.val(RecipeUnitConversion.formatNumber(total, false, 0));
    });
}

NutritionalInformation.saveRecipe = function(recipe) {
    var loader = jQuery(NutritionalInformation.loader);
    jQuery('.save-recipe-buttons').append(loader);

    var nutritional = {};

    jQuery('.wpurp-recipe-nutritional .nutritional-data input').each(function() {
        var input = jQuery(this);
        var name = input.attr('name');

        nutritional[name] = input.val();
    });

    var data = {
        action: 'save_nutritional_recipe',
        security: wpurp_nutritional_information.nonce,
        recipe: recipe,
        nutritional: nutritional
    };

    jQuery.post(wpurp_nutritional_information.ajaxurl, data, function(html) {
        var msg = jQuery('<span>Saved successfully</span>');
        loader.replaceWith(msg);

        setTimeout(function() {
            msg.remove();
        }, 1000);
    }, 'html');
}

NutritionalInformation.resetRecipe = function(recipe) {
    jQuery('.wpurp-recipe-nutritional .nutritional-data input').each(function() {
        jQuery(this).val('');
    });
}