var hr_customDialog = {
    init: function(ed) {
        var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
    },
    update: function() {
        //text = tinyMCE.activeEditor.selection.getNode();
        var ed = tinyMCEPopup.editor, h, f = document.forms[0], st = '';
        var hr_class = $('.mceFocus:checked').val();
        var x = 0;
        if (hr_class != '') {
            h = '<hr class="'+hr_class+'"/>';
            ed.execCommand("mceInsertContent", false, h);
        }

        tinyMCEPopup.close();
    }
};
function setFormValue(name, value) {
    document.forms[0].elements[name].value = value;
}
//tinyMCEPopup.requireLangPack();
//tinyMCEPopup.onInit.add(hr_customDialog.init, hr_customDialog);
