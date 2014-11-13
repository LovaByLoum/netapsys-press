<?php
/**
 * modele de methode d'importation
 */

//:::::::::::::::::::::::: HOOK FOR SHEET IMPORT ::::::::::::::::::::::::
//importation de la feuille 0 pour ke type de publication POSTTYPE
add_action("importer_sheet_POSTTYPE_0", "import_element_function",10,2);
function import_element_function($feuilleNum, $file){
    $startRow = 2;
    $datasheet = jpress_read_excel($feuilleNum, $file ,$startRow);
	
    //or if is a step
    //$step = 100;
    //$datasheet = jpress_read_excel_by_step($feuilleNum, $file ,$startRow, $step);
    
    //do import here
    /*foreach($datasheet as $row) {
		$row["COLUMNNAME"]
    }*/
}

//for cron job task
add_action('cron_importer_function', 'cron_import_element_function');
function cron_import_element_function(){
	//do something
}

//:::::::::::::::::::::::: USER FUNCTION  ::::::::::::::::::::::::
//here
?>