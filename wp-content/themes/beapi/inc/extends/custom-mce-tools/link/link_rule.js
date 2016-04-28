var media_linkDialog = {
    init: function(ed) {
        var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
    },
    update: function() {
        //text = tinyMCE.activeEditor.selection.getNode();
        var ed = tinyMCEPopup.editor, h, f = document.forms[0], st = '';
        var media_link = f.media_link.value;
        var url_link = f.url_link.value;
        var titre_link = f.titre_link.value;
        var target_link = '';
        if(f.target_link.checked) {
            target_link = f.target_link.value;
        }
        if (url_link != '') {
            if(titre_link == '') {
                titre_link = 'Lire le rapport complet';
            }
            h = '<a class="btn '+media_link+'" target="'+target_link+'" href="'+url_link+'" title="'+titre_link+'">'+titre_link+'</a>';
            ed.execCommand("mceInsertContent", false, h);
        }

        tinyMCEPopup.close();
    }
};
function setFormValue(name, value) {
    document.forms[0].elements[name].value = value;
}
//tinyMCEPopup.requireLangPack();
//tinyMCEPopup.onInit.add(media_linkDialog.init, media_linkDialog);
