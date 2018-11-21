WPUltimatePostGrid = WPUltimatePostGrid || {};

WPUltimatePostGrid.initPaginationLoad_filter = function(container) {
    var grid_id = container.data('grid');
    var grid = WPUltimatePostGrid.grids[grid_id];

    grid.container.on('wpupgFiltered', function() {
        var filtered_posts = [];

        for(var taxonomy in grid.filters) {
            if(grid.filters.hasOwnProperty(taxonomy)) {
                var taxonomy_filters = grid.filters[taxonomy];

                if(taxonomy_filters) {
                    for(var i = 0; i < taxonomy_filters.length; i++) {
                        filtered_posts = filtered_posts.concat(grid.data.posts.taxonomies[taxonomy][taxonomy_filters[i]]);
                    }
                }
            }
        }

        // Load More button as well?
        var load_more_container = container.siblings('#' + container.attr('id') + '.wpupg-pagination-load_more');
        if(load_more_container) {
            if(filtered_posts.length > 0 ) {
                load_more_container.find('.wpupg-pagination-button').fadeOut();
            } else {
                var all_posts = Object.keys(grid.data.posts.all).map(function (key) {return grid.data.posts.all[key]});
                if(load_more_container && all_posts.length > grid.posts.length) {
                    load_more_container.find('.wpupg-pagination-button').fadeIn();
                }
            }
        }

        var posts_to_load = jQuery(filtered_posts).not(grid.posts).get();
        if(posts_to_load.length > 0) {
            // Get posts via AJAX
            var data = {
                action: 'wpupg_get_filter_posts',
                security: wpupg_public.nonce,
                grid: grid_id,
                posts: posts_to_load
            };

            WPUltimatePostGrid.ajaxGetFilterPosts(container, data);
        }
    });
};

WPUltimatePostGrid.updatePaginationLoad_filter = function(container, page) {
};

WPUltimatePostGrid.ajaxGetFilterPosts = function(container, data) {
    var spinner = container.find('.wpupg-pagination-load_filter');

    spinner.addClass('wpupg-spinner');

    // Get recipes through AJAX
    jQuery.post(wpupg_public.ajax_url, data, function(html) {
        var posts = jQuery(html).toArray();
        WPUltimatePostGrid.grids[data.grid].container.isotope('insert', posts);
        WPUltimatePostGrid.grids[data.grid].page = 0;
        WPUltimatePostGrid.updatePosts(data.grid);
        WPUltimatePostGrid.filterGrid(data.grid);
        WPUltimatePostGrid.checkLinks(data.grid);

        WPUltimatePostGrid.grids[data.grid].container.imagesLoaded( function() {
            WPUltimatePostGrid.grids[data.grid].container.isotope('layout');
        });

        spinner.removeClass('wpupg-spinner');
    });
};