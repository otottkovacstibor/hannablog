import Modal from './modal/Modal';

// Opening Modal
jQuery(document).ready(function($) {
    jQuery(document).on('click', '.wprm-modal-button', function() {
        var editor_id = jQuery(this).data('editor');
        Modal.open(editor_id);
    });
    
    // Edit Recipe button
    jQuery(document).on('click', '.wprm-modal-edit-button', function() {
        var editor_id = jQuery(this).data('editor');
        var recipe_id = jQuery(this).data('recipe');
        
        Modal.open(editor_id, {
            recipe_id: recipe_id
        });
    });
});