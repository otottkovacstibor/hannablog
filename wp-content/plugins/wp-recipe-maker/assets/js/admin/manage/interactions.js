import * as api from './api';
import Modal from '../modal/Modal';

export default function initDatatableInteractions() {
    jQuery(document).on('click', '.wprm-manage-recipes-seo', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id');

        Modal.open(false, {
            recipe_id: id
        });
    });

    jQuery(document).on('change', '.wprm-manage-ingredients-link-nofollow', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            nofollow = jQuery(this).val();
        
        api.update_term_metadata(id, 'ingredient_link_nofollow', nofollow);
    });

    jQuery(document).on('click', '.wprm-manage-ingredients-actions-rename', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-ingredients-name-' + id).text();
        
        var new_name = prompt('What do you want to rename "' + name + '" to?', name).trim();
        if(new_name) {
            api.rename_term(id, 'ingredient', new_name);
        }
    });

    jQuery(document).on('click', '.wprm-manage-ingredients-actions-link', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-ingredients-name-' + id).text(),
            link_container = jQuery('#wprm-manage-ingredients-link-' + id),
            link = link_container.text();
        
        var new_link = prompt('What do you want the link for "' + name + '" to be?', link).trim();
        api.update_term_metadata(id, 'ingredient_link', new_link);
    });

    jQuery(document).on('click', '.wprm-manage-ingredients-actions-merge', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-ingredients-name-' + id).text();
        
        var new_term = parseInt(prompt('What is the ID of the ingredient that you want to merge "' + name + '" into?', ''));
        if(new_term) {
            api.delete_or_merge_term(id, 'ingredient', new_term);
        }
    });

    jQuery(document).on('click', '.wprm-manage-taxonomies-actions-rename', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-taxonomies-name-' + id).text(),
            taxonomy = jQuery('.wprm-manage-taxonomies').data('taxonomy');
        
        var new_name = prompt('What do you want to rename "' + name + '" to?', name).trim();
        if(new_name) {
            api.rename_term(id, taxonomy, new_name);
        }
    });

    jQuery(document).on('click', '.wprm-manage-taxonomies-actions-merge', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-taxonomies-name-' + id).text(),
            taxonomy = jQuery('.wprm-manage-taxonomies').data('taxonomy');
        
        var new_term = parseInt(prompt('What is the ID of the term that you want to merge "' + name + '" into?', ''));
        if(new_term) {
            api.delete_or_merge_term(id, taxonomy, new_term);
        }
    });

    jQuery(document).on('click', '.wprm-manage-ingredients-actions-delete', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id');
        api.delete_or_merge_term(id, 'ingredient', 0);
    });

    jQuery(document).on('click', '.wprm-manage-taxonomies-actions-delete', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-taxonomies-name-' + id).text(),
            taxonomy = jQuery('.wprm-manage-taxonomies').data('taxonomy');

        if(confirm('Are you sure you want to delete "' + name + '"?')) {
            api.delete_or_merge_term(id, taxonomy, 0);
        }
    });

    jQuery(document).on('click', '.wprm-manage-recipes-actions-edit', function(e) {
        e.preventDefault();

        var id = jQuery(this).data('id');

        Modal.open(false, {
            recipe_id: id
        });
    });

    jQuery(document).on('click', '.wprm-manage-recipes-actions-delete', function(e) {
        e.preventDefault();
        
        var id = jQuery(this).data('id'),
            name = jQuery('#wprm-manage-recipes-name-' + id).text();
        
        if(confirm('Are you sure you want to delete "' + name + '"?')) {
            api.delete_recipe(id);
        }
    });

    jQuery(document).on('click', '.wprm-manage-ingredients-bulk-delete', function(e) {
        e.preventDefault();

        var ingredients = jQuery('.wprm-manage-ingredients-bulk:checkbox:checked');
        var ids = [];

        ingredients.each(function() {
            ids.push(parseInt(jQuery(this).val()));
        });

        if(ids.length > 0) {
            api.delete_terms(ids, 'ingredient', 0);
        }
    });
}
