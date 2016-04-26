<?php
/**$
 * clear translations tables
 **/

global $wpdb;

$wpdb->query("DELETE FROM {$wpdb->prefix}icl_translations WHERE element_id IS NULL");
echo "delete row without element id.<br>";

$wpdb->query("UPDATE {$wpdb->prefix}icl_translations SET language_code = 'fr' WHERE language_code = '' OR language_code IS NULL");
echo "make fr row without language code.<br>";

echo 'done.';
?>