<?php
/*
Plugin Name: Gestion de cache
Description: Offre les fonctionnalités suivantes : vidage du cache WP Cache et JpressZone cache
Author: Johary
*/


add_action('admin_menu', 'gds_admin_menu', 999);
function gds_admin_menu() {
    global $submenu, $menu;

    $aam_cap = 'manage_options';
    add_submenu_page(
    	'index.php',
    	__('Gestion de cache', 'gds'),
    	__('Gestion de cache', 'gds'),
    	$aam_cap,
    	'gestion-cache',
    	'gds_page'
    );
}
function gds_page(){
	global $cache_path;
	?>
		<div id="wpbody">
			<div id="wpbody-content">
				<div class="icon32" id="icon-options-general"><br></div>
				<h2 class="nav-tab-wrapper">
					<a href="#list-env-tab" title="Liste d'envoie" class="nav-tab nav-tab-active" id="list-env">Gestion de cache</a>
				</h2>
		
				<div id="tjg-admin-theme" class="wrap">
					<form enctype="multipart/form-data" method="post">
						<input type="submit" name="vidercache" value="Vider la cache" class="button-primary"/>
						<label>Vider la cache de wordpress</label>
					</form>
					<pre><?php 
					//wp super cache
					if (isset($_POST["vidercache"]) && function_exists( 'prune_super_cache' )){
						prune_super_cache ($cache_path, true);
						echo '* Cache WP Super Cache vidé.<br>';
					}
					
					//jpres zone cache
					if (isset($_POST["vidercache"]) && function_exists( 'jzc_purge' )){
						jzc_purge (null);
						echo '* jPress Zone cache vidé.<br>';
					}
					?></pre>
				</div>
				<div class="clear"></div>
			</div><!-- wpbody-content -->
			<div class="clear"></div>
		</div>
	<?php 
 }