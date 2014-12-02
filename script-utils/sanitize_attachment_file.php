<?php
/**
 * renommer les noms des attachments en alphanumerique
 */

global $wpdb;
$upload_dir = wp_upload_dir();
$file_url = $upload_dir['baseurl'];

$sql = "SELECT ID,guid FROM {$wpdb->prefix}posts WHERE post_type='attachment'";
$attachments = $wpdb->get_results($sql);
foreach ($attachments as $attachment) {
        $file_info = pathinfo($attachment->guid);
        $nomFichier = $file_info['basename'];
        $extension = $file_info["extension"];
        $filename = $file_info["filename"];
        $newnomFichier = sanitize_title($filename) . '.' . $extension;
        $url_fichier = $file_url. '/'. $nomFichier;

        if( $nomFichier!= $newnomFichier ){
            //update the post
            $sql = "UPDATE {$wpdb->prefix}posts SET guid = '" . $file_url .'/'. $newnomFichier . "' WHERE ID = " . $attachment->ID ;
            $wpdb->query($sql);

            //update the metas
            $sql = "SELECT meta_id FROM {$wpdb->prefix}postmeta WHERE " . $wpdb->prepare("meta_value='%s'", $url_fichier)  ;
            $ids = $wpdb->get_col($sql);
            if($ids){
                $ids = implode(',', $ids);
                $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '" . $file_url .'/'. $newnomFichier . "' WHERE meta_id IN ( " . $ids .")";
                $wpdb->query($sql);
            }
            $sql = "SELECT meta_id FROM {$wpdb->prefix}postmeta WHERE " . $wpdb->prepare("meta_value='%s'", $nomFichier)  ;
            $ids = $wpdb->get_col($sql);
            if($ids){
                $ids = implode(',', $ids);
                $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '" . $newnomFichier . "' WHERE meta_id IN ( " . $ids .")";
                $wpdb->query($sql);
            }

            echo 'Renomination réussie de ' . $nomFichier .' en <a href="media.php?attachment_id=' .$attachment->ID . '&action=edit">' .$newnomFichier .'</a><br>';
        }
}
echo 'done.';
?>
