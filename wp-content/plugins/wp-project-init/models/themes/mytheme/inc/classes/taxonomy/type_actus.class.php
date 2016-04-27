<?php
/**
 * classe de service pour la taxonomie type actus
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

class CTypeActus {

  private static $_elements;
	
  public function __construct() {
    
  }
  
  /**
   * fonction qui prend les informations son Id. 
   * 
   * @param type $pid
   */
  public static function getById($pid) {
    $pid = intval($pid);
    
    //On essaye de charger l'element
    if(!isset(self::$_elements[$pid])) {
      self::_load($pid);
    }
    //Si on a pas réussi à chargé l'article (pas publiée?)
    if(!isset(self::$_elements[$pid])) {
      return FALSE;
    }

    return self::$_elements[$pid];
  }
  
  /**
   * fonction qui charge toutes les informations dans le variable statique $_elements.
   * 
   * @param type $pid 
   */
  private static function _load($tid) {
	  $tid = intval($tid);
    $t = get_term($tid, TAXONOMY_TYPE_ACTUS);

    $element = new stdClass();

    //champs wp
    $element->id = $t->term_id;
    $element->titre = $t->name;
    $element->slug = $t->slug;
    $element->description = $t->description;

    //champs personnalisés
    if ( class_exists('Acf') ){
      $element->icone = get_field( FIELD_TYPE_ACTUS_ICONE, TAXONOMY_TYPE_ACTUS . '_' . $tid);
    }else{
      $element->icone = get_template_directory_uri() . '/images/default.jpg';
    }
    //...

    //stocker dans le tableau statique
    self::$_elements[$tid] = $element;

  }
  
  /**
   * fonction qui retourne une liste filtrée
   * 
   */
  public static function getBy( ) {
	  $args = array (
      'hide_empty' => false,
    );
	
	  $elements = get_terms( TAXONOMY_TYPE_ACTUS, $args);
    $elts = array();
    foreach ($elements as $t) {
    	$elt = self::getById(intval($t->term_id));
    	$elts[]=$elt;
    }
    return $elts;
    
  }

  //gestion de colonne BO
  public static function add_column($columns) {
    $columns['icone'] = __("Icone", "mytheme");
    return $columns;
  }

  //gestion des valeurs des colonnes BO
  public static function manage_column($out, $column_name, $term_id){
    switch($column_name){
      case 'icone':
        $t = CTypeActus::getById($term_id);
        echo '<img src="' . $t->icone . '"/>';
        break;
      default:
        break;
    }

    return $out;
  }
}

add_filter('manage_edit-' . TAXONOMY_TYPE_ACTUS . '_columns', 'CTypeActus::add_column');
add_action( 'manage_' . TAXONOMY_TYPE_ACTUS . '_custom_column', 'CTypeActus::manage_column', 10, 3 );
?>