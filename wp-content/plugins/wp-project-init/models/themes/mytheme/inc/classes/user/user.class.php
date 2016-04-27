<?php
/**
 * classe de service pour les users
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */
class CUser {

  private static $_elements;
	
  public function __construct() {
    
  }
  
  /**
   * fonction qui prend les informations son Id. 
   * 
   * @param type $uid
   */
  public static function getById($uid) {
    $uid = intval($uid);
    
    //On essaye de charger l'element
    if(!isset(self::$_elements[$uid])) {
      self::_load($uid);
    }
    //Si on a pas réussi à chargé l'article (pas publiée?)
    if(!isset(self::$_elements[$uid])) {
      return FALSE;
    }

    return self::$_elements[$uid];
  }
  
  /**
   * fonction qui charge toutes les informations dans le variable statique $_elements.
   * 
   * @param type $uid 
   */
  private static function _load( $uid ) {
    global $wpdb;
    $uid = intval($uid);
    $user = get_user_by('id',$uid);

    $element = new stdClass();
    //traitement des données
    $element->id          =   $user->data->ID;
    $element->nom         =   $user->data->user_firstname;
    $element->prenom      =   $user->data->user_lastname;
    $element->email       =   $user->data->user_email;
    $element->pseudo      =   $user->data->user_login;
    $element->role        =   $user->roles[0];
    $element->register    =   mysql2date( get_option( 'date_format' ), $user->data->user_registered );

    //champ personnalisé
    $element->civilite    =   get_user_meta($uid, FIELD_USER_CIVILITE, true);
    $element->annee       =   get_user_meta($uid, FIELD_USER_DATE_NAISSANCE, true);
    $element->adresse     =   get_user_meta($uid, FIELD_USER_ADRESSE, true);
    $element->ville       =   get_user_meta($uid, FIELD_USER_VILLE, true);
    $element->cp          =   get_user_meta($uid, FIELD_USER_CP, true);

    //...

    //stocker dans le tableau statique
    self::$_elements[$uid] = $element;
  }
  
  // requete des utilisateur selon leur role ou par nom, email, ...
  public static function getBy( $role = "", $search = ""  ){
    $args = array(
      'role' => $role,
      'search' => $search       
    );
    $elements = get_users($args);
    $elts = array();
    foreach ($elements as $id) {
    	$elt = self::getById(intval($id->ID));
    	$elts[]=$elt;
    }
    return $elts;
  }

}  
?>