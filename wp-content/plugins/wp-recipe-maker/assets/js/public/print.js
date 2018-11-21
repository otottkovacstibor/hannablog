import { get_active_system } from '../../../../wp-recipe-maker-premium/addons-pro/unit-conversion/assets/js/shared/unit-conversion';

function print_recipe(recipe_id, servings, system) {
	var print_window = window.open(wprm_public.home_url + 'wprm_print/' + recipe_id, '_blank');
	print_window.onload = function() {
		print_window.focus();
		print_window.document.title = document.title;
		print_window.history.pushState('', 'Print Recipe', location.href.replace(location.hash,""));
		print_window.set_print_system(system);
		print_window.set_print_servings(servings);

		setTimeout(function() {
			print_window.print();
		}, 250);
	};
};

jQuery(document).ready(function($) {
	jQuery('.wprm-recipe-print, .wprm-print-recipe-shortcode').on('click', function(e) {

		var recipe_id = jQuery(this).data('recipe-id');

		// Backwards compatibility.
		if (!recipe_id) {
			recipe_id = jQuery(this).parents('.wprm-recipe-container').data('recipe-id');
		}

		// Follow the link if still no recipe id, otherwise override link functionality.
		if (recipe_id) {
			e.preventDefault();

			var	recipe = jQuery('#wprm-recipe-container-' + recipe_id);
			var servings = false,
				system = 1;

			if (0 < recipe.length) {
				servings = parseInt(recipe.find('.wprm-recipe-servings').data('servings'));
				system = get_active_system(recipe);
			}

			print_recipe(recipe_id, servings, system);
		}
	});
});
