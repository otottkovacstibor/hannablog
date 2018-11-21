import rangy from 'rangy';
import MediumEditor from 'medium-editor';
import 'medium-editor/dist/css/medium-editor.css';
import 'medium-editor/dist/css/themes/beagle.css';

import {initRichEditorLinks, RichEditorLinksExtension} from './RichEditorLinks';
import Modal from './Modal';

let RichEditor = {
    editor: false,
    clear: function() {
        this.editor.resetContent();
    },
    set: function(content) {
        this.editor.setContent(content);
    },
    init: function() {
        if (this.editor) {
            this.editor.addElements('.wprm-rich-editor');
        } else {
            rangy.init();
            initRichEditorLinks();

            let args = {
                placeholder: {
                    text: wprm_admin.modal.text.medium_editor_placeholder,
                    hideOnClick: true
                },
                autoLink: true,
                anchorPreview: {
                    showWhenToolbarIsVisible: false,
                },
                imageDragging: false,
                toolbar: {
                        buttons: ['bold', 'italic', 'underline', 'subscript', 'superscript']
                },
                extensions: {}
            };

            args.toolbar.buttons.push('links');
            args.extensions.links = new this.extensions.links();

            if(wprm_admin.addons.premium) {
                args.toolbar.buttons.push('adjustable_servings');
                args.toolbar.buttons.push('timer');

                args.extensions.adjustable_servings = new this.extensions.adjustable_servings();
                args.extensions.timer = new this.extensions.timer();
            }

            this.editor = new MediumEditor('.wprm-rich-editor', args);
            this.editor.subscribe('editableInput', function() {
                Modal.changes_made = true;
            });
        }
    },
    extensions: {
        adjustable_servings: MediumEditor.Extension.extend({
            name: 'adjustable_servings',
            init: function () {
                this.button = this.document.createElement('button');
                this.button.classList.add('medium-editor-action');
                this.button.innerHTML = '<b>Adjustable</b>';
                this.button.title = 'Adjustable Quantity';
        
                this.on(this.button, 'click', this.handleClick.bind(this));
            },
            getButton: function () {
                return this.button;
            },
            handleClick: function (event) {
                var selection = rangy.getSelection(),
                    range = selection.getRangeAt(0),
                    original_range = range.cloneRange(),
                    end_range = range.cloneRange(),
                    text = range.getDocument().createTextNode('[adjustable]'),
                    end_text = range.getDocument().createTextNode('[/adjustable]');
        
                end_range.collapse(false);
                end_range.insertNode(end_text);
                end_range.detach();
                range.setEndAfter(end_text);
        
                range.insertNode(text);
                rangy.getSelection().setSingleRange(original_range);
            }
        }),
        timer: MediumEditor.Extension.extend({
            name: 'timer',
            init: function () {
                this.button = this.document.createElement('button');
                this.button.classList.add('medium-editor-action');
                this.button.innerHTML = '<b>Timer</b>';
                this.button.title = 'Timer';
        
                this.on(this.button, 'click', this.handleClick.bind(this));
            },
            getButton: function () {
                return this.button;
            },
            handleClick: function (event) {
                var selection = rangy.getSelection(),
                    range = selection.getRangeAt(0),
                    original_range = range.cloneRange(),
                    end_range = range.cloneRange(),
                    text = range.getDocument().createTextNode('[timer minutes=0]'),
                    end_text = range.getDocument().createTextNode('[/timer]');
        
                end_range.collapse(false);
                end_range.insertNode(end_text);
                end_range.detach();
                range.setEndAfter(end_text);
        
                range.insertNode(text);
                rangy.getSelection().setSingleRange(original_range);
            }
        }),
        links: RichEditorLinksExtension,
    }
}
export default RichEditor;