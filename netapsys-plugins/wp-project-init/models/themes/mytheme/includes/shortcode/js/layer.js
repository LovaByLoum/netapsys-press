(function() {
	var called = false;
	tinymce.create('tinymce.plugins.customLayer', {
		init : function(ed, url) {
			ed.addButton('customLayer', {
				title : 'Ajouter une legende',
				image : url + '/images/layer.png',
				cmd : 'mceLayerInsert'
			});

			ed.addCommand('mceLayerInsert', function(ui, v) {
				//Render template BO
				tb_show('', ajaxurl + '?action=addShortcodelayer');

				if(called == false) {
					called = true;
					jQuery('#submit-liste').live("click", function(e) {
						e.preventDefault();
						img = tinyMCE.activeEditor.selection.getNode();
						imghtml = tinyMCE.activeEditor.selection.getNode().outerHTML;
						parent = tinyMCE.activeEditor.dom.getParent(img,'DIV');
						if (img.tagName!="IMG"){
							alert('Veuillez séléctionner une image avant d\'inserer une légende');
						}else if(parent && parent.className == "block-caption"){
							alert('L\'image possède déjà du texte en legende');
						}else{
							tinyMCE.activeEditor.execCommand('mceInsertContent', 0, document_create_shortcode_layer(imghtml));
						}
						tb_remove();
					});
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('customLayer', tinymce.plugins.customLayer);
})();

function document_create_shortcode_layer(img) {
	_html = '<div class="block-caption">';
        _html += img;
        _html += '<div class="text-caption"><div class="containercaption">';
            _html += '<p>Séléctionnez et remplacer ce texte.</p>';
        _html += '</div></div>';
    _html += '</div>';
	return _html;
}