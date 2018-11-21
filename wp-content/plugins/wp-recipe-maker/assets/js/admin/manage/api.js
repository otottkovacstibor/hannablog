export function rename_term(term_id, taxonomy, new_name) {
	var data = {
			action: 'wprm_rename_term',
			security: wprm_admin.nonce,
			term_id: term_id,
			taxonomy: taxonomy,
			new_name: new_name
	};

	jQuery.post(wprm_admin.ajax_url, data, function() {
		jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
	});
};

export function update_term_metadata(term_id, field, value) {
	var data = {
			action: 'wprm_update_term_metadata',
			security: wprm_admin.nonce,
			term_id: term_id,
			field: field,
			value: value
	};

	jQuery.post(wprm_admin.ajax_url, data, function() {
		jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
	});
};

export function delete_or_merge_term(term_id, taxonomy, new_term_id) {
	var data = {
			action: 'wprm_delete_or_merge_term',
			security: wprm_admin.nonce,
			term_id: term_id,
			taxonomy: taxonomy,
			new_term_id: new_term_id
	};

	jQuery.post(wprm_admin.ajax_url, data, function() {
		jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
	});
};

export function delete_terms(term_ids, taxonomy) {
	var data = {
			action: 'wprm_delete_terms',
			security: wprm_admin.nonce,
			term_ids: term_ids,
			taxonomy: taxonomy
	};

	jQuery.post(wprm_admin.ajax_url, data, function() {
		jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
	});
};

export function delete_recipe(recipe_id) {
	var data = {
			action: 'wprm_delete_recipe',
			security: wprm_admin.nonce,
			recipe_id: recipe_id,
	};

	jQuery.post(wprm_admin.ajax_url, data, function() {
		jQuery('.wprm-manage-datatable').DataTable().ajax.reload(null, false);
	});
};