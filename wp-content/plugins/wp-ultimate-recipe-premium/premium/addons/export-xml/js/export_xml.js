var ExportXML = ExportXML || {};

ExportXML.loader = '<div class="wpurp-loader"><div></div><div></div><div></div></div>';

ExportXML.selectAllRecipes = function() {
    jQuery('.xml-recipe').each(function() {
        jQuery(this).attr('checked', true);
    });
};

ExportXML.deselectAllRecipes = function() {
    jQuery('.xml-recipe').each(function() {
        jQuery(this).attr('checked', false);
    });
};

ExportXML.selectDate = function() {
    var date_from = jQuery('#wpurp_export_date_from').val();
    var date_to = jQuery('#wpurp_export_date_to').val();

    if(!date_from && !date_to) {
        return ['all'];
    } else {
        var loader = jQuery(ExportXML.loader);
        jQuery('.wpurp_export_select_date').append(loader);

        var data = {
            action: 'export_xml_date',
            security: wpurp_export_xml.nonce,
            date_from: date_from,
            date_to: date_to
        };

        return jQuery.post(wpurp_export_xml.ajaxurl, data, function(posts) {
            loader.remove();
            console.log(posts);
            return posts;
        }, 'json');
    }
};

ExportXML.selectAuthor = function() {
    var author = parseInt( jQuery('#wpurp_export_author').find(':selected').val() );

    if(author == 0) {
        return ['all'];
    } else {
        var loader = jQuery(ExportXML.loader);
        jQuery('.wpurp_export_select_author').append(loader);

        var data = {
            action: 'export_xml_author',
            security: wpurp_export_xml.nonce,
            author: author
        };

        return jQuery.post(wpurp_export_xml.ajaxurl, data, function(posts) {
            loader.remove();
            return posts;
        }, 'json');
    }
};

ExportXML.updateSelection = function() {
    ExportXML.deselectAllRecipes();

    jQuery.when(ExportXML.selectAuthor(), ExportXML.selectDate()).done(function(author_posts, date_posts) {
        author_posts = author_posts[0];
        date_posts = date_posts[0];

        var posts = 'all';
        if(author_posts != 'all' && date_posts != 'all') {
            posts = jQuery(author_posts).filter(date_posts);
        } else if(author_posts != 'all') {
            posts = author_posts;
        } else {
            posts = date_posts;
        }

        console.log(author_posts);
        console.log(date_posts);
        console.log(posts);

        if(posts == 'all') {
            ExportXML.selectAllRecipes();
        } else {
            jQuery('.xml-recipe').each(function() {
                var recipe = jQuery(this),
                    id = parseInt( recipe.val() );

                if(jQuery.inArray(id, posts) != -1) {
                    jQuery(this).attr('checked', true);
                }
            });
        }
    });
};

jQuery(document).ready(function($) {
    // Datepicker
    jQuery('.wpurp_date').datepicker();

    // Events
    jQuery('#wpurp_export_date_from').on('change', function() {
        ExportXML.updateSelection();
    });
    jQuery('#wpurp_export_date_to').on('change', function() {
        ExportXML.updateSelection();
    });

    jQuery('#wpurp_export_author').on('change', function() {
        ExportXML.updateSelection();
    });
});