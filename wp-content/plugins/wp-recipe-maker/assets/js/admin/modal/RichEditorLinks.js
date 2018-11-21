import MediumEditor from 'medium-editor';

export function initRichEditorLinks() {
    // Source: medium-editor.js
    MediumEditor.prototype.createLink = function (opts) {
        var currentEditor = MediumEditor.selection.getSelectionElement(this.options.contentWindow),
            customEvent = {},
            targetUrl;

        // Make sure the selection is within an element this editor is tracking
        if (this.elements.indexOf(currentEditor) === -1) {
            return;
        }

        try {
            this.events.disableCustomEvent('editableInput');
            // TODO: Deprecate support for opts.url in 6.0.0
            if (opts.url) {
                MediumEditor.util.deprecated('.url option for createLink', '.value', '6.0.0');
            }
            targetUrl = opts.url || opts.value;
            if (targetUrl && targetUrl.trim().length > 0) {
                var currentSelection = this.options.contentWindow.getSelection();
                if (currentSelection) {
                    var currRange = currentSelection.getRangeAt(0),
                        commonAncestorContainer = currRange.commonAncestorContainer,
                        exportedSelection,
                        startContainerParentElement,
                        endContainerParentElement,
                        textNodes;

                    // If the selection is contained within a single text node
                    // and the selection starts at the beginning of the text node,
                    // MSIE still says the startContainer is the parent of the text node.
                    // If the selection is contained within a single text node, we
                    // want to just use the default browser 'createLink', so we need
                    // to account for this case and adjust the commonAncestorContainer accordingly
                    if (currRange.endContainer.nodeType === 3 &&
                        currRange.startContainer.nodeType !== 3 &&
                        currRange.startOffset === 0 &&
                        currRange.startContainer.firstChild === currRange.endContainer) {
                        commonAncestorContainer = currRange.endContainer;
                    }

                    startContainerParentElement = MediumEditor.util.getClosestBlockContainer(currRange.startContainer);
                    endContainerParentElement = MediumEditor.util.getClosestBlockContainer(currRange.endContainer);

                    // If the selection is not contained within a single text node
                    // but the selection is contained within the same block element
                    // we want to make sure we create a single link, and not multiple links
                    // which can happen with the built in browser functionality
                    if (commonAncestorContainer.nodeType !== 3 && commonAncestorContainer.textContent.length !== 0 && startContainerParentElement === endContainerParentElement) {
                        var parentElement = (startContainerParentElement || currentEditor),
                            fragment = this.options.ownerDocument.createDocumentFragment();

                        // since we are going to create a link from an extracted text,
                        // be sure that if we are updating a link, we won't let an empty link behind (see #754)
                        // (Workaroung for Chrome)
                        this.execAction('unlink');

                        exportedSelection = this.exportSelection();
                        fragment.appendChild(parentElement.cloneNode(true));

                        if (currentEditor === parentElement) {
                            // We have to avoid the editor itself being wiped out when it's the only block element,
                            // as our reference inside this.elements gets detached from the page when insertHTML runs.
                            // If we just use [parentElement, 0] and [parentElement, parentElement.childNodes.length]
                            // as the range boundaries, this happens whenever parentElement === currentEditor.
                            // The tradeoff to this workaround is that a orphaned tag can sometimes be left behind at
                            // the end of the editor's content.
                            // In Gecko:
                            // as an empty <strong></strong> if parentElement.lastChild is a <strong> tag.
                            // In WebKit:
                            // an invented <br /> tag at the end in the same situation
                            MediumEditor.selection.select(
                                this.options.ownerDocument,
                                parentElement.firstChild,
                                0,
                                parentElement.lastChild,
                                parentElement.lastChild.nodeType === 3 ?
                                parentElement.lastChild.nodeValue.length : parentElement.lastChild.childNodes.length
                            );
                        } else {
                            MediumEditor.selection.select(
                                this.options.ownerDocument,
                                parentElement,
                                0,
                                parentElement,
                                parentElement.childNodes.length
                            );
                        }

                        var modifiedExportedSelection = this.exportSelection();

                        textNodes = MediumEditor.util.findOrCreateMatchingTextNodes(
                            this.options.ownerDocument,
                            fragment,
                            {
                                start: exportedSelection.start - modifiedExportedSelection.start,
                                end: exportedSelection.end - modifiedExportedSelection.start,
                                editableElementIndex: exportedSelection.editableElementIndex
                            }
                        );
                        // If textNodes are not present, when changing link on images
                        // ex: <a><img src="http://image.test.com"></a>, change fragment to currRange.startContainer
                        // and set textNodes array to [imageElement, imageElement]
                        if (textNodes.length === 0) {
                            fragment = this.options.ownerDocument.createDocumentFragment();
                            fragment.appendChild(commonAncestorContainer.cloneNode(true));
                            textNodes = [fragment.firstChild.firstChild, fragment.firstChild.lastChild];
                        }

                        // Creates the link in the document fragment
                        MediumEditor.util.createLink(this.options.ownerDocument, textNodes, targetUrl.trim());

                        // Chrome trims the leading whitespaces when inserting HTML, which messes up restoring the selection.
                        var leadingWhitespacesCount = (fragment.firstChild.innerHTML.match(/^\s+/) || [''])[0].length;

                        // Now move the created link back into the original document in a way to preserve undo/redo history
                        MediumEditor.util.insertHTMLCommand(this.options.ownerDocument, fragment.firstChild.innerHTML.replace(/^\s+/, ''));
                        exportedSelection.start -= leadingWhitespacesCount;
                        exportedSelection.end -= leadingWhitespacesCount;

                        this.importSelection(exportedSelection);
                    } else {
                        this.options.ownerDocument.execCommand('createLink', false, targetUrl);
                    }

                    if (this.options.targetBlank || opts.target === '_blank') {
                        MediumEditor.util.setTargetBlank(MediumEditor.selection.getSelectionStart(this.options.ownerDocument), targetUrl);
                    } else {
                        MediumEditor.util.removeTargetBlank(MediumEditor.selection.getSelectionStart(this.options.ownerDocument), targetUrl);
                    }

                    if (opts.rel === 'nofollow') {
                        MediumEditor.util.setNofollow(MediumEditor.selection.getSelectionStart(this.options.ownerDocument), targetUrl);
                    } else {
                        MediumEditor.util.removeNofollow(MediumEditor.selection.getSelectionStart(this.options.ownerDocument), targetUrl);
                    }

                    if (opts.buttonClass) {
                        MediumEditor.util.addClassToAnchors(MediumEditor.selection.getSelectionStart(this.options.ownerDocument), opts.buttonClass);
                    }
                }
            }
            // Fire input event for backwards compatibility if anyone was listening directly to the DOM input event
            if (this.options.targetBlank || opts.target === '_blank' ||  opts.rel === 'nofollow' || opts.buttonClass) {
                customEvent = this.options.ownerDocument.createEvent('HTMLEvents');
                customEvent.initEvent('input', true, true, this.options.contentWindow);
                for (var i = 0, len = this.elements.length; i < len; i += 1) {
                    this.elements[i].dispatchEvent(customEvent);
                }
            }
        } finally {
            this.events.enableCustomEvent('editableInput');
        }
        // Fire our custom editableInput event
        this.events.triggerCustomEvent('editableInput', customEvent, currentEditor);
    };

    MediumEditor.util.setNofollow = function (el, anchorUrl) {
        var i, url = anchorUrl || false;
        if (el.nodeName.toLowerCase() === 'a') {
            el.rel = 'nofollow';
        } else {
            el = el.getElementsByTagName('a');

            for (i = 0; i < el.length; i += 1) {
                if (false === url || url === el[i].attributes.href.value) {
                    el[i].rel = 'nofollow';
                }
            }
        }
    };

    MediumEditor.util.removeNofollow = function (el, anchorUrl) {
        var i;
        if (el.nodeName.toLowerCase() === 'a') {
            el.removeAttribute('rel');
        } else {
            el = el.getElementsByTagName('a');

            for (i = 0; i < el.length; i += 1) {
                if (anchorUrl === el[i].attributes.href.value) {
                    el[i].removeAttribute('rel');
                }
            }
        }
    };
};

export let RichEditorLinksExtension = MediumEditor.extensions.form.extend({
	/* Anchor Form Options */

	/* customClassOption: [string]  (previously options.anchorButton + options.anchorButtonClass)
		* Custom class name the user can optionally have added to their created links (ie 'button').
		* If passed as a non-empty string, a checkbox will be displayed allowing the user to choose
		* whether to have the class added to the created link or not.
		*/
	customClassOption: null,

	/* customClassOptionText: [string]
		* text to be shown in the checkbox when the __customClassOption__ is being used.
		*/
	customClassOptionText: 'Button',

	/* linkValidation: [boolean]  (previously options.checkLinkFormat)
		* enables/disables check for common URL protocols on anchor links.
		*/
	linkValidation: false,

	/* placeholderText: [string]  (previously options.anchorInputPlaceholder)
		* text to be shown as placeholder of the anchor input.
		*/
	placeholderText: 'Paste or type a link',

	/* targetCheckbox: [boolean]  (previously options.anchorTarget)
		* enables/disables displaying a "Open in new window" checkbox, which when checked
		* changes the `target` attribute of the created link.
		*/
	targetCheckbox: true,

	/* targetCheckboxText: [string]  (previously options.anchorInputCheckboxLabel)
		* text to be shown in the checkbox enabled via the __targetCheckbox__ option.
		*/
	targetCheckboxText: 'Open in new tab',

	nofollowCheckbox: true,
	nofollowCheckboxText: 'Use nofollow',

	// Options for the Button base class
	name: 'links',
	action: 'createLink',
	aria: 'link',
	tagNames: ['a'],
	contentDefault: '<span class="dashicons dashicons-admin-links"></span>',
	contentFA: '<i class="fa fa-link"></i>',

	init: function () {
		MediumEditor.extensions.form.prototype.init.apply(this, arguments);

		this.subscribe('editableKeydown', this.handleKeydown.bind(this));
	},

	// Called when the button the toolbar is clicked
	// Overrides ButtonExtension.handleClick
	handleClick: function (event) {
		event.preventDefault();
		event.stopPropagation();

		var range = MediumEditor.selection.getSelectionRange(this.document);

		if (range.startContainer.nodeName.toLowerCase() === 'a' ||
			range.endContainer.nodeName.toLowerCase() === 'a' ||
			MediumEditor.util.getClosestTag(MediumEditor.selection.getSelectedParentElement(range), 'a')) {
			return this.execAction('unlink');
		}

		if (!this.isDisplayed()) {
			this.showForm();
		}

		return false;
	},

	// Called when user hits the defined shortcut (CTRL / COMMAND + K)
	handleKeydown: function (event) {
		if (MediumEditor.util.isKey(event, MediumEditor.util.keyCode.K) && MediumEditor.util.isMetaCtrlKey(event) && !event.shiftKey) {
			this.handleClick(event);
		}
	},

	// Called by medium-editor to append form to the toolbar
	getForm: function () {
		if (!this.form) {
			this.form = this.createForm();
		}
		return this.form;
	},

	getTemplate: function () {
		var template = [
			'<input type="text" class="medium-editor-toolbar-input" placeholder="', this.placeholderText, '" style="padding-left: 25px; width: 265px;">'
		];

		template.push(
			'<a href="#" class="medium-editor-toolbar-save">',
			this.getEditorOption('buttonLabels') === 'fontawesome' ? '<i class="fa fa-check"></i>' : this.formSaveLabel,
			'</a>'
		);

		template.push('<a href="#" class="medium-editor-toolbar-close">',
			this.getEditorOption('buttonLabels') === 'fontawesome' ? '<i class="fa fa-times"></i>' : this.formCloseLabel,
			'</a>');

		// both of these options are slightly moot with the ability to
		// override the various form buildup/serialize functions.

		if (this.targetCheckbox) {
			// fixme: ideally, this targetCheckboxText would be a formLabel too,
			// figure out how to deprecate? also consider `fa-` icon default implcations.
			template.push(
				'<div class="medium-editor-toolbar-form-row" style="padding-left: 20px;">',
				'<input type="checkbox" class="medium-editor-toolbar-anchor-target">',
				'<label>',
				this.targetCheckboxText,
				'</label>',
				'</div>'
			);
		}

		if (this.nofollowCheckbox) {
			template.push(
				'<div class="medium-editor-toolbar-form-row" style="padding-left: 20px;">',
				'<input type="checkbox" class="medium-editor-toolbar-anchor-nofollow">',
				'<label>',
				this.nofollowCheckboxText,
				'</label>',
				'</div>'
			);
		}

		if (this.customClassOption) {
			// fixme: expose this `Button` text as a formLabel property, too
			// and provide similar access to a `fa-` icon default.
			template.push(
				'<div class="medium-editor-toolbar-form-row">',
				'<input type="checkbox" class="medium-editor-toolbar-anchor-button">',
				'<label>',
				this.customClassOptionText,
				'</label>',
				'</div>'
			);
		}

		return template.join('');

	},

	// Used by medium-editor when the default toolbar is to be displayed
	isDisplayed: function () {
		return MediumEditor.extensions.form.prototype.isDisplayed.apply(this);
	},

	hideForm: function () {
		MediumEditor.extensions.form.prototype.hideForm.apply(this);
		this.getInput().value = '';
	},

	showForm: function (opts) {
		var input = this.getInput(),
			targetCheckbox = this.getAnchorTargetCheckbox(),
			nofollowCheckbox = this.getAnchorNofollowCheckbox(),
			buttonCheckbox = this.getAnchorButtonCheckbox();

		opts = opts || { value: '' };
		// TODO: This is for backwards compatability
		// We don't need to support the 'string' argument in 6.0.0
		if (typeof opts === 'string') {
			opts = {
				value: opts
			};
		}

		this.base.saveSelection();
		this.hideToolbarDefaultActions();
		MediumEditor.extensions.form.prototype.showForm.apply(this);
		this.setToolbarPosition();

		input.value = opts.value;
		input.focus();

		// If we have a target checkbox, we want it to be checked/unchecked
		// based on whether the existing link has target=_blank
		if (targetCheckbox) {
			targetCheckbox.checked = opts.target === '_blank';
		}

		if (nofollowCheckbox) {
			nofollowCheckbox.checked = opts.rel === 'nofollow';
		}

		// If we have a custom class checkbox, we want it to be checked/unchecked
		// based on whether an existing link already has the class
		if (buttonCheckbox) {
			var classList = opts.buttonClass ? opts.buttonClass.split(' ') : [];
			buttonCheckbox.checked = (classList.indexOf(this.customClassOption) !== -1);
		}
	},

	// Called by core when tearing down medium-editor (destroy)
	destroy: function () {
		if (!this.form) {
			return false;
		}

		if (this.form.parentNode) {
			this.form.parentNode.removeChild(this.form);
		}

		delete this.form;
	},

	// core methods

	getFormOpts: function () {
		// no notion of private functions? wanted `_getFormOpts`
		var targetCheckbox = this.getAnchorTargetCheckbox(),
			nofollowCheckbox = this.getAnchorNofollowCheckbox(),
			buttonCheckbox = this.getAnchorButtonCheckbox(),
			opts = {
				value: this.getInput().value.trim()
			};

		if (this.linkValidation) {
			opts.value = this.checkLinkFormat(opts.value);
		}

		opts.target = '_self';
		if (targetCheckbox && targetCheckbox.checked) {
			opts.target = '_blank';
		}

		opts.rel = '';
		if (nofollowCheckbox && nofollowCheckbox.checked) {
			opts.rel = 'nofollow';
		}

		if (buttonCheckbox && buttonCheckbox.checked) {
			opts.buttonClass = this.customClassOption;
		}

		return opts;
	},

	doFormSave: function () {
		var opts = this.getFormOpts();
		this.completeFormSave(opts);
	},

	completeFormSave: function (opts) {
		this.base.restoreSelection();
		this.execAction(this.action, opts);
		this.base.checkSelection();
	},

	ensureEncodedUri: function (str) {
		return str === decodeURI(str) ? encodeURI(str) : str;
	},

	ensureEncodedUriComponent: function (str) {
		return str === decodeURIComponent(str) ? encodeURIComponent(str) : str;
	},

	ensureEncodedParam: function (param) {
		var split = param.split('='),
			key = split[0],
			val = split[1];

		return key + (val === undefined ? '' : '=' + this.ensureEncodedUriComponent(val));
	},

	ensureEncodedQuery: function (queryString) {
		return queryString.split('&').map(this.ensureEncodedParam.bind(this)).join('&');
	},

	checkLinkFormat: function (value) {
		// Matches any alphabetical characters followed by ://
		// Matches protocol relative "//"
		// Matches common external protocols "mailto:" "tel:" "maps:"
		// Matches relative hash link, begins with "#"
		var urlSchemeRegex = /^([a-z]+:)?\/\/|^(mailto|tel|maps):|^\#/i,
			// telRegex is a regex for checking if the string is a telephone number
			telRegex = /^\+?\s?\(?(?:\d\s?\-?\)?){3,20}$/,
			split = value.split('?'),
			path = split[0],
			query = split[1];

		if (telRegex.test(value)) {
			return 'tel:' + value;
		} else {
			// Check for URL scheme and default to http:// if none found
			return (urlSchemeRegex.test(value) ? '' : 'http://') +
				// Ensure path is encoded
				this.ensureEncodedUri(path) +
				// Ensure query is encoded
				(query === undefined ? '' : '?' + this.ensureEncodedQuery(query));
		}
	},

	doFormCancel: function () {
		this.base.restoreSelection();
		this.base.checkSelection();
	},

	// form creation and event handling
	attachFormEvents: function (form) {
		var close = form.querySelector('.medium-editor-toolbar-close'),
			save = form.querySelector('.medium-editor-toolbar-save'),
			input = form.querySelector('.medium-editor-toolbar-input');

		// Handle clicks on the form itself
		this.on(form, 'click', this.handleFormClick.bind(this));

		// Handle typing in the textbox
		this.on(input, 'keyup', this.handleTextboxKeyup.bind(this));

		// Handle close button clicks
		this.on(close, 'click', this.handleCloseClick.bind(this));

		// Handle save button clicks (capture)
		this.on(save, 'click', this.handleSaveClick.bind(this), true);

	},

	createForm: function () {
		var doc = this.document,
			form = doc.createElement('div');

		// Anchor Form (div)
		form.className = 'medium-editor-toolbar-form';
		form.id = 'medium-editor-toolbar-form-anchor-' + this.getEditorId();
		form.innerHTML = this.getTemplate();
		this.attachFormEvents(form);

		return form;
	},

	getInput: function () {
		return this.getForm().querySelector('input.medium-editor-toolbar-input');
	},

	getAnchorTargetCheckbox: function () {
		return this.getForm().querySelector('.medium-editor-toolbar-anchor-target');
	},

	getAnchorNofollowCheckbox: function () {
		return this.getForm().querySelector('.medium-editor-toolbar-anchor-nofollow');
	},

	getAnchorButtonCheckbox: function () {
		return this.getForm().querySelector('.medium-editor-toolbar-anchor-button');
	},

	handleTextboxKeyup: function (event) {
		// For ENTER -> create the anchor
		if (event.keyCode === MediumEditor.util.keyCode.ENTER) {
			event.preventDefault();
			this.doFormSave();
			return;
		}

		// For ESCAPE -> close the form
		if (event.keyCode === MediumEditor.util.keyCode.ESCAPE) {
			event.preventDefault();
			this.doFormCancel();
		}
	},

	handleFormClick: function (event) {
		// make sure not to hide form when clicking inside the form
		event.stopPropagation();
	},

	handleSaveClick: function (event) {
		// Clicking Save -> create the anchor
		event.preventDefault();
		this.doFormSave();
	},

	handleCloseClick: function (event) {
		// Click Close -> close the form
		event.preventDefault();
		this.doFormCancel();
	}
});