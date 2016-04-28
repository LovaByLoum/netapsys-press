<?php
/**
 * register taxo type actualite
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */

add_action('init', 'beapi_init_typeactus', 2);
function beapi_init_typeactus(){
  //taxonomies
  $labels = get_custom_taxonomy_labels( 'Type d\'actualité', 'Types d\'actualité', 1);
  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
  );
  register_taxonomy( TAXONOMY_TYPE_ACTUS, POST_TYPE_ACTUALITE, $args );
}