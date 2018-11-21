import Modal from './modal/Modal';
import './modal/tabs/import-text';
import './modal/tabs/recipe-snippets';
import './modal/tabs/recipe';

import '../../css/admin/modal.scss';

jQuery(document).ready(function($) {
    let container = jQuery('.wprm-modal-container');

    if (container) {
        Modal.init(container);
    }
});