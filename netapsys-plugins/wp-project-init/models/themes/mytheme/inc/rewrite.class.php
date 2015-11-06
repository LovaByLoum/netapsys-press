<?php
/**
 * @package LDC
 * @subpackage includes
 * @contributor JOHARY
 * @copyright 2013
 */

/**
 * Reecriture personalisée des urls
 */

class CRewrite {

  /**
   * Constructor function.
   **/

  function __construct () {} // End constructor

  /**
   * rewrite post type url
   */
  function post_type_link($permalink, $post, $leavename){
    global $wpdb;
    //article
    if($post->post_type =='post'){
      $rubriques = wp_get_post_terms($post->ID, 'rubrique');
      $rubriques = CRubrique::rearange_rubriques($rubriques);
      $rubrique = end($rubriques);
      if($rubrique && $rubrique->term_id > 0){
            if(ICL_LANGUAGE_CODE == 'fr'){
                $permalink = home_url() . '/rubrique/'.$rubrique->slug . '/'.$post->post_name .'/'.$post->ID .'/';
            }else{
                $permalink = home_url() . '/'.ICL_LANGUAGE_CODE.'/rubrique/'.$rubrique->slug . '/'.$post->post_name .'/'.$post->ID .'/';
            }

      }
    }elseif($post->post_type =='actualite'){
        //$permalink = get_term_link(ID_SOUS_RUBRIQUE_MEDIATHEQUE) . '?actusid=' . $post->ID;
        $permalink = get_term_link(ID_SOUS_RUBRIQUES_ACTUS, RUBRIQUE) . $post->post_name .'/'.$post->ID .'/';
    }elseif($post->post_type =='marque'){
    	$permalink = get_permalink(PAGE_NOSMARQUES_ID) . $post->post_name;
    }elseif($post->post_type =='metier'){
      $rubrique = wp_get_post_terms($post->ID, 'rubrique');
      if($rubrique && count($rubrique) > 2){
        $rubrique = self::trie_terme_by_parent_id($rubrique);
        $rubique_associe = end($rubrique);
        $permalink = get_term_link($rubique_associe,'rubrique') . '?metierid=' . $post->ID;
      }
    }elseif($post->post_type =='doc-telechargeable'){
        $post_date = substr($post->post_date,0, 7);
        $time = strtotime($post_date);
        $annee = date("Y",$time);
        $mois = date("n",$time);
        $tmp_exos = CInfos_reglementee::get_exos_doc($mois,$annee);
        list($annee1, $annee2) = explode('/',$tmp_exos);
        //list($doc) = wp_get_post_terms($post->ID,TAX_DOCUMENT);
        $permalink = get_term_link(ID_INFOS_REGLEMENTEE, RUBRIQUE).'?exo='.$annee2;
        /*if($doc && $doc->term_id>0){
            $permalink.='&doc='.$doc->term_id ;
        }*/
    }

    return $permalink;
  }

  /**
   * fonction qui trie le terme par parent
   */
  public static function trie_terme_by_parent_id($array_term = array()){
    $min = $array_term[0];
    foreach($array_term as $key => $row){
      if($min->parent > $row->parent){
        $tmp = $array_term[$key - 1];
        $array_term[$key -1] = $array_term[$key];
        $array_term[$key] = $tmp;
        $min = $array_term[$key];
      }
    }

    return $array_term;
  }
    /**
     * rewrite tax url
     */
    function term_link($termlink, $term, $taxonomy){
        if($term->parent == 0){
            $pages = get_posts(array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'meta_key' => '_wp_page_template',
                'meta_value' => 'page-accueil-rubrique.php',
                'rubrique' => $term->slug,
                'numberposts' => 1
            ));
            if(!empty($pages) && isset($pages[0]->ID)){
                $termlink = get_permalink($pages[0]->ID);
            }
        }
        return $termlink;
    }

    /**
   * create_custom_rewrite_rules()
   * Creates the custom rewrite rules.
   * return array $rules.
   **/

  public function create_custom_rewrite_rules() {
    global $wp_rewrite, $wpdb;

    $url = $_SERVER['REQUEST_URI'];
    $rubrique_pattern = "#/rubrique/([A-Za-z0-9-_%]+)/([A-Za-z0-9-_%]+)/([0-9]+)/\/?#i";
    $rubrique_niveau2_pattern = "#/rubrique/([A-Za-z0-9-_%]+)/([A-Za-z0-9-_%]+)/([A-Za-z0-9-_%]+)/([0-9]+)/\/?#i";
    $rubrique_niveau2_paginate_pattern = "#/rubrique/([A-Za-z0-9-_%]+)/([A-Za-z0-9-_%]+)/page/([0-9]+)/\/?#i";
    $page_pattern = "#/([A-Za-z0-9-_%]+)/([A-Za-z0-9-_%]+)/\/?#i";
    //sous rubrique pattern
    if (preg_match($rubrique_pattern, $url, $matches)){
      $rubrique = get_term_by('slug',$matches[1],'rubrique');
      $rubriquename =  $matches[1];
      $postname =  $matches[2];

      $_GET['rubrique'] = $rubrique->slug;

      // Add the rewrite tokens
      $rewritepostname = '%postname%';
      $wp_rewrite->add_rewrite_tag( $rewritepostname, '(.*?)', 'postname=' );
      $rewrite_keywords_structure = $wp_rewrite->root . 'rubrique/%rubrique%/%postname%/';
      $new_rule = $wp_rewrite->generate_rewrite_rules( $rewrite_keywords_structure );
      $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;
    }elseif (preg_match($rubrique_niveau2_paginate_pattern, $url, $matches)){
      $rubrique = get_term_by('slug',$matches[2],'rubrique');
      $rubriquename =  $matches[2];
      $page =  $matches[3];

      $_GET['rubrique'] = $rubrique->slug;
      $_GET['page'] = $page;

      // Add the rewrite tokens
      $rewritepage = '%page%';
      $wp_rewrite->add_rewrite_tag( $rewritepage, '(.*?)', 'page=' );
      $rewriterubrique = '%rubrique%';
      $wp_rewrite->add_rewrite_tag( $rewriterubrique, '(.*?)', 'rubrique=' );
      $rewrite_keywords_structure = $wp_rewrite->root . 'rubrique/.*?/%rubrique%/page/%page%/';
      $new_rule = $wp_rewrite->generate_rewrite_rules( $rewrite_keywords_structure );
      $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;

    }elseif (preg_match($rubrique_niveau2_pattern, $url, $matches)){
        $rubrique = get_term_by('slug',$matches[2],'rubrique');
        $rubriquename =  $matches[2];
        $postname =  $matches[3];

        $_GET['rubrique'] = $rubrique->slug;

        // Add the rewrite tokens
        $rewritepostname = '%postname%';
        $wp_rewrite->add_rewrite_tag( $rewritepostname, '(.*?)', 'postname=' );
        $rewrite_keywords_structure = $wp_rewrite->root . 'rubrique/%rubrique%/%postname%/%page%/';
        $new_rule = $wp_rewrite->generate_rewrite_rules( $rewrite_keywords_structure );
        $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;
    }elseif (preg_match($page_pattern, $url, $matches)){
      $page_id = get_page_by_slug($matches[1]) ;
      $postname =  $matches[2];
      $_GET['page_id'] = $page_id;

      // Add the rewrite tokens
      $rewritepostname = '%marquename%';
      $wp_rewrite->add_rewrite_tag( $rewritepostname, '(.*?)', 'marquename=' );
      $rewrite_keywords_structure = $wp_rewrite->root . '%pagename%/%marquename%/';
      $new_rule = $wp_rewrite->generate_rewrite_rules( $rewrite_keywords_structure );
      $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;
    }

    return $wp_rewrite->rules;


  } // End create_custom_rewrite_rules()

  /**
   * add_custom_page_variables()
   * Add the custom token as an allowed query variable.
   * return array $public_query_vars.
   **/

  public function add_custom_page_variables( $public_query_vars ) {
    $public_query_vars[] = 'postname';
    $public_query_vars[] = 'marquetname';
    $public_query_vars[] = 'paged';
    return $public_query_vars;
  } // End add_custom_page_variables()

  /**
   * flush_rewrite_rules()
   * Flush the rewrite rules, which forces the regeneration with new rules.
   * return void.
   **/

  public function flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
  } // End flush_rewrite_rules()


} // End Class

// Instantiate class.
$oRewrite = new CRewrite();
add_filter('post_type_link', array(&$oRewrite,'post_type_link'),10,3);
add_filter('post_link', array(&$oRewrite,'post_type_link'),10,3);
add_filter('term_link', array(&$oRewrite,'term_link'),10,3);
add_action( 'init', array(&$oRewrite, 'flush_rewrite_rules') );
add_action( 'generate_rewrite_rules', array(&$oRewrite, 'create_custom_rewrite_rules') );
add_filter( 'query_vars', array(&$oRewrite, 'add_custom_page_variables') );
?>