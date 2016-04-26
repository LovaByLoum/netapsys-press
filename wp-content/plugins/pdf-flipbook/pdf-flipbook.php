<?php
/*
Plugin Name: PDF flipbook
Description: Affiche les fichiers pdf comme un livre (utilisant turn.js). Pour les liens dans le RTE c'est gerer par l'extension. Pour les liens des champs personnalisés, appliquer le filtre apply_filters('pdf_flip_url', $votreurl) ou rediriger votre post attachement au http://VOTRESITE/pdf-flipbook?fichier=XXXX où XXXX est l'identifiant de l'attachement
Author: Johary
*/

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
	pdfflip_minify_ressource();
}

function pdfflip_minify_ressource(){
	//js
	$ressource_js = array(
		dirname(__FILE__) . "/js/jquery-1.7.1.min.js" => true,
		dirname(__FILE__) . "/js/turn.js" => false,
		dirname(__FILE__) . "/js/compatibility.js" => false,
		dirname(__FILE__) . "/js/110n.js" => false,
		dirname(__FILE__) . "/js/pdf.js" => false,
		dirname(__FILE__) . "/js/debugger.js" => false,
		dirname(__FILE__) . "/js/viewer.js" => false,
	); 
	
	$jsminfile = dirname(__FILE__) . "/js/pdf-flipbook.min.js";
	if (!is_file($jsminfile) && !file_exists($jsminfile)){
		if (class_exists('JSMin')){
			$result = "";
			foreach ($ressource_js as  $js => $min){
				if ($min){
					$result.= file_get_contents($js);
				}else{
					$result.= JSMin::minify(file_get_contents($js));
				}
			}
			pdfflip_create_file($jsminfile,$result);
		}
	}
	
	//css
	$ressource_css = array(
		dirname(__FILE__) . "/css/viewer.css" => false,
		dirname(__FILE__) . "/css/book.css" => false,
		dirname(__FILE__) . "/css/magazine.css" => false,
		dirname(__FILE__) . "/css/jquery.ui.css" => false,
		dirname(__FILE__) . "/css/steve-jobs.css" => false
	); 
	
	$cssminfile = dirname(__FILE__) . "/css/pdf-flipbook.min.css";
	if (!is_file($cssminfile) && !file_exists($cssminfile)){
		if (class_exists('CssMin')){
			$result = "";
			foreach ($ressource_css as  $css => $min){
				if ($min){
					$result.= file_get_contents($css);
				}else{
					$result.= CssMin::minify(file_get_contents($css));
				}
			}
			pdfflip_create_file($cssminfile,$result);
		}
	}
}

function pdfflip_create_file($filename, $somecontent, $openmode = "w"){
    if (!$handle = @fopen($filename, $openmode)) {
         return false;
    }
    if (@fwrite($handle, $somecontent) === FALSE) {
		return false;
    }
    @fclose($handle);
	return true;
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
?>
