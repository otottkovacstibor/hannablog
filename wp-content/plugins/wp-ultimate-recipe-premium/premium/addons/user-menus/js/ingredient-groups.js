var IngredientGroups = IngredientGroups || {};

jQuery(document).ready(function() {
    IngredientGroups.init();
});

IngredientGroups.max_group_key = 0;
IngredientGroups.groups = {};

IngredientGroups.init = function() {
    jQuery('#newGroup').keyup(function (e) {
        if (e.keyCode == 13) {
            IngredientGroups.setNewGroup(jQuery(this).val());
        }
    });

    jQuery('.ingredient-group').each(function() {
        var group = jQuery(this);
        var key = parseInt( group.data('group-key') );

        if( key > IngredientGroups.max_group_key ) {
            IngredientGroups.max_group_key = key;
        }

        IngredientGroups.groups[group.html()] = key;
    });

    console.log(IngredientGroups.groups);
}

IngredientGroups.selectGroup = function(key) {
    jQuery('.ingredients input').each(function() {
        var checkbox = jQuery(this);

        if(checkbox.hasClass('ingredient-group-' + key)) {
            checkbox.attr('checked', true);
        } else {
            checkbox.attr('checked', false);
        }
    });
}

IngredientGroups.setGroup = function(submit) {
    var input = jQuery(submit).siblings('#newGroup');

    IngredientGroups.setNewGroup(input.val());
}

IngredientGroups.setNewGroup = function(group) {
    var group_name = group.trim();

    // Get group key
    var groups = IngredientGroups.groups;
    var key = -1;
    for (var group in groups) {
        if (groups.hasOwnProperty(group) && group == group_name) {
            key = groups[group];
        }
    }

    if(key == -1) {
        IngredientGroups.max_group_key++;
        key = IngredientGroups.max_group_key;
        IngredientGroups.groups[group_name] = key;

        var select_link = jQuery('<a href="#" onclick="IngredientGroups.selectGroup(' + key +')">' + group_name + '</a>');
        jQuery('.select-group-links').append(', ').append(select_link);
    }

    var ingredients = [];
    jQuery('.ingredients input:checked').each(function() {
        var checkbox = jQuery(this);

        ingredients.push(checkbox.val());

        checkbox.removeClass().addClass('ingredient-group-' + key);
        checkbox.siblings('.group').html(group_name);
        checkbox.attr('checked', false);
    });

    IngredientGroups.update(ingredients, group_name);
}

IngredientGroups.update = function(ingredients, group) {
    var data = {
        action: 'ingredient_groups_save',
        security: wpurp_ingredient_groups.nonce,
        ingredients: ingredients,
        group: group
    };

    jQuery.post(wpurp_ingredient_groups.ajaxurl, data, function(data) {
        // Nothing to do.
    });
}