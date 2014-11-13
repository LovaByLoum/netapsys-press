<?php
/*
Plugin Name: jPress Create Post Table
Description: Créer des tables personnalisés pour les posts. Les posts seront enregistrés vers d'autres tables personnalisés non reconnues par WordPress, mais seront toujours gerés par l'API de WordPress. Cet extension est utile lorsqu'on veut geré des contenus vraiment volumineux avec plusieurs flux de données (champs personnalisés) et que la table wp_posts et wp_postmeta deviennent trop lourdes et inadaptées (soucis d'indexation et de jointure).
Version: 1.0.0
Author: Johary Ranarimanana
Contibutor: Fitiavana Ramanantsoa
*/

//pour activer/desactiver la prise en compte des tables customs
global $jcpt_options;
if(is_null($jcpt_options)){
    $jcpt_options = get_option('jcpt_options');
}
if (isset($jcpt_options['doturn']) && $jcpt_options['doturn']){
    require_once('inc/api-turn-hooks.php');
}

//création de menu pour la page d'aministration
add_action('admin_menu', 'jcpt_admin_head');
function jcpt_admin_head(){
    add_submenu_page(
        'options-general.php',
        'Création de table pour les contenus',
        'Create Post Table',
        'manage_options',
        'posttable_admin',
        'jcpt_admin'
    );
}

//page d'administration custom posts
function jcpt_admin(){
    include('admin-page.php');
}

//determine la table stockage d'un id
function jcpt_whereis($post_id){
    global $jcpt_options,$wpdb,$jcpt_wherecache;

    if(!is_null($jcpt_wherecache)){
        if(isset($jcpt_wherecache[$post_id]))
            return $jcpt_wherecache[$post_id];
    }else{
        $jcpt_wherecache = array();
    }

    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    foreach ($jcpt_options['enable'] as $pt)	{
        if($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}".$pt."s WHERE ID=".$post_id)){
            $jcpt_wherecache[$post_id] = $wpdb->prefix .$pt."s";
            return $wpdb->prefix .$pt."s";
        }
    }
    $jcpt_wherecache[$post_id] = $wpdb->prefix ."posts";
    return $wpdb->prefix ."posts";
}

//determine le post typ par son id
function jcpt_whois($post_id){
    global $jcpt_options,$wpdb, $jcpt_whocache;

    if(!is_null($jcpt_whocache)){
        if(isset($jcpt_whocache[$post_id]))
            return $jcpt_whocache[$post_id];
    }else{
        $jcpt_whocache = array();
    }

    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    foreach ($jcpt_options['enable'] as $pt)	{
        if($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}".$pt."s WHERE ID=".$post_id)){
            $jcpt_whocache[$post_id] = $pt;
            return $pt;
        }
    }
    $jcpt_whocache[$post_id] = "post";
    return "post";
}

//get max ID
function jcpt_getnewid(){
    global $jcpt_options,$wpdb;
    $max = 0;
    foreach ($jcpt_options['enable'] as $pt)	{
        $newid= $wpdb->get_var("SELECT MAX(ID)  FROM {$wpdb->prefix}".$pt."s");
        if($newid>$max)$max=$newid;
    }
    $newid= $wpdb->get_var("SELECT MAX(ID)  FROM {$wpdb->prefix}posts");
    if($newid>$max)$max=$newid;
    return $max+1;
}

//get post column
function jcpt_get_columns($post_type){
    global $jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    return $jcpt_options['column'][$post_type];
}
//get type column
function jcpt_get_types($post_type){
    global $jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    return $jcpt_options['type'][$post_type];
}

//get post meta name
function jcpt_get_metanames($post_type){
    global $jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    return $jcpt_options['value'][$post_type];
}

//get post meta name for column
function jcpt_get_column_metaname($post_type, $columnname){
    global $jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    foreach ($jcpt_options['column'][$post_type] as $key => $column ) {
        if($columnname == $column ){
            return $jcpt_options['value'][$post_type][$key] ;
        }
    }
    return false;
}

//get post column by metaname
function jcpt_get_column_by_metaname($post_type, $metaname){
    global $jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
    }
    foreach ($jcpt_options['value'][$post_type] as $key => $meta ) {
        if($metaname == $meta ){
            return $jcpt_options['column'][$post_type][$key] ;
        }
    }
    return false;
}
//get_value column by ID
function jcpt_get_value_column($posttype,$column,$pid){
  global $wpdb;

  return $wpdb->get_var("SELECT {$column} FROM " . $wpdb->prefix . $posttype . "s WHERE ID = {$pid}");
}

//fonction reprise de contenu
function jcpt_import_content($posttype=''){
    global $jcpt_options,$wpdb,$wp_utils;
    $status = get_option('jcpt_status_' . $posttype,'');

    if($status == 'fini') die('fini');

    // get option post table
    if(is_null($jcpt_options))
        $jcpt_options = get_option('jcpt_options');

    if(!in_array($posttype,$jcpt_options['enable'])) return;

    $column = $jcpt_options['column'][$posttype];
    $value = $jcpt_options['value'][$posttype];

    $offset = get_option('jcpt_offset_' . $posttype,0);
    $limit=100;
    update_option('jcpt_offset_' . $posttype, $offset+ $limit );
    $wp_utils->log('debut offset : ' . $offset,'log_import_'.$posttype.'.txt');
    // get content.
    $res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE (post_status = 'publish' OR post_status= 'private') AND post_type = '{$posttype}' ORDER BY ID ASC LIMIT " . $offset . "," . $limit);

    if(count($res) <= 0){
        update_option('jcpt_status_' . $posttype,'fini');
        die('fini');
    }

    $save = array();
    foreach($res as $row){
        // get taxonomy rattache of post type.
        $tax = get_object_taxonomies($posttype);

        $pid_old = $row->ID;
        unset($row->ID);

        $pid = jcpt_getnewid();
        // Insert content into new table.
        // $pid = wp_insert_post((array) $row);

        $sql = "INSERT INTO {$wpdb->prefix}{$posttype}s (ID,post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES({$pid},{$row->post_author}, '{$row->post_date}', '{$row->post_date_gmt}', '" . addslashes($row->post_content)."', '" . addslashes($row->post_title). "', '" . addslashes($row->post_excerpt)."', '{$row->post_status}', '{$row->comment_status}', '{$row->ping_status}', '{$row->post_password}', '{$row->post_name}', '{$row->to_ping}', '{$row->pinged}', '{$row->post_modified}', '{$row->post_modified_gmt}', '{$row->post_content_filtered}', '{$row->post_parent}', '{$row->guid}', '{$row->menu_order}', '{$row->post_type}', '{$row->post_mime_type}', '{$row->comment_count}')";
        $success = $wpdb->query($sql);

        if($pid && $success){
            $save[$pid_old] = $pid;
            foreach($value as $key => $row2){

                if(empty($row2)) continue;

                $meta = get_post_meta($pid_old,$row2,true);

                if(!$meta) continue;

                if(is_array($meta)) $meta = maybe_serialize($meta);

                // Update a field rattache.
                $wpdb->query("UPDATE " . $wpdb->prefix . $posttype . "s SET " . $column[$key] . " = '" . addslashes($meta) . "' WHERE ID = {$pid} ");

                if(is_array($tax) && count($tax) > 0){
                    foreach($tax as $t){
                        // get term rattache.
                        $tax_old = wp_get_post_terms($pid_old, $t, array("fields" => "ids"));
                        // Update term to new post.
                        wp_set_post_terms( $pid, $tax_old, $t );
                    }
                }
            }

            $wp_utils->log('ok','log_import_'.$posttype.'.txt');
            $wp_utils->log('ID new = ' . $pid . ' ID old = ' . $pid_old,'log_import_'.$posttype.'.txt');

            $jcpt_save = get_option('jcpt_save_' . $posttype,array());
            $save += $jcpt_save;
            update_option('jcpt_save_' . $posttype,$save);
        }else{
            $wp_utils->log('nok','log_import_'.$posttype.'.txt');
            $wp_utils->log($sql,'log_import_'.$posttype.'.txt');
        }
    }
}


//////// cron job ///////////////////////////////////////////////
add_filter('cron_schedules', 'jcpt_add_scheduled_interval');
// add once interval to wp schedules
function jcpt_add_scheduled_interval($schedules) {
    $schedules['cinq_minute'] = array('interval'=>300, 'display'=>'Once 5 minute');
    $schedules['dix_minute'] = array('interval'=>600, 'display'=>'Once 10 minute');
    return $schedules;
}

if (!wp_next_scheduled('jcpt_cinq_minute_action')) {
    wp_schedule_event(time(), 'cinq_minute', 'jcpt_cinq_minute_action');
}
if (!wp_next_scheduled('jcpt_dix_minute_action')) {
    wp_schedule_event(time(), 'dix_minute', 'jcpt_dix_minute_action');
}

//add 10mn action
if(!empty($jcpt_options['cronjob'])){
    add_action('jcpt_dix_minute_action', 'jcpt_cron');
}
function jcpt_cron(){
    global $jcpt_options;
    // get option post table
    if(is_null($jcpt_options))
        $jcpt_options =  get_option('jcpt_options');
    if(!empty($jcpt_options['cronjob'])){
        foreach ( $jcpt_options['enable'] as $post_type) {
            if(in_array($post_type, $jcpt_options['cronjob'])){
                jcpt_import_content($post_type);
            }
        }
    }
}
//add_action('jcpt_dix_minute_action', 'jcpt_cinqmin_action');
//add_action('jcpt_dix_minute_action', 'jcpt_dixmin_action');
