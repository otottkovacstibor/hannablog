import Modal from '../Modal';
import * as utils from '../utils';

Modal.actions.reset_snippets = function(args) {
    jQuery('#wprm-recipe-jump-id').val('0').trigger('change');
    jQuery('#wprm-recipe-jump-text').val('');

    jQuery('#wprm-recipe-jump-video-id').val('0').trigger('change');
    jQuery('#wprm-recipe-jump-video-text').val('');

    jQuery('#wprm-recipe-print-id').val('0').trigger('change');
    jQuery('#wprm-recipe-print-text').val('');
}

Modal.actions.insert_jump_to_recipe = function(button) {
    var id = parseInt(jQuery('#wprm-recipe-jump-id').val()),
        text = utils.shortcode_escape(jQuery('#wprm-recipe-jump-text').val()),
        shortcode = '[wprm-recipe-jump';

    if (id > 0) {
        shortcode += ' id="' + id + '"';
    }

    if (text) {
        shortcode += ' text="' + text + '"';
    }

    shortcode += ']';

    utils.add_text_to_editor(shortcode);
    Modal.close();
};

Modal.actions.insert_jump_to_video = function(button) {
    var id = parseInt(jQuery('#wprm-recipe-jump-video-id').val()),
        text = utils.shortcode_escape(jQuery('#wprm-recipe-jump-video-text').val()),
        shortcode = '[wprm-recipe-jump-video';

    if (id > 0) {
        shortcode += ' id="' + id + '"';
    }

    if (text) {
        shortcode += ' text="' + text + '"';
    }

    shortcode += ']';

    utils.add_text_to_editor(shortcode);
    Modal.close();
};

Modal.actions.insert_print_recipe = function(button) {
    var id = parseInt(jQuery('#wprm-recipe-print-id').val()),
        text = utils.shortcode_escape(jQuery('#wprm-recipe-print-text').val()),
        shortcode = '[wprm-recipe-print';

    if (id > 0) {
        shortcode += ' id="' + id + '"';
    }

    if (text) {
        shortcode += ' text="' + text + '"';
    }

    shortcode += ']';

    utils.add_text_to_editor(shortcode);
    Modal.close();
};