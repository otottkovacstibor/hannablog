WPUltimatePostGrid = WPUltimatePostGrid || {};

WPUltimatePostGrid.initPaginationInfinite_load = function(container) {
    var grid_id = container.data('grid');
    var grid = WPUltimatePostGrid.grids[grid_id];

    jQuery(window).scroll(function(){
        WPUltimatePostGrid.checkScroll(grid_id);
    });
    grid.container.on('arrangeComplete', function() {
        WPUltimatePostGrid.checkScroll(grid_id);
    });
};

WPUltimatePostGrid.checkScroll = function(grid_id) {
    var grid = WPUltimatePostGrid.grids[grid_id];

    if(!grid.loading && !grid.loaded) {
        var last_elem = grid.container.find('.wpupg-item:visible:last');

        var load_more = true;
        if(last_elem.length > 0) {
            var docViewTop = jQuery(window).scrollTop();
            var docViewBottom = docViewTop + jQuery(window).height();

            var elemTop = last_elem.offset().top;
            load_more = (elemTop <= docViewBottom) && (elemTop >= docViewTop);
        }

        if(load_more) {
            grid.loading = true;

            var page = grid.page + 1;
            var all_posts = Object.keys(grid.data.posts.all).map(function (key) {return grid.data.posts.all[key]});
            var posts_to_load = jQuery(all_posts).not(grid.posts).get();

            // Check if Filtered
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

            // Only load from the filtered posts when filtered
            if(filtered_posts.length > 0) {
                posts_to_load = jQuery(filtered_posts).not(grid.posts).get();
            }

            // Get new posts via AJAX
            var data = {
                action: 'wpupg_get_more_posts',
                security: wpupg_public.nonce,
                grid: grid_id,
                posts: posts_to_load.length == 0 ? [-1] : posts_to_load, // Make sure an array gets passed on
                page: page
            };

            WPUltimatePostGrid.ajaxGetInfinitePosts(grid.container, data);
        }
    }
}

WPUltimatePostGrid.updatePaginationInfinite_load = function(container, page) {
    // Get all posts via AJAX
    var data = {
        action: 'wpupg_get_more_posts',
        security: wpupg_public.nonce,
        grid: container.data('grid'),
        page: page,
        all: true
    };

    WPUltimatePostGrid.ajaxGetInfinitePosts(container, data);
};

WPUltimatePostGrid.ajaxGetInfinitePosts = function(container, data) {
    var spinner = container.find('.wpupg-pagination-infinite_load');
    spinner.addClass('wpupg-spinner');

    var grid = WPUltimatePostGrid.grids[data.grid];

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
            grid.loading = false;
        });

        var all_posts = Object.keys(grid.data.posts.all).map(function (key) {return grid.data.posts.all[key]});
        if(all_posts.length == grid.posts.length) {
            grid.loaded = true;
        }

        spinner.removeClass('wpupg-spinner');
    });
};