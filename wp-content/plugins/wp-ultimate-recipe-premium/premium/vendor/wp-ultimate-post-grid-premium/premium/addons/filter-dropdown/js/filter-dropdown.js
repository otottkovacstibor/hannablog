WPUltimatePostGrid = WPUltimatePostGrid || {};

WPUltimatePostGrid.initFilterDropdown = function(container) {
    var grid_id = container.data('grid');
    var dropdowns = container.find('.wpupg-filter-dropdown-item');

    WPUltimatePostGrid.grids[grid_id].multiselect_type = container.data('multiselect-type');
    WPUltimatePostGrid.grids[grid_id].inverse = container.data('inverse');

    var dropdown_args = {
        allowClear: true
    };

    if(wpupg_public.dropdown_hide_search) {
        dropdown_args.minimumResultsForSearch = Infinity;
    }
    
    dropdowns.each(function() {
        var taxonomy = jQuery(this).data('taxonomy');

        jQuery(this).select2wpupg(dropdown_args).on('change', function() {
            var terms = jQuery(this).val();
            if(terms) {
                if(!jQuery.isArray(terms)) terms = [terms];

                WPUltimatePostGrid.grids[grid_id].filters[taxonomy] = terms;
            } else {
                WPUltimatePostGrid.grids[grid_id].filters[taxonomy] = [];
            }

            WPUltimatePostGrid.filterGrid(grid_id);
        });
    });
};

WPUltimatePostGrid.updateFilterDropdown = function(container, taxonomy, terms) {
    container.find('#wpupg-filter-dropdown-' + taxonomy).select2wpupg('val', terms);
};