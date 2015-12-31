<?php
/**
 * register post types and taxonomies
 */

add_action('init', 'mytheme_init_posttax',1);
function mytheme_init_posttax(){
	init_actus();
	//add stuff
}

//actus
function init_actus(){
  //post type
	  $labels = get_custom_post_type_labels( 'actualité', 'actualités', 1 );
    $data = array(
		'capability_type'      => 'post',
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
	register_post_type( 'actualite', $data);

	//taxonomies
	$labels = get_custom_taxonomy_labels( 'Type d\'actualité', 'Types d\'actualité', 1);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
	);
	register_taxonomy( 'type_actualite', 'actualite', $args );

  //taxonomies
  $labels = get_custom_taxonomy_labels( 'Mot clé', 'Mots clé', 1);
  $args = array(
    'hierarchical'      => false,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
  );
  register_taxonomy( 'actus_tag', 'actualite', $args );
	
}