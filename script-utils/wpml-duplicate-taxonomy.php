<?php
/**
 * permet de duliquer les termes d'un taxonomy vers une langue (wpml)
 * 
 */
global $sitepress;
if(!$sitepress){
	echo 'wpml non activé';
	exit;
}

//liste de taxonomies à dupliquer et la langu de destination
global $taxonomies, $destlang;
$taxonomies = array('rubrique');
$langues = array('en','pl','de','es');

foreach ($langues as $destlang) {
	echo '------------- DUPLICATION VERS '. $destlang . '----------------<br>';
	foreach ($taxonomies as $tax) {
		echo '------------- DUPLICATION DU TAXONOMIE '. $tax  . '----------------<br>';
		//remove some filter
		remove_filter('get_terms_orderby', 'TO_applyorderfilter', 10, 2);
		remove_filter('get_terms_orderby', 'TO_get_terms_orderby', 1, 2);
	
		$allterms = get_terms($tax,array(
			'hide_empty'=>false,
			'orderby' => 'id', 
			'order' => 'ASC',
			'hierarchical' => true
		));
		foreach ($allterms as $t) {
			echo $t->term_id.' - ' .$t->name .  '<br>';
			$the_trad_parent = icl_object_id($t->parent,$tax,true,$destlang);
			echo 'parent traduit = ' . $the_trad_parent  .'<br>';
			$the_trad_name = $t->name.' ('.$destlang.')';
			$the_trad_term = wp_insert_term($the_trad_name,$tax, array('parent'=>$the_trad_parent));
			if(isset($the_trad_term['term_id'])){
				echo 'inserer avec succès : ' . $the_trad_name .' => '. $the_trad_term['term_id'] . '<br>';
				$trid = $sitepress->get_element_trid($t->term_id, 'tax_' . $tax);
				$sitepress->set_element_language_details($the_trad_term['term_id'], 'tax_' . $tax, $trid, $destlang);
				echo 'correspondance faite : ' . $trid . '<br><br>';
			}
		}
		
		//regenerate hierarchy
		delete_option("{$tax}_children");
	}
}
echo 'all done.';