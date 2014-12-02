<?php 
/**
 * renomme les fichier en safemode
 *
 */
global $wpdb;

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] ;
$file_url = $upload_dir['baseurl'] ;
$dossier = @opendir($file_path);

$extension_autorised = '[a-z0-9]+';

//change this to retrieve some extension
//$extension_autorised = 'pdf';

while (($Fichier = @readdir($dossier))!==false)
{
    if (is_file($file_path.'/'.$Fichier) && $Fichier != "." && $Fichier != ".." && preg_match('#(.+?).(' . $extension_autorised . ')$#' , $Fichier, $matches))
    {	
    	//$nomFichier = utf8_encode($Fichier);
    	$nomFichier = $Fichier;
    	$file_info = pathinfo($nomFichier);
    	$extension = $file_info["extension"];
    	$filename = $file_info["filename"];
    	$newnomFichier = sanitize_title($filename) . '.' . $extension;
        $url_fichier = $file_url. '/'. $nomFichier;
        
        if( ($aid = check_attachment($url_fichier)) && $nomFichier!= $newnomFichier ){
        	//update the post
        	$sql = "UPDATE {$wpdb->prefix}posts SET guid = '" . $file_url .'/'. $newnomFichier . "' WHERE ID = " . $aid ;
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
        	
        	//renommage du fichier
        	rename($file_path . '/' .$Fichier , $file_path . '/' .$newnomFichier);
        	
        	echo 'Renomination réussie de ' . $nomFichier .' en <a href="media.php?attachment_id=' .$aid . '&action=edit">' .$newnomFichier .'</a><br>';
        }
    }
}
@closedir($dossier);
echo 'done.';

function check_attachment($url){
	global $wpdb;
	
	//rechercher par url
	$query = "SELECT ID FROM {$wpdb->prefix}posts WHERE " . $wpdb->prepare("guid='%s'", $url) ." AND post_type='attachment'";
	return $wpdb->get_var($query);
}
?>
