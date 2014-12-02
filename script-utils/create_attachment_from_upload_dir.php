<?php
/**
 * create post attachment from files in upload dir
 */
global $wpdb;

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] ;
$file_url = $upload_dir['baseurl'] ;
$dossier = @opendir($file_path);


$extension_autorised = '[a-z]+';

//change this to retrieve some extension
$extension_autorised = 'jpg|png';

while (($Fichier = @readdir($dossier))!==false)
{
    if (is_file($file_path.'/'.$Fichier) && $Fichier != "." && $Fichier != ".." && preg_match('#(.+?).(' . $extension_autorised . ')$#' , $Fichier, $matches))
    {
    	$extension = $matches[2];
    	$filename = $matches[1];
        $nomFichier = $Fichier;
        $url_fichier = $file_url. '/'. $nomFichier;
			
        //ignore thumbnail for image
        $ignore_thumbnail = false;
        if(in_array($extension,array('jpg','jpeg','png','bmp','gif','ico','png','tif','dib','jpe','jfif')) && preg_match("#-([0-9]+)x([0-9]+).".$extension. "$#",$nomFichier,$matches)){
        	$ignore_thumbnail = true;
        }
        if(check_attachment($url_fichier) || $ignore_thumbnail){
        	//do nothing	
        }else{
        	$wp_type = wp_check_filetype_and_ext($file_path.'/'.$Fichier,$nomFichier);
        	$object = array(
				'post_title' => basename($filename),
				'post_mime_type' => $wp_type['type'],
				'guid' => $url_fichier
			);

			// Save the data
			$postID = wp_insert_attachment($object, $nomFichier);
        	echo 'Attachment created <a href="media.php?attachment_id=' .$postID . '&action=edit">' .$url_fichier .'</a><br>';
        }
    }
}
@closedir($dossier);
echo 'done.';

function check_attachment($url){
	global $wpdb;
	
	//rechercher par url
	$query = "SELECT ID FROM `{$wpdb->posts}` WHERE guid='" . $url ."' AND post_type='attachment'";
	return $wpdb->get_var($query);
}
?>