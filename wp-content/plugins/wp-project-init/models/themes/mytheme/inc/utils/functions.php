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
   * Fonction pour debugger (message privé).
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

if(!function_exists('wp_log')){
  /**
   * créer un fichier log personnalisé dans wp-content/logs
   */
  function wp_log( $data ){
    $path_log = ABSPATH .'logs';
    @mkdir($path_log);
    $path_log.= '/wp.log';
    if(is_array($data) ||is_object($data) ){
      ob_start();
      print_r($data);
      $str = ob_get_contents();
      ob_clean();
    }else{
      $str = $data;
    }
    $str = date('Y-m-d H:i:s') . '     ' . $str . "\r\n";
    wp_create_file($path_log, $str,'a');
  }
  /**
   * creer un fichier et insere un contenu
   *
   * @param string $filename
   * @param string $somecontent
   * @return bool
   */
  function wp_create_file($filename, $somecontent, $openmode = "w"){
    if (!$handle = @fopen($filename, $openmode)) {
      return false;
    }
    if (@fwrite($handle, $somecontent) === FALSE) {
      return false;
    }
    @fclose($handle);
    return true;
  }
}

if(!function_exists('wp_minify')){
  //minification des ressources
  function wp_minify($array_file, $type){
    $file = 'mytheme';
    $minified = get_template_directory_uri() . '/' . $type .'/' . $file . '.min.' . $type ;
    $minifiedpath = get_template_directory() . '/' . $type .'/' . $file . '.min.' . $type ;

    if (is_file($minifiedpath)){
      return $minified;
    }else{
      $result = '';
      foreach($array_file as $key => $value) {
        if ($type == 'css'){
          if( $value ){
            $result .= "\n" . CssMin::minify(file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type));
          }
          else {
            $result .= "\n" . file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$key);
          }
        }else{
          if( $value ){
            $result .= "\n" . JSMin::minify(file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type));
          }else{
            $result .= "\n" . file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type);
          }
        }
      }

      if ($type == 'css'){
        $result=str_replace('@charset "utf-8";', '', $result);
        $result = '@charset "utf-8";' . $result;
      }
      wp_create_file($minifiedpath,$result);
      return $minified;
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

if(!function_exists('wp_get_post_by_template')){
  /**
   * fonction qui recherche les posts par son template
   */
  function wp_get_post_by_template($meta_value, $dir_page_template = 'page-templates/'){
    $args = array(
      'post_type' => 'page',
      'meta_key' => '_wp_page_template',
      'meta_value' => $dir_page_template . $meta_value,
      'suppress_filters' => FALSE,
      'numberposts' => 1,
      //'fields' => 'ids'
    );
    $posts = get_posts($args);
    if(isset($posts) && !empty($posts)){
      return $posts[0];
    }else{
      global $post;
      return $post;
    }
  }
}

if (!function_exists('get_post_by_slug')) :
  //fonction recherchant les post par slug
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

//requiert tous les fichiers d'un dossier
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

//custom capabilities for post
function wp_get_custom_posts_capabilities( $capability_type, $is_taxo = false ){
  if ( $is_taxo ){
    $caps =  array(
      "manage_terms"        => "manage_{$capability_type}s",
      "edit_terms"          => "edit_{$capability_type}s",
      "delete_terms"        => "delete_{$capability_type}s",
      "assign_terms"        => "assign_{$capability_type}s",
    );
  }else{
    $caps =  array(
      "edit_post"                   => "edit_{$capability_type}",
      "edit_private_posts"          => "edit_private_{$capability_type}s",
      "edit_published_posts"        => "edit_published_{$capability_type}s",
      "delete_post"                 => "delete_{$capability_type}",
      "delete_posts"                => "delete_{$capability_type}s",
      "delete_private_posts"        => "delete_private_{$capability_type}s",
      "delete_published_posts"      => "delete_published_{$capability_type}s",
      "delete_others_posts"         => "delete_others_{$capability_type}s",
      "edit_posts"                  => "edit_{$capability_type}s",
      "edit_others_posts"           => "edit_others_{$capability_type}s",
      "publish_posts"               => "publish_{$capability_type}s",
      "read_private_posts"          => "read_private_{$capability_type}s",
      "create_posts"                => "edit_{$capability_type}s",
    );
  }

  //perform affichage dans Advanced Access Manager
  if ( class_exists('mvb_WPAccess') ){
    $keys = array_unique(array_values($caps));
    $str = '';
    foreach ( $keys as $v){
      $str.="\$grouped_list['" . str_replace('_', ' ', ucfirst($capability_type)) . "'][]='$v';\n";
    }

    $str .=
      "foreach( \$grouped_list['Miscelaneous'] as \$k => \$v ){
          if ( in_array( \$v,  \$grouped_list['" . str_replace('_', ' ', ucfirst($capability_type)) . "'] ) ){
        unset( \$grouped_list['Miscelaneous'][\$k] );
      }
  }\n";
    $str.= 'return $grouped_list;';
    $function = create_function('$grouped_list', $str );
    add_filter('aam_grouped_list',  $function);
  }

  return $caps;
}

/*fonction d'encryptage*/
function wp_encrypt_text($str){
  $str = base64_encode($str);
  return $str;
}

/*function de decryptage*/
function wp_decrypt_text($str){
  $str = base64_decode($str);
  return $str;
}

