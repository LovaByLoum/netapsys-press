<?php
/*
 * delete file js
 */
function delete_js_file(){
  $files = scandir(get_template_directory() . '/js/');
  echo '<h2> Suppresion des fichiers Javascript minifier</h2><br/>';
  $i = 0; 
  foreach ($files as $file){
    if (strlen($file) > 2){
       $extension= wp_check_filetype($file, false);
       if ( $extension['ext']  == 'js'){
         $name = preg_replace( '/\.[^.]+$/', '', $file );
         if (strpos($name, 'minified')){
           unlink(get_template_directory() . '/js/' . $file);
           echo '<b>' . $file . '</b>  à été supprimer <br/>';
           $i++;
         }
       }
    }
  }
   return $i;
}

/*
 * delete file css
 */

function delete_css_file(){
  $files = scandir(get_template_directory() . '/css/');
  $files_other = scandir(get_template_directory() . '/assets/css/');
  echo '<h2> Suppresion des fichiers CSS minifier</h2><br/>';
  $i = 0; 
  foreach ($files as $file){
    if (strlen($file) > 2){
       $extension= wp_check_filetype($file, false);
       if ( $extension['ext']  == 'css'){
         $name = preg_replace( '/\.[^.]+$/', '', $file );
         if (strpos($name, 'minified')){
           unlink(get_template_directory() . '/css/' . $file);
           echo '<b>' . $file . '</b>  à été supprimer <br/>';
           $i++;
         }
       }
    }
  }
  foreach ($files_other as $file){
    if (strlen($file) > 2){
       $extension= wp_check_filetype($file, false);
       if ( $extension['ext']  == 'css'){
         $name = preg_replace( '/\.[^.]+$/', '', $file );
         if (strpos($name, 'minified')){
           unlink(get_template_directory() . '/assets/css/' . $file);
           echo '<b>' . $file . '</b>  à été supprimer <br/>';
           $i++;
         }
       }
    }
  }
   return $i;
 }
$data_css = delete_css_file();
if ($data_css != 0){
  echo '<h4>Les ' . $data_css . ' fichiers css minifier sont tous supprimés</h4>';
}else{
  echo '<h4>Il n\'y plus des fichiers css minifier</h4>';
}
$data_js = delete_js_file();
if ($data_js != 0){
  echo '<h4>Les ' . $data_js . ' fichiers javascript minifier sont tous supprimés</h4>';
}else{
  echo '<h4>Il n\'y plus des fichiers javascript minifier</h4>';
}

?>
