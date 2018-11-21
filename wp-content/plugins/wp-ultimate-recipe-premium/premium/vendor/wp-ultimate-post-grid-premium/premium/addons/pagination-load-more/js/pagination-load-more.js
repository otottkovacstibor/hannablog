WPUltimatePostGrid = WPUltimatePostGrid || {};

WPUltimatePostGrid.initPaginationLoad_more = function(container) {
    var grid_id = container.data('grid');
    var grid = WPUltimatePostGrid.grids[grid_id];
    var button = container.find('.wpupg-pagination-button').first();

    button.click(function() {
        var page = WPUltimatePostGrid.grids[grid_id].page + 1;
        var all_posts = Object.keys(grid.data.posts.all).map(function (key) {return grid.data.posts.all[key]});
        var posts_to_load = jQuery(all_posts).not(grid.posts).get();

        // Get new posts via AJAX
        var data = {
            action: 'wpupg_get_more_posts',
            security: wpupg_public.nonce,
            grid: grid_id,
            posts: posts_to_load.length == 0 ? [-1] : posts_to_load, // Make sure an array gets passed on
            page: page
        };

        WPUltimatePostGrid.ajaxGetMorePosts(container, data);
    });

    var margin = container.data('margin-vertical') + 'px ' + container.data('margin-horizontal') + 'px';
    var padding = container.data('padding-vertical') + 'px ' + container.data('padding-horizontal') + 'px';
    var border = container.data('border-width') + 'px solid ' + container.data('border-color');
    var background_color = container.data('background-color');
    var text_color = container.data('text-color');

    var active_border = container.data('border-width') + 'px solid ' + container.data('active-border-color');
    var active_background_color = container.data('active-background-color');
    var active_text_color = container.data('active-text-color');

    var hover_border = container.data('border-width') + 'px solid ' + container.data('hover-border-color');
    var hover_background_color = container.data('hover-background-color');
    var hover_text_color = container.data('hover-text-color');

    WPUltimatePostGrid.grids[grid_id].pagination_style = {
        margin: margin,
        padding: padding,
        border: border,
        background_color: background_color,
        text_color: text_color,
        active_border: active_border,
        active_background_color: active_background_color,
        active_text_color: active_text_color,
        hover_border: hover_border,
        hover_background_color: hover_background_color,
        hover_text_color: hover_text_color
    }

    button
        .css('margin', margin)
        .css('padding', padding)
        .css('border', border)
        .css('background-color', background_color)
        .css('color', text_color)
        .hover(function() {
            if(!button.hasClass('active')) {
                button
                    .css('border', hover_border)
                    .css('background-color', hover_background_color)
                    .css('color', hover_text_color);
            }
        }, function() {
            if(!button.hasClass('active')) {
                button
                    .css('border', border)
                    .css('background-color', background_color)
                    .css('color', text_color);
            }
        })
        .on('checkActiveFilter', function() {
            if(button.hasClass('active')) {
                button
                    .css('border', active_border)
                    .css('background-color', active_background_color)
                    .css('color', active_text_color);
            } else {
                button
                    .css('border', border)
                    .css('background-color', background_color)
                    .css('color', text_color);
            }
        }).trigger('checkActiveFilter');
};

WPUltimatePostGrid.updatePaginationLoad_more = function(container, page) {
    // Get all posts via AJAX
    var data = {
        action: 'wpupg_get_more_posts',
        security: wpupg_public.nonce,
        grid: container.data('grid'),
        page: page,
        all: true
    };

    WPUltimatePostGrid.ajaxGetMorePosts(container, data);
};

WPUltimatePostGrid.ajaxGetMorePosts = function(container, data) {
    var button = container.find('.wpupg-pagination-button');
    var grid = WPUltimatePostGrid.grids[data.grid];

    button.addClass('wpupg-spinner').addClass('active').trigger('checkActiveFilter');
    button.css('color', WPUltimatePostGrid.grids[data.grid].pagination_style.active_background_color);

    // Get recipes through AJAX
    jQuery.post(wpupg_public.ajax_url, data, function(html) {
        var posts = jQuery(html).toArray();
        WPUltimatePostGrid.grids[data.grid].container.isotope('insert', posts);
        WPUltimatePostGrid.grids[data.grid].page = data.page;
        WPUltimatePostGrid.filterGrid(data.grid);
        WPUltimatePostGrid.checkLinks(data.grid);
        WPUltimatePostGrid.updatePosts(data.grid);

        WPUltimatePostGrid.grids[data.grid].container.imagesLoaded( function() {
            WPUltimatePostGrid.grids[data.grid].container.isotope('layout');
        });

        var all_posts = Object.keys(grid.data.posts.all).map(function (key) {return grid.data.posts.all[key]});
        if(all_posts.length == grid.posts.length) {
            button.fadeOut();
        } else {
            button.removeClass('wpupg-spinner').removeClass('active').trigger('checkActiveFilter');
            button.css('color', WPUltimatePostGrid.grids[data.grid].pagination_style.active_text_color);
        }
    });
};