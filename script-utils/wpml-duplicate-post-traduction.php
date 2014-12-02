<?php


search_element_to_duplicate('post', 'page');
echo 'Page done<br>';

/**
 * retrieve base for element need to duplicate
 */
function search_element_to_duplicate($type = 'post', $typename = 'post'){
  global $sitepress, $wpdb, $iclTranslationManagement;
  if($type=='post'){
    $posts = get_posts(array(
      'post_type' => $typename,
      'post_status' => 'publish',
      'suppress_filters' => false,
      'fields'=> 'ids',
      'numberposts' => -1,
      'orderby' => 'ID',
      'order' => 'ASC',
    ));

    foreach ( $posts as $id) {
      $trid = $sitepress->get_element_trid($id, 'post_' . $typename);
      $translations = $sitepress->get_element_translations($trid, 'post_' . $typename);
      $language_details_original = $sitepress->get_element_language_details($id, 'post_' . $typename);
      $data['iclpost'] = array($id);
      foreach($sitepress->get_active_languages() as $lang => $details){
        if($lang != $language_details_original->language_code && !isset($translations[$lang]) ){
          $data['duplicate_to'][$lang] = 1;
        }else{
          unset($data['duplicate_to'][$lang]);
        }
      }
        if(isset($data['duplicate_to'])){
            echo '. duplicate post ' . $id . '<br>';
            $iclTranslationManagement->make_duplicates($data);
        }
    }
  }elseif($type='tax'){
    $terms = get_terms($typename, 'orderby=id&hide_empty=0&suppress_filters=false&fields=ids&number=-1');
    foreach ( $terms as $id) {
      $trid = $sitepress->get_element_trid($id, 'tax_' . $typename);
      $translations = $sitepress->get_element_translations($trid, 'tax_' . $typename);
      $language_details_original = $sitepress->get_element_language_details($id, 'tax_' . $typename);
      $data['iclpost'] = array($id);
      foreach($sitepress->get_active_languages() as $lang => $details){
        if($lang != $language_details_original->language_code && !isset($translations[$lang]) ){
          $data['duplicate_to'][$lang] = 1;
        }
      }
      foreach($data['iclpost'] as $term_id){
        foreach($data['duplicate_to'] as $lang => $one){
          $this->make_duplicate_term($term_id, $typename,$lang);
        }
      }
    }
    //reset hierarchy
    delete_option($typename.'_children');
  }
}

?>