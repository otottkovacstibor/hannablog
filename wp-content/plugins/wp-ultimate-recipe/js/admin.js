// Getting Started Lightbox
jQuery(window).load(function() {
    if(jQuery('#wpurp-getting-started-lightbox').length > 0) {
        tb_show('WP Ultimate Recipe','#TB_inline?height=630&amp;width=600&amp;inlineId=wpurp-getting-started-lightbox&amp;modal=true',null);

        jQuery(document).on('click', '#wpurp-getting-started-submit', function() {
            var url = jQuery(this).data('url');
            setTimeout(function(){
                window.location.href = url;
            }, 1000);
        });
    }
});