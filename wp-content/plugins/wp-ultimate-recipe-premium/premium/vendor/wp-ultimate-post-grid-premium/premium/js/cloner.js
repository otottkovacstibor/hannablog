jQuery(document).ready(function() {
    jQuery('.clone-grid').on('click', function() {
        var grid = jQuery(this).data('grid');

        var data = {
            action: 'clone_grid',
            security: wpupg_cloner.nonce,
            grid: grid
        };

        jQuery.post(wpupg_cloner.ajax_url, data, function(out) {
            window.location = out.redirect;
        }, 'json');
    });
});