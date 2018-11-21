jQuery(document).ready(function() {
    if(jQuery('.recipe-tooltip.vote-attention').length) {
        jQuery('.recipe-tooltip.vote-attention').mouseenter();
        jQuery('.vote-attention-message').show();
        jQuery('.user-rating-stats').hide();

        setTimeout(function() {
            jQuery('.recipe-tooltip.vote-attention').mouseleave();

            setTimeout(function() {
                jQuery('.vote-attention-message').hide();
                jQuery('.user-rating-stats').show();
            }, 500);
        }, 5000);
    }

    jQuery('.wpurp-container .user-star-rating.user-can-vote i').hover(function() {
        var stars = jQuery(this).parents('.user-star-rating');
        var icon_full = stars.data('icon-full');
        var icon_half = stars.data('icon-half');
        var icon_empty = stars.data('icon-empty');

        jQuery(this)
            .prevAll().andSelf()
            .removeClass(icon_half)
            .removeClass(icon_empty)
            .addClass(icon_full);

        jQuery(this)
            .nextAll()
            .removeClass(icon_full)
            .removeClass(icon_half)
            .addClass(icon_empty);
    }, function() {
        var stars = jQuery(this).parents('.user-star-rating');
        var icon_full = stars.data('icon-full');
        var icon_half = stars.data('icon-half');
        var icon_empty = stars.data('icon-empty');

        jQuery(this)
            .siblings()
            .andSelf()
            .removeClass(icon_full)
            .removeClass(icon_half)
            .removeClass(icon_empty)
            .each(function() {
                jQuery(this).addClass(jQuery(this).data('original-icon'));
            });
    });

    jQuery('.wpurp-container .user-star-rating.user-can-vote i').click(function() {
        var stars = jQuery(this).data('star-value');
        var rating_stars = jQuery(this).parents('.user-star-rating');
        var recipe = rating_stars.data('recipe-id');

        var data = {
            action: 'rate_recipe',
            security: wpurp_user_ratings.nonce,
            stars: stars,
            recipe: recipe
        };

        jQuery.post(wpurp_user_ratings.ajax_url, data, function(rating) {
            var tooltip = rating_stars.nextAll('.recipe-tooltip-content:first');

            var icon_full = rating_stars.data('icon-full');
            var icon_half = rating_stars.data('icon-half');
            var icon_empty = rating_stars.data('icon-empty');

            tooltip.find('.user-rating-votes').text(rating.votes);
            tooltip.find('.user-rating-rating').text(rating.rating);
            tooltip.find('.user-rating-current-rating').text(stars);

            rating_stars.find('i').each(function(index, elem) {
                var star = jQuery(elem);

                star.removeClass(icon_full)
                    .removeClass(icon_half)
                    .removeClass(icon_empty);

                if(index < rating.stars) {
                    star.addClass(icon_full)
                        .data('original-icon', icon_full);
                } else if(index == rating.stars && rating.half_star == true) {
                    star.addClass(icon_half)
                        .data('original-icon', icon_half);
                } else {
                    star.addClass(icon_empty)
                        .data('original-icon', icon_empty);
                }
            });
        }, 'json');
    });
});