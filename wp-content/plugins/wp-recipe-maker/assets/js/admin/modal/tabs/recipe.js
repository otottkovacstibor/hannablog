import * as utils from '../utils';
import Modal from '../Modal';
import Recipe from '../Recipe';

Modal.actions.set_recipe = function(args) {
    let recipe_id = args.recipe_id ? args.recipe_id : 0;
    let clone_recipe_id = args.clone_recipe_id ? args.clone_recipe_id : 0;
    
    Recipe.id = recipe_id;
	Recipe.type = 0 === recipe_id ? 'insert' : 'update';
	Recipe.fields = false;
	Recipe.ingredients = {};
    Recipe.instructions = {};

    Recipe.clear();
    
    if ('insert' === Recipe.type) {
		var button = jQuery('.wprm-button-action'),
			button_save = jQuery('.wprm-button-action-save');

		jQuery('.wprm-router.active').find('.wprm-menu-item').each(function() {
			jQuery(this).data('button', wprm_admin.modal.text.action_button_insert);
		});
		button.text(wprm_admin.modal.text.action_button_insert);
		button_save.show();

		if(clone_recipe_id) {
			var data = {
				action: 'wprm_get_recipe',
				security: wprm_admin.nonce,
				recipe_id: clone_recipe_id
			};

			utils.start_loader(button);
			utils.start_loader(button_save);

			jQuery.post(wprm_admin.ajax_url, data, function(out) {
				utils.stop_loader(button);
				utils.stop_loader(button_save);

				if (out.success) {
					Recipe.fields = out.data.recipe;
					Recipe.set(out.data.recipe);
					Modal.changes_made = false;
				}
			}, 'json');
		}
	} else {
		var button = jQuery('.wprm-button-action'),
			button_save = jQuery('.wprm-button-action-save');

		jQuery('.wprm-router.active').find('.wprm-menu-item').each(function() {
			jQuery(this).data('button', wprm_admin.modal.text.action_button_update);
		});
		button.text(wprm_admin.modal.text.action_button_update);
		button_save.show();

		Modal.disable_menu();

		var data = {
			action: 'wprm_get_recipe',
			security: wprm_admin.nonce,
			recipe_id: recipe_id
		};

		utils.start_loader(button);
		utils.start_loader(button_save);

		jQuery.post(wprm_admin.ajax_url, data, function(out) {
			utils.stop_loader(button);
			utils.stop_loader(button_save);

			if (out.success) {
				Recipe.fields = out.data.recipe;
				Recipe.set(out.data.recipe);
				Modal.changes_made = false;
				jQuery('.wprm-frame-title').find('h1').text(wprm_admin.modal.text.edit_recipe);
			}
		}, 'json');
	}
}

Modal.actions.insert_update_recipe = function(button) {
	let recipe = Recipe.get();

	// Ajax call to recipe saver
	var data = {
			action: 'wprm_save_recipe',
			security: wprm_admin.nonce,
			recipe_id: Recipe.id,
			recipe: recipe
	};

	jQuery('.wprm-button-action-save').prop('disabled', true);
	utils.start_loader(button);

	jQuery.ajax({
		type: 'POST',
		url: wprm_admin.ajax_url,
		data: data,
		success: function(out) {
			utils.stop_loader(button);
			jQuery('.wprm-button-action-save').prop('disabled', false);

			if (out.success) {
				if (Recipe.id === 0) {
					Recipe.id = out.data.id;
				}

				if(!button.hasClass('wprm-button-action-save')) {
					if ( Modal.gutenberg ) {
						Modal.args.gutenbergCallback(Recipe.id);
					} else {
						if ('insert' === Recipe.type) {
							utils.add_text_to_editor('[wprm-recipe id="' + out.data.id + '"]');
						} else if(Modal.active_editor_id) {
							// Refresh content in editor to reload recipe preview
							if (typeof tinyMCE !== 'undefined' && tinyMCE.get(Modal.active_editor_id) && !tinyMCE.get(Modal.active_editor_id).isHidden()) {
								tinyMCE.get(Modal.active_editor_id).focus(true);
								tinyMCE.activeEditor.setContent(tinyMCE.activeEditor.getContent());
							}
						}
	
						if(jQuery('.wprm-manage-datatable').length > 0) {
							jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
						}
					}

					Modal.close();
				}
			}
		},
		error: function(out) {
			console.log(out);
			alert( 'An error occured during saving. This could have happened because you got logged out of WordPress. If so, a login form should show up now.' );
			if (wp.heartbeat) {
				wp.heartbeat.connectNow();
			}
			utils.stop_loader(button);
			jQuery('.wprm-button-action-save').prop('disabled', false);
		},
		dataType: 'json',
	});
};

Modal.actions.edit_recipe = function(button) {
	var id = parseInt(jQuery('#wprm-edit-recipe-id').val());
	if(id != 0) {
		var editor = Modal.active_editor_id;
		Modal.close();
		Modal.open(editor, {
			recipe_id: id
		});
	}
};

Modal.actions.insert_recipe = function(button) {
	var id = parseInt(jQuery('#wprm-insert-recipe-id').val());
	if(id != 0) {
		var shortcode = '[wprm-recipe id="' + id + '"]';

		if ( Modal.gutenberg ) {
			Modal.args.gutenbergCallback(id);
		} else {
			utils.add_text_to_editor(shortcode);
		}

		Modal.close();
	}
};