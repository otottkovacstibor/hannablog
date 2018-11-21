import '../../../../vendor/select2/js/select2.min.js';
import '../../../../vendor/select2/css/select2.min.css';

import * as utils from './utils';
import Modal from './Modal';
import Recipe from './Recipe';
import RichEditor from './RichEditor';

export default function recipeInit(recipe) {
    // Check for changes made
    jQuery('.wprm-modal').on('change', 'input, textarea, #wprm-recipe-author-display, .wprm-recipe-tags, #wprm-ingredient-links-type', function() {
        Modal.changes_made = true;
    });

    // Recipe and Instruction Image handler
    jQuery('.wprm-recipe-details-form, .wprm-recipe-instructions-form').on('click', '.wprm-recipe-image-add', function(e) {
        utils.select_media('image', jQuery(this).parents('.wprm-recipe-image-container'));
    });
    jQuery('.wprm-recipe-details-form, .wprm-recipe-instructions-form').on('click', '.wprm-recipe-image-remove', function(e) {
        e.preventDefault();
        utils.remove_media('image', jQuery(this).parents('.wprm-recipe-image-container'));
    });

    // Recipe Video handler
    jQuery('.wprm-recipe-notes-form').on('click', '.wprm-recipe-video-add', function(e) {
        utils.select_media('video', jQuery(this).parents('.wprm-recipe-video-container'));
    });
    jQuery('.wprm-recipe-notes-form').on('click', '.wprm-recipe-video-remove', function(e) {
        utils.remove_media('video', jQuery(this).parents('.wprm-recipe-video-container'));
    });
    jQuery('.wprm-recipe-notes-form').on('click', '.wprm-recipe-video-embed', function(e) {
        utils.set_media_embed('video', jQuery(this).parents('.wprm-recipe-video-container'), '');
    });

    // Initialize rich editor
    RichEditor.init();

    // Type
    jQuery('#wprm-recipe-type').select2_wprm({
        width: '95%'
    });

    // Author
    jQuery('#wprm-recipe-author-display').select2_wprm({
        width: '95%'
    });

    jQuery(document).on('change', '#wprm-recipe-author-display', function() {
        var author_display = jQuery(this).val(),
            default_display = jQuery(this).find('option:first').data('default');

        if(author_display == 'custom' || (author_display == 'default' && default_display == 'custom')) {
            jQuery('#wprm-recipe-author-name-container').show();
            jQuery('#wprm-recipe-author-link-container').show();
        } else {
            jQuery('#wprm-recipe-author-name-container').hide();
            jQuery('#wprm-recipe-author-link-container').hide();
        }
    });
    jQuery('#wprm-recipe-author-display').change();

    // Recipe Times
    jQuery('.wprm-recipe-time').on('keyup change', function() {
        var container = jQuery(this),
            prep_time_container = jQuery('#wprm-recipe-prep-time'),
            prep_time = prep_time_container.val(),
            cook_time_container = jQuery('#wprm-recipe-cook-time'),
            cook_time = cook_time_container.val(),
            total_time_container = jQuery('#wprm-recipe-total-time'),
            total_time = total_time_container.val();

        if (container.is('#wprm-recipe-prep-time')) Recipe.prep_time_set = true;
        if (container.is('#wprm-recipe-cook-time')) Recipe.cook_time_set = true;
        if (container.is('#wprm-recipe-total-time')) Recipe.total_time_set = true;

        if (prep_time && cook_time && !Recipe.total_time_set) total_time_container.val(parseInt(prep_time) + parseInt(cook_time));
        if (total_time && prep_time && !Recipe.cook_time_set) cook_time_container.val(parseInt(total_time) - parseInt(prep_time));
        if (total_time && cook_time && !Recipe.prep_time_set) prep_time_container.val(parseInt(total_time) - parseInt(cook_time));
    });

    // Recipe Tags
    jQuery('.wprm-recipe-tags').select2_wprm({
        width: '95%',
        tags: true
    });

    // Add Recipe Ingredients and Instructions
    jQuery('.wprm-recipe-ingredients-add').on('click', function() {
        Recipe.addIngredient();
    });
    jQuery('.wprm-recipe-ingredients-add-group').on('click', function() {
        Recipe.addIngredientGroup();
    });
    jQuery('.wprm-recipe-instructions-add').on('click', function() {
        Recipe.addInstruction();
    });
    jQuery('.wprm-recipe-instructions-add-group').on('click', function() {
        Recipe.addInstructionGroup();
    });

    // Add new ingredient/instruction on TAB
    jQuery('.wprm-recipe-ingredients').on('keydown', '.wprm-recipe-ingredient-notes, .wprm-recipe-ingredient-group-name', function(e) {
        var keyCode = e.keyCode || e.which,
            input = jQuery(this);

        if (!e.shiftKey && keyCode == 9 && jQuery(this).parents('tr').is('tr:last-child')) {
            e.preventDefault();
            Recipe.addIngredient();
        }
    });
    jQuery('.wprm-recipe-instructions').on('keydown', '.wprm-recipe-instruction-text, .wprm-recipe-instruction-group-name', function(e) {
        var keyCode = e.keyCode || e.which,
            input = jQuery(this);

        if (!e.shiftKey && keyCode == 9 && jQuery(this).parents('tr').is('tr:last-child')) {
            e.preventDefault();
            Recipe.addInstruction();
        }
    });

    // Remove Recipe Ingredients and Instructions
    jQuery('.wprm-recipe-ingredients-instructions-form').on('click', '.wprm-recipe-ingredients-instructions-delete', function() {
        jQuery(this).parents('tr').remove();
    });

    // Sort Recipe Ingredients and Instructions
    jQuery('.wprm-recipe-ingredients, .wprm-recipe-instructions').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.wprm-recipe-ingredients-instructions-sort',
    });

    // Save recipe button.
    jQuery('.wprm-button-action-save').on('click', function() {
        Modal.actions.insert_update_recipe(jQuery(this));
    });
}