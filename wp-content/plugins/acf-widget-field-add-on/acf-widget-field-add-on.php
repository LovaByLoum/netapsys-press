<?php
/*
* Plugin Name: ACF - Widget Field add-on
* Description: This plugin is an add-on for Advanced Custom Fields. It allows you to add widget field
* Author:      Johary Ranarimanana
* Version:     1.0
* Text Domain: acf
* Domain Path: /lang/
*/

add_action( 'admin_init', 'acf_wfao_admin_init' );
function acf_wfao_admin_init() {
  wp_enqueue_script( 'acf-wfao-jquery-livequery', plugins_url( 'js/jquery.livequery.js', __FILE__ ) );
  wp_enqueue_style( 'acf-wfao-style', plugins_url( 'css/acf_widget.css', __FILE__ ) );
  wp_enqueue_script( 'json2' );
  wp_enqueue_script( 'acf-wfao-script', plugins_url( 'js/acf_widget.js', __FILE__  ) );
}

add_action( 'init', 'acf_wfao_init' );
function acf_wfao_init() {
  include 'field.php';
  include 'field-api.php';
}

//*API*//
function acf_register_widget( $name, $callback ) {
	global $acf_widgets;
	$widget = new stdClass();
	$widget->slug = sanitize_title( $name );
	$widget->name = $name;
	$widget->callback = $callback;
	if( is_null( $acf_widgets ) ) $acf_widgets = array();
	$acf_widgets[$widget->slug] = $widget;
}

add_action( 'wp_ajax_acf_load_widget', 'acf_wfao_load_widget' );
function acf_wfao_load_widget() {
	$val = $_POST['value'];
	global $acf_widgets;
	if( isset( $acf_widgets[$val] ) ) {
		$widget = $acf_widgets[$val];
		if( is_array( $widget->callback ) ) {
      call_user_func( "{$widget->callback[0]}::{$widget->callback[1]}" );
		} else {
      call_user_func( $widget->callback );
		}
	} else {
		echo 'widget non trouvé';
	}
	die;
}
?>