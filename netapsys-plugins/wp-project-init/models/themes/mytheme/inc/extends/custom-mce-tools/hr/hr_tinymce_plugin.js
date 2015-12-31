(function() {
    tinymce.create('tinymce.plugins.hr_custom', {
        init: function(ed, url) {
            // Register commands
            ed.addCommand('wp_cmd_hr_custom', function() {
                ed.windowManager.open({
                    file: url + '/hr.php',
                    width: 450,
                    height: 250,
                    inline: 1
                }, {
                    plugin_url: url
                });
            });

            ed.addButton('hr_custom', {
                title: 'Ligne horizontale personnalisée',
                image: url + '/hr.png',
                cmd: 'wp_cmd_hr_custom'
            });
        },
        createControl: function(n, cm) {
            return null;
        },
        getInfo: function() {
            return {
                longname: "Gestionnaire de ligne horizontale",
                author: '__WPI__THEME__AUTHOR__',
                version: "0.1"
            };
        }
    });
    tinymce.PluginManager.add('hr_custom', tinymce.plugins.hr_custom);
})();