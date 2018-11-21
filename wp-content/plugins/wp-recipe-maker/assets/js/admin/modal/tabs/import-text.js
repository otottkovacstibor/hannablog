import '../../../../../vendor/texthighlighter/TextHighlighter.min.js';

import Modal from '../Modal';
import Recipe from '../Recipe';
import RichEditor from '../RichEditor';

let text_import_step = '';
let text_import_highlighter;
let text_import = {};
let text_import_waiting = false;

function start_text_import() {
    jQuery('.wprm-button-import-text-reset').removeAttr('disabled');
    jQuery('.wprm-button-import-text-clear').removeAttr('disabled');
    jQuery('.wprm-button-import-text-next').removeAttr('disabled');

    text_import_step = 'input';
    jQuery('#import-text-highlight-sandbox').textHighlighter();
    text_import_highlighter = jQuery('#import-text-highlight-sandbox').getHighlighter()
};

function btn_text_import_reset() {
    btn_text_import_clear(true);
    jQuery('#import-text-highlight-sandbox').text('');
    text_import = {};
    text_import_waiting = false;
    
    jQuery('.wprm-button-import-text-reset').attr('disabled', 'disabled');
    jQuery('.wprm-button-import-text-clear').attr('disabled', 'disabled');
    jQuery('.wprm-button-import-text-next').attr('disabled', 'disabled');

    jQuery('.import-text-step').hide();
    jQuery('#import-text-step-input').show();
    jQuery('#import-text-highlight-sandbox').hide();
};

function btn_text_import_clear(all) {
    if(all || text_import_step == 'input') {
        jQuery('#import-text-input-recipe').val('');
    }

    if(all || text_import_step == 'ingredient-groups') {
        jQuery('#import-text-ingredient-groups').find('input').attr('checked', false);
    }

    if(all || text_import_step == 'instruction-groups') {
        jQuery('#import-text-instruction-groups').find('input').attr('checked', false);
    }

    text_import_highlighter.removeHighlights();
};

 function btn_text_import_next() {
    if(text_import_step == 'input') {
        text_import.raw = jQuery('#import-text-input-recipe').val();
        jQuery('#import-text-highlight-sandbox').html(text_import.raw.replace(/\r?\n/g,'<br/>')).show();

        text_import_step = 'name';
    } else if(text_import_step == 'name') {
        text_import.name = get_highlighted_text();

        text_import_step = 'summary';
    } else if(text_import_step == 'summary') {
        text_import.summary = get_highlighted_text();

        jQuery('#import-text-highlight-sandbox').show();

        text_import_step = 'ingredients';
    } else if(text_import_step == 'ingredients') {
        var ingredients = text_import_highlighter.getHighlights();
        text_import.ingredients_raw = ingredients;
        
        jQuery('#import-text-ingredient-groups').html('');
        for(var i = 0, l = ingredients.length; i<l; i++) {
            var text = jQuery(ingredients[i]).text().trim();
            text = text.replace(/^(\d\.\s+|[a-z]\)\s+|•\s+|[A-Z]\.\s+|[IVX]+\.\s+)/g, "");
            var ingredient = '<div class="import-text-ingredient"><input type="checkbox" id="ingredient-' + i + '"> ' + '<label for="ingredient-' + i + '">' + text + '</label></div>';
            jQuery('#import-text-ingredient-groups').append(ingredient);
        }
        jQuery('.import-text-group-warning').hide();
        
        if(ingredients.length == 0) {
            jQuery('#import-text-highlight-sandbox').show();
            text_import.ingredients = [];
            text_import_step = 'instructions';
        } else {
            jQuery('#import-text-highlight-sandbox').hide();
            text_import_step = 'ingredient-groups';
        }
    } else if(text_import_step == 'ingredient-groups') {
        var ingredients = [],
            ingredient_group = {
                name: '',
                ingredients: []
        };

        jQuery('#import-text-ingredient-groups').find('.import-text-ingredient').each(function() {
            var is_ingredient_group = jQuery(this).find('input').is(':checked'),
                ingredient = jQuery(this).find('label').text();

            if(is_ingredient_group) {
                ingredients.push(ingredient_group);

                ingredient_group = {
                    name: ingredient,
                    ingredients: []
                }
            } else {
                ingredient_group.ingredients.push({raw: ingredient});
            }
        });
        ingredients.push(ingredient_group);

        text_import.ingredients = [];

        // Parse ingredients
        var data = {
            action: 'wprm_parse_ingredients',
            security: wprm_admin.nonce,
            ingredients: ingredients
        };

        text_import_waiting = true;
        jQuery.post(wprm_admin.ajax_url, data, function(out) {
            text_import_waiting = false;
            if (out.success) {
                text_import.ingredients = out.data.ingredients;
            }
        }, 'json');

        jQuery('#import-text-highlight-sandbox').show();

        text_import_step = 'instructions';
    } else if(text_import_step == 'instructions') {
        var instructions = text_import_highlighter.getHighlights();
        text_import.instructions_raw = instructions;
        
        jQuery('#import-text-instruction-groups').html('');
        for(var i = 0, l = instructions.length; i<l; i++) {
            var text = jQuery(instructions[i]).text().trim();
            text = text.replace(/^(\d\.\s+|[a-z]\)\s+|•\s+|[A-Z]\.\s+|[IVX]+\.\s+)/g, "");
            var instruction = '<div class="import-text-instruction"><input type="checkbox" id="instruction-' + i + '"> ' + '<label for="instruction-' + i + '">' + text + '</label></div>';
            jQuery('#import-text-instruction-groups').append(instruction);
        }
        jQuery('.import-text-group-warning').hide();

        if(instructions.length == 0) {
            jQuery('#import-text-highlight-sandbox').show();
            text_import.instructions = [];
            text_import_step = 'notes';
        } else {
            jQuery('#import-text-highlight-sandbox').hide();
            text_import_step = 'instruction-groups';
        }
    } else if(text_import_step == 'instruction-groups') {
        var instructions = [],
            instruction_group = {
                name: '',
                instructions: []
        };

        jQuery('#import-text-instruction-groups').find('.import-text-instruction').each(function() {
            var is_instruction_group = jQuery(this).find('input').is(':checked'),
                instruction = jQuery(this).find('label').text();

            if(is_instruction_group) {
                instructions.push(instruction_group);

                instruction_group = {
                    name: instruction,
                    instructions: []
                }
            } else {
                instruction_group.instructions.push({text: instruction});
            }
        });
        instructions.push(instruction_group);

        text_import.instructions = instructions;

        jQuery('#import-text-highlight-sandbox').show();

        text_import_step = 'notes';
    } else if(text_import_step == 'notes') {
        text_import.notes = get_highlighted_text();

        jQuery('#import-text-highlight-sandbox').hide();
        jQuery('.wprm-button-import-text-reset').attr('disabled', 'disabled');
        jQuery('.wprm-button-import-text-clear').attr('disabled', 'disabled');
        jQuery('.wprm-button-import-text-next').attr('disabled', 'disabled');
        
        if(text_import_waiting) {
            text_import_step = 'waiting';
            text_import_waiting_check();
        } else {
            jQuery('.wprm-button-import-text-reset').removeAttr('disabled');
            import_recipe();
            text_import_step = 'finished';
        }
    } else if(text_import_step == 'waiting') {
        if(!text_import_waiting) {
            jQuery('.wprm-button-import-text-reset').removeAttr('disabled');
            import_recipe();
            text_import_step = 'finished';
        }
    }

    jQuery('.import-text-step').hide();
    jQuery('#import-text-step-' + text_import_step).show();
    text_import_highlighter.removeHighlights();
};

 function text_import_waiting_check() {
    if(text_import_waiting) {
        setTimeout(text_import_waiting_check, 200);
    } else {
        btn_text_import_next();
    }
};

 function get_highlighted_text() {
    var highlight_parts = text_import_highlighter.getHighlights();
    var highlight = '';

    for(var i = 0, l = highlight_parts.length; i<l; i++) {
        if(i > 0) {
            highlight += ' ';
        }
        highlight += jQuery(highlight_parts[i]).text().trim();
    }

    return highlight;
};

 function import_recipe() {
    if(text_import.name) {
        jQuery('#wprm-recipe-name').val(text_import.name);
    }

    if(text_import.summary) {
        RichEditor.set(text_import.summary);
    }

    if(text_import.notes) {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('wprm_recipe_notes') && !tinyMCE.get('wprm_recipe_notes').isHidden()) {
			tinyMCE.get('wprm_recipe_notes').focus(true);
			tinyMCE.activeEditor.setContent(text_import.notes);
		} else {
			jQuery('#wprm_recipe_notes').val(text_import.notes);
		}
    }

    if(text_import.instructions.length > 0) {
        Recipe.setInstructions(text_import.instructions);
    }
    
    if(text_import.ingredients.length > 0) {
        Recipe.setIngredients(text_import.ingredients);
    }
};

jQuery(document).ready(function($) {
    jQuery('#import-text-input-recipe').on('keydown change', function() {
        start_text_import();
    });

    jQuery('.wprm-button-import-text-reset').on('click', function() {
        if(confirm(wprm_admin.modal.text.import_text_reset)) {
            btn_text_import_reset();
        }
    });

    jQuery('.wprm-button-import-text-clear').on('click', function() {
        btn_text_import_clear(false);
    });

    jQuery('.wprm-button-import-text-next').on('click', function() {
        btn_text_import_next();
    });

    jQuery('.import-text-input-groups').on('change', 'input', function() {
        var groups = jQuery(this).parents('.import-text-input-groups');

        if(groups.find('input').length == groups.find('input:checked').length) {
            jQuery('.import-text-group-warning').show();
        }
    });
});