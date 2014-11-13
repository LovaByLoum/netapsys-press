<?php
/*
  Plugin Name: jpress-zone-cache
  Description: Permet de mettre en cache des zones de la page et d'avoir differents instances de cache de la même zone en spécifiant des paramêtres comme le rôle de l'utilisateur, langue, etc ...
  Author: Johary
*/
global $jzc_db_cache_config;
global $jzc_db_caches;
global $jzc_dir_caches;

$jzc_db_cache_config = $wpdb->prefix.'jzc_cache_config';
$jzc_db_caches = $wpdb->prefix.'jzc_caches';
$jzc_dir_caches = get_template_directory().'/zone_cache_files/';

add_action('admin_menu', 'jzc_admin_menu', 999);
function jzc_admin_menu() {
    global $submenu, $menu;
	
	$cap = 'activate_plugins';
    add_menu_page(
            __('Zone cache', 'jzc'),
            __('Zone cache', 'jzc'),
            $cap,
            'zone-cache',
            'jzc_cache_config_page',
            WP_PLUGIN_URL . '/jpress-zone-cache/images/running_man.png'
    );
}

function jzc_cache_config_page(){
	require_once(dirname(__FILE__) . '/admin/jzc-cache-config-page.php' );
}

add_action('admin_init', 'jzc_init');
function jzc_init() {
	if(is_admin()){
		jzc_install();
	}
}

function jzc_install()
{
    global $wpdb;
    global $jzc_db_cache_config;
    global $jzc_db_caches;
    global $jzc_dir_caches;
	
    //create theme dir
	if(!file_exists($jzc_dir_caches)){
		@mkdir($jzc_dir_caches);
	}
	
    // create table on first install
    if($wpdb->get_var("show tables like '$jzc_db_cache_config'") != $jzc_db_cache_config) {

        $sql = "CREATE TABLE  ".$jzc_db_cache_config." (
          id bigint(20) NOT NULL auto_increment,
          name varchar(255) default NULL,
          role int(1) default NULL,
          langue int(1) default NULL,
          zone_file varchar(255) default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";

    	$results = $wpdb->query($sql);
    }
    
    if($wpdb->get_var("show tables like '$jzc_db_caches'") != $jzc_db_caches) {

        $sql = "CREATE TABLE  ".$jzc_db_caches." (
          id bigint(20) NOT NULL auto_increment,
          cache_id bigint(20) NOT NULL,
          html text default NULL,
          role varchar(100) default NULL,
          langue varchar(50) default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";

    	$results = $wpdb->query($sql);
    }
}

function jzc_get_list_cache($offset = 0, $limit = NULL)
{
	global $jzc_db_cache_config;
    global $wpdb;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ".$jzc_db_cache_config . " WHERE 1=1 " ;
    $sql .=  " ORDER BY id ASC " ;
    
    if(!is_null($limit)){
    	$sql .= " LIMIT " . $offset . ", " . $limit ;
    }
   	$results = array();
    $results["data"] = $wpdb->get_results($sql);
    $sql = "SELECT FOUND_ROWS() as nbrTotal;";
    $results["total"] = $wpdb->get_var($sql);
    
    return  $results;
}

function jzc_get_list_caches($offset = 0, $limit = NULL)
{
	global $jzc_db_caches;
    global $wpdb;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ".$jzc_db_caches . " WHERE 1=1 " ;
    $sql .=  " ORDER BY id ASC " ;
    
    if(!is_null($limit) && $limit>0){
    	$sql .= " LIMIT " . $offset . ", " . $limit ;
    }
   	$results = array();
    $results["data"] = $wpdb->get_results($sql);
    $sql = "SELECT FOUND_ROWS() as nbrTotal;";
    $results["total"] = $wpdb->get_var($sql);
    
    return  $results;
}

function jzc_set_cache($name, $role, $langue, $zone_file )
{
	global $jzc_db_cache_config;
    global $wpdb;
    $wpdb->insert($jzc_db_cache_config, array(
    	'name' => sanitize_title($name),
    	'role' => $role,
    	'langue' => $langue,
    	'zone_file' => $zone_file
    ));
}

function jzc_delete_cache($ids)
{
	$ids = implode(',',$ids);
	global $jzc_db_cache_config;
    global $wpdb;
    $sql = "DELETE FROM {$jzc_db_cache_config} WHERE id IN(" . $ids .")";
    $wpdb->query($sql);
}

function jzc_delete_caches($ids)
{
	$ids = implode(',',$ids);
	global $jzc_db_caches;
    global $wpdb;
    $sql = "DELETE FROM {$jzc_db_caches} WHERE id IN(" . $ids .")";
    $wpdb->query($sql);
}

function jzc_get_cache_by($field,$id)
{
	global $jzc_db_cache_config;
    global $wpdb;
    $sql = "SELECT * FROM {$jzc_db_cache_config} WHERE {$field} = '" . $id . "'";
    return $wpdb->get_row($sql);
}

function jzc_generate_caches($ids)
{
	$ids = implode(',',$ids);
	global $jzc_db_cache_config;
    global $wpdb;
    $sql = "SELECT * FROM {$jzc_db_cache_config} WHERE id IN(" . $ids .")";
    $results = $wpdb->get_results($sql);
    foreach ($results as $result) {
    	jzc_generate_cache($result);
    }
}

function jzc_generate_cache($cache){
	$dirname = WP_PLUGIN_DIR . '/jpress-zone-cache/zone_file/';
	
	//langue wpml case
	/*if ($cache->langue == '1' && class_exists('SitePress')){
		global $sitepress, $jzc_lang,  $wp_query;
        $langues = $sitepress->get_active_languages();
		foreach ( $langues as $lang) {
			ob_start();
			include ($dirname . $cache->zone_file );
			$output = ob_get_contents();
			ob_end_clean();
			//jzc_save_caches($output);

		}
	}*/
}

function jzc_save_caches($html, $cache_id, $role = NULL, $langue = NULL){
	global $jzc_db_caches,$wpdb;
	$postvar = array(
    	'cache_id' => $cache_id,
    	'html' => $html
    );
    if(!is_null($role)){
    	$postvar['role'] = $role;
    }
    if(!is_null($langue)){
    	$postvar['langue'] = $langue;
    }
	$wpdb->insert($jzc_db_caches, $postvar);
}

function jzc_get_cache_html($cache_slug, $role = NULL, $lang = NULL){
	global $wpdb,$jzc_db_caches,$jzc_db_cache_config,$jzc_dir_caches;
    $sql = "SELECT {$jzc_db_caches}.html FROM {$jzc_db_caches} 
    		INNER JOIN 	{$jzc_db_cache_config} ON {$jzc_db_caches}.cache_id =  {$jzc_db_cache_config}.id
		    WHERE {$jzc_db_cache_config}.name = '" . $cache_slug ."' ";
    if($role)
    	$sql .= " AND {$jzc_db_caches}.role = '" . $role . "' " ;
    if($lang)
    	$sql .= " AND {$jzc_db_caches}.langue = '" . $lang . "' " ;
    $sql .=  " LIMIT 1" ;
    
    $html = $wpdb->get_var($sql);
    if($html){
    	return $html;
    }else{
    	//$dirname = WP_PLUGIN_DIR . '/jpress-zone-cache/zone_file/';
    	$dirname = $jzc_dir_caches;
    	$cache = jzc_get_cache_by('name',$cache_slug);
    	ob_start();
		include ($dirname . $cache->zone_file );
		$output = ob_get_contents();
		ob_end_clean();
		jzc_save_caches($output,$cache->id,$role,$lang);
    	return $output;
    }
}

function jzc_purge_cache($slug, $lang = NULL, $role=NULL )
{
    global $jzc_db_caches,$wpdb;
    $cache = jzc_get_cache_by('name',$slug);

    $sql = "DELETE FROM {$jzc_db_caches} WHERE cache_id = " . $cache->id ;
    if (!is_null($lang)){
        $sql.= " AND langue ='" . $lang."' ";
    }
    if (!is_null($role)){
        $sql.= " AND role ='" . $role."' ";
    }
    $wpdb->query($sql);
}

add_filter('cron_schedules', 'jzc_add_scheduled_interval');
// add once 3 hours interval to wp schedules
function jzc_add_scheduled_interval($schedules) {
    $schedules['3h'] = array('interval'=>10800, 'display'=>'Once 3 hours');
    return $schedules;
}

if (!wp_next_scheduled('jzc_cron_purge')) {
    wp_schedule_event(time(), '3h', 'jzc_cron_purge');
}
add_action('jzc_cron_purge', 'jzc_purge');
?>