(function() {
	tinymce.create('tinymce.plugins.jpress_gallery', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('wp_cmd_jpress_gallery', function() {
				ed.windowManager.open({
					file : url + '/album.php',
					width : 350 ,
					height : 130,
					inline : 1
				}, {
					plugin_url : url
				});
			});
			
			
			ed.addButton('jpress_gallery', {
				title : 'Galerie d\'images',
				image : url+'/jpress_gallery_16_color.png',
				cmd: 'wp_cmd_jpress_gallery'		
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "jpress Gallery Manager Shortcode",
				author : 'Johary Ranarimanana',
				version : "0.1"
			};
		}
	});
	tinymce.PluginManager.add('jpress_gallery', tinymce.plugins.jpress_gallery);
})();