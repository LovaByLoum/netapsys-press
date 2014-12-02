<?php 
/**
 * renomme les fichier upload
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
        
        if( $nomFichier!= $newnomFichier ){
        	
        	//renommage du fichier
        	rename($file_path . '/' .$Fichier , $file_path . '/' .$newnomFichier);
        	
        	echo 'Renomination réussie de ' . $nomFichier .' en ' .$newnomFichier .'<br>';
        }
    }
}
@closedir($dossier);
echo 'done.';
?>
