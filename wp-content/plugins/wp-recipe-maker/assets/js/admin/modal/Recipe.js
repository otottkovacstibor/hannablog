import * as utils from './utils';
import recipeInit from './RecipeInit';
import RichEditor from './RichEditor';

let Recipe = {
    id: 0,
    type: 'insert',
    fields: false,
    ingredient: {},
    instructions: {},
    prep_time_set: false,
    cook_time_set: false,
    total_time_set: false,
    init: function() {
        recipeInit(this);
    },
    clear: function() {
        // Recipe Details
        jQuery('#wprm-recipe-type').val('food').change();
		utils.remove_media('image', jQuery('.wprm-recipe-image-container'));
		jQuery('#wprm-recipe-name').val('');
		RichEditor.clear(); // Recipe summary
		jQuery('#wprm-recipe-author-display').val('default').change();
		jQuery('#wprm-recipe-author-link').val('');
		jQuery('#wprm-recipe-servings').val('');
		jQuery('#wprm-recipe-servings-unit').val('');
		jQuery('#wprm-recipe-calories').val('');
		jQuery('#wprm-recipe-prep-time').val('');
		jQuery('#wprm-recipe-cook-time').val('');
		jQuery('#wprm-recipe-total-time').val('');
		jQuery('#wprm-recipe-custom-time').val('');
		jQuery('#wprm-recipe-custom-time-label').val('');

		this.prep_time_set = false;
		this.cook_time_set = false;
		this.total_time_set = false;

        jQuery('.wprm-recipe-tags').val(null).trigger('change');
        
        // Default custom author.
        var author_name_input = jQuery('#wprm-recipe-author-name');
        author_name_input.val(author_name_input.data('default'));

		// Ingredients & Instructions
		jQuery('.wprm-recipe-ingredients .wprm-recipe-ingredients-instructions-delete, .wprm-recipe-instructions .wprm-recipe-ingredients-instructions-delete').each(function() {
			jQuery(this).click();
		});
		jQuery('.wprm-recipe-ingredients-add').click();
		jQuery('.wprm-recipe-instructions-add').click();

        // Recipe Notes
        utils.remove_media('video', jQuery('.wprm-recipe-video-container'));
		if (typeof tinyMCE !== 'undefined' && tinyMCE.get('wprm_recipe_notes') && !tinyMCE.get('wprm_recipe_notes').isHidden()) {
            tinyMCE.get('wprm_recipe_notes').focus(true);
            
            // Check for error caused by EasyRecipe.
            jQuery('.wprm-easyrecipe-warning').hide();
            try {
                tinyMCE.activeEditor.setContent('');
            } catch(err) {
                jQuery('.wprm-easyrecipe-warning').show();
            }
		} else {
            jQuery('#wprm_recipe_notes').val('');
		}
    },
    set: function(recipe) {
        // Recipe Details
		if (parseInt(recipe.image_id) > 0) {
            utils.set_media('image', jQuery('.wprm-recipe-details-form #wprm-recipe-image-container'), recipe.image_id, recipe.image_url);
        }

        if (parseInt(recipe.pin_image_id) > 0) {
            utils.set_media('image', jQuery('.wprm-recipe-details-form #wprm-recipe-pin-image-container'), recipe.pin_image_id, recipe.pin_image_url);
        }

        jQuery('#wprm-recipe-type').val(recipe.type).change();
        jQuery('#wprm-recipe-name').val(recipe.name);
        RichEditor.set(recipe.summary);
        jQuery('#wprm-recipe-servings-unit').val(recipe.servings_unit);
        jQuery('#wprm-recipe-custom-time-label').val(recipe.custom_time_label);

        jQuery('#wprm-recipe-author-display').val(recipe.author_display).change();
        jQuery('#wprm-recipe-author-name').val(recipe.author_name);
        jQuery('#wprm-recipe-author-link').val(recipe.author_link);

        var servings = parseInt(recipe.servings) > 0 ? parseInt(recipe.servings) : '',
            calories = recipe.nutrition.calories ? parseFloat(recipe.nutrition.calories) : '',
            prep_time = parseInt(recipe.prep_time) > 0 ? parseInt(recipe.prep_time) : '',
            cook_time = parseInt(recipe.cook_time) > 0 ? parseInt(recipe.cook_time) : '',
            total_time = parseInt(recipe.total_time) > 0 ? parseInt(recipe.total_time) : '',
            custom_time = parseInt(recipe.custom_time) > 0 ? parseInt(recipe.custom_time) : '';

        jQuery('#wprm-recipe-servings').val(servings);
        jQuery('#wprm-recipe-calories').val(calories);
        jQuery('#wprm-recipe-prep-time').val(prep_time);
        jQuery('#wprm-recipe-cook-time').val(cook_time);
        jQuery('#wprm-recipe-total-time').val(total_time);
        jQuery('#wprm-recipe-custom-time').val(custom_time);

        if (prep_time) this.prep_time_set = true;
        if (cook_time) this.cook_time_set = true;
        if (total_time) this.total_time_set = true;

        for (var tag in recipe.tags) {
            if (recipe.tags.hasOwnProperty(tag)) {
                this.setTags(recipe, tag);
            }
        }
        
        // Ingredients & Instructions
        this.setIngredients(recipe.ingredients);
        this.setInstructions(recipe.instructions);

        // Recipe Notes
        if (parseInt(recipe.video_id) > 0) {
            utils.set_media('video',jQuery('.wprm-recipe-notes-form .wprm-recipe-video-container'), recipe.video_id, recipe.video_thumb_url);
        } else if (recipe.video_embed) {
            utils.set_media_embed('video',jQuery('.wprm-recipe-notes-form .wprm-recipe-video-container'), recipe.video_embed);
        }

        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('wprm_recipe_notes') && !tinyMCE.get('wprm_recipe_notes').isHidden()) {
            tinyMCE.get('wprm_recipe_notes').focus(true);
            tinyMCE.activeEditor.setContent(recipe.notes);
        } else {
            jQuery('#wprm_recipe_notes').val(recipe.notes);
        }
    },
    setTags: function(recipe, tag) {
        var term_ids = [],
            select = jQuery('#wprm-recipe-tag-' + tag);

		for (var i = 0, l = recipe.tags[tag].length; i < l; i++) {
            var term = recipe.tags[tag][i];
            term_ids.push(term.term_id);

            // Add term to options if not in there
            if (select.find('option[value=' + term.term_id + ']').length === 0) {
                select.append('<option value="' + term.term_id + '">' + term.name + '</option>');
            }
		}
		select.val(term_ids).trigger('change');
    },
    setIngredients: function(ingredients) {
        jQuery('.wprm-recipe-ingredients .wprm-recipe-ingredients-instructions-delete').each(function() {
            jQuery(this).click();
        });
    
        var i, l, group, j, m;
    
        for (i = 0, l = ingredients.length; i < l; i++) {
            group = ingredients[i];
    
            if (i > 0 || group.name !== '') {
                Recipe.addIngredientGroup(group.name);
            }
    
            for (j = 0, m = group.ingredients.length; j < m; j++) {
                var ingredient = group.ingredients[j];
                var uid = Recipe.addIngredient(ingredient.amount, ingredient.unit, ingredient.name, ingredient.notes);
                Recipe.ingredients[uid] = ingredient;
            }
        }
    },
    setInstructions: function(instructions) {
        jQuery('.wprm-recipe-instructions .wprm-recipe-ingredients-instructions-delete').each(function() {
            jQuery(this).click();
        });
    
        var i, l, group, j, m;
    
        for (i = 0, l = instructions.length; i < l; i++) {
            group = instructions[i];
    
            if (i > 0 || group.name !== '') {
                Recipe.addInstructionGroup(group.name);
            }
    
            for (j = 0, m = group.instructions.length; j < m; j++) {
                var instruction = group.instructions[j];
                var uid = Recipe.addInstruction(instruction.text, instruction.image);
                Recipe.instructions[uid] = instruction;
            }
        }
    },
    get: function() {
        // Default author display
        var author_display = jQuery('#wprm-recipe-author-display').val();

        if(author_display == 'default') {
            author_display = jQuery('#wprm-recipe-author-display').find('option:first').data('default');	
        }

        // Recipe Details
        var recipe = {
            type: jQuery('#wprm-recipe-type').val(),
            image_id: jQuery('#wprm-recipe-image-id').val(),
            pin_image_id: jQuery('#wprm-recipe-pin-image-id').val(),
            video_id: jQuery('#wprm-recipe-video-id').val(),
            video_embed: jQuery('#wprm-recipe-video-embed').val(),
            name: jQuery('#wprm-recipe-name').val(),
            summary: jQuery('#wprm-recipe-summary').val(),
            author_display: author_display,
            author_name: jQuery('#wprm-recipe-author-name').val(),
            author_link: jQuery('#wprm-recipe-author-link').val(),
            servings: jQuery('#wprm-recipe-servings').val(),
            servings_unit: jQuery('#wprm-recipe-servings-unit').val(),
            prep_time: jQuery('#wprm-recipe-prep-time').val(),
            cook_time: jQuery('#wprm-recipe-cook-time').val(),
            total_time: jQuery('#wprm-recipe-total-time').val(),
            custom_time: jQuery('#wprm-recipe-custom-time').val(),
            custom_time_label: jQuery('#wprm-recipe-custom-time-label').val(),
            nutrition: {
                calories: jQuery('#wprm-recipe-calories').val()
            },
            tags: {
                course: jQuery('#wprm-recipe-tag-course').val(),
                cuisine: jQuery('#wprm-recipe-tag-cuisine').val()
            }
        };

        // Recipe Tags
        recipe.tags = {};
        jQuery('.wprm-recipe-tags').each(function() {
            recipe.tags[jQuery(this).data('key')] = jQuery(this).val();
        });

        // Recipe Ingredients
        recipe.ingredients = this.getIngredients();

        // Recipe Instructions
        var instructions = [];
        var instruction_group = {
            name: '',
            instructions: []
        };
        jQuery('.wprm-recipe-instructions').find('tr').each(function() {
            var row = jQuery(this);
            if (row.hasClass('wprm-recipe-instruction-group')) {
                // Add current instruction group to instructions
                instructions.push(instruction_group);

                instruction_group = {
                        name: row.find('.wprm-recipe-instruction-group-name').val(),
                        instructions: []
                };
            } else {
                instruction_group.instructions.push({
                        text: row.find('textarea.wprm-recipe-instruction-text').val(),
                        image: row.find('.wprm-recipe-instruction-image').val()
                });
            }
        });
        // Add remaining instruction group
        instructions.push(instruction_group);

        recipe.instructions = instructions;

        // Recipe Notes
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('wprm_recipe_notes') && !tinyMCE.get('wprm_recipe_notes').isHidden()) {
            recipe.notes = tinyMCE.get('wprm_recipe_notes').getContent();
        } else {
            recipe.notes = jQuery('#wprm_recipe_notes').val();
        }

        return recipe;
    },
    getIngredients: function() {
        var ingredients = [];
        var ingredient_group = {
            name: '',
            ingredients: []
        };
        jQuery('.wprm-recipe-ingredients').find('tr').each(function() {
            var row = jQuery(this);
            if (row.hasClass('wprm-recipe-ingredient-group')) {
                // Add current ingredient group to ingredients
                ingredients.push(ingredient_group);

                ingredient_group = {
                    name: row.find('.wprm-recipe-ingredient-group-name').val(),
                    ingredients: []
                };
            } else {
                var uid = row.data('uid'),
                    ingredient = {};

                // Get original values.
                if(Recipe.ingredients.hasOwnProperty(uid)) {
                    ingredient = Recipe.ingredients[uid];
                }

                // Update ingredients.
                ingredient['amount'] = row.find('.wprm-recipe-ingredient-amount').val();
                ingredient['unit'] = row.find('.wprm-recipe-ingredient-unit').val();
                ingredient['name'] = row.find('.wprm-recipe-ingredient-name').val();
                ingredient['notes'] = row.find('.wprm-recipe-ingredient-notes').val();

                // Unit Conversion.
                var converted = jQuery('#wprm-ingredient-conversion-' + uid);

                if (converted.length > 0) {
                    var amount = converted.find('.wprmuc-system-2-amount').val(),
                        unit = converted.find('.wprmuc-system-2-unit').val();

                    ingredient['converted'] = {
                        2: {
                            amount: amount,
                            unit: unit,
                        }
                    };
                }
                
                // Add ingredient to group.
                ingredient_group.ingredients.push(ingredient);
            }
        });

        // Add remaining ingredient group
        ingredients.push(ingredient_group);

        return ingredients;
    },
    addIngredient: function(amount = '', unit = '', name = '', notes = '') {
        var clone = jQuery('.wprm-recipe-ingredients-placeholder').find('.wprm-recipe-ingredient').clone();
        jQuery('.wprm-recipe-ingredients-instructions-form .wprm-recipe-ingredients').append(clone);

        clone.find('.wprm-recipe-ingredient-amount').val(utils.decode_html_entities(amount)).focus();
        clone.find('.wprm-recipe-ingredient-unit').val(utils.decode_html_entities(unit));
        clone.find('.wprm-recipe-ingredient-name').val(utils.decode_html_entities(name));
        clone.find('.wprm-recipe-ingredient-notes').val(utils.decode_html_entities(notes));

        while(true) {
            var uid = Math.floor(Math.random() * 99999);
            if(!Recipe.ingredients.hasOwnProperty(uid)) {
                clone.data('uid', uid);
                Recipe.ingredients[uid] = {};
                return uid;
            }
        }
    },
    addIngredientGroup: function(name = '') {
        var clone = jQuery('.wprm-recipe-ingredients-placeholder').find('.wprm-recipe-ingredient-group').clone();
        jQuery('.wprm-recipe-ingredients-instructions-form .wprm-recipe-ingredients').append(clone);
        clone.find('input:first').val(name).focus();
    },
    addInstruction: function(text = '', image_id = 0) {
        var clone = jQuery('.wprm-recipe-instructions-placeholder').find('.wprm-recipe-instruction').clone();

        clone.find('.wprm-recipe-instruction-text').addClass('wprm-rich-editor');
        jQuery('.wprm-recipe-ingredients-instructions-form .wprm-recipe-instructions').append(clone);
        clone.find('.wprm-recipe-instruction-text').val(text);
        RichEditor.init();
        clone.find('.wprm-recipe-instruction-text').focus();
    
        // Get image thumbnail if there is an instruction image.
        if (parseInt(image_id) > 0) {
            var image_container = clone.find('.wprm-recipe-image-container'),
                button = image_container.find('.wprm-recipe-image-add');

            var data = {
                action: 'wprm_get_thumbnail',
                security: wprm_admin.nonce,
                image_id: image_id
            };

            utils.start_loader(button);

            jQuery.post(wprm_admin.ajax_url, data, function(out) {
                utils.stop_loader(button);

                if (out.success) {
                    utils.set_media('image',image_container, image_id, out.data.image_url);
                }
            }, 'json');
        }
    
        while(true) {
            var uid = Math.floor(Math.random() * 99999);
            if(!Recipe.instructions.hasOwnProperty(uid)) {
                clone.data('uid', uid);
                Recipe.instructions[uid] = {};
                return uid;
            }
        }
        
    },
    addInstructionGroup: function(name = '') {
        var clone = jQuery('.wprm-recipe-instructions-placeholder').find('.wprm-recipe-instruction-group').clone();
        jQuery('.wprm-recipe-ingredients-instructions-form .wprm-recipe-instructions').append(clone);
        clone.find('input:first').val(name).focus();
    },
}
export default Recipe;