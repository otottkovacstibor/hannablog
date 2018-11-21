export default function modalInit(modal) {
    // Closing Modal.
    jQuery(modal.container).on('click', '.wprm-modal-close, .wprm-modal-backdrop', function() {
        if( ! modal.changes_made || confirm(wprm_admin.modal.text.modal_close_confirm) ) {
            modal.close();
        }
    });

    // Modal Menu.
    jQuery('.wprm-menu').on('click', '.wprm-menu-item', function() {
        var menu_item = jQuery(this),
            menu_target = menu_item.data('menu'),
            menu_tab = menu_item.data('tab');

        // Hide Menu if on Mobile
        jQuery('.wprm-menu').removeClass('visible');

        // Set clicked on tab as the active one
        jQuery('.wprm-menu').find('.wprm-menu-item').removeClass('active');
        menu_item.addClass('active');

        // Show correct menu
        jQuery('.wprm-frame-router').find('.wprm-router').removeClass('active');
        jQuery('.wprm-frame-router').find('#wprm-menu-' + menu_target).addClass('active');

        // Show the first tab as active or whichever tab was passed along
        var active_tab = false;
        jQuery('.wprm-router').find('.wprm-menu-item').removeClass('active');
        jQuery('.wprm-frame-router').find('#wprm-menu-' + menu_target).find('.wprm-menu-item').each(function(index) {
            if (index === 0 || jQuery(this).data('tab') == menu_tab) {
                    active_tab = jQuery(this);
            }
        });

        if (active_tab) {
            active_tab.click();
        }

        // Change main title
        jQuery('.wprm-frame-title').find('h1').text(menu_item.text());
    });

    // Modal Menu on Mobile
    jQuery(modal.container).on('click', '.wprm-frame-title', function() {
        jQuery('.wprm-menu').toggleClass('visible');
    });

    // Modal Tabs
    jQuery('.wprm-router').on('click', '.wprm-menu-item', function() {
        var menu_item = jQuery(this),
            tab_target = menu_item.data('tab'),
            tab_button = menu_item.data('button');

        // Set clicked on tab as the active one
        jQuery('.wprm-router').find('.wprm-menu-item').removeClass('active');
        menu_item.addClass('active');

        // Hide action button if no callback is set
        if (menu_item.data('callback')) {
            jQuery('.wprm-button-action').text(tab_button).show();

            if('wprm-menu-recipe' == menu_item.parents('.wprm-router').attr('id')) {
                jQuery('.wprm-button-action-save').show();
            } else {
                jQuery('.wprm-button-action-save').hide();
            }
        } else {
            jQuery('.wprm-button-action').hide();
            jQuery('.wprm-button-action-save').hide();
        }

        // Show correct tab
        jQuery('.wprm-frame-content').find('.wprm-frame-content-tab').removeClass('active');
        jQuery('.wprm-frame-content').find('#wprm-tab-' + tab_target).addClass('active');
    });

    // Insert or Update Button
    jQuery('.wprm-button-action').on('click', function() {
        var active_tab = jQuery('.wprm-router.active').find('.wprm-menu-item.active'),
            callback = active_tab.data('callback');

        if (callback && typeof modal.actions[callback] == 'function') {
            modal.actions[callback](jQuery(this));
        }
    });

    // Prevent Divi Builder bug.
    jQuery(modal.container).keydown( function(e) {
        e.stopPropagation();
    });

    // Select Recipes Dropdown
    jQuery('.wprm-recipes-dropdown').select2_wprm({
        width: '250px',
        ajax: {
            type: 'POST',
            url: wprm_admin.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'wprm_search_recipes',
                    security: wprm_admin.nonce,
                    search: params.term
                };
            },
            processResults: function (out, params) {
                return {
                    results: out.data.recipes_with_id,
                };
            },
            cache: true
        },
    });

    jQuery('.wprm-recipes-dropdown-with-first').select2_wprm({
        width: '250px',
        ajax: {
            type: 'POST',
            url: wprm_admin.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'wprm_search_recipes',
                    security: wprm_admin.nonce,
                    search: params.term
                };
            },
            processResults: function (out, params) {
                var default_options = [{
                    id: '0',
                    text: wprm_admin.modal.text.first_recipe_on_page,
                }];
                return {
                    results: default_options.concat(out.data.recipes_with_id),
                };
            },
            cache: true
        },
    });
}