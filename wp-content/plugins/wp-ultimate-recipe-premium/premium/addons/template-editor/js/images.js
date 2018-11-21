jQuery(document).ready(function() {
    var custom_uploader = wp.media({
        title: 'Choose an Image',
        button: {
            text: 'Choose Image'
        },
        multiple: false
    })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            window.opener.wpurp_image_manager(attachment.url, attachment.width, attachment.height);
            window.close();
        })
        .open();
});