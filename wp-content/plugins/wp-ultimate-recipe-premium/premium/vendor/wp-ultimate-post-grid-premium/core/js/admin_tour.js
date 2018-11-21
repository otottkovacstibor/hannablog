jQuery(document).ready( function($) {
    wpupg_open_pointer(0);

    function wpupg_open_pointer(i) {
        pointer = wpupg_admin_tour.pointers[i];

        options = $.extend( pointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: pointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });

        $(pointer.target).pointer( options ).pointer('open');
    }
});