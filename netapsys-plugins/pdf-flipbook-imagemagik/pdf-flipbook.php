<?php
/*
Plugin Name: PDF flipbook with ImageMagik
Description: Affiche les fichiers pdf comme un livre (utilisant turn.js). Pour les liens dans le RTE c'est gerer par l'extension. Pour les liens des champs personnalisés, appliquer le filtre apply_filters('pdf_flip_url', $votreurl) ou rediriger votre post attachement au http://VOTRESITE/pdf-flipbook?fichier=XXXX où XXXX est l'identifiant de l'attachement. Imagemagik doit être installé sur le serveur
Author: Johary
*/
require_once("imagemagik-utils.php");

add_action('init', 'pdfflip_init');
add_filter('the_content', 'pdfflip_the_content');
add_filter('template_include', 'pdfflip_template_include');

function pdfflip_init(){
	if (is_admin()){
		pdfflip_install();
	}
}

function pdfflip_install(){
	$post = pdfflip_get_post_by_slug('pdf-flipbook');
	if(!$post){
		$my_post = array(
		  'post_title'    => 'PDF flipbook',
		  'post_content'  => '',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type'     => 'page',
		);
	 	wp_insert_post($my_post);
	 	$post_id = pdfflip_get_post_by_slug('pdf-flipbook');
	 	$post = get_post($post_id);
	 	if ($post->post_name = 'pdf-flipbook'){
	 		add_post_meta($post->ID, '_wp_page_template', 'pdf-flipbook-page.php');
	 	}
	}
}

function pdfflip_the_content($the_content){
	$the_content = preg_replace_callback('#<a(.*?)href="(.*?)"(.*?)>#', 'pdfflip_pregcallback',$the_content);
	return $the_content;
}
function pdfflip_pregcallback($matches)
{
  //if pdf
  if(preg_match("#". get_option("siteurl") ."(.*?).pdf#",$matches[2])){	
  	$attachment = pdfflip_get_attachement($matches[2]);
  	$pagepdf = pdfflip_get_post_by_slug('pdf-flipbook');
  	$permalink = get_permalink($pagepdf). "?fichier=" . $attachment->ID ;
  	return '<a' . $matches[1] .'href="'. $permalink .'"'. $matches[3] .'>';
  }else{
  	return '<a' . $matches[1] .'href="'. $matches[2] .'"'. $matches[3] .'>';
  }
}

/**
 * recupere post par le postname
 *
 * @param string $slug
 */
function pdfflip_get_post_by_slug($slug){
	global $wpdb;
	$post = $wpdb->get_var($sql = "SELECT ID FROM {$wpdb->posts} WHERE post_name ='{$slug}' AND post_type='page' LIMIT 1");    
	return $post;
}

function pdfflip_template_include($template){
	global $post;
	$tpl = get_post_meta($post->ID, '_wp_page_template', true);
	if ( $tpl == 'pdf-flipbook-page.php'){
		$tplpath = plugin_dir_path(__FILE__). 'pdf-flipbook-page.php' ;
		return $tplpath;
	}else{
		return $template;
	}
}

/**
 * retourne l'atatchement correspondant à l'url
 *
 * @param int $post_id
 * @param int $url
 * @return $attachment
 */
function pdfflip_get_attachement( $url){
	global $wpdb;
	
	//rechercher par url
	$query = "SELECT p.ID FROM `{$wpdb->posts}` AS p WHERE p.guid='" . $url ."' AND p.post_type='attachment'";
	$results = $wpdb->get_results($query);
	if (is_array($results) && !empty($results)) {
		$attachment = $results[0];
		return $attachment;
	}else{
		return false;
	}
}

//url filter
add_filter('pdf_flip_url', 'pdfflip_the_url');
function pdfflip_the_url($the_url){
  //if pdf
  if(preg_match("#". get_option("siteurl") ."(.*?).pdf#",$the_url)){	
  	$attachment = pdfflip_get_attachement($the_url);
  	$pagepdf = pdfflip_get_post_by_slug('pdf-flipbook');
  	$permalink = get_permalink($pagepdf). "?fichier=" . $attachment->ID ;
  	return $permalink;
  }else{
  	return $the_url;
  }
}

add_filter('cron_schedules', 'pdfflip_add_scheduled_interval');
// add once 5 minute interval to wp schedules
function pdfflip_add_scheduled_interval($schedules) {
	$schedules['minutes_5'] = array('interval'=>60, 'display'=>'Once 5 minutes');
	return $schedules;
}

if (!wp_next_scheduled('my_5mn_task')) {
	wp_schedule_event(time(), 'minutes_5', 'my_5mn_task');
}
add_action('my_5mn_task', 'verify_pdf_log');
function verify_pdf_log() {
	$newpdf = unserialize(get_option('new_pdf'));
	if ($newpdf && !empty($newpdf)){
		$pdfurl = array_shift($newpdf);
		oan_log('convert pdf '.$pdfurl);
		update_option('new_pdf', serialize($newpdf));
		$pdf = new PDFFLIP($pdfurl);
		$pdf->removeImages();
		$pdf->convertPdf();
	}
}

//upload de media
add_filter('wp_handle_upload', 'pdfflip_handle_upload',10,2);
add_filter('wp_handle_media_replace', 'pdfflip_handle_upload',10,2);
function pdfflip_handle_upload($args, $action){
	if($args['type'] == 'application/pdf'){
		$newpdf = unserialize(get_option('new_pdf'));
		if (is_array($newpdf)){
			$newpdf[]=$args['url'];
			update_option('new_pdf', serialize($newpdf));
		}else{
			$newpdf = array();
			$newpdf[]=$args['url'];
			add_option('new_pdf', serialize($newpdf));
		} 
	}
	return $args;
}

?>
