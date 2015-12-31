(function() {
    tinymce.create('tinymce.plugins.media_link', {
        init: function(ed, url) {
            // Register commands
            ed.addCommand('wp_cmd_media_link', function() {
                ed.windowManager.open({
                    file: url + '/link.php',
                    width: 550,
                    height: 560,
                    inline: 1
                }, {
                    plugin_url: url
                });
            });

            ed.addButton('media_link', {
                title: 'Lien vers une page',
                image: url + '/link.png',
                cmd: 'wp_cmd_media_link'
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
    tinymce.PluginManager.add('media_link', tinymce.plugins.media_link);
})();