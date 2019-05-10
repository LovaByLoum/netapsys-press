<?php
/*
  Plugin Name: jpress-zone-cache
  Description: Permet de mettre en cache des zones de la page et d'avoir differentes instances de cache de la même zone en spécifiant des paramêtres comme le rôle de l'utilisateur, langue, device etc ...
  Author: Johary Ranarimanana
*/

define ( 'JZC_URL', plugins_url('jpress-zone-cache') );
define ( 'JZC_PATH', dirname(__FILE__) );

require_once 'includes/main.class.php';
require_once 'includes/service.class.php';

global $jpress_zone_cache;
$jpress_zone_cache = new jPressZoneCache();

//API for theme
function jzc_get_cache($cache_slug, $role = NULL, $lang = NULL){
    global $jpress_zone_cache;
    $html = jPressZoneCacheService::get_cache($cache_slug, $role = NULL, $lang = NULL);
    if( $html ){
        return $html;
    } else {
        $dirname = $jpress_zone_cache->dir_caches;
        $cache = jPressZoneCacheService::get_cache_by('name',$cache_slug);
        ob_start();
        include ($dirname . $cache->zone_file );
        $output = ob_get_contents();
        ob_end_clean();
        jPressZoneCacheService::save_caches($output,$cache->id,$role,$lang);
        return $output;
    }
}

?>