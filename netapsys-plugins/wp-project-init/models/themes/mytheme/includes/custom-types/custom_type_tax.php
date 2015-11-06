<?php
/**
 * register post types and taxonomies
 */

add_action('init', 'init_posttax',1);
function init_posttax(){
	init_club();
	init_actus();
	init_article();
	init_licence();
	init_licencie();
	init_focus();
}

//club
function init_club(){
	$labels = array(
		"name"				=> "Clubs",
		"singular_name"		=> "Club",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter un nouveau club",
		"edit_item"			=> "Modifier club",
		"new_item"			=> "Nouveau club",
		"view_item"			=> "Voir club",
		"search_items"		=> "Rechercher des clubs",
		"not_found"			=> "Aucun club trouvé",
		"not_found_in_trash"=> "Aucun club trouvé dans la corbeille",
		"parent_item_colon"	=> "Club parent",
		"all_items"			=> "Tous les clubs",
		"menu_name"         => "Clubs",
		"parent_item_colon" => "",
      );
    $data = array(
		'capability_type'      => 'post',
		'supports'             => array( 'title' , 'editor' , 'excerpt' ),
		'hierarchical'         => false,
		'exclude_from_search'  => false,
		'public'               => true,
		'show_ui'              => true,
		'show_in_nav_menus'    => true,
		'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/club.png',
		'menu_position'        => 5,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( 'club', $data);

  //taxonomies
  $labels = array(
    'name'          => "Ligues",
    'singular_name' => "Ligue",
    'search_items'  => "Rechercher des ligues",
    'all_items'     => "Toutes les ligues",
    'edit_item'     => "Modifier",
    'update_item'   => "Mettre à jour",
    'add_new_item'  => "Ajouter une nouvelle ligue",
    'new_item_name' => "Nouveau nom"
  );

  $args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_admin_column'=> true
  );
  register_taxonomy( 'ligue', array('club','tribe_events'), $args );

    //ville
  $labels = array(
    'name'          => "Villes",
    'singular_name' => "Ville",
    'search_items'  => "Rechercher des villes",
    'all_items'     => "Toutes les villes",
    'edit_item'     => "Modifier",
    'update_item'   => "Mettre à jour",
    'add_new_item'  => "Ajouter une nouvelle ville",
    'new_item_name' => "Nouveau nom"
  );

  $args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_admin_column'=> true
  );
  register_taxonomy( 'ville', 'club', $args );

   //statut juridique
    $labels = array(
        'name'          => "Statuts juridiques",
        'singular_name' => "Statut juridique",
        'search_items'  => "Rechercher",
        'all_items'     => "Tous",
        'edit_item'     => "Modifier",
        'update_item'   => "Mettre à jour",
        'add_new_item'  => "Ajouter",
        'new_item_name' => "Nouveau nom"
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_admin_column'=> true
    );
    register_taxonomy( 'statut-juridique', 'club', $args );
}

//actus
function init_actus(){
	$labels = array(
		"name"				=> "Actualités",
		"singular_name"		=> "Actualité",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter une nouvelle actualité",
		"edit_item"			=> "Modifier actualité",
		"new_item"			=> "Nouvelle actualité",
		"view_item"			=> "Voir actualité",
		"search_items"		=> "Rechercher des actualités",
		"not_found"			=> "Aucune actualité trouvée",
		"not_found_in_trash"=> "Aucune actualité trouvée dans la corbeille",
		"parent_item_colon"	=> "Actualité parente",
		"all_items"			=> "Toutes les actualités",
		"menu_name"         => "Actualités",
		"parent_item_colon" => "",
      );
    $data = array(
		'capability_type'      => 'post',
		'supports'             => array( 'title', 'editor', 'thumbnail'),
		'hierarchical'         => false,
		'exclude_from_search'  => false,
		'public'               => true,
		'show_ui'              => true,
		'show_in_nav_menus'    => true,
		'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/actualite.png',
		'menu_position'        => 6,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( 'actualite', $data);
	
	
	//taxonomies
	$labels = array(
		'name'          => "Types d'actualité",
		'singular_name' => "Type d'actualité",
		'search_items'  => "Rechercher des types d'actualité",
		'all_items'     => "Tous les types d'actualité",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau type d'actualité",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    'show_admin_column'=> true
	);
	register_taxonomy( 'type_actualite', 'actualite', $args );
	
}

//article
function init_article(){
	/*$labels = array(
		"name"				=> "Articles",
		"singular_name"		=> "Article",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter un nouvel article",
		"edit_item"			=> "Modifier article",
		"new_item"			=> "Nouveau article",
		"view_item"			=> "Voir article",
		"search_items"		=> "Rechercher des articles",
		"not_found"			=> "Aucun article trouvé",
		"not_found_in_trash"=> "Aucun article trouvé dans la corbeille",
		"parent_item_colon"	=> "Article parent",
		"all_items"			=> "Tous les articles",
		"menu_name"         => "Articles",
		"parent_item_colon" => "",
      );
    $data = array(
		'capability_type'      => 'post',
		'supports'             => array( 'title', 'editor','page-attributes'),
		'hierarchical'         => true,
		'exclude_from_search'  => false,
		'public'               => true,
		'show_ui'              => true,
		'show_in_nav_menus'    => true,
		'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/article.png',
		'menu_position'        => 7,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( 'article', $data);*/
	
	//taxonomies
	/*$labels = array(
		'name'          => "Catégories d'article",
		'singular_name' => "Catégorie d'article",
		'search_items'  => "Rechercher des catégories d'article",
		'all_items'     => "Tous les catégories d'article",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau catégorie d'article",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    'show_admin_column'=> true
	);
	register_taxonomy( 'categorie_article', 'article', $args );*/
	
	//taxonomies
	$labels = array(
		'name'          => "Rubriques",
		'singular_name' => "Rubrique",
		'search_items'  => "Rechercher des rubriques",
		'all_items'     => "Toutes les rubriques",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter une nouvelle rubrique",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    	'show_admin_column'=> true
	);
	register_taxonomy( 'rubrique', array('post', 'page'), $args );
}

//licence
function init_licence(){
	$labels = array(
		"name"				=> "Licences",
		"singular_name"		=> "Licence",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter une nouvelle licence",
		"edit_item"			=> "Modifier licence",
		"new_item"			=> "Nouvelle licence",
		"view_item"			=> "Voir licence",
		"search_items"		=> "Rechercher des licences",
		"not_found"			=> "Aucune licence trouvée",
		"not_found_in_trash"=> "Aucune licence trouvée dans la corbeille",
		"parent_item_colon"	=> "Licence parente",
		"all_items"			=> "Toutes les licences",
		"menu_name"         => "Licences",
		"parent_item_colon" => "",
      );
  $data = array(
    'capability_type'      => 'post',
    'supports'             => array('') ,
    'hierarchical'         => false,
    'exclude_from_search'  => true,
    'public'               => false,
    'show_ui'              => true,
    'show_in_nav_menus'    => false,
    'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/licence.png',
    'menu_position'        => 8,
    'labels'               => $labels,
    'query_var'            => true,
  );
	register_post_type( 'licence', $data);
	
	//taxonomies
	$labels = array(
		'name'          => "Types de licence",
		'singular_name' => "Type de licence",
		'search_items'  => "Rechercher des types de licence",
		'all_items'     => "Tous les types de licenceé",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau type de licence",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    'show_admin_column'=> true
	);
	register_taxonomy( 'type_licence', array('licence','wpsc-product'), $args );

	//taxonomies
	$labels = array(
		'name'          => "Types d'assurance",
		'singular_name' => "Type d'assurance",
		'search_items'  => "Rechercher des types d'assurance",
		'all_items'     => "Tous les types d'assurance",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau type d'assurance",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    //'show_admin_column'=> true
	);
	register_taxonomy( 'type_assurance', 'licence', $args );

	//taxonomies
	$labels = array(
		'name'          => "Types de char à voile",
		'singular_name' => "Type de char à voile",
		'search_items'  => "Rechercher des types de char à voile",
		'all_items'     => "Tous les types de char à voile",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau type de char à voile",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels/*,
		'show_admin_column'=> true*/
	);
	register_taxonomy( 'type_char_voile', array('licence','tribe_events'), $args );
}

//licencie
function init_licencie(){
	$labels = array(
		"name"				=> "Licenciés",
		"singular_name"		=> "Licencié",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter un nouveau licencié",
		"edit_item"			=> "Modifier licencié",
		"new_item"			=> "Nouveau licencié",
		"view_item"			=> "Voir licencié",
		"search_items"		=> "Rechercher des licenciés",
		"not_found"			=> "Aucun licencié trouvé",
		"not_found_in_trash"=> "Aucun licencié trouvé dans la corbeille",
		"parent_item_colon"	=> "Licencié parent",
		"all_items"			=> "Tous les licenciés",
		"menu_name"         => "Licenciés",
		"parent_item_colon" => "",
  );
  $data = array(
		'capability_type'      => 'post',
		'supports'             => array('') ,
		'hierarchical'         => false,
		'exclude_from_search'  => true,
		'public'               => false,
		'show_ui'              => true,
		'show_in_nav_menus'    => false,
		'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/licencie.png',
		'menu_position'        => 9,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( 'licencie', $data);
	
	//taxonomies
	$labels = array(
		'name'          => "Diplômes",
		'singular_name' => "Diplôme",
		'search_items'  => "Rechercher des diplômes",
		'all_items'     => "Tous les diplômes",
		'edit_item'     => "Modifier",
		'update_item'   => "Mettre à jour",
		'add_new_item'  => "Ajouter un nouveau diplôme",
		'new_item_name' => "Nouveau nom"
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
    'show_admin_column'=> true
	);
	register_taxonomy( 'diplome', 'licencie', $args );
}

//init focus
//club
function init_focus(){
	$labels = array(
		"name"				=> "Portraits",
		"singular_name"		=> "Portrait",
		"add_new"			=> "Ajouter" ,
		"add_new_item"		=> "Ajouter un nouveau portrait",
		"edit_item"			=> "Modifier portrait",
		"new_item"			=> "Nouveau portrait",
		"view_item"			=> "Voir portrait",
		"search_items"		=> "Rechercher des portraits",
		"not_found"			=> "Aucun portrait trouvé",
		"not_found_in_trash"=> "Aucun portrait trouvé dans la corbeille",
		"parent_item_colon"	=> "Portrait parent",
		"all_items"			=> "Tous les portraits",
		"menu_name"         => "Portrait",
		"parent_item_colon" => "",
      );
    $data = array(
		'capability_type'      => 'post',
		'supports'             => array( 'title' , 'editor' ,'excerpt' ),
		'hierarchical'         => false,
		'exclude_from_search'  => false,
		'public'               => true,
		'show_ui'              => true,
		'show_in_nav_menus'    => true,
		'menu_icon'            => get_template_directory_uri() . '/inc/custom/images/focus.png',
		'menu_position'        => 10,
		'labels'               => $labels,
		'query_var'            => true,
	);
	register_post_type( 'portrait', $data);

}
