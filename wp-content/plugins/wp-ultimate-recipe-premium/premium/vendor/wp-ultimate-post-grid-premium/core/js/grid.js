var WPUltimatePostGrid = WPUltimatePostGrid || {};

WPUltimatePostGrid.grids = {};

WPUltimatePostGrid.initGrid = function(container) {
    var grid_id = container.data('grid');

    var args = {
        itemSelector: '.wpupg-item',
        transitionDuration: wpupg_public.animationSpeed,
        hiddenStyle: {
            opacity: 0
        },
        visibleStyle: {
            opacity: 1
        }
    };

    if(wpupg_public.rtl) {
        args.isOriginLeft = false;
    }

    var layout_mode = container.data('layout-mode');
    if(layout_mode) {
        args.layoutMode = layout_mode;
    }

    if(layout_mode == 'masonry' && container.data('centered')) {
        args.masonry = {
            isFitWidth: true
        };
    }

    container.isotope(args);

    container.imagesLoaded( function() {
        container.isotope('layout');
    });

    WPUltimatePostGrid.grids[grid_id] = {
        container: container,
        centered: container.data('centered'),
        data: window['wpupg_grid_' + container.data('grid-id')],
        id: container.data('grid-id'),
        pages: [0],
        page: 0,
        filters: {}
    };

    WPUltimatePostGrid.checkLinks(grid_id);
    WPUltimatePostGrid.updatePosts(grid_id);

    jQuery(container).on('hover', '.wpupg-item', function(e) {
        var hovering = e.type == 'mouseenter';
        WPUltimatePostGrid.hoverGridItem(jQuery(this), hovering);
    });
};

WPUltimatePostGrid.hoverGridItem = function(grid_item, hovering) {
    if(hovering) {
        grid_item.addClass('wpupg-hovering');

        grid_item.find('.wpupg-show-on-hover').each(function() {
            var item = jQuery(this),
                effect = item.data('hover-in'),
                duration = parseInt(item.data('hover-in-duration'));

            if(effect == 'fade') {
                item.fadeIn(duration);
            } else if(effect == 'slide') {
                item.slideDown(duration);
            } else {
                item.show();
            }
        });

        grid_item.find('.wpupg-hide-on-hover').each(function() {
            var item = jQuery(this),
                effect = item.data('hover-out'),
                duration = parseInt(item.data('hover-out-duration'));

            if(effect == 'fade') {
                item.fadeOut(duration);
            } else if(effect == 'slide') {
                item.slideUp(duration);
            } else {
                item.hide();
            }
        });
    } else {
        grid_item.removeClass('wpupg-hovering');

        grid_item.find('.wpupg-show-on-hover').each(function() {
            var item = jQuery(this),
                effect = item.data('hover-out'),
                duration = parseInt(item.data('hover-out-duration'));

            if(effect == 'fade') {
                item.fadeOut(duration);
            } else if(effect == 'slide') {
                item.slideUp(duration);
            } else {
                item.hide();
            }
        });

        grid_item.find('.wpupg-hide-on-hover').each(function() {
            var item = jQuery(this),
                effect = item.data('hover-in'),
                duration = parseInt(item.data('hover-in-duration'));

            if(effect == 'fade') {
                item.fadeIn(duration);
            } else if(effect == 'slide') {
                item.slideDown(duration);
            } else {
                item.show();
            }
        });
    }
};

WPUltimatePostGrid.updatePosts = function(grid_id) {
    var grid = WPUltimatePostGrid.grids[grid_id];

    var posts = [];

    grid.container.find('.wpupg-item').each(function() {
        var post_id = jQuery(this).data('id');
        posts.push(post_id);
    });

    WPUltimatePostGrid.grids[grid_id].posts = posts;
};

WPUltimatePostGrid.filterGrid = function(grid_id) {
    var grid = WPUltimatePostGrid.grids[grid_id];
    var filters = [];

    // Page filter
    if(grid.pagination_type == 'pages') {
        var page = grid.page || 0;
        filters.push(['.wpupg-page-' + page]);
    }

    // Taxonomy filters
    for(var taxonomy in grid.filters) {
        if(grid.filters.hasOwnProperty(taxonomy)) {
            var taxonomy_filters = grid.filters[taxonomy];

            var match_one_filters = [];
            var match_all_filter = '';
            var filter = '';

            if(taxonomy_filters) {
                for(var i = 0; i < taxonomy_filters.length; i++) {
                    filter = '.wpupg-tax-' + taxonomy + '-' + taxonomy_filters[i];

                    if(grid.multiselect_type == 'match_one') {
                        match_one_filters.push(filter);
                    } else {
                        match_all_filter += filter;
                    }
                }
            }

            if(grid.multiselect_type == 'match_one') {
                if(match_one_filters.length > 0) filters.push(match_one_filters);
            } else {
                if(match_all_filter !== '') filters.push([match_all_filter]);
            }
        }
    }

    grid.filter = '';
    WPUltimatePostGrid.getFilterString(grid_id, '', filters, 0);
    if(grid.inverse) {
        if(grid.pagination_type == 'pages') {
            var page = grid.page || 0;
            var filter_without_page = grid.filter.replace('.wpupg-page-' + page, '');

            grid.filter = '.wpupg-page-' + page + ':not(' + filter_without_page + ')';
        } else {
            grid.filter = ':not(' + grid.filter + ')';
        }
    }

    grid.container.isotope({ filter: grid.filter });
    grid.container.trigger('wpupgFiltered');
    WPUltimatePostGrid.updateDeeplink();
};

WPUltimatePostGrid.getFilterString = function(grid_id, s, attrs, k) {
    if(k==attrs.length) {
        if(WPUltimatePostGrid.grids[grid_id].filter !== '') s = ', ' + s;
        WPUltimatePostGrid.grids[grid_id].filter += s;
    } else {
        for(var i=0; i<attrs[k].length;i++) {
            WPUltimatePostGrid.getFilterString(grid_id, s+attrs[k][i], attrs, k+1);
        }
    }
};

WPUltimatePostGrid.updateDeeplink = function() {
    var link = '';

    for(var grid_id in WPUltimatePostGrid.grids) {
        if(WPUltimatePostGrid.grids.hasOwnProperty(grid_id)) {
            var grid = WPUltimatePostGrid.grids[grid_id];
            var grid_link = '';

            // Page filter
            if(grid.page != 0) grid_link += '|p:' + (grid.page+1);

            // Taxonomy filters
            for(var taxonomy in grid.filters) {
                if(grid.filters.hasOwnProperty(taxonomy)) {
                    var taxonomy_filter = grid.filters[taxonomy];

                    if(taxonomy_filter.length > 0) {
                        grid_link += '|' + taxonomy + ':' + taxonomy_filter.join(',');
                    }
                }
            }

            if(grid_link != '') {
                if(link != '') link += '#';
                link += grid_id;
                link += grid_link;
            }
        }
    }

    if(link == '') link = '*';
    document.location.hash = link;
};

WPUltimatePostGrid.restoreDeeplink = function() {
    var link = document.location.hash;
    link = link.substr(1);

    if(link && link != '*') {
        // Make sure characters are not URL encoded
        link = link.replace('%23', '#');
        link = link.replace('%7C', '|');
        link = link.replace('%3A', ':');
        link = link.replace('%2C', ',');

        var grids = link.split('#');

        for(var i=0; i < grids.length; i++) {
            var parts = grids[i].split('|');
            var grid_id = parts[0];

            for(var j=1; j < parts.length; j++) {
                var filter = parts[j].split(':');

                if(filter[0] == 'p') {
                    var page = parseInt(filter[1]) - 1;
                    WPUltimatePostGrid.updatePagination(jQuery('#wpupg-grid-' + grid_id + '-pagination'), page);
                } else {
                    var terms = filter[1].split(',');
                    WPUltimatePostGrid.updateFilter(jQuery('#wpupg-grid-' + grid_id + '-filter'), filter[0], terms);
                }
            }
        }
    }
};

WPUltimatePostGrid.checkLinks = function(grid_id) {
    var grid = WPUltimatePostGrid.grids[grid_id];
    var link_type = grid.container.data('link-type');
    var link_target = grid.container.data('link-target');

    if(link_type != 'none') {
        grid.container.find('.wpupg-item').each(function() {
            var item = jQuery(this);

            // Remove links from item
            item.find('a:not(.wpupg-keep-link, .wpurp-recipe-favorite, .wpurp-recipe-add-to-shopping-list, .wpurp-recipe-add-to-meal-plan, .wpurp-recipe-print-button, .wpurp-recipe-grid-link)').each(function() {
                var link = jQuery(this);
                if(link.parents('.sharrre').length == 0) {
                    link.replaceWith(function() { return link.contents(); });
                }
            });

            var link = item.data('custom-link') ? item.data('custom-link') : ( link_target == 'post' ? item.data('permalink') : item.data('image') );
            var rel = link_target == 'post' ? '' : ' rel="lightbox"';

            // Add link around item
            if (link && item.parent('a').length == 0) {
                item.wrap('<a href="' + link + '" target="' + link_type + '"' + rel + '></a>');
            }
        });
    }
};

WPUltimatePostGrid.updateFilter = function(container, taxonomy, terms) {
    var type = container.data('type');
    var func = 'updateFilter' + type.charAt(0).toUpperCase() + type.slice(1);
    if(typeof WPUltimatePostGrid[func] == 'function') {
        WPUltimatePostGrid[func](container, taxonomy, terms);
    }
};

WPUltimatePostGrid.updateFilterIsotope = function(container, taxonomy, terms) {
    for(var i = 0; i < terms.length; i++) {
        container.find('.wpupg-filter-tag-' + terms[i]).click();
    }
};

WPUltimatePostGrid.initFilterIsotope = function(container) {
    var grid_id = container.data('grid');

    WPUltimatePostGrid.grids[grid_id].multiselect = container.data('multiselect');
    WPUltimatePostGrid.grids[grid_id].multiselect_type = container.data('multiselect-type');
    WPUltimatePostGrid.grids[grid_id].inverse = container.data('inverse');

    container.find('.wpupg-filter-isotope-term').click(function() {
        var filter_item = jQuery(this);
        var taxonomy = filter_item.data('taxonomy');
        var filter_all = filter_item.data('filter') == undefined;
        var filter_term = filter_item.data('filter');

        var filters = [];

        if(WPUltimatePostGrid.grids[grid_id].multiselect && !filter_all) {
            // Make sure "All" is not selected
            filter_item.siblings('.wpupg-filter-tag-').removeClass('active').trigger('checkActiveFilter');

            filters = WPUltimatePostGrid.grids[grid_id].filters || [];

            if(filters[taxonomy] == undefined) filters[taxonomy] = [];

            var term_index = filters[taxonomy].indexOf(filter_term);
            if(term_index == -1) {
                // Term was not select before, add
                filter_item.addClass('active').trigger('checkActiveFilter');
                filters[taxonomy].push(filter_term);
            } else {
                // Term was already selected, remove
                filter_item.removeClass('active').trigger('checkActiveFilter');
                filters[taxonomy].splice(term_index, 1);

                // Set "All" active if no terms left
                var nbr_terms = 0;

                for(var filter_taxonomy in filters) {
                    if(filters.hasOwnProperty(filter_taxonomy)) {
                        var filter_taxonomy_terms = filters[filter_taxonomy];

                        if(filter_taxonomy_terms) {
                            nbr_terms += filter_taxonomy_terms.length;
                        }
                    }
                }

                if(nbr_terms == 0) {
                    filter_item.siblings('.wpupg-filter-tag-').addClass('active').trigger('checkActiveFilter');
                }
            }
        } else {
            filter_item.siblings('.wpupg-filter-isotope-term').removeClass('active').trigger('checkActiveFilter');
            filter_item.addClass('active').trigger('checkActiveFilter');

                if(!filter_all) {
                    filters[taxonomy] = [filter_term];
                }
        }

        WPUltimatePostGrid.grids[grid_id].filters = filters;
        WPUltimatePostGrid.filterGrid(grid_id);
    });

    var filter_type = container.data('filter-type');
    var filter_items = container.find('.wpupg-filter-item');

    if(filter_type == 'isotope') {
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

        filter_items.each(function() {
            var filter_item = jQuery(this);

            filter_item
                .css('margin', margin)
                .css('padding', padding)
                .css('border', border)
                .css('background-color', background_color)
                .css('color', text_color)
                .hover(function() {
                    if(!filter_item.hasClass('active')) {
                        filter_item
                            .css('border', hover_border)
                            .css('background-color', hover_background_color)
                            .css('color', hover_text_color);
                    }
                }, function() {
                    if(!filter_item.hasClass('active')) {
                        filter_item
                            .css('border', border)
                            .css('background-color', background_color)
                            .css('color', text_color);
                    }
                })
                .on('checkActiveFilter', function() {
                    if(filter_item.hasClass('active')) {
                        filter_item
                            .css('border', active_border)
                            .css('background-color', active_background_color)
                            .css('color', active_text_color);
                    } else {
                        filter_item
                            .css('border', border)
                            .css('background-color', background_color)
                            .css('color', text_color);
                    }
                }).trigger('checkActiveFilter');
        });
    }
};

WPUltimatePostGrid.updatePagination = function(container, page) {
    var type = container.data('type');

    if(type) {
        var func = 'updatePagination' + type.charAt(0).toUpperCase() + type.slice(1);
        if(typeof WPUltimatePostGrid[func] == 'function') {
            WPUltimatePostGrid[func](container, page);
        }
    }
};

WPUltimatePostGrid.updatePaginationPages = function(container, page) {
    container.find('.wpupg-page-' + page).click();
};

WPUltimatePostGrid.initPaginationPages = function(container) {
    var grid_id = container.data('grid');

    container.find('.wpupg-pagination-term').click(function() {
        var pagination_item = jQuery(this);
        pagination_item.siblings('.wpupg-pagination-term').removeClass('active').trigger('checkActiveFilter');

        var page = parseInt(pagination_item.addClass('active').trigger('checkActiveFilter').data('page'));

        WPUltimatePostGrid.scrollTo(WPUltimatePostGrid.grids[grid_id].container);

        if(WPUltimatePostGrid.grids[grid_id].pages.indexOf(page) !== -1) {
            WPUltimatePostGrid.grids[grid_id].page = page;
            WPUltimatePostGrid.filterGrid(grid_id);
        } else {
            // Get new page via AJAX
            var data = {
                action: 'wpupg_get_page',
                security: wpupg_public.nonce,
                grid: grid_id,
                page: page
            };

            pagination_item.addClass('wpupg-spinner');
            pagination_item.css('color', WPUltimatePostGrid.grids[grid_id].pagination_style.active_background_color);

            // Get recipes through AJAX
            jQuery.post(wpupg_public.ajax_url, data, function(html) {
                var posts = jQuery(html).toArray();
                WPUltimatePostGrid.grids[grid_id].container.isotope('insert', posts);
                WPUltimatePostGrid.grids[grid_id].page = page;
                WPUltimatePostGrid.filterGrid(grid_id);
                WPUltimatePostGrid.checkLinks(grid_id);

                WPUltimatePostGrid.grids[grid_id].container.imagesLoaded( function() {
                    WPUltimatePostGrid.grids[grid_id].container.isotope('layout');
                });

                pagination_item.removeClass('wpupg-spinner');
                pagination_item.css('color', WPUltimatePostGrid.grids[grid_id].pagination_style.active_text_color);

                WPUltimatePostGrid.grids[grid_id].pages.push(page);
                WPUltimatePostGrid.updatePosts(grid_id);
            });
        }
    });

    var pagination_items = container.find('.wpupg-pagination-term');

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

    pagination_items.each(function() {
        var pagination_item = jQuery(this);

        pagination_item
            .css('margin', margin)
            .css('padding', padding)
            .css('border', border)
            .css('background-color', background_color)
            .css('color', text_color)
            .hover(function() {
                if(!pagination_item.hasClass('active')) {
                    pagination_item
                        .css('border', hover_border)
                        .css('background-color', hover_background_color)
                        .css('color', hover_text_color);
                }
            }, function() {
                if(!pagination_item.hasClass('active')) {
                    pagination_item
                        .css('border', border)
                        .css('background-color', background_color)
                        .css('color', text_color);
                }
            })
            .on('checkActiveFilter', function() {
                if(pagination_item.hasClass('active')) {
                    pagination_item
                        .css('border', active_border)
                        .css('background-color', active_background_color)
                        .css('color', active_text_color);
                } else {
                    pagination_item
                        .css('border', border)
                        .css('background-color', background_color)
                        .css('color', text_color);
                }
            }).trigger('checkActiveFilter');
    });
};

WPUltimatePostGrid.scrollTo = function(target) {
    var scroll_to = target.offset().top - 50;
    scroll_to = scroll_to < 0 ? 0 : scroll_to;

    jQuery('html,body').animate({
        scrollTop: scroll_to
    }, 500);
};

jQuery(document).ready(function($) {
    $('.wpupg-grid').each(function() {
        WPUltimatePostGrid.initGrid($(this));
    });

    $('.wpupg-filter').each(function() {
        var container = $(this);
        var grid_id = container.data('grid');
        var type = container.data('type');

        WPUltimatePostGrid.grids[grid_id].filter_type = type;

        var func = 'initFilter' + type.charAt(0).toUpperCase() + type.slice(1);
        if(typeof WPUltimatePostGrid[func] == 'function') {
            WPUltimatePostGrid[func](container);
        }
    });

    $('.wpupg-pagination').each(function() {
        var container = $(this);
        var grid_id = container.data('grid');
        var type = container.data('type');

        WPUltimatePostGrid.grids[grid_id].pagination_type = type;

        var func = 'initPagination' + type.charAt(0).toUpperCase() + type.slice(1);
        if(typeof WPUltimatePostGrid[func] == 'function') {
            WPUltimatePostGrid[func](container);
        }
    });
    WPUltimatePostGrid.restoreDeeplink();
});