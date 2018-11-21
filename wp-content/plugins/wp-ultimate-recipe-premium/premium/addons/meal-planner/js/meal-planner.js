var RecipeMealPlanner = RecipeMealPlanner || {};

RecipeMealPlanner.loader = '<div class="wpurp-loader"><div></div><div></div><div></div></div>';
RecipeMealPlanner.templates = [];
RecipeMealPlanner.ajaxUpdateTimer = null;

RecipeMealPlanner.init = function() {
    // Add Recipe Select
    RecipeMealPlanner.initSelect();
    var meal_planner = jQuery('.wpurp-meal-plan');
    var calendar = jQuery('.wpurp-meal-plan-calendar-container');
    var shopping_list = jQuery('.wpurp-meal-plan-shopping-list-container');

    var mobile = meal_planner.hasClass('wpurp-meal-plan-mobile');

    // Group By Options
    jQuery('.wpurp-meal-plan-group-by').on('click', function(e) {
        e.preventDefault();

        var link = jQuery(this);
        if(!link.hasClass('wpurp-meal-plan-group-by-selected'))
        {
            RecipeMealPlanner.groupBy(jQuery(this).data('groupby'));
            link.siblings('.wpurp-meal-plan-group-by-selected').removeClass('wpurp-meal-plan-group-by-selected');
            link.addClass('wpurp-meal-plan-group-by-selected');
        }
    });

    // Change Date
    calendar.on('click', '.wpurp-meal-plan-date-change:not(.wpurp-disabled)', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var loader = jQuery(RecipeMealPlanner.loader);
        var admin = calendar.find('.wpurp-meal-plan-calendar').data('admin');
        var id = calendar.find('.wpurp-meal-plan-calendar').data('meal-plan-id');
        var start_date = calendar.find('.wpurp-meal-plan-calendar').data('start-date');
        var end_date = calendar.find('.wpurp-meal-plan-calendar').data('end-date');
        var nbr_days = calendar.find('.wpurp-meal-plan-calendar').data('nbr-days');

        calendar.find('.wpurp-meal-plan-date').replaceWith(loader);
        calendar.find('.wpurp-meal-plan-date-readable').html('&nbsp;');

        var data = {
            action: 'meal_planner_change_date',
            security: wpurp_meal_planner.nonce,
            admin: admin,
            id: id,
            start_date: start_date,
            end_date: end_date,
            nbr_days: nbr_days,
            going_back: jQuery(this).hasClass('wpurp-meal-plan-date-prev')
        };

        jQuery.post(wpurp_meal_planner.ajaxurl, data, function(html) {
            calendar.html(html);
            RecipeMealPlanner.initSortable(calendar);
        });
    });

    // Sortable
    jQuery('.wpurp-meal-plan-recipe-container').sortable({
        connectWith: '.wpurp-meal-plan-recipe-list',
        placeholder: 'wpurp-meal-plan-recipe-placeholder',
        stop: function(e, ui) {
            if(!jQuery(ui.item).parent().hasClass('wpurp-meal-plan-recipe-container')) {
                jQuery('.wpurp-meal-plan-recipe-container').slideUp(200);
                RecipeMealPlanner.saveMenu(calendar);
            }
        }
    });

    RecipeMealPlanner.initSortable(calendar);

    // Course Actions
    if(!mobile) {
        calendar.on('hover', '.wpurp-meal-plan-course .wpurp-meal-plan-header', function(e) {
            var hovering = e.type == 'mouseenter';

            if(hovering) {
                jQuery(this).find('.wpurp-meal-plan-actions').show();
            } else {
                jQuery(this).find('.wpurp-meal-plan-actions').hide();
            }
        });
    }

    // Move Course
    calendar.on('click', '.wpurp-course-move:not(.wpurp-disabled)', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var button = jQuery(this);
        var course = button.closest('.wpurp-meal-plan-course');

        if(button.hasClass('wpurp-course-up')) {
            course.insertBefore(course.prev());
        } else {
            course.insertAfter(course.next());
        }

        RecipeMealPlanner.checkCourseActions();
        RecipeMealPlanner.saveMenu(calendar);
    });

    // Edit Course
    calendar.on('click', '.wpurp-course-edit:not(.wpurp-disabled)', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var button = jQuery(this);
        button.addClass('wpurp-disabled');

        var course_name = button.parents('.wpurp-meal-plan-header').find('.wpurp-meal-plan-course-name');

         RecipeMealPlanner.getInput(course_name, course_name.text(), function(name) {
             button.removeClass('wpurp-disabled');

            if(name) {
                course_name.text(name);
                RecipeMealPlanner.saveMenu(calendar);
            }
        });
    });

    // Remove Course
    calendar.on('click', '.wpurp-course-delete:not(.wpurp-disabled)', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var button = jQuery(this);
        button.addClass('wpurp-disabled');

        var course_name = button.parents('.wpurp-meal-plan-header').find('.wpurp-meal-plan-course-name');

        RecipeMealPlanner.getConfirm(course_name, wpurp_meal_planner.textDeleteCourse, function(delete_course) {
            button.removeClass('wpurp-disabled');

            if(delete_course) {
                button.closest('.wpurp-meal-plan-course').remove();
                RecipeMealPlanner.saveMenu(calendar);
            }
        });
    });

    // Add Course
    calendar.on('click', '.wpurp-meal-plan-add-course', function(e) {
        e.preventDefault();
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        RecipeMealPlanner.getInput(jQuery(this), '', function(name) {
            if(name) {
                var placeholder = calendar.find('.wpurp-meal-plan-course-placeholder');
                var new_course = placeholder.clone();

                new_course
                    .insertBefore(placeholder)
                    .addClass('wpurp-meal-plan-course')
                    .removeClass('wpurp-meal-plan-course-placeholder')
                    .data('course', name)
                    .find('.wpurp-meal-plan-course-name').text(name);

                new_course.find('.wpurp-meal-plan-recipe-list').sortable({
                    connectWith: '.wpurp-meal-plan-recipe-list',
                    placeholder: 'wpurp-meal-plan-recipe-placeholder',
                    stop: function() {
                        RecipeMealPlanner.saveMenu(calendar);
                    }
                });

                RecipeMealPlanner.checkCourseActions();
                RecipeMealPlanner.saveMenu(calendar);
            }
        });
    });

    // Add to Meal Planner
    calendar.on('click', '.wpurp-meal-plan-add-meal-planner', function(e) {
        e.preventDefault();
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');
        var button = jQuery(this);

        var id = calendar.find('.wpurp-meal-plan-calendar').data('meal-plan-id');
        var today_date = new Date();
        var today = ('0' + (today_date.getMonth()+1)).slice(-2) + '/' + ('0' + today_date.getDate()).slice(-2) + '/' + today_date.getFullYear();

        RecipeMealPlanner.getDate(button, today, function(date) {
            if(date) {
                var data = {
                    action: 'meal_planner_add_from_meal_plan',
                    security: wpurp_meal_planner.nonce,
                    id: id,
                    date: date
                };

                var loader = jQuery(RecipeMealPlanner.loader);
                button.replaceWith(loader);

                jQuery.post(wpurp_meal_planner.ajaxurl, data, function(data) {
                    var text = jQuery('<span class="wpurp-meal-plan-text"></span>');
                    text.html(wpurp_meal_planner.textAddToMealPlan);
                    loader.replaceWith(text);
                });
            }
        });
    });

    // Recipe Details
    calendar.on('click', '.wpurp-meal-plan-recipe', function() {
        var recipe = jQuery(this);
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        if(recipe.hasClass('wpurp-recipe-selected')) {
            calendar.find('.wpurp-recipe-close').click();
        } else {
            if(RecipeMealPlanner.editingRecipe) RecipeMealPlanner.editingRecipe.removeClass('wpurp-recipe-selected');

            RecipeMealPlanner.editingRecipe = recipe;
            recipe.addClass('wpurp-recipe-selected');

            var recipe_id = recipe.data('recipe');
            var servings = recipe.data('servings');
            var recipe_title = recipe.find('.wpurp-meal-plan-recipe-title').text();

            var details_row = calendar.find('.wpurp-meal-plan-selected-recipe-details');
            var details = calendar.find('.wpurp-meal-plan-selected-recipe-details-container');

            calendar.find('.recipe-selected').hide().find('.recipe-title').text(recipe_title);
            calendar.find('.recipe-not-selected').hide();
            calendar.find('.recipe-details-loader').show();

            details.slideUp(200, function() {
                // Close any other recipe details that might be open when displaying multiple plans
                jQuery('.wpurp-recipe-close:visible').click();

                details_row.hide();
                RecipeMealPlanner.getRecipeTemplate(recipe_id, function(template) {
                    calendar.find('.recipe-selected').show();
                    calendar.find('.recipe-details-loader').hide();

                    details.html(template);

                    details.find('.adjust-recipe-servings, .advanced-adjust-recipe-servings').val(servings).trigger('change');

                    details_row.show();
                    details.slideDown(200);
                });
            });
        }
    });

    // Close Recipe
    calendar.on('click', '.wpurp-recipe-close', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var details_row = calendar.find('.wpurp-meal-plan-selected-recipe-details');
        var details = calendar.find('.wpurp-meal-plan-selected-recipe-details-container');

        calendar.find('.recipe-selected').hide();
        calendar.find('.recipe-not-selected').show();
        calendar.find('.recipe-details-loader').hide();

        RecipeMealPlanner.editingRecipe.removeClass('wpurp-recipe-selected');

        details.slideUp(200, function() {
            details_row.hide();
        });
    });

    // Delete Recipe
    calendar.on('click', '.wpurp-recipe-delete', function() {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var button = jQuery(this);

        var recipe_name = button.parents('.wpurp-meal-plan-header').find('.recipe-selected');

        RecipeMealPlanner.getConfirm(recipe_name, wpurp_meal_planner.textDeleteRecipe, function(delete_recipe) {
            if(delete_recipe) {
                RecipeMealPlanner.editingRecipe.remove();
                button.siblings('.wpurp-recipe-close').click();

                RecipeMealPlanner.saveMenu(calendar);
            }
        });
    });

    // Changed Servings
    calendar.on('keyup change', '.wpurp-meal-plan-selected-recipe-details .adjust-recipe-servings, .wpurp-meal-plan-selected-recipe-details .advanced-adjust-recipe-servings', function(e) {
        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');

        var servings_new = jQuery(this).val();

        if(isNaN(servings_new) || servings_new <= 0){
            servings_new = 1;
        }

        RecipeMealPlanner.editingRecipe.data('servings', servings_new);
        RecipeMealPlanner.editingRecipe.find('.wpurp-meal-plan-recipe-servings').text('(' + servings_new + ')');
        RecipeMealPlanner.saveMenu(calendar);
    });

    // Generate Shopping List
    calendar.on('click', '.wpurp-meal-plan-shopping-list', function(e) {
        e.preventDefault();

        var calendar = jQuery(this).parents('.wpurp-meal-plan-calendar-container');
        var shopping_list = calendar.parents('.wpurp-meal-plan').find('.wpurp-meal-plan-shopping-list-container');

        var id = calendar.find('.wpurp-meal-plan-calendar').data('meal-plan-id');
        var start_date = '' + calendar.find('.wpurp-meal-plan-calendar').data('start-date');
        var end_date = '' + calendar.find('.wpurp-meal-plan-calendar').data('end-date');
        var nbr_days = calendar.find('.wpurp-meal-plan-calendar').data('nbr-days');

        var from_date = new Date(start_date.substring(0,4) + '-' + start_date.substring(4,6) + '-' + start_date.substring(6,8));

        if( end_date ) {
            from_date = new Date('2000-01-01');
            var to_date = new Date(end_date.substring(0,4) + '-' + end_date.substring(4,6) + '-' + end_date.substring(6,8));
        } else {
            var to_date = new Date(from_date);
            to_date.setDate(to_date.getDate() + nbr_days - 1);
        }

        var from = ('0' + (from_date.getMonth()+1)).slice(-2) + '/' + ('0' + from_date.getDate()).slice(-2) + '/' + from_date.getFullYear();
        var to = ('0' + (to_date.getMonth()+1)).slice(-2) + '/' + ('0' + to_date.getDate()).slice(-2) + '/' + to_date.getFullYear();

        RecipeMealPlanner.getDates(jQuery(this), from, to, function(from, to, unit_system) {
            if(from && to) {
                shopping_list.slideUp(200, function() {
                    var data = {
                        action: 'meal_planner_shopping_list',
                        security: wpurp_meal_planner.nonce,
                        id: id,
                        from: from,
                        to: to,
                        unit_system: unit_system
                    };

                    var loader = jQuery('<tr><td colspan="2">' + RecipeMealPlanner.loader + '</td></tr>');
                    var tbody = shopping_list.find('tbody');

                    var placeholder_group = shopping_list.find('.wpurp-shopping-list-group-placeholder');
                    var placeholder_ingredient = shopping_list.find('.wpurp-shopping-list-ingredient-placeholder');

                    tbody.empty().html(loader);

                    shopping_list.slideDown(400, function() {
                        jQuery.post(wpurp_meal_planner.ajaxurl, data, function(data) {
                            var ingredients = RecipeMealPlanner.generateShoppingList(data.ingredients, data.unit_system);
                            ingredients.append(placeholder_group);
                            ingredients.append(placeholder_ingredient);

                            tbody.replaceWith(ingredients);

                            shopping_list.find('tbody').sortable({
                                helper: function(e, ui) {
                                    ui.children().each(function() {
                                        jQuery(this).width(jQuery(this).width());
                                    });
                                    return ui;
                                }
                            });
                        }, 'json');
                    });
                })
            }
        });
    });

    // Actions
    if(!mobile) {
        shopping_list.on('hover', '.wpurp-meal-plan-shopping-list tbody tr', function(e) {
            var hovering = e.type == 'mouseenter';

            if(hovering) {
                jQuery(this).find('.wpurp-meal-plan-actions').show();
            } else {
                jQuery(this).find('.wpurp-meal-plan-actions').hide();
            }
        });
    }

    // Close Shopping List
    shopping_list.on('click', '.wpurp-shopping-list-close', function() {
        var shopping_list = jQuery(this).parents('.wpurp-meal-plan-shopping-list-container');
        shopping_list.slideUp(200);
    });

    // Edit Group
    shopping_list.on('click', '.wpurp-group-edit:not(.wpurp-disabled)', function() {
        var button = jQuery(this);
        button.addClass('wpurp-disabled');

        var group_name = button.parents('.wpurp-shopping-list-group').find('.wpurp-shopping-list-group-name');

        RecipeMealPlanner.getInput(group_name, group_name.text(), function(name) {
            button.removeClass('wpurp-disabled');

            if(name) {
                group_name.text(name);
            }
        });
    });

    // Remove Group
    shopping_list.on('click', '.wpurp-group-delete:not(.wpurp-disabled)', function() {
        jQuery(this).closest('.wpurp-shopping-list-group').remove();
    });

    // Edit Ingredient
    shopping_list.on('click', '.wpurp-ingredient-edit:not(.wpurp-disabled)', function() {
        var button = jQuery(this);
        button.addClass('wpurp-disabled');

        var ingredient_name = button.parents('.wpurp-shopping-list-ingredient').find('.wpurp-shopping-list-ingredient-name');
        var ingredient_quantity = button.parents('.wpurp-shopping-list-ingredient').find('.wpurp-shopping-list-ingredient-quantity');
        var ingredient_checkbox = button.parents('.wpurp-shopping-list-ingredient').find('.wpurp-shopping-list-ingredient-checkbox');

        ingredient_quantity.hide();
        ingredient_checkbox.hide();

        RecipeMealPlanner.getTwoInput(ingredient_name, ingredient_name.text(), ingredient_quantity.text(), function(name, quantity) {
            button.removeClass('wpurp-disabled');

            if(name || quantity) {
                ingredient_name.text(name);
                ingredient_quantity.text(quantity);
            }
            ingredient_quantity.show();
            ingredient_checkbox.show();
        });
    });

    // Remove Ingredient
    shopping_list.on('click', '.wpurp-ingredient-delete:not(.wpurp-disabled)', function() {
        jQuery(this).closest('.wpurp-shopping-list-ingredient').remove();
    });

    // Add Group
    shopping_list.on('click', '.wpurp-meal-plan-add-group', function(e) {
        e.preventDefault();
        var shopping_list = jQuery(this).parents('.wpurp-meal-plan-shopping-list-container');

        RecipeMealPlanner.getInput(jQuery(this), '', function(name) {
            if(name) {
                var placeholder = shopping_list.find('.wpurp-shopping-list-group-placeholder');
                var new_group = placeholder.clone();

                new_group
                    .insertBefore(placeholder)
                    .addClass('wpurp-shopping-list-group')
                    .removeClass('wpurp-shopping-list-group-placeholder')
                    .find('.wpurp-shopping-list-group-name').text(name);
            }
        });
    });

    // Add Ingredient
    shopping_list.on('click', '.wpurp-meal-plan-add-ingredient', function(e) {
        e.preventDefault();
        var shopping_list = jQuery(this).parents('.wpurp-meal-plan-shopping-list-container');

        RecipeMealPlanner.getTwoInput(jQuery(this), '', '', function(name, quantity) {
            if(name || quantity) {
                var placeholder = shopping_list.find('.wpurp-shopping-list-ingredient-placeholder');
                var new_ingredient = placeholder.clone();

                new_ingredient
                    .insertBefore(jQuery('.wpurp-shopping-list-group-placeholder'))
                    .addClass('wpurp-shopping-list-ingredient')
                    .removeClass('wpurp-shopping-list-ingredient-placeholder')
                    .find('.wpurp-shopping-list-ingredient-name').text(name);

                new_ingredient.find('.wpurp-shopping-list-ingredient-quantity').text(quantity);
            }
        });
    });

    // Ingredient Checkbox
    shopping_list.on('change', '.wpurp-shopping-list-ingredient-checkbox', function(e) {
        var checkbox = jQuery(this);
        if(checkbox.is(':checked')) {
            checkbox.closest('tr').addClass('ingredient-checked');
        } else {
            checkbox.closest('tr').removeClass('ingredient-checked');
        }
    });

    // Save Shopping List
    shopping_list.on('click', '.wpurp-meal-plan-shopping-list-save', function(e) {
        e.preventDefault();
        var shopping_list = jQuery(this).parents('.wpurp-meal-plan-shopping-list-container');

        var shopping_list_data = [];
        var group = {
            name: '',
            ingredients: []
        };

        shopping_list.find('.wpurp-shopping-list-group, .wpurp-shopping-list-ingredient').each(function(index) {
            var row = jQuery(this);

            if(row.hasClass('wpurp-shopping-list-group')) {
                if(index > 0) {
                    shopping_list_data.push(group);
                }
                group = {
                    name: row.find('.wpurp-shopping-list-group-name').text(),
                    ingredients: []
                }
            } else {
                group.ingredients.push({
                    name: row.find('.wpurp-shopping-list-ingredient-name').text(),
                    quantity: row.find('.wpurp-shopping-list-ingredient-quantity').text()
                });
            }
        });
        // Add last group
        shopping_list_data.push(group);

        var data = {
            action: 'meal_planner_shopping_list_save',
            security: wpurp_meal_planner.nonce,
            shopping_list: shopping_list_data
        };

        jQuery.post(wpurp_meal_planner.ajaxurl, data, function(link) {
            window.open(link);
        });
    });

    // Print Shopping List
    shopping_list.on('click', '.wpurp-meal-plan-shopping-list-print', function(e) {
        e.preventDefault();
        var shopping_list = jQuery(this).parents('.wpurp-meal-plan-shopping-list-container');

        shopping_list.find('.wpurp-meal-plan-shopping-list').print({
            globalStyles: false,
            stylesheet: wpurp_meal_planner.addonUrl + '/css/print.css',
            prepend: '<style>' + wpurp_meal_planner.print_shoppinglist_style + '</style>'
        });
    });
};

RecipeMealPlanner.initSelect = function() {
    jQuery('.wpurp-meal-plan-add-recipe').select2({
        width: 'off'
    }).on('change', function() {
        // Add the selected recipe
        RecipeMealPlanner.addRecipe(jQuery(this).select2('data'));

        // Clear the selection
        jQuery(this).select2('val', '');
    });
};

RecipeMealPlanner.initSortable = function(calendar) {
    jQuery('.wpurp-meal-plan:not(.wpurp-meal-plan-shortcode)').find('.wpurp-meal-plan-recipe-list').sortable({
        connectWith: '.wpurp-meal-plan-recipe-list',
        placeholder: 'wpurp-meal-plan-recipe-placeholder',
        stop: function() {
            RecipeMealPlanner.saveMenu(calendar);
        }
    });
};

RecipeMealPlanner.addRecipe = function(recipe) {
    if(recipe.id !== '')
    {
        // Cache Recipe Template
        RecipeMealPlanner.getRecipeTemplate(recipe.id);

        var recipe_data = {
            id: recipe.id,
            name: recipe.text,
            link: recipe.element[0].dataset.link,
            image: recipe.element[0].dataset.thumb,
            servings_original: recipe.element[0].dataset.servings
        };

        var container = jQuery('.wpurp-meal-plan-recipe-container');

        container.slideUp(200, function() {
            container.find('.wpurp-meal-plan-recipe').remove();

            var recipe_block = '<div class="wpurp-meal-plan-recipe" data-recipe="' + recipe_data.id +'" data-servings="' + recipe_data.servings_original + '"><img src="' + recipe_data.image + '"><span class="wpurp-meal-plan-recipe-title">' + recipe_data.name + '</span> <span class="wpurp-meal-plan-recipe-servings">(' + recipe_data.servings_original + ')</span></div>';
            container.append(recipe_block);
            container.slideDown(400);
        });
    }
};

RecipeMealPlanner.getRecipeTemplate = function(recipe_id, callback) {
    if(RecipeMealPlanner.templates[recipe_id] == undefined) {
        var data = {
            action: 'get_recipe_template',
            security: wpurp_meal_planner.nonce,
            recipe_id: recipe_id
        };

        jQuery.post(wpurp_meal_planner.ajaxurl, data, function(template) {
            RecipeMealPlanner.templates[recipe_id] = template.output;
            if(callback !== undefined) callback(template.output);
        }, 'json');
    } else{
        if(callback !== undefined) callback(RecipeMealPlanner.templates[recipe_id]);
    }
};

RecipeMealPlanner.groupBy = function(groupby) {
    var loader = jQuery(RecipeMealPlanner.loader);
    jQuery('.wpurp-meal-plan-group-by-container').append(loader);

    var data = {
        action: 'meal_planner_groupby',
        security: wpurp_meal_planner.nonce,
        groupby: groupby,
        grid: wpurp_meal_planner_grid.slug
    };

    jQuery.post(wpurp_meal_planner.ajaxurl, data, function(html) {
        loader.remove();
        jQuery('.wpurp-meal-plan-add-recipe').select2('destroy').off().html(html);
        RecipeMealPlanner.initSelect();
    });
};

RecipeMealPlanner.checkCourseActions = function() {
    var courses = jQuery('.wpurp-meal-plan-course');
    courses.each(function(index, elem) {
        jQuery(elem).find('.wpurp-course-move').removeClass('wpurp-disabled');

        if(index == 0) {
            jQuery(elem).find('.wpurp-course-up').addClass('wpurp-disabled');
        } else if(index == courses.length - 1) {
            jQuery(elem).find('.wpurp-course-down').addClass('wpurp-disabled');
        }
    });
};

RecipeMealPlanner.getInput = function(replace, value, callback) {
    var form = jQuery('<form class="wpurp-meal-plan-form"></form>');
    var input = jQuery('<input type="text" class="wpurp-meal-plan-input">');
    var submit = jQuery('<button type="submit" class="wpurp-meal-plan-button"><i class="fa fa-check"></i></button>');
    var cancel = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-close"></i></button>');

    form.append(input).append(submit).append(cancel);

    replace.hide().after(form);
    input.val(value);
    input.focus().select();

    form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var value = input.val();
        replace.show();
        form.remove();

        callback(value.trim());
    });

    cancel.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback('');
    });
};

RecipeMealPlanner.getTwoInput = function(replace, value1, value2, callback) {
    var form = jQuery('<form class="wpurp-meal-plan-form"></form>');
    var input1 = jQuery('<input type="text" class="wpurp-meal-plan-input">');
    var input2 = jQuery('<input type="text" class="wpurp-meal-plan-input">');
    var submit = jQuery('<button type="submit" class="wpurp-meal-plan-button"><i class="fa fa-check"></i></button>');
    var cancel = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-close"></i></button>');

    form.append(input1).append(input2).append(submit).append(cancel);

    replace.hide().after(form);
    input1.val(value1);
    input2.val(value2);
    input1.focus().select();

    form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var value1 = input1.val();
        var value2 = input2.val();
        replace.show();
        form.remove();

        callback(value1.trim(),value2.trim());
    });

    cancel.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback('');
    });
};

RecipeMealPlanner.getConfirm = function(replace, text, callback) {
    var form = jQuery('<form class="wpurp-meal-plan-form"></form>');
    var message = jQuery('<span class="wpurp-meal-plan-form-message">' + text + '</span>');
    var confirm = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-check"></i></button>');
    var cancel = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-close"></i></button>');

    form.append(message).append(confirm).append(cancel);

    replace.hide().after(form);

    confirm.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback(true);
    });

    cancel.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback(false);
    });
};

RecipeMealPlanner.getDate = function(replace, date_start, callback) {
    var form = jQuery('<form class="wpurp-meal-plan-form"></form>');
    var date = jQuery('<input type="text" class="wpurp-meal-plan-input wpurp-meal-plan-input-date">');
    var submit = jQuery('<button type="submit" class="wpurp-meal-plan-button"><i class="fa fa-check"></i></button>');
    var cancel = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-close"></i></button>');

    form.append(date).append(submit).append(cancel);

    replace.hide().after(form);

    date.val(date_start);
    date.datepicker();

    form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var date_val = date.val();
        replace.show();
        form.remove();

        callback(date_val.trim());
    });

    cancel.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback('');
    });
};

RecipeMealPlanner.getDates = function(replace, from_start, to_start, callback) {
    var form = jQuery('<form class="wpurp-meal-plan-form"></form>');
    var from = jQuery('<input type="text" class="wpurp-meal-plan-input wpurp-meal-plan-input-date">');
    var to = jQuery('<input type="text" class="wpurp-meal-plan-input wpurp-meal-plan-input-date">');
    var unit_system = jQuery('<select class="wpurp-meal-plan-input wpurp-meal-plan-input-unit-system"></select>');
    var submit = jQuery('<button type="submit" class="wpurp-meal-plan-button"><i class="fa fa-check"></i></button>');
    var cancel = jQuery('<button class="wpurp-meal-plan-button"><i class="fa fa-close"></i></button>');

    for(var i = 0, l = wpurp_unit_conversion.systems.length; i < l; i++)
    {
        var system = wpurp_unit_conversion.systems[i];
        var selected = i == wpurp_meal_planner.default_unit_system ? ' selected' : '';
        unit_system.append(jQuery('<option value="' + i + '"' + selected + '>' + system.name + '</option>'));
    }

    var dates = jQuery('<span class="wpurp-meal-plan-input-dates"></span>').append(from).append(' - ').append(to);
    form.append(dates).append(unit_system).append(submit).append(cancel);

    replace.hide().after(form);

    from.val(from_start);
    to.val(to_start);

    from.datepicker();
    to.datepicker();

    form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var from_val = from.val();
        var to_val = to.val();
        var unit_system_val = unit_system.val();
        replace.show();
        form.remove();

        callback(from_val.trim(), to_val.trim(), unit_system_val);
    });

    cancel.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        replace.show();
        form.remove();

        callback('', '');
    });
};

RecipeMealPlanner.saveMenu = function(calendar_container) {
    var calendar = calendar_container.find('.wpurp-meal-plan-calendar');

    var id = calendar.data('meal-plan-id');
    var start_date = calendar.data('start-date');
    var nbr_days = calendar.data('nbr-days');
    var courses = [];

    calendar.find('.wpurp-meal-plan-course').each(function(index_course) {
        var course = jQuery(this);
        var name = course.find('.wpurp-meal-plan-course-name').text();
        var days = [];

        course.find('.wpurp-meal-plan-recipe-list').each(function(index_day) {
            var recipes = [];

            jQuery(this).find('.wpurp-meal-plan-recipe').each(function(index_recipe) {
                var recipe = jQuery(this);
                var recipe_id = recipe.data('recipe');
                var servings = recipe.data('servings');

                recipes[index_recipe] = {
                    id: recipe_id,
                    servings: servings
                };
            });

            days[index_day] = recipes;
        });

        courses[index_course] = {
            name: name,
            days: days
        };
    });

    var menu = {
        id: id,
        start_date: start_date,
        nbr_days: nbr_days,
        courses: courses
    };

    // Reset if already running
    clearTimeout(RecipeMealPlanner.ajaxUpdateTimer);

    RecipeMealPlanner.ajaxUpdateTimer = setTimeout(function() {
        RecipeMealPlanner.ajaxSaveMenu(menu);
    }, 1000);
};

RecipeMealPlanner.ajaxSaveMenu = function(menu) {
    var data = {
        action: 'meal_planner_save',
        security: wpurp_meal_planner.nonce,
        security_admin: wpurp_meal_planner.nonce_admin,
        menu: menu
    };

    jQuery.post(wpurp_meal_planner.ajaxurl, data, function(html) { console.log(html); });
};

RecipeMealPlanner.generateShoppingList = function(ingredients_data, unit_system)
{
    var ingredients = [];
    var ingredients_plural = [];

    // Choose last cup type in systems. Not a perfect system.
    var cup_type = 236.6;
    for(var i = 0, l = wpurp_unit_conversion.systems.length; i < l; i++)
    {
        var system = wpurp_unit_conversion.systems[i];
        if(jQuery.inArray('cup', system.units_volume) != -1) {
            cup_type = parseFloat(system.cup_type);
        }
    }

    for(var i = 0, l = ingredients_data.length; i < l; i++)
    {
        var ingredient = ingredients_data[i];

        // Prepare Arrays
        if(ingredients_plural[ingredient.name] === undefined) {
            ingredients_plural[ingredient.name] = ingredient.plural;
        }

        if(ingredients[ingredient.group] === undefined) {
            ingredients[ingredient.group] = [];
        }

        if(ingredients[ingredient.group][ingredient.name] === undefined) {
            ingredients[ingredient.group][ingredient.name] = [];
        }


        // Quantity Values
        var amount = ingredient.amount;
        var unit = ingredient.unit;
        var unit_type = RecipeUnitConversion.getUnitFromAlias(ingredient.unit);

        // Unit Conversions if Enabled
        if(unit_type !== undefined && wpurp_meal_planner.consolidate_ingredients == '1') {
            var unit_abbreviation = RecipeUnitConversion.getAbbreviation(unit_type);

            // Adjust for cup type
            if(unit_abbreviation == 'cup') {
                var qty_cup = new Qty('1 cup').to('ml').scalar;

                if(Math.abs(cup_type - qty_cup) > 0.1) { // 236.6 == 236.588238
                    amount = amount * (cup_type / qty_cup);
                }
            }

            var quantity = new Qty('' + amount + ' ' + unit_abbreviation);
            var base_quantity = quantity.toBase();

            if(base_quantity.units() == 'm3') {
                base_quantity = base_quantity.to('l');
            }

            unit = base_quantity.units();
            amount = base_quantity.scalar;
        }

        // Need to have a name for every unit
        if(unit == '') {
            unit = 'wpurp_nounit';
        }

        // Prepare Array
        if(ingredients[ingredient.group][ingredient.name][unit] === undefined) {
            ingredients[ingredient.group][ingredient.name][unit] = 0.0;
        }

        // Add Amount
        ingredients[ingredient.group][ingredient.name][unit] += parseFloat(amount);
    }

    // Sort ingredients by name
    var group_keys = Object.keys(ingredients);
    group_keys.sort(function (a, b) { // Case insensitive sort
        return a.toLowerCase().localeCompare(b.toLowerCase());
    });

    // Result
    var ingredient_table = jQuery('<tbody></tbody>');

    // Loop over Ingredient Groups
    for(i = 0, l = group_keys.length; i < l; i++)
    {
        var group_key = group_keys[i];
        var group = ingredients[group_key];

        var group_row = jQuery('<tr class="wpurp-shopping-list-group"><td colspan="2"><span class="wpurp-shopping-list-group-name">' + group_key + '</span><span class="wpurp-meal-plan-actions"><i class="fa fa-pencil wpurp-group-edit"></i><i class="fa fa-trash wpurp-group-delete"></i></span></td></tr>');
        ingredient_table.append(group_row);

        var ingredient_keys = Object.keys(group);
        ingredient_keys.sort(function (a, b) { // Case insensitive sort
            return a.toLowerCase().localeCompare(b.toLowerCase());
        });

        for(var j = 0, m = ingredient_keys.length; j < m; j++)
        {
            ingredient = ingredient_keys[j];

            var units = group[ingredient];
            for(var unit in units)
            {
                var amount = units[unit];

                if(isFinite(amount))
                {
                    var ingredient_row = jQuery('<tr class="wpurp-shopping-list-ingredient"></tr>');

                    var actual_unit = RecipeUnitConversion.getUnitFromAlias(unit);

                    // Get the Unit Systems we need to generate (only 1 for now)
                    var systems = [unit_system];

                    var plural = false;

                    for(var s = 0; s < systems.length; s++) {
                        var converted_amount = amount;
                        var converted_unit = unit;

                        if(unit == 'wpurp_nounit') {
                            converted_unit = '';
                        } else if(actual_unit !== undefined) {
                            if(wpurp_meal_planner.consolidate_ingredients == '1' || (!RecipeUnitConversion.isUniversal(actual_unit) && jQuery.inArray(systems[s], RecipeUnitConversion.getUnitSystems(actual_unit)) == -1)) {
                                var quantity = RecipeUnitConversion.convertUnitToSystem(amount, actual_unit, 0, systems[s]);
                                var converted_amount = quantity.amount;
                                converted_unit = RecipeUnitConversion.getUserAbbreviation(quantity.unit, converted_amount);
                            }
                        }

                        converted_amount = RecipeUnitConversion.formatNumber(converted_amount, wpurp_meal_planner.fractions);
                        if(converted_amount !== '1') {
                            plural = true;
                        }

                        ingredient_row.append('<td><span class="wpurp-shopping-list-ingredient-quantity">' + converted_amount + ' ' + converted_unit + '</span><span class="wpurp-meal-plan-actions"><i class="fa fa-pencil wpurp-ingredient-edit"></i><i class="fa fa-trash wpurp-ingredient-delete"></i></span></td>');
                    }

                    var ingredient_name = plural ? ingredients_plural[ingredient] : ingredient;
                    var ingredient_name = ingredient_name.charAt(0).toUpperCase() + ingredient_name.slice(1);

                    var checkbox = '';
                    if(wpurp_meal_planner.checkboxes == '1') {
                        checkbox = '<input type="checkbox" class="wpurp-shopping-list-ingredient-checkbox"> ';
                    }

                    ingredient_row.prepend('<td>' + checkbox + '<span class="wpurp-shopping-list-ingredient-name">' + ingredient_name + '</span></td>');

                    ingredient_table.append(ingredient_row);
                }
            }
        }
    }

    return ingredient_table;
};

jQuery(document).ready(function(){
    if(jQuery('.wpurp-meal-plan').length > 0) {
        RecipeMealPlanner.init();
    }
});