(function() {
	var called = false;
	tinymce.create('tinymce.plugins.doc', {
		init : function(ed, url) {
			ed.addButton('document', {
				title : 'Document',
				image : url + '/images/download.png',
				cmd : 'mceDocumentInsert'
			});

			ed.addCommand('mceDocumentInsert', function(ui, v) {
				tb_show('', ajaxurl + '?action=addShortcodeDocument');
				if(called == false) {
					called = true;
					jQuery('#submit-doc').live("click", function(e) {
						e.preventDefault();

						tinyMCE.activeEditor.execCommand('mceInsertContent', 0, document_create_shortcode());

						tb_remove();
					});
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('document', tinymce.plugins.doc);
})();

function document_create_shortcode() {
	_val = jQuery('span.select-container.views select#doc_id').val();
	_type = jQuery('.type-affichage.checked').val();
	if(_val){
		return '[document  id="' + _val + '" type="' + _type + '"]';	
	}else{
		return '';
	}
	
}