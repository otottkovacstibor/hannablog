var wpurp_adjustable_servings = {};

wpurp_adjustable_servings.updateShortcode = function(recipe, servings_new) {
    var servings_original = parseFloat(recipe.data('servings-original'));

    recipe.find('.wpurp-adjustable-quantity').each(function() {
        var quantity_element = jQuery(this);

        // Only do this once.
        if('undefined' == typeof quantity_element.data('original_quantity')) {
            var quantity = wpurp_adjustable_servings.parse_quantity(quantity_element.text());
            quantity /= servings_original;

            quantity_element
                .data('original_quantity', quantity_element.text())
                .data('unit_quantity', quantity);
        }
        
        // Adjust quantity.
        if(servings_new == servings_original) {
            quantity_element.text(quantity_element.data('original_quantity'));
        } else {
            var quantity = parseFloat(quantity_element.data('unit_quantity')) * servings_new;

            if(!isNaN(quantity)) {
                quantity_element.text(wpurp_adjustable_servings.toFixed(quantity, false));
            }
        }
    });
};

wpurp_adjustable_servings.parse_quantity = function(sQuantity) {
    // Use . for decimals
    sQuantity = sQuantity.replace(',', '.');
        
    // Replace fraction characters with equivalent
    var fractionsRegex = /(\u00BC|\u00BD|\u00BE|\u2150|\u2151|\u2152|\u2153|\u2154|\u2155|\u2156|\u2157|\u2158|\u2159|\u215A|\u215B|\u215C|\u215D|\u215E)/;
    var fractionsMap = {
        '\u00BC': ' 1/4', '\u00BD': ' 1/2', '\u00BE': ' 3/4', '\u2150': ' 1/7',
        '\u2151': ' 1/9', '\u2152': ' 1/10', '\u2153': ' 1/3', '\u2154': ' 2/3',
        '\u2155': ' 1/5', '\u2156': ' 2/5', '\u2157': ' 3/5', '\u2158': ' 4/5',
        '\u2159': ' 1/6', '\u215A': ' 5/6', '\u215B': ' 1/8', '\u215C': ' 3/8',
        '\u215D': ' 5/8', '\u215E': ' 7/8'
    };
    sQuantity = (sQuantity + '').replace(fractionsRegex, function(m, vf) {
        return fractionsMap[vf];
    });

    // Split by spaces
    sQuantity = sQuantity.trim();
    var parts = sQuantity.split(' ');

    var quantity = false;

    if(sQuantity !== '') {
        quantity = 0;

        // Loop over parts and add values
        for(var i = 0; i < parts.length; i++) {
            if(parts[i].trim() !== '') {
                var division_parts = parts[i].split('/', 2);
                var part_quantity = parseFloat(division_parts[0]);

                if(division_parts[1] !== undefined) {
                    var divisor = parseFloat(division_parts[1]);

                    if(divisor !== 0) {
                        part_quantity /= divisor;
                    }
                }

                quantity += part_quantity;
            }			
        }
    }

    return quantity;
}

wpurp_adjustable_servings.updateAmounts = function(amounts, servings_original, servings_new)
{
    amounts.each(function() {
        var amount = parseFloat(jQuery(this).data('normalized'));
        var fraction = jQuery(this).data('fraction');

        if(servings_original == servings_new)
        {
            jQuery(this).text(jQuery(this).data('original'));
        }
        else
        {
            if(!isFinite(amount)) {
                jQuery(this).addClass('recipe-ingredient-nan');
            } else {
                var new_amount = servings_new * amount/servings_original;
                var new_amount_text = wpurp_adjustable_servings.toFixed(new_amount, fraction);
                jQuery(this).text(new_amount_text);
            }
        }
    });
}

wpurp_adjustable_servings.toFixed = function(amount, fraction)
{
    if(fraction) {
        var fractioned_amount = Fraction(amount.toString()).snap();
        if(fractioned_amount.denominator < 100) {
            return fractioned_amount;
        }
    }

    if(amount == '' || amount == 0) {
        return '';
    }
    // reformat to fixed
    var precision = parseInt(wpurp_servings.precision);
    var formatted = amount.toFixed(precision);

    // increase the precision if reformated to 0.00, failsafe for endless loop
    while(parseFloat(formatted) == 0) {
        precision++;
        formatted = amount.toFixed(precision);

        if(precision > 10) {
            return '';
        }
    }

    // ends with .00, remove
    if(precision > 0) {
        var zeroes = Array(precision+1).join('0');
        formatted = formatted.replace(new RegExp('\.' + zeroes + '$'),'');
    }

    // Change decimal character
    if(typeof wpurp_servings !== 'undefined') {
        formatted = formatted.replace('.', wpurp_servings.decimal_character);
    }

    return formatted;
}


jQuery(document).ready(function() {

    jQuery(document).on('keyup change', '.adjust-recipe-servings', function(e) {
        var servings_input = jQuery(this);

        var amounts = servings_input.parents('.wpurp-container').find('.wpurp-recipe-ingredient-quantity');
        var servings_original = parseFloat(servings_input.data('original'));
        var servings_new = servings_input.val();

        if(isNaN(servings_new) || servings_new <= 0){
            servings_new = 1;
        }

        wpurp_adjustable_servings.updateAmounts(amounts, servings_original, servings_new);
        wpurp_adjustable_servings.updateShortcode(servings_input.parents('.wpurp-container'), servings_new);

        RecipePrintButton.update(servings_input.parents('.wpurp-container'));
    });

    jQuery(document).on('blur', '.adjust-recipe-servings', function(e) {
        var servings_input = jQuery(this);

        var servings_new = servings_input.val();

        if(isNaN(servings_new) || servings_new <= 0){
            servings_new = 1;
        }

        servings_input.parents('.wpurp-container').find('.adjust-recipe-servings').each(function() {
            jQuery(this).val(servings_new);
        });

        RecipePrintButton.update(servings_input.parents('.wpurp-container'));
    });
});