<?php
/*
* Plugin Name: Advanced Custom Fields - URL Field add-on
* Description: This plugin is an add-on for Advanced Custom Fields. It allows you to choose URL between posts link and external link
* Author:      Johary Ranarimanana
* Version:     1.0
* Text Domain: acf
* Domain Path: /lang/
*/

add_action('admin_init', 'acf_url_object_admin_init');
function acf_url_object_admin_init(){
  wp_enqueue_script('livequery',plugin_dir_url(__FILE__).'js/jquery.livequery.js');
  wp_enqueue_script('json2');
  wp_enqueue_script('acf_url_field',plugin_dir_url(__FILE__).'js/acf_url.js');
}  

add_action( 'init', 'acf_url_object_init');
function acf_url_object_init(){
  include 'field.php';
}

?>