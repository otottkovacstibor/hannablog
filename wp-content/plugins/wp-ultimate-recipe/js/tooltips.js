jQuery(document).ready(function() {
    if(jQuery('.recipe-tooltip').length) {
        jQuery('.recipe-tooltip').jt_tooltip({
            offset: [-10, 0],
            effect: 'fade',
            delay: 250,
            relative: true
        });

        jQuery('.wpupg-grid').find('.recipe-tooltip-content').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    }

    jQuery('.wpupg-grid').on('arrangeComplete', function() {
        jQuery('.wpupg-grid').find('.recipe-tooltip-content').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });
});