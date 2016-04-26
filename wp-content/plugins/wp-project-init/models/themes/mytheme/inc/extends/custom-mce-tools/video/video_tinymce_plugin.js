(function() {
    tinymce.create('tinymce.plugins.video', {
        init: function(ed, url) {
            // Register commands
            ed.addCommand('wp_cmd_video', function() {
                ed.windowManager.open({
                    file: url + '/video.php',
                    width: 400,
                    height: 130,
                    inline: 1
                }, {
                    plugin_url: url
                });
            });

            ed.addButton('video', {
                title: 'Video Youtube',
                image: url + '/youtube.png',
                cmd: 'wp_cmd_video'
            });
        },
        createControl: function(n, cm) {
            return null;
        },
        getInfo: function() {
            return {
                longname: "Gestionnaire de video",
                author: '__WPI__THEME__AUTHOR__',
                version: "0.1"
            };
        }
    });
    tinymce.PluginManager.add('video', tinymce.plugins.video);
})();