<?php
/**
 * modifie les ids term et term_taxonomy pour qu'on ait les mêmes ids
 */

global $wpdb;
$max_term_id = $wpdb->get_var("SELECT MAX(term_id) FROM {$wpdb->prefix}terms");
echo 'term_id max : ' . $max_term_id. '<br>';

$max_term_taxonomy_id = $wpdb->get_var("SELECT MAX(term_taxonomy_id) FROM {$wpdb->prefix}term_taxonomy");
echo 'term_taxonomy_id max : ' . $max_term_taxonomy_id. '<br>';

$max_id = max($max_term_id,$max_term_taxonomy_id);
echo 'max id : ' . $max_id . '<br>';

//selectionner les terms ayant differrent ids
$terms  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_taxonomy WHERE term_taxonomy_id <>term_id");
$id = $max_id;
foreach ( $terms as $term) {
    $id++;
    $wpdb->query("UPDATE {$wpdb->prefix}terms SET term_id = " . $id . " WHERE term_id = " . $term->term_id);
    $wpdb->query("UPDATE {$wpdb->prefix}term_taxonomy SET term_id = " . $id . ", term_taxonomy_id = " . $id ." WHERE term_taxonomy_id = " . $term->term_taxonomy_id);
    $wpdb->query("UPDATE {$wpdb->prefix}term_relationships SET term_taxonomy_id = " . $id . " WHERE term_taxonomy_id = " . $term->term_taxonomy_id);
    //wpml
    if($wpdb->get_var("SHOW TABLES LIKE '%icl_translations'")) {
        $wpdb->query("UPDATE {$wpdb->prefix}icl_translations SET element_id = " . $id . " WHERE element_id = " . $term->term_taxonomy_id . " AND element_type LIKE 'tax_%' ");
    }
    echo 'term id modified : ' . $term->term_id . ' to ' . $id . '<br>';
}
$id++;
$wpdb->query("ALTER TABLE {$wpdb->prefix}terms AUTO_INCREMENT = {$id}");
$wpdb->query("ALTER TABLE {$wpdb->prefix}term_taxonomy AUTO_INCREMENT = {$id}");
echo 'autoinc set to '  . $id .'<br>';

echo 'done.';
?>