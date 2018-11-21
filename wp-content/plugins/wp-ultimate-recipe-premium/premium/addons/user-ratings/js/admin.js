jQuery(document).ready(function() {
    jQuery('.reset-recipe-rating').on('click', function() {
        if(confirm(wpurp_user_ratings.confirm)) {
            var recipe = jQuery(this).data('recipe');

            var data = {
                action: 'reset_recipe_rating',
                security: wpurp_user_ratings.nonce,
                recipe: recipe
            };

            jQuery.post(wpurp_user_ratings.ajax_url, data, function(out) {
                window.location.reload();
            });
        }
    });
});