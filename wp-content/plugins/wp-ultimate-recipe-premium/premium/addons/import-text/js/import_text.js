var ImportText = ImportText || {};

jQuery(document).ready(function() {
    // Init + Add New From Text functions
    ImportText.init();

    // Import recipes from other plugins
    if( wpurp_import_ingredients.length > 0 ) {
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
    ImportText.rangyCSStitle        = rangy.createCssClassApplier("region-title-highlight", {normalize: true});
    ImportText.rangyCSSdescription  = rangy.createCssClassApplier("region-description-highlight", {normalize: true});
    ImportText.rangyCSSingredients  = rangy.createCssClassApplier("region-ingredients-highlight", {normalize: true});
    ImportText.rangyCSSinstructions = rangy.createCssClassApplier("region-instructions-highlight", {normalize: true});
    ImportText.rangyCSSnotes        = rangy.createCssClassApplier("region-notes-highlight", {normalize: true});

    jQuery('#input-recipe').on('keyup change', function() {
        ImportText.raw_input = jQuery(this).val();
        ImportText.updateRegionsText();
    });

    // Select region
    jQuery('.regions-button').on('click', function() {

        // Get region type from button id
        var type = jQuery(this).attr('id').replace('region-', '');

        // Get and highlight selected text
        var text = ImportText.getAndHighlightText(type);
        ImportText.recipe[type] = text;

        // Update corresponding form field and trigger change event
        if(type == 'instructions' || type == 'ingredients') {
            jQuery('#raw_recipe_'+type).val(text).change();
        } else {
            jQuery('#recipe_'+type).val(text).change();
        }
    });

    // Adaptive textareas
    jQuery('#raw_recipe_ingredients, #raw_recipe_instructions').on('keyup change', function() {
        ImportText.adaptiveTextarea(jQuery(this));
        ImportText.getSeparateLines(jQuery(this));
    });

    jQuery('#recipe_instructions_separate_type').on('change', function() {
        // Trigger change in instructions
        jQuery('#raw_recipe_instructions').change();
    });
};

ImportText.updateRegionsText = function() {
    jQuery('#regions-text').html(ImportText.raw_input.replace(/\r?\n/g,'<br/>'));
};

ImportText.getAndHighlightText = function(type) {
    var selectedText = '';
    var sel = rangy.getSelection(), rangeCount = sel.rangeCount;

    var range = rangy.createRange();
    range.selectNodeContents(jQuery('#regions-text')[0]);

    for (var i = 0; i < rangeCount; ++i) {
        var textInRange = sel.getRangeAt(i).intersection(range);

        if(textInRange !== null) {
            selectedText += textInRange.toHtml();
        }
    }

    selectedText = selectedText.replace(/<br\s*[\/]?>/gi, '\n');

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
        jQuery('#define-ingredient-details tbody').empty();
    }

    var group = '';
    var nbr_groups = 0;
    for(var i = 0, l = lines.length; i<l; i++) {

        if(lines[i].substr(0,1) == '!') {
            // Group
            group = lines[i].substr(1);
            target.append('<strong>' + group + '</strong>');
            nbr_groups++;
        } else {
            // Ingredient/Instruction
            target.append('<li>' + lines[i].replace(/\r?\n/g,'<br/>') + '</li>');

            if(id == 'raw_recipe_instructions') {
                var instruction = {
                    text: lines[i],
                    group: group
                }
                ImportText.addInstructionRow(instruction, i - nbr_groups);
            } else {
                var ingredient = ImportText.parseIngredient(lines[i]);
                ingredient.group = group;
                ImportText.addIngredientRow(ingredient, i - nbr_groups);
            }
        }
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

    jQuery('#define-ingredient-details tbody').append(row);
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