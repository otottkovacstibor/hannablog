var RecipePrintButton = RecipePrintButton || {};

RecipePrintButton.update = function(recipe) {
    recipe.find('.wpurp-recipe-print-button').each(function() {
        var button = jQuery(this);

        var link = button.data('original-link');

        if(!link) {
            link = button.attr('href');
            button.data('original-link', link);
        }

        var separator = wpurp_print.permalinks ? '/' : '=';

        if(link.slice(-1) != separator) {
            link += separator;
        }

        var system = recipe.find('select.adjust-recipe-unit:visible option:selected').text();

        if(system) {
            var system_link = system.toLowerCase()
                .replace(/ /g,'-')
                .replace(/[-]+/g, '-')
                .replace(/[^\w-]+/g,'');

            link += system_link + '/';
        }

        // Check if there is a servings changer (both free and Premium)
        var servings_input = recipe.find('input.adjust-recipe-servings:visible');
        if(servings_input.length == 0) {
            servings_input = recipe.find('input.advanced-adjust-recipe-servings:visible');
        }

        if(servings_input.length != 0) {
            link += servings_input.val();
        }

        button.attr('href', link);
    });
};

jQuery(document).ready(function() {

    jQuery(document).on('click', '.wpurp-recipe-print-button, .wpurp-print-recipe-shortcode', function(e) {
        // RecipeId only present when using old way
        var recipeId = jQuery(this).data('recipe-id');
        if(recipeId) {
            e.preventDefault();
            e.stopPropagation();

            var recipe = jQuery(this).parents('.wpurp-container');

            wpurp_print.servings_original = parseFloat(recipe.data('servings-original'));
            wpurp_print.old_system = parseInt(recipe.data('system-original'));
            wpurp_print.new_system = recipe.find('select.adjust-recipe-unit option:selected').val();

            // Check if the page was in RTL
            wpurp_print.rtl = jQuery('body').hasClass('rtl');

            // Check if there is a servings changer (both free and Premium)
            var servings_input = recipe.find('input.adjust-recipe-servings');
            if(servings_input.length == 0) {
                servings_input = recipe.find('input.advanced-adjust-recipe-servings');
            }

            // Take servings from serving changer if available or just use original
            if(servings_input.length == 0) {
                wpurp_print.servings_new = wpurp_print.servings_original;
            } else {
                wpurp_print.servings_new = parseFloat(servings_input.val());
            }

            // Get print template via AJAX
            wpurp_print.template = '';

            var data = {
                action: 'get_recipe_template',
                security: wpurp_print.nonce,
                recipe_id: recipeId
            };

            jQuery.post(wpurp_print.ajaxurl, data, function(template) {
                wpurp_print.template = template.output;
                wpurp_print.fonts = template.fonts;
            }, 'json');

            // Open print version of recipe in blank page
            window.open(wpurp_print.coreUrl + '/templates/print.php', '_blank');
        }
    });
});