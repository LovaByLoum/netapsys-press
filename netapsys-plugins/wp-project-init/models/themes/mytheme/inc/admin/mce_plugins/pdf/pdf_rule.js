var media_pdfDialog = {
    init: function(ed) {
        var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
    },
    update: function() {
        //text = tinyMCE.activeEditor.selection.getNode();
        var ed = tinyMCEPopup.editor, h, f = document.forms[0], st = '';
        var media_pdf = f.media_pdf.value;
        var url_pdf = f.url_pdf.value;
        var titre_pdf = f.titre_pdf.value;
        if (url_pdf != '') {
            h = '<a class="btn downBtn '+media_pdf+'" href="'+url_pdf+'" title="'+titre_pdf+'" download>'+titre_pdf+'</a>';
            ed.execCommand("mceInsertContent", false, h);
        }

        tinyMCEPopup.close();
    }
};
function setFormValue(name, value) {
    document.forms[0].elements[name].value = value;
}
//tinyMCEPopup.requireLangPack();
//tinyMCEPopup.onInit.add(media_pdfDialog.init, media_pdfDialog);
