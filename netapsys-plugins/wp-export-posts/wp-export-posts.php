<?php
/*
  Plugin Name: WP Export posts
  Description: Exportation de la liste du contenu des posts types
  Author: Fitiavana (Netapsys)
  Version: 0.1
*/

define('WPEP_PLUGIN_DIR', dirname(__FILE__));
define('WPEP_PLUGIN_URL', plugins_url( '', __FILE__ ));
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once 'Classes/PHPExcel.php';

add_action('admin_menu', 'wp_export_posts_page');
function wp_export_posts_page(){
  add_submenu_page('options-general.php',
    'Ajout bouton export',
    'Ajout bouton export',
    'manage_options',
    'wp-export-posts',
    'export_admin_page');
}

function export_admin_page(){
  include('wp-export-admin-page.php');
}

add_action('load-edit.php','wpep_admin_init');
function wpep_admin_init(){
  wpep_get_results_export();
}

add_action('restrict_manage_posts','wpep_restrict_manage_posts',50);
function wpep_restrict_manage_posts(){
  global $current_screen;
  $wp_export_options = get_option('wp_export_options');
  if(in_array($current_screen->post_type, $wp_export_options['enable'])){
    echo '
    	<div id="wpep_box" class="postbox closed" style="float:right;">
			<div class="handlediv" title="Cliquer pour inverser." onclick="jQuery(this).parent(\'.postbox\').toggleClass(\'closed\');"><br></div>
			<h3 class="hndle" style="padding: 8px;"><span>Export</span></h3>
			<div class="inside">' . wpep_export_box() . '</div>
		</div>';
  }
}

function wpep_export_box(){
	global $current_screen;
	$formatcolumn = apply_filters('wpep_get_format_column_'.$current_screen->post_type, array("all" => "Toutes les colonnes"));
	$html= '<label style="float: left;padding: 5px;min-width: 100px;">Lignes à exporter :</label>
			<select name="wpep_type">
    			<option value="1">Résultats de la recherche (Toutes les pages)</option>
    			<option value="2">Résultats de la recherche (La page courante)</option>
    			<option value="3">Toutes les entrées</option>
    		</select><br>
    		<label style="float: left;padding: 5px;min-width: 100px;clear:both;">Format colonne:</label>
    		<select name="wpep_format">';
    		
			$fchtml  = "";	
			foreach ($formatcolumn as $id => $label) {
				$fchtml	.= '<option value="' .$id .'">'.$label.'</option>';
			}		
	$html.=$fchtml;
    $html.='</select><br>
    		<div style="margin: 32px auto 0;display: block;width: 215px;">
    			<input type="submit" name="wpep_export_post" class="button-primary" value="Exporter en .csv" >
    			<input type="submit" name="wpep_export_post_txt" class="button-primary" value="Exporter en .txt" >
    		</div>';
    return $html;
}

if(is_admin())
	add_action('the_posts', 'wpep_the_posts');
function wpep_the_posts($p){
  global $wp_query,$current_user,$jcpt_options,$wpdb;

  if($wp_query->is_main_query()){
    if(!empty($wp_query->request)){
      session_start();

      //Compatibilite jcpt
      if(is_plugin_active('jpress-create-post-table/jpress-create-post-table.php')){

        if(is_null($jcpt_options))
          $jcpt_options = get_option('jcpt_options');

        $q = $wp_query->request;
        preg_match("/post_type = '(.*?)'/",$q,$matches);
        if(in_array($matches[1],$jcpt_options['enable'])){
          $q = str_replace($wpdb->prefix.'posts',$wpdb->prefix.$matches[1].'s',$q);
          $q = str_replace('AND 0 = 1','',$q);
          $regex = "/SELECT (.*?) FROM (.*?)  (INNER JOIN) (.*?) (ON) (.*?) WHERE (.*?) AND \((.*?)\) AND (.*?) GROUP BY (.*?) ORDER BY (.*?) (LIMIT) (.*?)$/";
          preg_match($regex,$q,$orderby);
          if((isset($_REQUEST['orderby'])  && !empty($_REQUEST['orderby'])) && (isset($orderby[11]) && !empty($orderby[11]))){
            $meta_key = jcpt_get_column_by_metaname($matches[1],$_REQUEST['orderby']);
            $q = preg_replace($regex,"SELECT $1 FROM $2 WHERE ($8) GROUP BY $10 ORDER BY " . $meta_key . " " . ((isset($_REQUEST['order']) && !empty($_REQUEST['order'])) ? $_REQUEST['order'] : "ASC"). " $12 $13",$q);
          }
          $wp_query->request = $q;
        }
      }
    }

    $_SESSION['tmp_sql'] = $wp_query->request;
  }

  return $p;
}

/**
 * set cache
 */
function wpep_set_cache($username,$somecontent){
  $path_log = dirname(__FILE__).'/cache';
  if(!file_exists($path_log)){
    mkdir($path_log);
  }
  $path_log.= '/'.$username.'.cache';
  if (!$handle = fopen($path_log, 'w')) {
    return false;
  }
  if (fwrite($handle, $somecontent) === FALSE) {
    return false;
  }
  fclose($handle);
  return true;
}

/**
 * get cache
 */
function wpep_get_cache($username){
  $path_log = dirname(__FILE__).'/cache';
  if(!file_exists($path_log)){
    return false;
  }
  $path_log.= '/'.$username.'.cache';
  if (!$handle = fopen($path_log, 'r')) {
    return false;
  }
  $contents = fread($handle, filesize($path_log));
  fclose($handle);
  return $contents;
}


function wpep_get_results_export($tp = null){
  global $wpdb, $current_screen;

  if($current_screen){
    $tp = ($current_screen->post_type)?$current_screen->post_type:$current_screen->id;
  }

  do_action('wpep_before_export_'.$tp,$_REQUEST['wpep_format']);

  session_start();
  if(isset($_GET) && (isset($_GET['wpep_export_post']) || isset($_GET['wpep_export_post_txt']))){
    ini_set("memory_limit","512M");
    if(isset($_SESSION) && isset($_SESSION['tmp_sql']) && !empty($_SESSION['tmp_sql'])){
      $query = $_SESSION['tmp_sql'];
      $reg1 = "/SELECT(.*?)FROM/";
      $reg2 = "/LIMIT(.*?)$/";
      switch ($_GET['wpep_type']){
      	case 1:
      		$query = preg_replace($reg1,"SELECT {$wpdb->prefix}posts.* FROM",$query);
      		$query = preg_replace($reg2,"",$query);
      		break;
      	case 2:
      		$query = preg_replace($reg1,"SELECT {$wpdb->prefix}posts.* FROM",$query);
      		break;
      	case 3:
      	default:
      		$query = "SELECT {$wpdb->prefix}posts.* FROM {$wpdb->prefix}posts  WHERE {$wpdb->prefix}posts.post_type = '" .$current_screen->post_type . "' AND ({$wpdb->prefix}posts.post_status = 'publish' OR {$wpdb->prefix}posts.post_status = 'future' OR {$wpdb->prefix}posts.post_status = 'draft' OR {$wpdb->prefix}posts.post_status = 'pending' OR {$wpdb->prefix}posts.post_status = 'private')  ORDER BY {$wpdb->prefix}posts.post_date DESC";
      		break;
      }
      // Compatibilité avec le plugin jPress Create Post Table.
      $active_plugin = is_plugin_active('jpress-create-post-table/jpress-create-post-table.php');
      if($active_plugin){
        $jcpt_options = get_option('jcpt_options',array());
        if((isset($jcpt_options) && !empty($jcpt_options)) && (in_array($_REQUEST['post_type'],$jcpt_options['enable']))){
          $table =  $wpdb->prefix . $_REQUEST['post_type'] . 's';
          $query = str_replace($wpdb->prefix . "posts",$table,$query);
        }
      }

      $query = apply_filters('jcpt_query_' . $tp, $query);

      $results = $wpdb->get_results($query,ARRAY_A);
      wpep_vide_file();
      $filename = apply_filters('wpep_filename_'.$current_screen->post_type, 'liste_' . $current_screen->post_type , $_REQUEST['wpep_format']);
      if(isset($_GET['wpep_export_post'])){
      	wpep_create_xsl($results,$filename);
      }elseif(isset($_GET['wpep_export_post_txt'])){
      	wpep_create_txt($results,$filename);
      }
    }
  }
}
function wpep_vide_file(){
  //vider
  $tmp_dir = WPEP_PLUGIN_DIR . "/tmp/";
  $files = glob( $tmp_dir . '/*.*' );
  if( is_array( $files ) ) {
    foreach ( $files as $file ) {
      if( is_file( $file ) ){
        @unlink($file);
      }
    }
  }
}

function wpep_create_xsl($txtDataArray, $filename = "export", $tp = null) {
  global $current_screen;

  if(count($txtDataArray) <= 0)
    return;

  if($current_screen){
    $tp = ($current_screen->post_type)?$current_screen->post_type:$current_screen->id;
  }

  $txtDataHead = apply_filters('wpep_data_head_txt_' . $tp, array_keys($txtDataArray[0]) , count($txtDataArray));

  header('Content-Type: application/csv;charset=UTF-8');
  header('Content-Disposition: attachement; filename="'.$filename.'.csv";');
  $f = fopen('php://output', 'w');

  if(count($txtDataHead) >=1 ){
    $header = array_map('utf8_decode',$txtDataHead);
    fputcsv($f, $header, ";");
  }

  if(!empty($txtDataArray)){
      foreach ($txtDataArray as $rowdata) {
        $rowdata = apply_filters('wpep_data_txt_' . $tp, $rowdata);
        if(isset($rowdata[0]) && is_array($rowdata[0])){
          foreach($rowdata as $data){
            $data = array_map('utf8_decode',$data);
            fputcsv($f, $data, ";");
          }
        }else{
          $rowdata = array_map('utf8_decode',$rowdata);
          fputcsv($f, $rowdata, ";");
        }
      }
  }else{
      fputcsv($f, 'Aucun résultat', ";");
  }

  fclose($f);
  die();
}

function wpep_create_csv($data){
  //use the stream capture to get the CSV formatted data, since we need to mess with the encoding later
  ob_start();

  $fp = fopen("php://output",'w');

  //use fputcsv instead of reinventing the wheel:
  foreach($data as $csvRow){
    fputcsv($fp, $csvRow, chr(9));
  }

  fclose($fp);

  $str = ob_get_contents();
  ob_end_clean();

  //Properly encode the CSV so Excel can open it: Credit for this goes to someone called Eugene Murai
  $str = chr(255).chr(254).mb_convert_encoding( $str, 'UTF-16LE', 'UTF-8');
  return $str;
}

/*function wpep_create_xsl($xlsDataArray,$file_name ="liste",$tp=null){
  global $current_screen;
  if($current_screen){
  	$tp = ($current_screen->post_type)?$current_screen->post_type:$current_screen->id;
  }
  $objPHPExcel = new PHPExcel();
  $empty_results_value = "Aucun enregistrement";
  $alphas = range('A', 'Z');
  $alphas = array_merge($alphas,array('AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ'));

  $file_name = $file_name.'_'.date('Ymd_His');
  // Set document properties
  $objPHPExcel->getProperties()->setCreator("WPEP")
    ->setLastModifiedBy("Administrateur")
    ->setTitle($file_name.".xls")
    ->setSubject($file_name.".xls")
    ->setDescription($file_name.".xls")
    ->setKeywords($file_name.".xls")
    ->setCategory($file_name.".xls");

  $current_sheet = $objPHPExcel->setActiveSheetIndex(0);

  $xlsDataHead = apply_filters('wpep_data_head_xls_' . $tp, array_keys($xlsDataArray[0]),count($xlsDataArray));
  if(count($xlsDataArray)>=1){
    // Add header
    $row = 1;
    $col = 0;
    $nbr_cl = apply_filters("wpep_get_col_excel_" . $tp, 'A1:BZ1');
    $current_sheet->getStyle($nbr_cl)->applyFromArray(
      array(
        'font'    => array(
          'name'      => 'Arial',
          'bold'      => true,
          'italic'    => false
        )
      )
    );
    foreach ($xlsDataHead as $head) {
      $current_sheet->setCellValueByColumnAndRow($col++, $row, $head);
    }

    //data
    $row = 2;
    $col = 0;


    foreach ($xlsDataArray as $rowdata) {
      $rowdata = apply_filters('wpep_data_xls_' . $tp, $rowdata);
      foreach ($rowdata as $key => $coldata) {
        if(is_array($coldata)){
          foreach ( $coldata as $subcoldata) {
            $current_sheet->setCellValueByColumnAndRow($col++, $row, get_empty_char($subcoldata));
          }
          if($key!=sizeof($rowdata)-1){
            $row++;
            $col=0;
          }
        }else{
          $current_sheet->setCellValueByColumnAndRow($col++, $row, get_empty_char($coldata));
        }
      }
      $row++;
      $col=0;
    }
  }else{
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $empty_results_value);
  }

  // Rename worksheet
  $current_sheet->setTitle('Liste');


  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $objPHPExcel->setActiveSheetIndex(0);
  foreach ($alphas as $alpha){
    $objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
  }
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $file = WPEP_PLUGIN_DIR  ."/tmp/" . $file_name. ".xls";
  $url = WPEP_PLUGIN_URL  ."/tmp/" . $file_name. ".xls";
  $objWriter->save($file);

  wp_redirect($url);
  exit;
}*/

/**/
function get_empty_char($var){
  if($var===false){
    return '';
  }else{
    return $var;
  }
}

function wpep_create_txt($txtDataArray,$file_name ="liste"){
  global $current_screen;
  //$gluetxt = "	";
  $gluetxt = ";";
  $tp = ($current_screen->post_type)?$current_screen->post_type:$current_screen->id;
  
  $empty_results_value = "Aucun enregistrement";

  //$file_name = $file_name.'_'.date('Ymd_His');
  $txt = '';
  $txtDataHead = apply_filters('wpep_data_head_txt_' . $tp, array_keys($txtDataArray[0]) , count($txtDataArray));
  if(count($txtDataHead)>=1){
    $txt = implode($gluetxt,$txtDataHead);
	$txt.="\r\n";
	
    foreach ($txtDataArray as $rowdata) {
      $rowdata = apply_filters('wpep_data_txt_' . $tp, $rowdata);
      if(is_array($rowdata)){
         $glue = '';
        foreach ( $rowdata as $key => $subrowdata) {
            if(is_array($subrowdata)){
                $txt .= implode('	',$subrowdata);
                if($key<sizeof($rowdata)){
                    $txt.="\r\n";
                }
            }else{
                $txt.= $glue.$subrowdata ;
                $glue = $gluetxt;
            }
        }
        $txt.="\r\n";
      }
    }
  }else{
    $txt = $empty_results_value;
  }

  $file = WPEP_PLUGIN_DIR  ."/tmp/" . $file_name. ".txt";
  $url = WPEP_PLUGIN_URL  ."/tmp/" . $file_name. ".txt";
  
  wpep_create_file($file,$txt);
  
  header('Content-disposition: attachment; filename='.$file_name.'.txt');
	header('Content-Type: application/force-download');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($file));
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	readfile($file);die;
	
  exit;
}

function wpep_create_file($filename, $somecontent, $openmode = "w"){
    if (!$handle = @fopen($filename, $openmode)) {
         return false;
    }
    if (@fwrite($handle, $somecontent) === FALSE) {
		return false;
    }
    @fclose($handle);
	return true;
}
