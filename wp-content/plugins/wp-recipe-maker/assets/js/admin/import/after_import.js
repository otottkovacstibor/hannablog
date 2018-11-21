import Modal from '../modal/Modal';

jQuery(document).ready(function($) {
	// Edit imported recipe
	jQuery(document).on('click', '.wprm-import-recipes-actions-edit', function(e) {
		e.preventDefault();

		var id = jQuery(this).data('id');

		Modal.open(false, {
			recipe_id: id
		});
	});
});