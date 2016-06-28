<?php
/**
 * @package jpress-job-manager
  * @contributor JOHARY
  */

/**
 * Reecriture personalisée des urls
 */

class JM_Rewrite {

    /**
     * Constructor function.
     **/

    function __construct () {} // End constructor

    /**
     * create_custom_rewrite_rules()
     * Creates the custom rewrite rules.
     * return array $rules.
     **/

    public function create_custom_rewrite_rules() {
        global $wp_rewrite;

        $url = $_SERVER['REQUEST_URI'];
        if ( $_SERVER['PROJECT_BASE_PATH'] != '/' ){
            $url = str_replace( $_SERVER['PROJECT_BASE_PATH'], '', $_SERVER['REQUEST_URI']);
        }

        $postuler_pattern = "#(.*?)/" . JM_REWRITE_SLUG_POSTULER . "/(.*?)/?$#i";
        if ( preg_match( $postuler_pattern, $url, $matches ) ){
            $slugpage = (end(explode('/', $matches[1])));
            $page_id = jpress_jm_get_post_id_by( 'name', $slugpage, 'page');
            if ($page_id > 0){
                // Add the rewrite tokens
                $rewriteoffrepostuler = '%offrepostuler%';
                $wp_rewrite->add_rewrite_tag( $rewriteoffrepostuler, '(.*?)', JM_REWRITE_QUERYVAR_OFFREPOSTULER . '=' );
                $rewrite_keywords_structure = $wp_rewrite->root . '%pagename%/' . JM_REWRITE_SLUG_POSTULER . '/' . $rewriteoffrepostuler . '/';
                $new_rule = $wp_rewrite->generate_rewrite_rules( $rewrite_keywords_structure );
                $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;

            }
        }
        return $wp_rewrite->rules;

    } // End create_custom_rewrite_rules()

    /**
     * add_custom_page_variables()
     * Add the custom token as an allowed query variable.
     * return array $public_query_vars.
     **/

    public function add_custom_page_variables( $public_query_vars ) {
        $public_query_vars[] = JM_REWRITE_QUERYVAR_OFFREPOSTULER;
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
$oRewrite = new JM_Rewrite();
add_action( 'init', array(&$oRewrite, 'flush_rewrite_rules') );
add_action( 'generate_rewrite_rules', array(&$oRewrite, 'create_custom_rewrite_rules') );
add_filter( 'query_vars', array(&$oRewrite, 'add_custom_page_variables') );
?>