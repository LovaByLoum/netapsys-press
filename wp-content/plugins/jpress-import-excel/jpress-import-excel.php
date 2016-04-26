<?php
/*
  Plugin Name: jpress-import-excel
  Description: Importation de contenu d'un fichier excel xls vers WP
  Author: Johary
*/
require_once ( dirname(__FILE__) . '/inc/excel_reader2.php' );
if(is_file(dirname(__FILE__) . '/import/import_action.php'))
	require_once ( dirname(__FILE__) . '/import/import_action.php' );

global $wpdb,$show_file_content;
$show_file_content = true;

add_action('admin_print_scripts', 'jpress_ie_admin_scripts');
function jpress_ie_admin_scripts()
{
  wp_enqueue_script('jquery');
  $myJsFile = plugin_dir_url(__FILE__) . 'js/jquery.fancybox.pack.js';
  wp_register_script('fancybox-js', $myJsFile);
  wp_enqueue_script( 'fancybox-js');

}

add_action('admin_print_styles', 'jpress_ie_admin_styles');
function jpress_ie_admin_styles()
{
  $myCssFile = plugin_dir_url(__FILE__) . 'css/jquery.fancybox.css';
  wp_register_style('fancybox-css', $myCssFile);
  wp_enqueue_style( 'fancybox-css');
}

add_action('admin_menu', 'jpress_admin_menu', 50);
function jpress_admin_menu() {
  $aam_cap = 'publish_pages';
  add_submenu_page('options-general.php','Importer excel options','Importer excel options','manage_options','jpress-import-excel-admin','jpress_admin_page');
  
  $jie_options = get_option('jie_options');
  if($jie_options && is_array($jie_options) && !empty($jie_options) && isset($jie_options["enable"])){
  	foreach ($jie_options['enable'] as $pt) {
  		switch ($pt){
  			case 'post':
  				$pagename = 'edit.php';
  				break;
  			case 'attachment':
  				$pagename = 'upload.php';
  				break;	
  			default:
  				$pagename = 'edit.php?post_type='.$pt;
  				break;
  		}
  		add_submenu_page(
          $pagename, 'Importer excel', 'Importer excel', $aam_cap, 'import_'.$pt, 'excel_2_table_modify'
  		);		
  	}
  }
  
}
function jpress_admin_page(){
  include('admin-page.php');

}

$uploadir = wp_upload_dir();

define('GL_EXCEL_DIR', 'excel_file');
define("DEFAULT_TEMP_FILE", ABSPATH . 'wp-content'. DIRECTORY_SEPARATOR.'uploads'. DIRECTORY_SEPARATOR. GL_EXCEL_DIR);
if(!file_exists(DEFAULT_TEMP_FILE)){
	@mkdir(DEFAULT_TEMP_FILE);
	@chmod(DEFAULT_TEMP_FILE,0775);	
}

function afficheFeuille($data, $feuille_num) {
   $html = '<form method="post" action="">';
   $html .= '<h3>Choisissez une feuille</h3>';
  $html .= '<h2 class="nav-tab-wrapper">';
  $class = '';
  for($i = 0; $i <= $data->sn; $i++) {
    if($i == $feuille_num) {$class = 'nav-tab-active';}else{$class = '';}
    $html.= '<a href="javascript:;" rel ="jpress-feuille_' . $i . '" title="feuille ' . $i . '" class="nav-tab ' . $class . '" id="feuille_' . $i . '" onclick="jQuery(\'#choix-feuille\').val('. $i.');jQuery(this).parents(\'form\').submit();">Feuille N°' . $i . '</a>';
  }
  $html .= '</h2>';
  $html .= '<input type="hidden" name="choix-feuille" value="0" id="choix-feuille"/>';
  $html .= '</form>';
  return $html;
}

function TableDatasByFeuille($file, $num_feuille) {
  $data = new Spreadsheet_Excel_Reader($file);
  return $data->dump_clean($num_feuille);
}

function excel_2_table_modify() {
  global $current_screen;
  $pt = $current_screen->post_type;
  switch ($pt){
	case 'post':
		$pagename = 'edit.php';
		break;
	case 'attachment':
		$pagename = 'upload.php';
		break;	
	default:
		$pagename = 'edit.php?post_type='.$pt;
		break;
  }
  if(isset($_POST['deletecheck'])) {
    $filesToDelete = $_POST['deletecheck'];
    foreach($filesToDelete as $filename) {
      $realNameFile = DEFAULT_TEMP_FILE . DIRECTORY_SEPARATOR . $filename;
      if(file_exists($realNameFile)) {
        @unlink($realNameFile);
      }
      delete_option('jie_option_'.sanitize_title($filename));
    }
  }
  if(isset($_FILES['upload']) && !empty($_FILES['upload'])) {
    for($countFiles = 0; $countFiles < count($_FILES['upload']['name']); $countFiles++) {
      if($_FILES['upload']['name'][$countFiles] != "") {
        $uploadFileName = $_FILES['upload']['name'][$countFiles];
        $uploadFileNameExtension = substr($uploadFileName, strlen($uploadFileName) - 4, strlen($uploadFileName));
        if($uploadFileNameExtension == ".xls") {
          $excelFileDirectory = DEFAULT_TEMP_FILE . DIRECTORY_SEPARATOR . $uploadFileName;
          if(!@move_uploaded_file($_FILES['upload']['tmp_name'][$countFiles], $excelFileDirectory)) {
            echo "<p>" . $uploadFileName . ": Erreur lors du téléchargement";
          }
        }else {
          echo "<p>" . $uploadFileName . ": Format non reconnu!<p>";
        }
      }
    }
  }
  ?>
  <div class="wrap">
    <div id='icon-options-general' class='icon32'><br />
    </div>

    <h2>Importer fichier excel</h2>
    <br/>
    <br/>
    <div class="wrap" id="msgsuccess" style="display:none;">
      <div id="message" class="updated below-h2">
        <p>Importation effectuée avec succés</p>
      </div>
    </div>
  </div>
  <form id="champ-settings" enctype="multipart/form-data" action="" method="post">
    <table  class="widefat">
      <thead>
        <tr>
          <th scope="col" width="15%">Supprimer</th>
          <th scope="col" >Nom du fichier</th>
          <th scope="col" >Télécharger</th>
          <th scope="col" >Importer</th>
        </tr>
      </thead>
      <tbody>
  <?php
  $excels = glob( DEFAULT_TEMP_FILE . DIRECTORY_SEPARATOR . "*.xls");
  $uploadir = wp_upload_dir();
  if($excels) {
    $cont = 0;
    foreach($excels as $excel) {
      $nameExcel = basename($excel, ".xls");
      $linkForDownload = $uploadir['baseurl'] . DIRECTORY_SEPARATOR . GL_EXCEL_DIR . DIRECTORY_SEPARATOR . $nameExcel . '.xls';
      $class = ('alternate' != $class) ? 'alternate' : '';
      ?>
            <tr>
              <th scope="row" class="check-column">
                <?php if(strpos($nameExcel,'modele')===false):?>
                <input type="checkbox" name="deletecheck[<?php echo $cont; ?>]" value="<?php echo $nameExcel . ".xls"; ?>"/></th>
                <?php endif;?>
              <td><?php echo $nameExcel; ?></td>
              <td><a href="<?php echo $linkForDownload; ?>">Télécharger</a></td>
              <td><a href="<?php echo $pagename;?>&page=import_<?php echo $pt;?>&file=<?php echo urlencode($nameExcel); ?>">Configurer</a></td>
            </tr>
      <?php $cont++;
    } ?>
  <?php }else { ?>
          <tr id='no-id'><td scope="row" colspan="5"><em>Pas de fichiers</em></td></tr>
          <?php
        }
        ?>
      </tbody>
    </table>
    Ajouter / mettre à jour (vous pouvez également sélectionner plusieurs fichiers) <input type="file" name="upload[]" multiple>
    <br>
    <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Valider"/>
  </form>

  <?php
  if(isset($_REQUEST['file'])) {
    $nameExcel = $_REQUEST['file'];
    if(isset($_POST["import"]) && isset($_POST["choix-feuille"]) && intval($_POST["choix-feuille"])>=0) {
      $feuilleNum = intval($_POST["choix-feuille"]);
      $upload_dir = wp_upload_dir();
      $file = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . GL_EXCEL_DIR . DIRECTORY_SEPARATOR . $nameExcel . ".xls";
      global $current_screen;
      $pt = $current_screen->post_type;

      if(file_exists($file)) {
        do_action('importer_sheet_'.$pt.'_'.$feuilleNum, $feuilleNum, $file );
      }
       ?>
      <script type="text/javascript">
        jQuery(document).ready(function(){
          jQuery("#msgsuccess").show();
        });
      </script>
      <?php
    }
    ?>

    <!--script type="text/javascript">
      jQuery(document).ready(function(){
        jQuery(".nav-tab").click(function(){
          jQuery(".nav-tab").removeClass('nav-tab-active');
          jQuery(this).addClass('nav-tab-active');

          jQuery(".tab-content").addClass('hidden');
          jQuery("#" + jQuery(this).attr('rel')).removeClass('hidden');
        });
      });
    </script-->

    <?php

    if(isset($_POST["choix-feuille"]) && intval($_POST["choix-feuille"])>=0){
         $feuilleNum = intval($_POST["choix-feuille"]);
    }else{
        $feuilleNum = 0;
    }
    importForm($nameExcel,$feuilleNum);
  }
}

function importForm($nameExcel, $feuille_num){
  global $show_file_content;
  $upload_dir = wp_upload_dir();
  $filename = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . GL_EXCEL_DIR . DIRECTORY_SEPARATOR . $nameExcel . ".xls";
  if(file_exists($filename)):
  $data = new Spreadsheet_Excel_Reader($filename);
  echo afficheFeuille($data, $feuille_num);
  $i = $feuille_num;
  
  $is_a_step = (get_option(sanitize_title($nameExcel. '_sheet_' . $feuille_num)))?true:false;
  
  if($feuille_num>=0 ):
    ?>
    <script type="text/javascript">
    	jQuery(document).ready(function(){
    		jQuery("#saveconfigcolumn").click(function(){
    			var select, glue;
    			glue = "";
    			select="";
    			for ($i=0;$i<jQuery(".columnconfig").length;$i++){
    				select+=glue+jQuery(".columnconfig").eq($i).val();
    				glue=",";
    			}
    			jQuery("#imgloadingemail").css({"visibility":"visible"});
    			jQuery.ajax({
    				url:"<?php echo admin_url('admin-ajax.php');?>",
    				data:{
    					action:"jie_saveconfig",
    					config:select,
    					optionname:"<?php echo sanitize_title($nameExcel . ".xls");?>"
    				},
    				success:function(){
						jQuery("#imgloadingemail").css({"visibility":"hidden"});    					
    				}
    			});
    			
    		})
    		
    	});
    </script>
    <div id="jpress-feuille_<?php echo $i; ?>" class="showing wrap tab-content">
      <form id="jpress-configuration-form" enctype="multipart/form-data" action="" method="post">
        <input type="hidden" name="choix-feuille" value="<?php echo $i; ?>"/>
        <input type="submit" name="import" value="<?php if($is_a_step):?>Continuer<?php else:?>Importer<?php endif;?>" class="button-primary" <?php if($is_a_step):?>style="background-color:brown;background-image:none;"<?php endif;?> onclick="return confirm('Êtes-vous sûre de votre configuration ?');"/>
      
      <?php if( $show_file_content   ):?>
      <div id="jpress-contenu-excel">
        <h3>Configuration des colonnes</h3>
        <div class="updated below-h2"><p>Veuillez identifier chaque colonne avec les noms de colonnes disponibles.<br>Les noms de colonnes seront utilisés par le processus d'importation, donc verifiez bien le nom de chaque colonne avant de cliquer sur le bouton "importer" à vos risques et périls.<br>N'oubliez pas de sauvegarder la configuration des colonnes pour le fichier xls courant, surtout si le processus d'importation se fait par lot.<br>Vous pouvez aussi charger la configuration des colonnes d'autres fichiers (fichier servant de modèle par exemple)</p></div>
        <div class="updated below-h2">
          <p>
          Si votre fichier à importer se sert des identifiants des entités du site, vous pouvez consulter la liste des identifiants en cliquant <a href="<?php echo plugin_dir_url(__FILE__).'identifiants.php';?>" class="fancybox fancybox.iframe"><strong>ici</strong></a>
          </p>
          <p>Si une colonne de votre fichier est inutile à l'import ou est à ignorer, laisser la colonne à la valeur "Aucun".<br>Veuillez contacter l'administrateur si le nom d'une colonne de votre fichier n'est pas présent dans la liste des noms de colonnes disponibles.</p>
        </div>

        <div>
        	<input type="button" class="button-secondary action" id="saveconfigcolumn" value="Sauvegarder la configuration"/>
        	<img id="imgloadingemail" style="visibility: hidden;" src="<?php echo plugin_dir_url(__FILE__);?>/images/loading.gif">
        </div>
        </br>
        <div>
        	<input type="submit" class="button-secondary action" id="loadconfig" name="loadconfig" value="Charger la configuration"/>
        	<select name="config">
        		<?php 
        		global $wpdb;
        		$optionsname = $wpdb->get_col("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE 'jie_option\_%'");
        		$html="";
        		foreach ($optionsname as $optionname) {
        			$html.='<option value="'.$optionname.'">'.str_replace("-xls",".xls",str_replace("jie_option_","",$optionname)).'</option>';
        		}
        		echo $html;?>
        	</select>
        </div>	
        <h2>Contenu du fichier <?php echo $nameExcel . ".xls"; ?></h2>
        <?php
          $atts = array ('fname' => urlencode($nameExcel));
          echo show_championship_table_feuille($atts, $i);
        ?>
      </div>
      <?php endif;?>
      </form>
    </div>
    <?php
   endif;
  endif;
  ?>
  <?php
}

function show_championship_table_feuille($atts, $num_feuille) {
  $output = "Fichier non trouvé.";
  if(isset($atts['fname'])) {
    $filename = urldecode($atts['fname']);
    $upload_dir = wp_upload_dir();
    $filename = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . GL_EXCEL_DIR . DIRECTORY_SEPARATOR . $filename . ".xls";
    if(file_exists($filename)) {
      $output = TableDatasByFeuille($filename, $num_feuille);
    }
  }
  return $output;
}

function show_championship_table($atts) {
  $output = "Fichier non trouvé.";
  if(isset($atts['fname'])) {
    $filename = urldecode($atts['fname']);
    $upload_dir = wp_upload_dir();
    $filename = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . GL_EXCEL_DIR . DIRECTORY_SEPARATOR . $filename . ".xls";
    if(file_exists($filename)) {
      $output = TableDatas($filename);
    }
  }
  return $output;
}

/**
 * @param $num_feuille
 * @param $file
 * @param $startRow debut ligne
 */
function jpress_read_excel($num_feuille, $file ,$startRow, $endRow = null){
    $data = new Spreadsheet_Excel_Reader($file);
    $datasheet = $data->get_data($num_feuille, $startRow, $endRow);
    if(!empty($datasheet)) {
        $datasheet = array_filter($datasheet);
        return $datasheet;
    }
    return false;
}

/**
 * @param $num_feuille
 * @param $file
 * @param $startRow debut ligne
 */
function jpress_read_excel_by_step($num_feuille, $file ,$initialStartRow, $step = 100){
    $file_info = pathinfo($file);
	$option = sanitize_title($file_info['filename']. '_sheet_' . $num_feuille);

	if($newStartRow = get_option($option)){
		$startRow = intval($newStartRow);
    	$endRow = $startRow+$step-1;
	}else{
		$startRow = $initialStartRow;
    	$endRow = $startRow+$step-1;
	}
    
    $datasheet = jpress_read_excel($num_feuille, $file ,$startRow, $endRow);
	
    if(count($datasheet)<$step){
		delete_option($option);
    }else{
    	update_option($option, intval($endRow+1));    	
    }
    
    return $datasheet;
}

add_filter("jie_column_list", "jie_column_list_callback",10,2);
function jie_column_list_callback($html,$col){
	global $current_screen;
  	$pt = $current_screen->post_type;
	$jie_options = get_option('jie_options');

	//column config
	if(isset($_POST["config"]) && isset($_POST["loadconfig"])){
		$jie_option_file = get_option($_POST["config"]);
	}else{
		$nameExcel = $_REQUEST['file'];
		$optionname = sanitize_title($nameExcel.".xls");
		$jie_option_file = get_option('jie_option_'.$optionname);
	}
	if(isset($jie_options["slug"][$pt])){
		$html="";
		foreach ($jie_options["slug"][$pt] as $key => $slug) {
			$selected = "";
			if($jie_option_file && isset($jie_option_file[$col-1])){
				$selected = ($jie_option_file[$col-1]==$slug)?"selected":"";
			}
			$html.='<option value="'.$slug.'" '.$selected.'>'.$jie_options["libelle"][$pt][$key]."</option>";		
		}
	}
	return $html;
}

add_action("wp_ajax_jie_saveconfig","jie_saveconfig");
function jie_saveconfig(){
	extract($_REQUEST);
	$config = explode(',',$config);
	update_option("jie_option_".$optionname,$config );
	die(1);
}

//////// cron job ///////////////////////////////////////////////

add_filter('cron_schedules', 'jie_add_scheduled_interval');
// add once 5 minute interval to wp schedules
function jie_add_scheduled_interval($schedules) {
	$schedules['une_minute'] = array('interval'=>60, 'display'=>'Once 1 minute');
	return $schedules;
}

if (!wp_next_scheduled('cron_importer_function')) {
	wp_schedule_event(time(), 'une_minute', 'cron_importer_function');
}
?>