var videoDialog = {
    init: function(ed) {
        var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
    },
    update: function() {
        //text = tinyMCE.activeEditor.selection.getNode();
        var ed = tinyMCEPopup.editor, h, f = document.forms[0], st = '';
        var url_form = f.url_video.value;
        var x = 0;
        if (url_form != '') {
            h = '[video src="'+url_form+'"]';
            ed.execCommand("mceInsertContent", false, h);
        }

        tinyMCEPopup.close();
    }
};
function setFormValue(name, value) {
    document.forms[0].elements[name].value = value;
}
//tinyMCEPopup.requireLangPack();
//tinyMCEPopup.onInit.add(videoDialog.init, videoDialog);
