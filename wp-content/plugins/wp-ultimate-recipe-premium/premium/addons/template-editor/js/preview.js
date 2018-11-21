jQuery(document).ready(function() {
    // Only keep first recipe
    jQuery('.wpurp-container').first().addClass('wpurp-container-keep');

    // Indicate which elements are parent to the recipe
    jQuery('.wpurp-container-keep').parents().each(function() {
        jQuery(this).addClass('wpurp-container-keep-parent');
    });

    // Remove all elements except for the recipe and its parents
    jQuery('body *').not('.wpurp-container-keep-parent, .wpurp-container-keep, .wpurp-container-keep *').remove();

    jQuery('body').prepend('<div style="text-align: center; margin-bottom: 5px;"><select onchange="wpurp_preview_type(this.value)"><option value="desktop">Desktop Preview</option><option value="mobile">Mobile Preview</option></select></div>');

    wpurp_preview_type('desktop');

    jQuery(document).on('hover', '.wpurp-container', function(e) {
        var hovering = e.type == 'mouseenter';
        WPUltimatePostGrid.hoverGridItem(jQuery(this), hovering);
    });
});

var wpurp_preview_type = function(type) {
    if(type == 'desktop') {
        jQuery('.wpurp-container').removeClass('wpurp-mobile-preview');
        jQuery('.wpurp-responsive-mobile').css('display', 'none');
        jQuery('.wpurp-responsive-desktop').css('display', 'block');
    } else {
        jQuery('.wpurp-container').addClass('wpurp-mobile-preview');
        jQuery('.wpurp-responsive-mobile').css('display', 'block');
        jQuery('.wpurp-responsive-desktop').css('display', 'none');
    }
}