jQuery(document).ready(function() {
    if(window.opener) {
        jQuery('#wpurp-meal-plan-shopping-list-mobile-tip').show();
    }

    jQuery('.wpurp-shopping-list-ingredient-checkbox').on('change', function(e) {
        var checkbox = jQuery(this);
        if(checkbox.is(':checked')) {
            checkbox.closest('tr').addClass('ingredient-checked');
        } else {
            checkbox.closest('tr').removeClass('ingredient-checked');
        }
    });
});