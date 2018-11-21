import Recipe from './Recipe';
import modalInit from './ModalInit';

let Modal = {
    changes_made: false,
    container: false,
    gutenberg: false,
    active_editor_id: false,
    args: {},
    actions: {},
    recipe: Recipe,
    init: function(container) {
        this.container = container;
        modalInit(this);
        this.recipe.init();
    },
    open: function(editor_id, args = {}) {
        this.args = args;

		// Enable menu items
		jQuery('.wprm-menu-item').show();
		jQuery('.wprm-menu-hidden').hide();

		this.active_editor_id = editor_id;
        jQuery('.wprm-modal-container').show();
        
        // Gutenberg check
        this.gutenberg = args.hasOwnProperty( 'gutenberg' ) && args.gutenberg;

		// Init tabs
		var tabs = jQuery('.wprm-router').find('.wprm-menu-item');
		jQuery(tabs).each(function() {
            let init_callback = jQuery(this).data('init');

            if (init_callback && typeof Modal.actions[init_callback] == 'function') {
                Modal.actions[init_callback](args);
            }
        });

		// Default to first menu item
        jQuery('.wprm-menu').find('.wprm-menu-item').first().click();

        // Optionally open a different menu item
        if( args.hasOwnProperty( 'menu' ) ) {
            var menu = jQuery('.wprm-menu').find('.wprm-menu-item[data-menu="' + args.menu + '"]');

            if ( menu ) {
                menu.click();
            }
        }
        
        this.changes_made = false;
    },
    close: function() {
        this.active_editor_id = false;
		jQuery('.wprm-menu').removeClass('visible');
		jQuery('.wprm-modal-container').hide();
    },
    disable_menu: function() {
		jQuery('.wprm-frame-menu').find('.wprm-menu-item').hide();
		jQuery('.wprm-menu-hidden').show();
    },
}
export default Modal;