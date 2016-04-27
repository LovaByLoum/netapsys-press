<?php
/**
 * classe de service pour ...
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */
class CSample {

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
  private static function _load($pid) {
	$pid = intval($pid);
    $p = get_post($pid);
	
    if($p->post_type == "SAMPLE"){
   	    $element = new stdClass();
		
   	    //traitement des données
   	    
   	    
   	    //stocker dans le tableau statique 
	    self::$_elements[$pid] = $element;
    }
  }
  
  /**
   * fonction qui retourne une liste filtrée
   * 
   */
  public static function getBy($tax = null) {
	  $args = array (
      'post_type' => 'SAMPLE',
      'post_status' => 'publish',
      'numberposts' => -1,
      'offset' => 0,
      'order' => 'DESC',
      'orderby' => 'date',
      'fields' => 'ids' 
    );
	
	  if(!is_null($tax)) {
      $args['tax_query'][] = array (
        'taxonomy' => 'TAXONOMY',
        'field' => 'id',
        'terms' => $tax
      );
    }
    	
    $elements = get_posts($args);
    $elts = array();
    foreach ($elements as $id) {
    	$elt = self::getById(intval($id));
    	$elts[]=$elt;
    }
    return $elts;
    
  }
}  
?>