jQuery(document).ready(function() {
    // Select2
    jQuery('.wpurp-recipe-grid-filter').select2({
        allowClear: true,
        width: 'off',
        dropdownAutoWidth: false
    });

    // Get and prepare all Recipe Grids on the page
    var RecipeGrids = {};
    jQuery('.wpurp-recipe-grid-container').each(function() {
        var grid_name = jQuery(this).data('grid-name');

        var recipes = window['wpurp_recipe_grid_'+grid_name]['recipes'];
        var template = window['wpurp_recipe_grid_'+grid_name]['template'];
        var orderby = window['wpurp_recipe_grid_'+grid_name]['orderby'];
        var order = window['wpurp_recipe_grid_'+grid_name]['order'];
        var limit = window['wpurp_recipe_grid_'+grid_name]['limit'];
        var images_only = window['wpurp_recipe_grid_'+grid_name]['images_only'];
        var filters_arr = window['wpurp_recipe_grid_'+grid_name]['filters'];
        var match_all = window['wpurp_recipe_grid_'+grid_name]['match_all'];
        var match_parents = window['wpurp_recipe_grid_'+grid_name]['match_parents'];

        var filters = {};
        for(var i = 0, l = filters_arr.length; i < l; i++) {
            jQuery.extend(filters, filters_arr[i]);
        }

        RecipeGrids[grid_name] = {
            recipes: recipes,
            template: template,
            orderby: orderby,
            order: order,
            limit: limit,
            images_only: images_only,
            offset: 0,
            filters: filters,
            match_all: match_all,
            match_parents: match_parents
        };
    });

    // Go to recipe when clicking on card in Grid
    jQuery(document).on('click', '.recipe-card', function() {
        var link = jQuery(this).data('link');
        window.location.href = link;
    });

    // Remove links in Grid (But load Socialite first if sharing buttons are present)
    if(jQuery('.recipe-card .socialite').length > 0) {
        Socialite.load();
    }
    jQuery('.recipe-card a:not(.wpurp-recipe-favorite, .wpurp-recipe-add-to-shopping-list, .wpurp-recipe-print-button, .wpurp-recipe-grid-link)').replaceWith(function() { return jQuery(this).contents(); });

    // Handle a filter selection
    jQuery('.wpurp-recipe-grid-filter').on('change', function() {
        var taxonomy = jQuery(this).attr('id').substr(7);
        var value = jQuery(this).val();
        var grid_name = jQuery(this).data('grid-name');

        if(value !== null && !jQuery.isArray(value)) {
            value = [value];
        }

        RecipeGrids[grid_name]['filters'][taxonomy] = value;

        jQuery('#wpurp-recipe-grid-'+grid_name).empty();
        getAndAddRecipesTo(grid_name);
    });

    function getAndAddRecipesTo(grid_name) {
        var data = {
            action: 'recipe_grid_get_recipes',
            security: wpurp_recipe_grid.nonce,
            grid: RecipeGrids[grid_name],
            grid_name: grid_name
        };

        var grid = jQuery('#wpurp-recipe-grid-'+grid_name);

        // Add spinner
        grid.append('<div id="floatingCirclesG"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div>');

        // Get recipes through AJAX
        jQuery.post(wpurp_recipe_grid.ajaxurl, data, function(html) {
            grid.append(html).find('#floatingCirclesG').remove();
            jQuery('.recipe-card a').replaceWith(function() { return jQuery(this).contents(); });
            grid.trigger('recipeGridChanged');
        });
    }
});