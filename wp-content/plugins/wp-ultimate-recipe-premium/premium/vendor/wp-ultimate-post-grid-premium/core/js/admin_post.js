jQuery('#wpupg_add_custom_image').on('click', function(e) {
    e.preventDefault();

    var button = jQuery(this);
    var input_url = button.parents('#wpupg_form_post').find('#wpupg_custom_image');
    var input_id = button.parents('#wpupg_form_post').find('#wpupg_custom_image_id');

    if(typeof wp.media == 'function') {
        var custom_uploader = wp.media({
                title: 'Insert Media',
                button: {
                    text: 'Set Custom Image'
                },
                multiple: false
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                input_url.val(attachment.url);
                input_id.val(attachment.id);
            })
            .open();
    }
});

jQuery('#wpupg_custom_image').on('keyup change', function() {
    jQuery(this).siblings('#wpupg_custom_image_id').val('');
});