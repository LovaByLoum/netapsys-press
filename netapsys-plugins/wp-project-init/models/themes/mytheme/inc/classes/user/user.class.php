<?php 
class user {
  const CIVILITE = 'user_civilite';
  const ANNEE_NAISSANCE = 'user_annee_naissance';
  const ADRESSE = 'user_adresse';
  const VILLE = 'user_ville';
  const CODEPOSTAL = 'user_codepostal';
  const NEWSLETTER = 'user_newsletter';
  const PSEUDO = 'user_pseudo';

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
  private static function _load($uid) {
    global $wpdb;
    $uid = intval($uid);
    $user = get_user_by('id',$uid);
    $post_posted = get_usernumposts($uid);
    $comment_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS total FROM $wpdb->comments WHERE comment_approved = 1 AND user_id = %s", $uid ) );
    $post_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS total FROM $wpdb->posts WHERE post_status= 'publish'  AND post_author = %s", $uid ) );
    
    //les amis et leur nombres que l'utilisateur connectè à accepte
    $array_amis =  array_unique((maybe_unserialize(get_user_meta($uid,USER_AMI,true)))? maybe_unserialize(get_user_meta($uid,USER_AMI,true)):array() );
    $nb_ami = sizeof($array_amis);
    
    //les astuces et leur nombres mis en favori par l'utilisateur connectè
    $post_fav_id = get_user_meta($uid, ASTUCE_FAVORIE);
    $post_fav = count($post_fav_id);
      
    $element = new stdClass();
    //traitement des données
    $element->civilite    =   get_user_meta($uid,self::CIVILITE,true);
    $element->nom         =   get_user_meta($uid,'first_name',true);
    $element->prenom      =   get_user_meta($uid,'last_name',true);
    $element->annee       =   get_user_meta($uid,self::ANNEE_NAISSANCE,true);
    $element->adresse     =   get_user_meta($uid,self::ADRESSE,true);
    $element->ville       =   get_user_meta($uid,self::VILLE,true);
    $element->cp          =   get_user_meta($uid,self::CODEPOSTAL,true);
    $element->optin       =   get_user_meta($uid,self::NEWSLETTER,true);
    $element->display_pseudo  =   get_user_meta($uid,self::PSEUDO,true);
    $element->email       =   $user->data->user_email;
    $element->pseudo      =   $user->data->user_login;
    $element->user_pass   =   $user->data->user_pass;
    $element->id          =   $user->data->ID;
    $element->role        =   $user->roles[0];
    $element->register    =   date("d", strtotime($user->data->user_registered)) . "/" . date("m", strtotime($user->data->user_registered)) . "/" . date("Y", strtotime($user->data->user_registered));
    $element->post_posted =   $post_posted;
    $element->nb_comment  =   $comment_count;
    $element->nb_posted   =   $post_count;
    $element->nb_favorie  =   $post_fav;
    $element->nb_ami      =   $nb_ami;
    $element->array_amis  =   $array_amis;
    $element->array_fav   =   $post_fav_id;
    $element->comment_attached   = self::getItemsComment($user->ID, $wpdb);
    $element->expert      =   self::getUserType($user->data->ID);
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
  
  // ajout de meta pour un utilisateur
  public static function update_user_metas($uid, $metas=array(), $update = false){
    if(is_array($metas) && !empty($metas)){
      foreach ( $metas as $key => $value) {
        if ($update){
          update_user_meta($uid, $key,$value);
        }else{
          add_user_meta($uid, $key,$value);
        }
      }
      return TRUE ;
    }else {return FALSE;}
  }
  
  //lister les utilisateur selon leur filtres 
  public function getItemsCallback($offset, $limit, $filters,$sorting, $extras){

    global $wpdb;
    $args = array(
      'offset' => $offset,
      'number' => $limit,
      'order'=> (is_null($sorting['order']))?'ASC':$sorting['order'],
			'orderby' => (is_null($sorting['orderby']))?'registered':$sorting['orderby'],
      'fields'  => 'ids'
    );

    //demande d'amis
    if(isset($extras['notification']) && $extras['notification']){
	    if(isset($extras['demande_ami']) && is_array($extras['demande_ami'])){
	      if(empty($extras['demande_ami'])){
	        return array('posts' => array() ,'count' => 0);
	      }else{
	        $args['include'] = $extras['demande_ami'];
	      }
	    }else{
	    	return array('posts' => array() ,'count' => 0);
	    }
    }

     //filtre ami
    if(isset($extras['list_ami']) && is_array($extras['list_ami'])){
      if(empty($extras['list_ami'])){
        return array('posts' => array() ,'count' => 0);
      }else{
        $args['include'] = $extras['list_ami'];
      }
    }

    //tester les categories astuce 
    if(!empty($filters) && isset($filters)){
      $param = $filters['categorie_astuce'];
       if($param){
         
         $sort = ($sorting['order'])?'ASC':$sorting['order'];
         //query pour les utilisateurs avec les filtres
         $users = $wpdb->get_results($sql="SELECT  DISTINCT p.post_author  FROM {$wpdb->prefix}posts AS p 
            INNER JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID 
            INNER JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
            INNER JOIN {$wpdb->prefix}terms t ON  tr.term_taxonomy_id = t.term_id
            WHERE t.term_id = {$param} AND p.post_type='astuce' ORDER BY u.user_registered {$sort} LIMIT {$offset} , {$limit} ");
         foreach ($users as $user) {
           $users_id[] = $user->post_author;
           }
           //query pour les nombres des utilisateurs en totalité
         $nb = $wpdb->get_var("SELECT  COUNT(DISTINCT p.post_author)   FROM {$wpdb->prefix}posts AS p 
            INNER JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID 
            INNER JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
            INNER JOIN {$wpdb->prefix}terms t ON  tr.term_taxonomy_id = t.term_id
            WHERE t.term_id = {$param} AND p.post_type='astuce'");

         return array('posts'=>$users_id , 'count'=>$nb);
       }
       $users = new WP_User_Query($args);
       return array('posts' => $users->results ,'count' =>$users->total_users);
    }else{
    $users = new WP_User_Query($args);
    return array('posts' => $users->results ,'count' =>$users->total_users);
    }
  }
  
  // le rendu de chaque utilisateur
  public static function renderItemCallback( $uid ){
    $favorie = '';
    $user = self::getById($uid);
    $id_template = wp_get_post_by_template('page-profil.php');
    $user_connecte_id = get_current_user_id();
    $id_amis = get_user_meta($user_connecte_id, 'ami_id');
    $class = CCategorieAstuce::getClassBySlug($user->expert[3]);
    $pseudo = (!empty($user->display_pseudo)?$user->display_pseudo:$user->nom.' '.$user->prenom);
    if(is_user_logged_in()){
      if($user_connecte_id != $uid && user::is_ami($uid, $user_connecte_id)){
        $favorie = '<p class="ami-accepte">Ami(e)</p>';
      }elseif($user_connecte_id != $uid && !user::demande_ami_send($uid, $user_connecte_id) && !user::demande_ami_send($user_connecte_id, $uid ) && !user::is_ami($uid, $user_connecte_id)){
        $favorie = '<a href="javascript:;" class="demande-ami" data-user_id="'.$user_connecte_id.'" data-ami_id="'.$uid.'" ></a>';
      }elseif($user_connecte_id != $uid && user::demande_ami_send($uid, $user_connecte_id)){
        $favorie = '<p class="demande-envoye">Demande d\'ami envoyé</p>';
      }elseif($user_connecte_id != $uid && user::demande_ami_send($user_connecte_id, $uid )){
        $favorie = '<a href="javascript:;" class="response ajout-ami" data-user_id="'.$user_connecte_id.'" data-ami_id="'.$uid.'" >Accepter</a><br><a href="javascript:;" class="response refu-ami" data-user_id="'.$user_connecte_id.'" data-ami_id="'.$uid.'" >Refuser</a>';
      }
    }
    
    //reinitialisation du badge
    $badge = '' ;
    //tester si le nombre d'astuce poster est superieur à la limite
    if($user->nb_posted > NUMBER_POSTED_BADGE){
      $badge = '<span class="badge" style="display:inline;"></span>';
    }
      $html = '<li style="margin-bottom: 15px;" class="un-profil '.$class.' '.$user->expert[2].'">
                  <div class="profil-container">
                    <div class="img-profil"><a href="'.  get_permalink($id_template).'?uid='.$user->id.'" title = "'.$pseudo.'">'.  get_avatar($uid,80).'</a><div class="picto-categorie"></div>
                    </div>
                    <div class="profil-rest">
                      <div class="profil-top">
                        <div class="left">
                          <div class="left-cont">
                            <a style="color: #000;font-weight: bold;" href="'.  get_permalink($id_template).'?uid='.$user->id.'" title = "'.$pseudo.'"><h3>'.$pseudo.'</h3></a> -
                            <span class="expert">'.self::getQualityUser($user->expert[1]).'</span>
                            <span class="categorie">'.$user->expert[0].'</span>
                            '.$badge.'  
                          </div>
                          <span class="inscrit-le">Inscrit(e) le '.$user->register.'</span>
                        </div>
                        <div class="right">'.$favorie.'</div>
                      </div>
                      <div class="profil-bottom">
                        <div class="info-profil articles">
                          <span class="nombre">'.$user->nb_posted.'</span>
                          <span class="info">artilces écrits</span>
                        </div>
                        <div class="info-profil commentaires">
                          <span class="nombre">'.$user->nb_comment.'</span>
                          <span class="info">commentaires</span>
                        </div>
                        <div class="info-profil amis">
                          <span class="nombre">'.  $user->nb_ami.'</span>
                          <span class="info">amis</span>
                        </div>
                        <div class="info-profil favoris">
                          <span class="nombre">'.$user->nb_favorie.'</span>
                          <span class="info">favoris</span>
                        </div>
                        <div class="info-profil nombre-w">
                          <span class="nombre">'.$user->expert[1].'</span>
                          <span class="pts">
                            pts
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>';
    return $html;
  }
  
  //fonction qui sert à trouver le titre et le date des commentaires de l'utilisateur
  public static function getTitleCommentAndDate($uid){
   $comments_data = get_commentdata($uid);
   $date = $comments_data['comment_date'];
    $date = date("d", strtotime($date)) . "/" . date("m", strtotime($date)) . "/" . date("Y", strtotime($date)) . " " . date("H", strtotime($date)).":". date("i", strtotime($date));
   $pid = intval($comments_data['comment_post_ID']);
   $post_comment = get_post($pid);
   $title = $post_comment->post_title;
   return array('title' => $title, 'date' => $date);
  }
  
  //trouver les commentaires que l'utilisateur à saisie
  public static function getItemsComment($uid){
    global $wpdb;
    $results = $wpdb->get_col( $wpdb->prepare(  "SELECT * FROM $wpdb->comments WHERE comment_approved = '1' AND user_id = %s", $uid ) );
    return $results;
  }
  
  //trouver les informations des utilisateurs
   public static function getByArrayUserId($array){
    foreach ($array as $key ) {
      $users[] = self::getById($key);
    }
    return $users;
  }
  
  //fonction qui gzre l'astuce
  public static function addVoteAstucePoint($vote, $categorie, $auteur){
    //tester s'il existe un valeur ppour l'auteur du post
    if($point_old = get_user_meta($auteur, POINT_AUTHOR.$categorie,true)){
      $point = $point_old + $vote;
      update_user_meta($auteur, POINT_AUTHOR.$categorie, $point);
      $response = true;
    }else{
      add_user_meta($auteur, POINT_AUTHOR.$categorie, $vote);
      $response = true;
    }
    return $response;
  }
  
  //fonction qui gere les astuces voter
  public static function addVoteAstucePost($uid,$post ){
    //tester si l'utilisateur recent a deja voter
      add_user_meta($uid, ID_POST_VOTER, $post);
      $response = true;
    return $response;
  }
  
  //fonction qui gere le badge user par catégories
  public static function getBadgeCat($values){
    switch (true){
      case  (0 <= $values) && ($values < BADGE_BRONZE):
        $badge = 'level-0';
        break;
      case  (BADGE_BRONZE <= $values) && ($values  < BADGE_ARGENT) :
        $badge = 'level-1';
        break;
      case  (BADGE_ARGENT <= $values) && ($values < BADGE_OR) :
        $badge = 'level-2';
        break;
      case  BADGE_OR <= $values:
        $badge = 'level-3';
        break;
      default :
        $badge = 'level-0';
        break;
    }
    return $badge;
  }
  
  //fonction qui gere la qualification d'un utilisateur 
  public static function getQualityUser($value){
    switch (true){
      case  0==$value:
        $qual = USER_NEW;
        break;
      case  (0 < $values) && ($values < DEBUTANT):
        $qual = USER_DEBUTANT; 
        break;
      case  (DEBUTANT <= $values) && ($values  < MOYEN) :
        $qual = USER_DEBUTANT;
        break;
      case  (MOYEN <= $values) && ($values < EXPERT) :
        $qual = USER_MOYEN;
        break;
      case  EXPERT <= $values:
        $qual = USER_EXPERT;
        break;
      default :
        $qual = '';
        break;
    }
    return $qual;
  }


  //function qui gere le type du de user
  public static function getUserType($user_id){
    $cats = CCategorieAstuce::getBy();
    foreach ($cats as  $cat) {
        $val = get_user_meta($user_id, POINT_AUTHOR.$cat->id,true);
        $el[$cat->slug] = $val;
        $name_cat[$cat->slug]  = $cat->name;   
    }
    $point_brico     =   array(($el[BRICOLAGE_SLUG])? $name_cat[BRICOLAGE_SLUG] : '', ($el[BRICOLAGE_SLUG]) ? $el[BRICOLAGE_SLUG] : 0, self::getBadgeCat($el[BRICOLAGE_SLUG]),($el[BRICOLAGE_SLUG])? BRICOLAGE_SLUG: '',);
    $point_deco      =   array(($el[DECO_SLUG])? $name_cat[DECO_SLUG]:'', ($el[DECO_SLUG]) ? $el[DECO_SLUG] : 0, self::getBadgeCat($el[DECO_SLUG]),($el[DECO_SLUG])? DECO_SLUG :'');
    $point_net       =   array(($el[NETTOYAGE_SLUG])? $name_cat[NETTOYAGE_SLUG]:'', (isset($el[NETTOYAGE_SLUG])) ? $el[NETTOYAGE_SLUG] : 0, self::getBadgeCat($el[NETTOYAGE_SLUG]),($el[NETTOYAGE_SLUG])? NETTOYAGE_SLUG : '');
    //tester les astuce par cat le plus grands valeur de vote
    if( $point_brico[1] < $point_deco[1]){
      if($point_deco[1] < $point_net[1]){
        $array_expert = $point_net;
      }else{
        $array_expert = $point_deco;
      }
    }else{
      if($point_brico[1] < $point_net[1]){
        $array_expert = $point_net;
      }else{
        $array_expert = $point_brico;
      }
    }
    return $array_expert;
  }
  
  //check is ami
  public static function is_ami($user_id, $ami_id){
    $amis =  maybe_unserialize(get_user_meta( $user_id, USER_AMI,true));
    if (is_array($amis) && in_array($ami_id,$amis)){
      return true;
    }else{
      return false;
    }
  }

  //verifie dans les demandes d'amis
  public static function demande_ami_send($user_id, $ami_id){
    $demandes_amis =  maybe_unserialize(get_user_meta( $user_id, DEMANDE_AMI,true));
    if (is_array($demandes_amis) && in_array($ami_id,$demandes_amis)){
      return true;
    }else{
      return false;
    }
  }

  //save activity
  public static function do_activite($action, $user_id, $astuce_id){
    $objet = array($action,$user_id,$astuce_id,date('d-m-Y H:i:s'));
    $activities = maybe_unserialize(get_option('user_activities'));
    if(is_array($activities)){
      array_unshift($activities, $objet);
    }else{
      $activities = array($objet);
    }

    //limit
    $activities = array_slice($activities, 0, 5);
    update_option('user_activities',$activities);
  }

//get activity
  public static function get_activite(){
    $activities = get_option('user_activities');
    return $activities;
  }
  
  //get url user
  public static function get_user_page_profil($user_id=NULL){
    $url_page = get_permalink(wp_get_post_by_template('page-profil.php'));
    if($user_id){
       $url_user = $url_page.'?uid='.$user_id;
       }else{
         $url_user = $url_page;
       }
    return $url_user;
  }
}  
?>