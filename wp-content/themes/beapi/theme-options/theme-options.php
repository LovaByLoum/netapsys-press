<?php
/**
 * Theme options
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */
global $beapi_options;
require_once get_template_directory() . '/theme-options/options.php';
$beapi_options = get_option( 'beapi_theme_options' );

add_action( 'admin_init', 'beapi_options_init' );
function beapi_options_init(){
 register_setting( 'beapi_options', 'beapi_theme_options','beapi_options_validate');
} 

function beapi_options_validate($input)
{
   $allfields_settings = beapi_get_all_settings();

   foreach ( $input as $i ){
     if ( isset($allfields_settings[$i]) ){
        switch ( $allfields_settings[$i]['type'] ){
          case 'text':
            $input[$i] = sanitize_text_field( $input[$i] );
            break;
          case 'select':
            break;
          case 'date':
            break;
          case 'url':
            $input[$i] = esc_url_raw( $input[$i] );
            break;
          case 'textarea':
            $input[$i] = sanitize_text_field( $input[$i] );
            break;
          case 'image':
            $input[$i] = beapi_image_validation(esc_url_raw( $input[$i]));
            break;
          default:
        }
     }
   }

	  return $input;
}
function beapi_image_validation($beapi_imge_url){
	$beapi_filetype = wp_check_filetype($beapi_imge_url);
	$beapi_supported_image = array('gif','jpg','jpeg','png','ico');
	if (in_array($beapi_filetype['ext'], $beapi_supported_image)) {
		return $beapi_imge_url;
	} else {
	return '';
	}
}
function beapi_get_all_settings(){
  global $beapi_options_settings;
  $allfields = array();
  foreach ( $beapi_options_settings as $tab) {
      $allfields = array_merge( $allfields, $tab );
  }
  return $allfields;
}

add_action( 'admin_enqueue_scripts', 'beapi_framework_load_scripts' );
function beapi_framework_load_scripts(){
	wp_enqueue_media();
	wp_enqueue_style( 'beapi_framework', get_template_directory_uri(). '/theme-options/css/theme-options.css' ,false, '1.0.0');
	// Enqueue custom option panel JS
	wp_enqueue_script( 'options-custom', get_template_directory_uri(). '/theme-options/js/theme-options.js', array( 'jquery' ) );
	wp_enqueue_script( 'media-uploader', get_template_directory_uri(). '/theme-options/js/media-uploader.js', array( 'jquery') );		
}

add_action( 'admin_menu', 'beapi_options_add_page' );
function beapi_options_add_page() {
	add_theme_page( 'beapi Options', 'Theme Options', 'edit_theme_options', 'beapi_framework', 'beapi_framework_page');
}

function beapi_framework_page(){
  include 'admin-page.php';
}
