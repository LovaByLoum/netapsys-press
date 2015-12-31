<?php
/**
 * fonctions utilitaires
 * deboguage, etc ...
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

if(!function_exists('mp')){
  /**
   * Fonction pour debugger.
   *
   * @param type $var
   * @param type $t
   */
  function mp($var, $t = true, $logged = 'administrator') {
    global $current_user;
    if ( $logged && is_user_logged_in() && in_array( $logged, $current_user->roles) ){
      print('<pre style="text-align: left;">');
      print_r($var);
      print('</pre>');
      if($t == true)
        die();
    }
  }
}

if(!function_exists('wp_limite_text')){
  /**
   * Fonction qui sert a tronqué un texte par nombre de caractere.
   */
  function wp_limite_text($string, $char_limit =NULL) {
    if($string && $char_limit){
      if(strlen($string) > $char_limit){
        $words = substr($string,0,$char_limit);
        $words = explode(' ', $words);
        array_pop($words);
        return implode(' ', $words).' ...';
      }else{
        return $string;
      }
    }else{
      return $string;
    }
  }
}

if(!function_exists('wp_limite_word')){
  /**
   * Fonction qui sert a tronqué un texte par nombre de mot.
   */
  function wp_limite_word($string, $word_limit =NULL) {
    if($string && $word_limit){
      $words = preg_split("/[\s,-:]+/", $string, -1 ,PREG_SPLIT_OFFSET_CAPTURE);
      if (isset($words[$word_limit-1])){
        $the_word = $words[$word_limit-1][0];
        $offset = intval($words[$word_limit-1][1]);
        $string = substr($string,0, $offset +strlen($the_word));
        if (isset($words[$word_limit])){
          $string.='...';
        }
      }
      return $string;
    }else{
      return $string;
    }
  }
}

/**
 * fonction qui retourne l'ID du post_meta par meta_value
 */
function wp_get_post_by_template($meta_value){
  $args = array(
    'post_type' => 'page',
    'meta_key' => '_wp_page_template',
    'meta_value' => $meta_value,
    'suppress_filters' => FALSE,
    'numberposts' => 1,
    'fields' => 'ids'
  );
  $posts = get_posts($args);
  if(isset($posts) && !empty($posts)){
    return $posts[0];
  }else{
    global $post;
    return $post;
  }
}

if (!function_exists('get_post_by_slug')) :
  function get_post_by_slug($slug, $pt, $pages = false)
  {
    if (empty($slug))
      return false;

    if (is_array($pages) && !empty($pages))
    {
      foreach ($pages as $page)
        if ($page->post_name == $slug)
          return $page;
    }

    global $wpdb;
    return $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_name = '". $wpdb->escape($slug)."' AND post_status = 'publish' AND post_type ='". $wpdb->escape($pt)."'");
  }
endif;


function require_once_files_in($path){
  if ( is_dir($path) ){
    $nodes = glob($path . '/*.php');
    foreach ($nodes as $node) {
      if(is_file($node)){
        require_once( $node );
      }
    }
  }
}

//obtenir les labels pour la création de post type en français accordé avec le genre et nombre
/*
 * @param $ptsingle string : nom post type au singulier
 * @param $ptplural string : nom post type au pluriel
 * @param $masculin boolean : definir si masculin
 */
function get_custom_post_type_labels($ptsingle, $ptplural, $masculin){
  $labels = array(
    "name"				=> ucfirst($ptsingle),
    "singular_name"		=> ucfirst($ptplural),
    "add_new"			=> "Ajouter" ,
    "add_new_item"		=> "Ajouter" . ($masculin ? " un nouveau " : " une nouvelle " ) . $ptsingle,
    "edit_item"			=> "Modifier " . $ptsingle,
    "new_item"			=> ($masculin ? "Nouveau " : "Nouvelle " ) . $ptsingle,
    "view_item"			=> "Voir " . $ptsingle,
    "search_items"		=> "Rechercher des "  . $ptplural ,
    "not_found"			=> ($masculin ? "Aucun " : "Aucune " ) . $ptsingle .  ($masculin ? " trouvé" : " trouvée" ),
    "not_found_in_trash"=> ($masculin ? "Aucun " : "Aucune " ) . $ptsingle .  ($masculin ? " trouvé " : " trouvée " ) . "dans la corbeille",
    "parent_item_colon"	=> ucfirst($ptsingle) . ($masculin ? " parent" : " parente" ),
    "all_items"			=> ($masculin ? "Tous les " : "Toutes les " ) . $ptplural,
    "menu_name"         => ucfirst($ptplural),
    "parent_item_colon" => "",
  );
  return $labels;
}

//obtenir les labels pour la création de taxonomie en français accordé avec le genre et nombre
/*
 * @param $taxsingle string : nom taxonomie au singulier
 * @param $taxplural string : nom taxonomie au pluriel
 * @param $masculin boolean : definir si masculin
 */
function get_custom_taxonomy_labels($taxsingle, $taxplural, $masculin){
  $labels = array(
    'name'                       => ucfirst($taxsingle),
    'singular_name'              => ucfirst($taxsingle),
    'search_items'               => 'Rechercher des '. $taxplural,
    'popular_items'              => ucfirst($taxplural) . ' les plus populaires',
    'all_items'                  => ($masculin ? "Tous les " : "Toutes les " ) . $taxplural,
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => 'Modifier',
    'update_item'                => 'Mettre à jour',
    'add_new_item'               => 'Ajouter ' . ($masculin ? "un " : "une " ) . $taxsingle,
    'new_item_name'              => 'Nouveau nom',
    'separate_items_with_commas' => 'Séparez les ' . $taxplural . ' par des virgules',
    'add_or_remove_items'        => 'Ajouter ou supprimer ' . ($masculin ? "un " : "une " ) . $taxsingle,
    'choose_from_most_used'      => 'Choisir parmi les ' . $taxplural . ' les plus '. ($masculin ? "utilisés" : "utilisées" ),
    'not_found'                  => ($masculin ? "Aucun " : "Aucune " ) . $taxsingle,
    'menu_name'                  => ucfirst($taxplural),
  );
  return $labels;
}