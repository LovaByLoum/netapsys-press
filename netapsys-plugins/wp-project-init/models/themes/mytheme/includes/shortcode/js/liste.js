(function() {
	var called = false;
	tinymce.create('tinymce.plugins.customListe', {
		init : function(ed, url) {
			ed.addButton('customListe', {
				title : 'Liste',
				image : url + '/images/liste.png',
				cmd : 'mceListeInsert'
			});

			ed.addCommand('mceListeInsert', function(ui, v) {
				//Render template BO
				tb_show('', ajaxurl + '?action=addShortcodeListe');

				if(called == false) {
					called = true;
					jQuery('#submit-liste').live("click", function(e) {
						e.preventDefault();

						tinyMCE.activeEditor.execCommand('mceInsertContent', 0, document_create_shortcode_liste());

						tb_remove();
					});
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('customListe', tinymce.plugins.customListe);
})();

function document_create_shortcode_liste() {
	var nbr_ligne = jQuery('#nombre-ligne').val();
	var _html = '<ul class="fenetre-publication list-height">';
	for(i=0; i<nbr_ligne; i++){
		if(i%2 == 0){
			_html += '<li class="odd"><span>Entrer votre texte ici</span></li>';
		}else{
			_html += '<li><span>Entrer votre texte ici</span></li>';
		}
	}
	_html += '</ul>';

	return _html;
}