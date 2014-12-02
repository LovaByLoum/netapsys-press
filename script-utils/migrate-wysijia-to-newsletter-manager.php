<?php
/**
 * permet de migrer les abonnées de wysijia vers newsletter manager
 * 
 */
global $wpdb;

$sql = "SELECT * FROM {$wpdb->prefix}wysija_user";
$r = $wpdb->get_results($sql);
foreach ( $r as $row) {
    $sql = "INSERT INTO {$wpdb->prefix}xyz_em_email_address (`id`, `email`, `create_time`, `last_update_time`) VALUES (NULL, '{$row->email}', '{$row->created_at}', '{$row->created_at}')";
    $wpdb->query($sql);
    $id = mysql_insert_id();

    //join list
    $sql = "INSERT INTO {$wpdb->prefix}xyz_em_address_list_mapping (`id`,`ea_id`,`el_id`,`create_time`,`last_update_time`,`status`) VALUES (NULL, '{$id}', '1', '{$row->created_at}', '{$row->created_at}','1')";
    $wpdb->query($sql);

    echo "new id :" . $id." -> email :" . $row->email . "<br>";
}


echo 'all done.';