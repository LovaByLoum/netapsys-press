<?php
/**
 * fusionner deux termes
 */
if (isset($_POST["gogo"])){
    global $wpdb;
    $id_dest = intval($_POST["id_dest"]);
    $zIds_dep = $_POST["ids_dep"];
    $ids_dep = explode(',',$zIds_dep);
    foreach ( $ids_dep as $id) {
        $id = intval(trim($id));

        $object_ids = $wpdb->get_col("SELECT {$wpdb->prefix}term_relationships.object_id FROM {$wpdb->prefix}term_relationships
                          WHERE {$wpdb->prefix}term_relationships.term_taxonomy_id = " . $id );
        $zObject_ids = implode(',', $object_ids);
        foreach ( $object_ids as $object_id) {
            $already_related = $wpdb->get_var("SELECT object_id FROM {$wpdb->prefix}term_relationships
                                          WHERE object_id = " . $object_id . "
                                          AND term_taxonomy_id = " . $id_dest);
            //delete old relation
            $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships  WHERE object_id = " . $object_id."
                                          AND term_taxonomy_id = " . $id);

            if ($already_related){
                //do nothing
                echo $object_id . ' already related to ' . $id_dest . '.<br>';
            }else{
                //insert object
                $wpdb->query("INSERT INTO {$wpdb->prefix}term_relationships (object_id,term_taxonomy_id,term_order) VALUES ( " . $object_id . " ," . $id_dest . ",0)");
                echo $object_id . ' related to ' . $id_dest . ' successfully.<br>';
            }
        }

        //clean term
        $term = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}term_taxonomy WHERE term_taxonomy_id = ".$id );
        clean_all_about_terms( $id ,$term->taxonomy);
    }

    //update count
    $count = $wpdb->get_var("SELECT COUNT(object_id) FROM {$wpdb->prefix}term_relationships
                                          WHERE term_taxonomy_id = " . $id_dest);
    $wpdb->query("UPDATE {$wpdb->prefix}term_taxonomy SET count = " . $count ." WHERE term_taxonomy_id = ". $id_dest);
    echo 'new count :' . $count . '<br>';

}


function clean_all_about_terms($ids, $taxo){
    global $wpdb;
    if (!empty($ids)){
        $wpdb->query("DELETE FROM {$wpdb->prefix}term_taxonomy WHERE term_taxonomy_id IN (". $ids .")");
        $wpdb->query("DELETE FROM {$wpdb->prefix}terms WHERE term_id IN (". $ids .")");
        $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id IN (". $ids .")");
        $wpdb->query("DELETE FROM {$wpdb->prefix}icl_translations WHERE element_id IN (". $ids .") AND element_type = 'tax_" . $taxo ."'");
        echo 'suppression '. $taxo.' : ' . $ids .'<br>';
    }
}
?>
<script>
    jQuery("#scriptform").append('<label>Id du terme de destination</label><input type="text" name="id_dest"/><label>Ids des termes à supprimer (séparez par des virgules)</label><input type="text" name="ids_dep"/><input type="hidden" name="gogo" value="1"/>');
</script>
