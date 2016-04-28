<?php
/**
 * register taxo mot clé
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */

add_action('init', 'beapi_init_motcle_actus', 2);
function beapi_init_motcle_actus(){
  //taxonomies
  $labels = get_custom_taxonomy_labels( 'Mot clé', 'Mots clé', 1);
  $args = array(
    'hierarchical'      => false,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
  );
  register_taxonomy( TAXONOMY_MOT_CLE_ACTUS, POST_TYPE_ACTUALITE, $args );
	
}