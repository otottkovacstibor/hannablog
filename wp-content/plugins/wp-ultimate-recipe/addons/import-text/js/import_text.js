var ImportText = ImportText || {};

jQuery(document).ready(function() {
    // Init + Add New From Text functions
    ImportText.init();

    // Import recipes from other plugins
    if( window.wpurp_import_ingredients !== undefined && wpurp_import_ingredients.length > 0 ) {
        for(var i = 0, l = wpurp_import_ingredients.length; i<l; i++) {
            var ingredient = ImportText.parseIngredient(wpurp_import_ingredients[i]);
            ingredient.group = '';
            ImportText.addIngredientRow(ingredient, i);
        }
    }
});

ImportText.init = function() {

    // Variables
    ImportText.raw_input = '';
    ImportText.recipe = {};

    // Rangy
    rangy.init();
    ImportText.rangyCSStitle        = rangy.createCssClassApplier("recipe_region_title_highlight", {normalize: true});
    ImportText.rangyCSSdescription  = rangy.createCssClassApplier("recipe_region_description_highlight", {normalize: true});
    ImportText.rangyCSSingredients  = rangy.createCssClassApplier("recipe_region_ingredients_highlight", {normalize: true});
    ImportText.rangyCSSinstructions = rangy.createCssClassApplier("recipe_region_instructions_highlight", {normalize: true});
    ImportText.rangyCSSnotes        = rangy.createCssClassApplier("recipe_region_notes_highlight", {normalize: true});

    jQuery('#recipe_import_text_plain').on('keyup change', function() {
        ImportText.raw_input = jQuery(this).val();
        ImportText.updateRegionsText();
    });

    // Select region
    jQuery('.recipe_region_button').on('click', function() {

        // Get region type from button id
        var type = jQuery(this).attr('id').replace('recipe_region_', '');

        // Get and highlight selected text
        var text = ImportText.getAndHighlightText(type);

        // Update corresponding form field and trigger change event
        if(type == 'instructions' || type == 'ingredients') {
            jQuery('#raw_recipe_'+type).val(text).change();
        } else {
            ImportText.recipe[type] = text;
        }
    });

    // Adaptive textareas
    jQuery('#raw_recipe_ingredients, #raw_recipe_instructions').on('keyup change', function() {
        ImportText.adaptiveTextarea(jQuery(this));
        ImportText.getSeparateLines(jQuery(this));
    });

    // Update ingredients
    jQuery('#recipe_import_define_ingredient_details').on('keyup change', 'input', function() {
        ImportText.updateIngredients();
    });

    jQuery('#recipe_instructions_separate_type').on('change', function() {
        // Trigger change in instructions
        jQuery('#raw_recipe_instructions').change();
    });

    // Start importing from text
    jQuery('.recipe_import_text').on('click', function() {
        jQuery('.recipe_import_text').hide();
        jQuery('.recipe_import_text_container').show();
        jQuery('.admin_recipe_form').hide();
    });

    // Cancel importing from text
    jQuery('.recipe_import_text_cancel').on('click', function() {
        jQuery('.recipe_import_text').show();
        jQuery('.admin_recipe_form').show();
        jQuery('.recipe_import_text_container').hide();
    });

    // Use importing from text
    jQuery('.recipe_import_text_use').on('click', function() {
        jQuery('.recipe_import_text').show();
        jQuery('.admin_recipe_form').show();
        jQuery('.recipe_import_text_container').hide();
        ImportText.useImport();
    });
};

ImportText.useImport = function() {
    if(ImportText.recipe['title'] !== undefined && ImportText.recipe['title']) {
        jQuery('#recipe_title').val(ImportText.recipe['title']);
    }

    if(ImportText.recipe['description'] !== undefined && ImportText.recipe['description']) {
        jQuery('#recipe_description').val(ImportText.recipe['description']);
    }

    if(ImportText.recipe['notes'] !== undefined && ImportText.recipe['notes']) {
        if(typeof tinymce != 'undefined') {
            var editor = tinymce.get('recipe_notes');
            if( editor && editor instanceof tinymce.Editor ) {
                editor.setContent(ImportText.recipe['notes']);
                editor.save( { no_events: true } );
            } else {
                jQuery('#recipe_notes').val(ImportText.recipe['notes']);
            }
        }
    }

    if(ImportText.recipe['ingredients'] !== undefined && ImportText.recipe['ingredients'].length > 0) {
        jQuery('.ingredient-group-delete:visible').click();
        jQuery('.ingredients-delete:visible').click();
        jQuery('.ingredient-group-label').val('');

        var first_group = false;
        var first_ingredient = true;

        for(var i = 0, l = ImportText.recipe['ingredients'].length; i<l; i++) {
            var ingredient = ImportText.recipe['ingredients'][i];

            if(ingredient.type !== undefined && ingredient.type == 'group') {
                if(i !== 0) {
                    jQuery('#ingredients-add-group').click();
                    jQuery('.ingredient-group-label').last().val(ingredient.group);

                    if(first_group) {
                        jQuery('.ingredient-group-label').first().val(first_group);
                        first_group = false;
                    }
                } else {
                    first_group = ingredient.group;
                }
            } else {
                if(!first_ingredient) {
                    jQuery('#ingredients-add').click();
                } else {
                    first_ingredient = false;
                }
                jQuery('.ingredient').last().find('.ingredients_amount').val(ingredient.amount);
                jQuery('.ingredient').last().find('.ingredients_unit').val(ingredient.unit);
                jQuery('.ingredient').last().find('.ingredients_name').val(ingredient.name);
                jQuery('.ingredient').last().find('.ingredients_notes').val(ingredient.notes);
            }
        }
    }

    if(ImportText.recipe['instructions'] !== undefined && ImportText.recipe['instructions'].length > 0) {
        jQuery('.instruction-group-delete:visible').click();
        jQuery('.instructions-delete:visible').click();
        jQuery('.instruction-group-label').val('');

        var first_group = false;
        var first_instruction = true;

        for(var i = 0, l = ImportText.recipe['instructions'].length; i<l; i++) {
            var instruction = ImportText.recipe['instructions'][i];

            if(instruction.type !== undefined && instruction.type == 'group') {
                if(i !== 0) {
                    jQuery('#instructions-add-group').click();
                    jQuery('.instruction-group-label').last().val(instruction.group);

                    if(first_group) {
                        jQuery('.instruction-group-label').first().val(first_group);
                        first_group = false;
                    }
                } else {
                    first_group = instruction.group;
                }
            } else {
                if(!first_instruction) {
                    jQuery('#instructions-add').click();
                } else {
                    first_instruction = false;
                }
                jQuery('.instruction').last().find('textarea').val(instruction.text);
            }
        }
    }
};

ImportText.updateRegionsText = function() {
    jQuery('#recipe_region_text').html(ImportText.raw_input.replace(/\r?\n/g,'<br/>'));
    ImportText.recipe = {};
};

ImportText.getAndHighlightText = function(type) {
    var selectedText = '';
    var sel = rangy.getSelection(), rangeCount = sel.rangeCount;

    var range = rangy.createRange();
    range.selectNodeContents(jQuery('#recipe_region_text')[0]);

    for (var i = 0; i < rangeCount; ++i) {
        var textInRange = sel.getRangeAt(i).intersection(range);

        if(textInRange !== null) {
            selectedText += textInRange.toHtml();
        }
    }

    selectedText = selectedText.replace(/<br\s*[\/]?>/gi, '\n');
    selectedText = selectedText.replace(/<span.*?>(.*?)<\/span>/gi, '$1');

    ImportText['rangyCSS' + type].undoToRange(range);
    ImportText['rangyCSS' + type].toggleSelection();
    range.detach();
    if(rangeCount > 0) {
        sel.collapseToStart();
    }
    return selectedText;
};

ImportText.adaptiveTextarea = function(textarea) {
    var scrollTop = jQuery(window).scrollTop();
    textarea.height(0).height(textarea.prop('scrollHeight'));
    jQuery(window).scrollTop(scrollTop);
};

ImportText.getSeparateLines = function(textarea) {
    var details = [];
    var type = 'every_line';
    var id = textarea.attr('id');
    var lines = textarea.val();
    var target = jQuery('#' + id + '_lines');

    if(id == 'raw_recipe_instructions') {
        type = jQuery('#recipe_instructions_separate_type option:selected').val();
    }

    if(type == 'every_line') {
        lines = lines.split('\n');
    } else {
        lines = lines.split(/\n\s*\n/);
    }

    target.empty();
    if(id == 'raw_recipe_instructions') {
        jQuery('#recipe_instructions_output').empty();
    } else {
        jQuery('#recipe_import_define_ingredient_details tbody').empty();
    }

    var group = '';
    var nbr_groups = 0;
    for(var i = 0, l = lines.length; i<l; i++) {

        if(lines[i].substr(0,1) == '!') {
            // Group
            group = lines[i].substr(1);
            target.append('<strong>' + group + '</strong>');
            nbr_groups++;
            details.push({
                type: 'group',
                group: group
            });
        } else {
            // Ingredient/Instruction
            target.append('<li>' + lines[i].replace(/\r?\n/g,'<br/>') + '</li>');

            if(id == 'raw_recipe_instructions') {
                var instruction = {
                    text: lines[i],
                    group: group
                };
                details.push(instruction);
                ImportText.addInstructionRow(instruction, i - nbr_groups);
            } else {
                var ingredient = ImportText.parseIngredient(lines[i]);
                ingredient.group = group;
                details.push(ingredient);
                ImportText.addIngredientRow(ingredient, i - nbr_groups);
            }
        }
    }

    if(id == 'raw_recipe_instructions') {
        ImportText.recipe['instructions'] = details;
    } else {
        ImportText.recipe['ingredients'] = details;
    }
};

ImportText.parseIngredient = function(text) {
    var ingredient = {};

    // Amount
    ingredient.amount = '';
    var regex = /^\s*\d[\s\/\-\d.,]*/g;
    if(regex.test(text)) {
        var matches = text.match(regex);
        ingredient.amount = matches[0].trim();

        text = text.replace(regex, '');
    }

    // Unit
    ingredient.unit = '';
    var units = wpurp_import_text.units;

    dance:
    for(var i = 0; i < units.length; i++) {
        var regex = new RegExp('(?:^|\\s)+'+units[i]+'\\s','g');

        if(regex.test(text)) {
            var matches = text.match(regex);
            ingredient.unit = matches[0].trim();

            text = text.replace(regex, '');
            break dance;
        }
    }

    // Notes
    ingredient.notes = '';
    var regex = /,.*/g;
    if(regex.test(text)) {
        var matches = text.match(regex);
        var match = matches[0].substring(1); // Drop the ,
        ingredient.notes = match.trim();

        text = text.replace(regex, '');
    }

    var regex = /\([^\)]*\)/g;
    if(regex.test(text)) {
        var matches = text.match(regex);
        for(var i in matches) {
            if(ingredient.notes != '') {
                ingredient.notes += ', ';
            }

            var match = matches[i].replace('(','').replace(')','');
            ingredient.notes += match.trim();
        }

        text = text.replace(regex, '');
    }

    // Name
    ingredient.name = text.trim();

    return ingredient;
};

ImportText.addIngredientRow = function(ingredient, i) {
    var row = jQuery('<tr></tr>');
    var amount = jQuery('<td><input type="text" name="recipe_ingredients['+i+'][amount]" class="ingredients_amount" id="ingredients_amount_'+i+'" value="'+ImportText.escapeAttr(ingredient.amount)+'" /></td>');
    var unit = jQuery('<td><input type="text" name="recipe_ingredients['+i+'][unit]" class="ingredients_unit" id="ingredients_unit_'+i+'" value="'+ImportText.escapeAttr(ingredient.unit)+'" /></td>');
    var name = jQuery('<td><input type="text" name="recipe_ingredients['+i+'][ingredient]" class="ingredients_name" id="ingredients_'+i+'" value="'+ImportText.escapeAttr(ingredient.name)+'" /></td>');
    var notes = jQuery('<td><input type="text" name="recipe_ingredients['+i+'][notes]" class="ingredients_notes" id="ingredients_notes_'+i+'" value="'+ImportText.escapeAttr(ingredient.notes)+'" /></td><input type="hidden" name="recipe_ingredients['+i+'][group]" class="ingredients_group" id="ingredient_group_'+i+'" value="'+ImportText.escapeAttr(ingredient.group)+'">');

    row.append(amount)
        .append(unit)
        .append(name)
        .append(notes);

    jQuery('#recipe_import_define_ingredient_details tbody').append(row);
};

ImportText.updateIngredients = function() {
    var ingredients = [];
    var group = '';
    jQuery('#recipe_import_define_ingredient_details tbody').find('tr').each(function() {
        var row = jQuery(this);

        var ingredient = {
            amount: row.find('.ingredients_amount').val(),
            unit: row.find('.ingredients_unit').val(),
            name: row.find('.ingredients_name').val(),
            notes: row.find('.ingredients_notes').val(),
            group: row.find('.ingredients_group').val()
        };

        if(ingredient.group !== group) {
            ingredients.push({
                type: 'group',
                group: ingredient.group
            });
            group = ingredient.group;
        }

        ingredients.push(ingredient);
    });

    ImportText.recipe['ingredients'] = ingredients;
};

ImportText.addInstructionRow = function(instruction, i) {
    jQuery('#recipe_instructions_output')
        .append('<textarea name="recipe_instructions['+i+'][description]" id="ingredient_description_'+i+'">'+instruction.text+'</textarea><input type="hidden" name="recipe_instructions['+i+'][group]" class="instructions_group" id="instruction_group_'+i+'" value="'+ImportText.escapeAttr(instruction.group)+'">');
};

ImportText.escapeAttr = function(s) {
    return ('' + s) /* Forces the conversion to string. */
        .replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
        .replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        /*
         You may add other replacements here for HTML only
         (but it's not necessary).
         Or for XML, only if the named entities are defined in its DTD.
         */
        .replace(/\r\n/g, '&#13;') /* Must be before the next replacement. */
        .replace(/[\r\n]/g, '&#13;');
    ;
}