WPRecipeMakerYoast = function() {
    YoastSEO.app.registerPlugin( 'wprecipemaker', {status: 'ready'} );
    YoastSEO.app.registerModification( 'content', this.wprmContentModification, 'wprecipemaker', 5 );
}

WPRecipeMakerYoast.prototype.wprmContentModification = function( data ) {
    data = data.replace(/(<p>)?\s*\[[^\[]*wprm-recipe[^\]]*\]\s*(<\/p>)?/ig, '');
    return data;
};

jQuery(window).on('YoastSEO:ready', function () {
    new WPRecipeMakerYoast();
});