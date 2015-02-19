<?php
/*
Plugin Name: Jenkins Press
Description: Gestion et mise en place de Jenkins dans uns site WordPress
Author: Johary, Valentinà Netapsys
*/
add_action('admin_menu', 'jp_init');
function jp_init(){
    add_options_page('Jenkins Press','Jenkins Press', 'manage_options', 'jenkins-press', 'jp_admin_page' );
}

function jp_admin_page(){
    include 'admin/admin-page.php';
}

function jp_secure_array_key($key, $array, $default = ''){
    return isset($array[$key])?$array[$key]:$default;
}

function jp_build_reconstruct(){
    $jenpress_options = get_option('jenpress_options');
    $dirsample = plugin_dir_path(__FILE__);

    $buildsamplecontent = file_get_contents($dirsample.'samples'.DIRECTORY_SEPARATOR.'build-sample.xml');

    //replace config
    $buildsamplecontent = str_replace('${JP_PROJECT_NAME}',$jenpress_options['project_name'],$buildsamplecontent);
    $buildsamplecontent = str_replace('${JP_BASEDIR}',$jenpress_options['basedir'],$buildsamplecontent);
    $buildsamplecontent = str_replace('${JP_SCRIPT_SUFFIX}',$jenpress_options['scriptsuffix'],$buildsamplecontent);

    //include files
    $includesfiles = explode("\n",$jenpress_options['includefiles']);
    $includesfilessection = '';
    foreach ($includesfiles as $path) {
        $includesfilessection.='<include name="' . trim($path) .'"/>'."\n";
    }
    $buildsamplecontent = str_replace('${JP_INCLUDE_FILES}',$includesfilessection,$buildsamplecontent);

    //exclude files
    $excludefiles = explode("\n",$jenpress_options['excludefiles']);
    $excludefilessection = '';
    foreach ($excludefiles as $path) {
        $excludefilessection.='<exclude name="' . trim($path) .'"/>'."\n";
    }
    $buildsamplecontent = str_replace('${JP_EXCLUDE_FILES}',$excludefilessection,$buildsamplecontent);

    //pdependignore
    $pdependignore = explode("\n",$jenpress_options['pdependignore']);
    $pdependignoresection = '';
    $glue = '';
    foreach ($pdependignore as $path) {
        $pdependignoresection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_PDEPENDIGNORE}',$pdependignoresection,$buildsamplecontent);

    //pdependpath
    $pdependpath = explode("\n",$jenpress_options['pdependpath']);
    $pdependpathsection = '';
    foreach ($pdependpath as $path) {
        $pdependpathsection.= '<arg path="${basedir}/' . trim($path) .'" />' ;
    }
    $buildsamplecontent = str_replace('${JP_PDEPENDPATH}',$pdependpathsection,$buildsamplecontent);

    //pmdignore
    $pmdexcludes = explode("\n",$jenpress_options['pmdignore']);
    $pmdexcludessection = '';
    $glue = '';
    foreach ($pmdexcludes as $path) {
        $pmdexcludessection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_PMDIGNORE}',$pmdexcludessection,$buildsamplecontent);

    //pmdpath
    $pmdpathinclude = explode("\n",$jenpress_options['pmdpath']);
    $pmdpathincludesection = '';
    $glue = '';
    foreach ($pmdpathinclude as $path) {
        $pmdpathincludesection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_PMDPATH}',$pmdpathincludesection,$buildsamplecontent);

    //phpcpdignore
    $phpcpdignore = explode("\n",$jenpress_options['phpcpdignore']);
    $phpcpdexcludessection = '';
    $glue = '';
    foreach ($phpcpdignore as $path) {
        $phpcpdexcludessection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_PHPCPDIGNORE}',$phpcpdexcludessection,$buildsamplecontent);

    //phpcpdpath
    $phpcpdpathinclude = explode("\n",$jenpress_options['phpcpdpath']);
    $phpcpdpathincludesection = '';
    foreach ($phpcpdpathinclude as $path) {
        $phpcpdpathincludesection.= '<arg path="${basedir}/' . trim($path) .'" />'."\n" ;
    }
    $buildsamplecontent = str_replace('${JP_PHPCPDPATH}',$phpcpdpathincludesection,$buildsamplecontent);

    //checkstyleignore
    $checkstyleignore = explode("\n",$jenpress_options['checkstyleignore']);
    $checkstyleexcludessection = '';
    $glue = '';
    foreach ($checkstyleignore as $path) {
        $checkstyleexcludessection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_CHECKSTYLEIGNORE}',$checkstyleexcludessection,$buildsamplecontent);

    //checkstylepath
    $checkstylepathinclude = explode("\n",$jenpress_options['checkstylepath']);
    $checkstylepathincludesection = '';
    foreach ($checkstylepathinclude as $path) {
        $checkstylepathincludesection.= '<arg path="${basedir}/' . trim($path) .'" />'."\n" ;
    }
    $buildsamplecontent = str_replace('${JP_CHECKSTYLEPATH}',$checkstylepathincludesection,$buildsamplecontent);

    //cbignore
    $cbignore = explode("\n",$jenpress_options['cbignore']);
    $cbexcludessection = '';
    $glue = '';
    foreach ($cbignore as $path) {
        $cbexcludessection.= $glue.'${basedir}/' . trim($path);
        $glue =',';
    }
    $buildsamplecontent = str_replace('${JP_CBIGNORE}',$cbexcludessection,$buildsamplecontent);

    //cbpath
    $cbpathinclude = explode("\n",$jenpress_options['cbpath']);
    $cbpathincludesection = '';
    foreach ($cbpathinclude as $path) {
        $cbpathincludesection.= '<arg path="${basedir}/' . trim($path) .'" />'."\n" ;
    }
    $buildsamplecontent = str_replace('${JP_CBPATH}',$cbpathincludesection,$buildsamplecontent);

    //SECTION ENABLE :::::::::::::::
    //php files
    $tagstart = '${JP_PHPFILES_SECTION}';
    $tagend = '${JP_PHPFILES_SECTION_END}';
    if(isset($jenpress_options['phpfiles_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //pdepend
    $tagstart = '${JP_PDEPEND_SECTION}';
    $tagend = '${JP_PDEPEND_SECTION_END}';
    if(isset($jenpress_options['pdepend_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //pmd
    $tagstart = '${JP_PMD_SECTION}';
    $tagend = '${JP_PMD_SECTION_END}';
    if(isset($jenpress_options['pmd_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //phpcpd
    $tagstart = '${JP_PHPCPD_SECTION}';
    $tagend = '${JP_PHPCPD_SECTION_END}';
    if(isset($jenpress_options['phpcpd_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //checkstyle
    $tagstart = '${JP_CHECKSTYLE_SECTION}';
    $tagend = '${JP_CHECKSTYLE_SECTION_END}';
    if(isset($jenpress_options['checkstyle_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //checkstyle
    $tagstart = '${JP_CB_SECTION}';
    $tagend = '${JP_CB_SECTION_END}';
    if(isset($jenpress_options['cb_section'])){
        $buildsamplecontent = str_replace($tagstart,'',$buildsamplecontent);
        $buildsamplecontent = str_replace($tagend,'',$buildsamplecontent);
    }else{
        $start = strpos($buildsamplecontent,$tagstart);
        $end = strpos($buildsamplecontent,$tagend);
        $buildsamplecontent = substr($buildsamplecontent,0,$start) . substr($buildsamplecontent,$end+strlen($tagend),strlen($buildsamplecontent));
    }

    //ANTCALL ENABLE ::::::::::::::
    //pmd
    if(isset($jenpress_options['antcall_pmd'])){
        //do nothing
    }else{
        $buildsamplecontent = str_replace('<antcall target="phpmd"/>', '',$buildsamplecontent);
    }

    //antcall_phpcpd
    if(isset($jenpress_options['antcall_phpcpd'])){
        //do nothing
    }else{
        $buildsamplecontent = str_replace('<antcall target="phpcpd"/>', '',$buildsamplecontent);
    }

    //antcall_phpcs
    if(isset($jenpress_options['antcall_phpcs'])){
        //do nothing
    }else{
        $buildsamplecontent = str_replace('<antcall target="phpcs"/>', '',$buildsamplecontent);
    }

    //write
    $buildpath = ABSPATH . 'build.xml';
    jp_create_file($buildpath,$buildsamplecontent,"w");

}

function jp_create_file($filename, $somecontent, $openmode = "w"){
    if (!$handle = @fopen($filename, $openmode)) {
        return false;
    }
    if (@fwrite($handle, $somecontent) === FALSE) {
        return false;
    }
    @fclose($handle);
    return true;
}