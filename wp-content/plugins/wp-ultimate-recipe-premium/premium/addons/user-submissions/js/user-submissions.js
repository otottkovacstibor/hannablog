jQuery(document).ready(function() {
    // Select2
    jQuery('#wpurp_user_submission_form select[multiple]').select2({
        allowClear: true,
        width: 'off',
        dropdownAutoWidth: false
    });

    jQuery('.user-submissions-delete-recipe').on('click', function() {
        var button = jQuery(this);
        if(confirm(wpurp_user_submissions.confirm_message + ' ' + button.data('title'))) {
            button.parent('li').remove();

            var data = {
                action: 'user_submissions_delete_recipe',
                security: wpurp_user_submissions.nonce,
                recipe: button.data('id')
            };

            jQuery.post(wpurp_user_submissions.ajaxurl, data);
        }
    });
});