<?php
/**
 * register taxo mot clé
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

add_action('init', 'mytheme_init_motcle_actus', 2);
function mytheme_init_motcle_actus(){
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