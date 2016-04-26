<?php
/**
 * rempli les translations inexistantes
 */
global $wpdb;

//max trid
$max_trid = $wpdb->get_var("SELECT MAX(trid) FROM {$wpdb->prefix}icl_translations");
echo "max trid :" . $max_trid . '<br>';

//taxonomy
$terms = $wpdb->get_results("SELECT {$wpdb->prefix}term_taxonomy.taxonomy,{$wpdb->prefix}term_taxonomy.term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy
LEFT JOIN {$wpdb->prefix}icl_translations ON {$wpdb->prefix}term_taxonomy.term_taxonomy_id = {$wpdb->prefix}icl_translations.element_id
WHERE {$wpdb->prefix}icl_translations.element_id IS NULL");

foreach ($terms as $term) {
    $max_trid++;
    $wpdb->query("INSERT INTO {$wpdb->prefix}icl_translations(element_type, element_id ,trid ,language_code ,source_language_code) VALUES ( 'tax_". $term->taxonomy . "', " . $term->term_taxonomy_id . ", " .  $max_trid . ", 'fr' , NULL)");
    echo 'term translation inserted : ' . $term->term_taxonomy_id . '<br>';
}
echo 'done.';

?>