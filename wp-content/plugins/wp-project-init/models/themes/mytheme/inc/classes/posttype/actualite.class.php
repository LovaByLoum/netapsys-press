<?php
/**
 * classe de service pour le type de post actualite
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

class CActualite {

  private static $_elements;
	
  public function __construct() {
    
  }
  
  /**
   * fonction qui prend les informations par son Id.
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
	
    if( $p->post_type == POST_TYPE_ACTUALITE ){
      $element = new stdClass();

      //champs wp
      $element->id =  $p->ID;
      $element->titre =  $p->post_title;
      $element->extrait =  !empty($p->post_excerpt) ? wp_limite_text($p->post_excerpt, 300) : wp_limite_text(  strip_tags( strip_shortcodes( $p->post_content ) ) , 300 );
      $element->description = $p->post_content;
      $element->comment_count =  $p->comment_count;

      if ( $tbid = get_post_thumbnail_id($p->ID) ){
        list($element->image) = wp_get_attachment_image_src($tbid);
      }else{
        $element->image = get_template_directory_uri() . '/images/default.jpg';
      }
      $element->date = get_the_date('c', $p);
      //...

      //terms
      $element->type_actus = wp_get_post_terms($p->ID, TAXONOMY_TYPE_ACTUS);
      //...

      //champs personnalisés
      if ( class_exists('Acf') ){
        $element->lieu = get_field(FIELD_ACTUALITE_LIEU, $p->ID);
        //...
      }else{
        $element->lieu = get_post_meta( $p->ID, FIELD_ACTUALITE_LIEU, true );
        //...
      }


   	  //stocker dans le tableau statique
	    self::$_elements[$pid] = $element;
    }
  }
  
  /**
   * fonction qui retourne une liste filtrée
   * 
   */
  public static function getBy( $limit = -1, $sorting = null, $data_filters = array(), $tax_filters = array(), $meta_filters  = array() ) {
    $args = array(
      'post_type' => POST_TYPE_ACTUALITE,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'paged' => get_query_var('paged'),
      'order'=> isset($sorting['order']) ? $sorting['order'] : 'DESC',
      'orderby' => isset($sorting['orderby']) ? $sorting['orderby'] : 'date',
      'fields' => 'ids'
    );

    if ( isset($data_filters['author']) ){
      $args['author'] = $data_filters['author'];
    }
    if ( isset($data_filters['search']) ) {
      $args['s'] = $data_filters["search"];
    }

    $args['meta_query'] = array();
    if ( isset($meta_filters['lieu']) ){
      $args['meta_query'][] =
        array(
          'key'     => FIELD_ACTUALITE_LIEU,
          'value'   => $meta_filters['lieu'],
          'compare' => 'IN',
        );
    }

    if(!empty($tax_filters)) {
      foreach ($tax_filters as $filter => $filterby) {
        if($filterby>0){
          $args['tax_query'][] = array (
            'taxonomy' => $filter,
            'field' => 'id',
            'terms' => array(intval($filterby)),
            'operator' => 'IN',
            'include_children' => true
          );
        }
      }
      $args['tax_query']['relation'] = 'AND';
    }

    $posts = query_posts($args);
    $elts = array();
    foreach ($posts as $id) {
      $elt = self::getById(intval($id));
      $elts[]=$elt;
    }
    return $elts;
  }

  //gestion de colonne BO
  public static function add_column( $columns ) {
    $columns['vignette'] = __("Vignette", "mytheme") ;
    $columns['lieu'] = __("Lieu", "mytheme") ;
    return $columns;
  }

  //gestion des valeurs des colonnes BO
  public static function manage_column($column_name, $post_id){
    $actu = self::getById($post_id);
    switch($column_name){
      case 'vignette':
        echo '<img src="' . $actu->image . '"/>';
        break;
      case 'lieu':
        echo $actu->lieu;
        break;
      default:
    }
  }

  //set you custom function

}
add_action( 'manage_edit-' . POST_TYPE_ACTUALITE . '_columns' , 'CActualite::add_column' );
add_action( 'manage_' . POST_TYPE_ACTUALITE . '_posts_custom_column', 'CActualite::manage_column', 10, 2 );
?>