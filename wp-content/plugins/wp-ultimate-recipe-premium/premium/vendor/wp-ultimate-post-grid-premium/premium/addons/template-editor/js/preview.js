var WPUltimatePostGrid = WPUltimatePostGrid || {};

jQuery(document).ready(function() {
    // Only keep first recipe
    jQuery('.wpupg-grid').first().addClass('wpupg-keep');

    // Indicate which elements are parent to the recipe
    jQuery('.wpupg-keep').parents().each(function() {
        jQuery(this).addClass('wpupg-keep-parent');
    });

    // Remove all elements except for the recipe and its parents
    jQuery('body *').not('.wpupg-keep-parent, .wpupg-keep, .wpupg-keep *').remove();
});