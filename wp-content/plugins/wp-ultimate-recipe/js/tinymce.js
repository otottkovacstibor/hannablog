(function() {
	tinymce.PluginManager.add('wpultimaterecipe', function(editor, url) {
		function replaceShortcodes(content) {
			return content.replace(/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/g, function(match) {
				return '';
			});
		}

		editor.on('BeforeSetContent', function(event) {
			event.content = replaceShortcodes(event.content);
		});
	});
})();
