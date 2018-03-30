<?php
/**
 * gestionnaire de reecriture d'URL
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
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

    //article
    if($post->post_type == 'post'){
      $permalink = home_url() . '/article/' . $post->post_name . '/';
    }

    return $permalink;
  }

  /**
   * rewrite tax url
   */
  function term_link($termlink, $term, $taxonomy){
    //do stuff

    return $termlink;
  }


  /**
   * create_custom_rewrite_rules()
   * Creates the custom rewrite rules.
   * return array $rules.
   **/

  public function create_custom_rewrite_rules() {
    global $wp_rewrite;

    $url = $_SERVER['REQUEST_URI'];
    $article_pattern = "#/article/([A-Za-z0-9-_%]+)/\/?#i";

    if (preg_match($article_pattern, $url, $matches)){
      $postname =  $matches[1];
      $p = get_post_by_slug( $postname, 'post');
      $_GET['p'] = $p->ID;
      // Add the rewrite tokens
      $rewritepostname = '%postname%';
      $wp_rewrite->add_rewrite_tag( $rewritepostname, '(.*?)', 'postname=' );
      $rewrite_keywords_structure = $wp_rewrite->root . 'article/%postname%/';
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
    $public_query_vars[] = 'post';
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
//add_filter('post_type_link', array(&$oRewrite,'post_type_link'),10,3);
//add_filter('post_link', array(&$oRewrite,'post_type_link'),10,3);
//add_filter('term_link', array(&$oRewrite,'term_link'),10,3);
add_action( 'init', array(&$oRewrite, 'flush_rewrite_rules') );
add_action( 'generate_rewrite_rules', array(&$oRewrite, 'create_custom_rewrite_rules') );
add_filter( 'query_vars', array(&$oRewrite, 'add_custom_page_variables') );
?>