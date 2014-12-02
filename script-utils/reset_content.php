<?php
/**
 * script pour reinitialiser les contenus
 */
//clean_taxonomy('category');
//clean_taxonomy('rubrique');
//clear_post();
clean_all();

function clean_taxonomy($taxonomy){
    global $wpdb;

    //get term
    $sql = "SELECT {$wpdb->prefix}terms.term_id FROM {$wpdb->prefix}terms
	INNER JOIN {$wpdb->prefix}term_taxonomy ON {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id
	WHERE {$wpdb->prefix}term_taxonomy.taxonomy = '" . $taxonomy . "'";
    $terms_ids = $wpdb->get_col($sql);
    $terms_ids = implode(',',$terms_ids);

    //delete term
    if($terms_ids){
        $sql = "DELETE FROM {$wpdb->prefix}terms WHERE term_id IN ( " . $terms_ids . ")";
        $wpdb->query($sql);
        echo 'done term ' .  $taxonomy . '<br>';
    }

    //taxonomy term
    $sql = "DELETE FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = '" . $taxonomy . "'";
    $wpdb->query($sql);
    echo 'done term taxonomy ' .  $taxonomy . '<br>';

    //icl_translations
    $sql = "DELETE FROM {$wpdb->prefix}icl_translations WHERE element_type =  'tax_" . $taxonomy . "'";
    $wpdb->query($sql);
    echo 'done icl_translations tax_' .  $taxonomy . '<br>';
    echo '--------------------------';
}

function clean_all(){
    global $wpdb;
    $sql = "SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy IN('nav_menu')";
    $terms_ids = $wpdb->get_col($sql);
    $terms_ids = implode(',',$terms_ids);

    //term
    $sql = "DELETE FROM {$wpdb->prefix}terms WHERE term_id NOT IN (" . $terms_ids . ")";
    $wpdb->query($sql);
    echo "done term <br>" ;

    //term relationships
    $sql = "DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id NOT IN (" . $terms_ids . ")";
    $wpdb->query($sql);
    echo "done term relationship <br>" ;

    //term taxonomy
    $sql = "DELETE FROM {$wpdb->prefix}term_taxonomy WHERE term_id NOT IN (" . $terms_ids . ")";
    $wpdb->query($sql);
    echo "done term taxonomy <br>" ;

    //translation relationship
    $sql = "SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE element_type IN('post_acf','post_nav_menu_item','post_page','tax_nav_menu')";
    $translation_ids = $wpdb->get_col($sql);
    $translation_ids = implode(',',$translation_ids);

    //translation
    $sql = "DELETE FROM {$wpdb->prefix}icl_translations WHERE translation_id NOT IN (" . $translation_ids . ")";
    $wpdb->query($sql);
    echo "done relation translation <br>" ;

    //post
    $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN('acf', 'edito', 'attachment', 'nav_menu_item', 'page', 'wp-types-group')";
    $ids = $wpdb->get_col($sql);
    $ids = implode(',',$ids);

    $sql = "DELETE FROM {$wpdb->prefix}posts WHERE ID NOT IN (" . $ids . ")";
    $wpdb->query($sql);
    echo "done post <br>" ;

    //post_meta
    $sql = "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id NOT IN (" . $ids . ")";
    $wpdb->query($sql);
    echo "done post meta<br>" ;
}

function clear_post(){
    global $wpdb;
    //post
    $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN('acf', 'edito','attachment', 'nav_menu_item', 'page', 'wp-types-group')";
    $ids = $wpdb->get_col($sql);
    $ids = implode(',',$ids);
	
    if ($ids){
	    $sql = "DELETE FROM {$wpdb->prefix}posts WHERE ID NOT IN (" . $ids . ")";
	    $wpdb->query($sql);
	    echo "done post <br>" ;
    }
    
    //translation relationship
    $sql = "SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE element_type IN('post_fichier')";
    $translation_ids = $wpdb->get_col($sql);
    $translation_ids = implode(',',$translation_ids);
    
    //term taxonomy
    if ($translation_ids){
	    $sql = "DELETE FROM {$wpdb->prefix}icl_translations WHERE translation_id IN (" . $translation_ids . ")";
	    $wpdb->query($sql);
	    echo "done relation translation <br>" ;
    }
}
?>