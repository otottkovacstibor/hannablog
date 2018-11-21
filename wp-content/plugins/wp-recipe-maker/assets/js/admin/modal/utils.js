import Modal from './Modal';

const shortcode_escape_map = {
	'"': "'",
    '[': '{',
    ']': '}'
};

export function shortcode_escape(text) {
    return String(text).replace(/["]/g, function(s) {
        return shortcode_escape_map[s];
    });
};

export function add_text_to_editor(text) {
    text = ' ' + text + ' ';

    if (Modal.active_editor_id) {
        if (typeof tinyMCE == 'undefined' || !tinyMCE.get(Modal.active_editor_id) || tinyMCE.get(Modal.active_editor_id).isHidden()) {
            var current = jQuery('textarea#' + Modal.active_editor_id).val();
            jQuery('textarea#' + Modal.active_editor_id).val(current + text);
        } else {
            tinyMCE.get(Modal.active_editor_id).focus(true);
            tinyMCE.activeEditor.selection.collapse(false);
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
        }
    }
};

export function select_media(type, container) {
    let media_arguments = {
        title: wprm_admin.modal.text.media_title,
        button: {
            text: wprm_admin.modal.text.media_button
        },
        multiple: false,
    };

    // Check what media type we're getting.
    if ( 'video' === type ) {
        media_arguments.frame = 'video';
        media_arguments.state = 'video-details';
    } else {
        // Default to image.
        media_arguments.library = {
            type: 'image',
        };
    }

    // Create a new media frame (don't reuse because we have multiple different inputs)
    let frame = wp.media(media_arguments);


    // Handle image selection
    frame.on('select', function() {
        var attachment = frame.state().get('selection').first().toJSON();
        set_media('image',container, attachment.id, attachment.url);
    });

    // Handle video selection
    frame.on('update', function() {
        let attachment = frame.state().media.attachment;

        if ( attachment ) {
            set_media('video',container, attachment.attributes.id, attachment.attributes.thumb.src);
        }
    });

    // Finally, open the modal on click
    frame.open();
};

export function set_media(type, container, media_id, media_url) {
    // Set placeholder if no media URL for video.
    if ( 'video' === type && '' === media_url ) {
        media_url = wprm_admin.wprm_url + 'assets/images/video.png';
    }

    container.find('.wprm-recipe-' + type + '-preview').html('');
    container.find('.wprm-recipe-' + type + '-preview').append('<img src="' + media_url + '" />');
    container.find('input').val(media_id);

    container.find('.wprm-recipe-' + type + '-add').addClass('hidden');
    container.find('.wprm-recipe-' + type + '-remove').removeClass('hidden');
    
    if ( 'video' === type ) {
        container.find('.wprm-recipe-' + type + '-embed').addClass('hidden');
    }

    Modal.changes_made = true;
};

export function remove_media(type, container) {
    container.find('.wprm-recipe-' + type + '-preview').html('');
    container.find('input').val('');

    container.find('.wprm-recipe-' + type + '-add').removeClass('hidden');
    container.find('.wprm-recipe-' + type + '-remove').addClass('hidden');

    if ( 'video' === type ) {
        container.find('.wprm-recipe-' + type + '-embed').removeClass('hidden');
        container.find('#wprm-recipe-' + type + '-embed').val('').addClass('hidden');
    }

    Modal.changes_made = true;
};

export function set_media_embed(type, container, code) {    
    container.find('.wprm-recipe-' + type + '-add').addClass('hidden');
    container.find('.wprm-recipe-' + type + '-embed').addClass('hidden');

    container.find('#wprm-recipe-' + type + '-embed').val(code).removeClass('hidden');
    container.find('.wprm-recipe-' + type + '-remove').removeClass('hidden');

    Modal.changes_made = true;
};

export function start_loader(button) {
    button
        .prop('disabled', true)
        .css('width', button.outerWidth())
        .data('text', button.html())
        .html('...');
};

export function stop_loader(button) {
    button
        .prop('disabled', false)
        .css('width', '')
        .html(button.data('text'));
};

export function decode_html_entities(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
};