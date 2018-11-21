import '../../../../vendor/select2/js/select2.min.js';
import '../../../../vendor/select2/css/select2.min.css';
// import 'datatables.net';
// import 'datatables.net-dt/css/jquery.dataTables.css';
import 'tooltipster';
import 'tooltipster/dist/css/tooltipster.bundle.css';

// Source: http://stackoverflow.com/questions/647259/javascript-query-string
function get_query_args() {
    var result = {}, queryString = location.search.slice(1),
        re = /([^&=]+)=([^&]*)/g, m;

    while (m = re.exec(queryString)) {
        result[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }

    return result;
}

function initDatatableFilters(datable) {
    var args = get_query_args();
    jQuery('.wprm-manage-recipes-filter').on('change', function() {
        datable.search('').draw();
    }).each(function() {
        var taxonomy = jQuery(this).data('taxonomy');

        if(args.hasOwnProperty(taxonomy) && jQuery(this).find("option[value='" + args[taxonomy] + "']").length > 0) {
            jQuery(this).val(args[taxonomy]);
        }
    }).select2_wprm().trigger('change');
}

export default function initDatatable() {
    // Get ID of datatable that's active on this page
    var wprm_active_datatable = '';
    jQuery('.wprm-manage-datatable').each(function() {
        wprm_active_datatable = jQuery(this).attr('id');
    });

    // Init datatable
    $.fn.dataTable.ext.errMode = 'throw';

    var datatable = jQuery('.wprm-manage-datatable').DataTable( {
        pageLength: 10,
        order: [ 0, 'desc' ],
        serverSide: true,
        ajax: {
            url: wprm_admin.ajax_url,
            type: 'POST',
            data: function ( d ) {
                d.action = 'wprm_manage_datatable';
                d.security = wprm_admin.nonce;
                d.table = wprm_active_datatable;

                // Check for advanced search filters.
                var search_filters = jQuery('.wprm-manage-recipes-filter');

                if(search_filters.length > 0) {
                    var search = jQuery('#wprm-manage-recipes_wrapper').find('input[type="search"]').val();

                    search_filters.each(function() {
                        var taxonomy = jQuery(this).data('taxonomy');
                        var value = parseInt(jQuery(this).val());

                        if(value > 0) {
                            search += '{{' + taxonomy + '=' + value +'}}';
                        }
                    });

                    d.search.value = search;
                }
            }
        },
        drawCallback: function() {
            // Select2.
            jQuery('.wprm-manage-datatable').find('select').select2_wprm();

            // Add tooltips.
            jQuery('.wprm-manage-ingredients-actions').tooltipster({
                content: '<div class="wprm-manage-ingredients-actions-tooltip">' + wprm_admin.manage.tooltip.ingredients + '</div>',
                contentAsHTML: true,
                functionBefore: function() {
                    var instances = jQuery.tooltipster.instances();
                    jQuery.each(instances, function(i, instance){
                        instance.close();
                    });
                },
                functionReady: function(instance, helper) {
                    var id = parseInt(jQuery(helper.origin).data('id')),
                        count = parseInt(jQuery(helper.origin).data('count')),
                        name = jQuery('#wprm-manage-ingredients-name-' + id).text();

                    jQuery(helper.tooltip).find('a').data('id', id);
                    jQuery(helper.tooltip).find('.tooltip-header').text('#' + id + ' - ' + name);

                    if(count > 0) {
                        jQuery(helper.tooltip)
                            .find('.wprm-manage-ingredients-actions-delete')
                            .remove();
                    }

                    // Adjust tooltip height to fit contents.
                    var inner = jQuery(helper.tooltip).find('.tooltipster-content > div');
                    jQuery(helper.tooltip).height(inner.outerHeight(true) + 15);
                },
                interactive: true,
                delay: 0,
                side: 'left',
                trigger: 'custom',
                triggerOpen: {
                    mouseenter: true,
                    touchstart: true
                },
                triggerClose: {
                    click: true,
                    tap: true
                },
            });

            jQuery('.wprm-manage-taxonomies-actions').tooltipster({
                content: '<div class="wprm-manage-taxonomies-actions-tooltip">' + wprm_admin.manage.tooltip.taxonomies + '</div>',
                contentAsHTML: true,
                functionBefore: function() {
                    var instances = jQuery.tooltipster.instances();
                    jQuery.each(instances, function(i, instance){
                        instance.close();
                    });
                },
                functionReady: function(instance, helper) {
                    var id = parseInt(jQuery(helper.origin).data('id')),
                        count = parseInt(jQuery(helper.origin).data('count')),
                        name = jQuery('#wprm-manage-taxonomies-name-' + id).text();

                    jQuery(helper.tooltip).find('a').data('id', id);
                    jQuery(helper.tooltip).find('.tooltip-header').text('#' + id + ' - ' + name);

                    // Adjust tooltip height to fit contents.
                    var inner = jQuery(helper.tooltip).find('.tooltipster-content > div');
                    jQuery(helper.tooltip).height(inner.outerHeight(true) + 15);
                },
                interactive: true,
                delay: 0,
                side: 'left',
                trigger: 'custom',
                triggerOpen: {
                    mouseenter: true,
                    touchstart: true
                },
                triggerClose: {
                    click: true,
                    tap: true
                },
            });

            jQuery('.wprm-manage-recipes-actions').tooltipster({
                content: '<div class="wprm-manage-recipes-actions-tooltip">' + wprm_admin.manage.tooltip.recipes + '</div>',
                contentAsHTML: true,
                functionBefore: function() {
                    var instances = jQuery.tooltipster.instances();
                    jQuery.each(instances, function(i, instance){
                        instance.close();
                    });
                },
                functionReady: function(instance, helper) {
                    var id = parseInt(jQuery(helper.origin).data('id')),
                        name = jQuery('#wprm-manage-recipes-name-' + id).text();

                    jQuery(helper.tooltip).find('.tooltip-header').text('#' + id + ' - ' + name);
                    jQuery(helper.tooltip).find('a').data('id', id);

                    // Adjust tooltip height to fit contents.
                    var inner = jQuery(helper.tooltip).find('.tooltipster-content > div');
                    jQuery(helper.tooltip).height(inner.outerHeight(true) + 15);
                },
                interactive: true,
                delay: 0,
                side: 'left',
                trigger: 'custom',
                triggerOpen: {
                    mouseenter: true,
                    touchstart: true
                },
                triggerClose: {
                    click: true,
                    tap: true
                },
            });
            
            jQuery('.wprm-manage-tooltip').tooltipster({
                delay: 0,
                side: 'left',
            });
        }
    } );

    initDatatableFilters(datatable);
}