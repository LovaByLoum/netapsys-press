(function() {
    tinymce.create('tinymce.plugins.media_pdf', {
        init: function(ed, url) {
            // Register commands
            ed.addCommand('wp_cmd_media_pdf', function() {
                ed.windowManager.open({
                    file: url + '/pdf.php',
                    width: 550,
                    height: 590,
                    inline: 1
                }, {
                    plugin_url: url
                });
            });

            ed.addButton('media_pdf', {
                title: 'Lien vers un PDF',
                image: url + '/pdf.png',
                cmd: 'wp_cmd_media_pdf'
            });
        },
        createControl: function(n, cm) {
            return null;
        },
        getInfo: function() {
            return {
                longname: "Gestionnaire des PDF",
                author: '__WPI__THEME__AUTHOR__',
                version: "0.1"
            };
        }
    });
    tinymce.PluginManager.add('media_pdf', tinymce.plugins.media_pdf);
})();