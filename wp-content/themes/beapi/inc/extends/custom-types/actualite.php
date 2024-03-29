<?php
/**
 * register post type actualite
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */

add_action('init', 'beapi_init_actus', 1);
function beapi_init_actus(){
  //post type
  $labels = get_custom_post_type_labels( 'actualité', 'actualités', 1 );
  $data = array(
    'capability'         => 'post',
		'supports'             => array( 'title', 'editor', 'thumbnail'),
		'hierarchical'         => false,
		'exclude_from_search'  => false,
		'public'               => true,
		'show_ui'              => true,
		'show_in_nav_menus'    => true,
		'menu_icon'            => get_template_directory_uri() . '/images/grey-icon/list_w__images.png',
		'menu_position'        => 6,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( POST_TYPE_ACTUALITE, $data);
	
}