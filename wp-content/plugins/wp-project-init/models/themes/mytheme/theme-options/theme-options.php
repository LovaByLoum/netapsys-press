<?php
/**
 * Theme options
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */
global $mytheme_options;
require_once get_template_directory() . '/theme-options/options.php';
$mytheme_options = get_option( 'mytheme_theme_options' );

add_action( 'admin_init', 'mytheme_options_init' );
function mytheme_options_init(){
 register_setting( 'mytheme_options', 'mytheme_theme_options','mytheme_options_validate');
} 

function mytheme_options_validate($input)
{
   $allfields_settings = mytheme_get_all_settings();

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
            $input[$i] = mytheme_image_validation(esc_url_raw( $input[$i]));
            break;
          default:
        }
     }
   }

	  return $input;
}
function mytheme_image_validation($mytheme_imge_url){
	$mytheme_filetype = wp_check_filetype($mytheme_imge_url);
	$mytheme_supported_image = array('gif','jpg','jpeg','png','ico');
	if (in_array($mytheme_filetype['ext'], $mytheme_supported_image)) {
		return $mytheme_imge_url;
	} else {
	return '';
	}
}
function mytheme_get_all_settings(){
  global $mytheme_options_settings;
  $allfields = array();
  foreach ( $mytheme_options_settings as $tab) {
      $allfields = array_merge( $allfields, $tab );
  }
  return $allfields;
}

add_action( 'admin_enqueue_scripts', 'mytheme_framework_load_scripts' );
function mytheme_framework_load_scripts(){
	wp_enqueue_media();
	wp_enqueue_style( 'mytheme_framework', get_template_directory_uri(). '/theme-options/css/theme-options.css' ,false, '1.0.0');
	// Enqueue custom option panel JS
	wp_enqueue_script( 'options-custom', get_template_directory_uri(). '/theme-options/js/theme-options.js', array( 'jquery' ) );
	wp_enqueue_script( 'media-uploader', get_template_directory_uri(). '/theme-options/js/media-uploader.js', array( 'jquery') );		
}

add_action( 'admin_menu', 'mytheme_options_add_page' );
function mytheme_options_add_page() {
	add_theme_page( 'mytheme Options', 'Theme Options', 'edit_theme_options', 'mytheme_framework', 'mytheme_framework_page');
}

function mytheme_framework_page(){
  include 'admin-page.php';
}

//compatibilité WPML, rendre les options translatables
if ( function_exists('icl_register_string')){
  add_action('wp_ajax_icl_tl_rescan', 'mytheme_options_wpml_translate');
  function mytheme_options_wpml_translate(){
    $theme_options = get_option( 'mytheme_theme_options' );
    foreach ( $theme_options as $key => $option ){
      if ( intval($option)>0 ) continue;
      if ( !is_string($option) ) continue;
      icl_register_string( 'mytheme_options', $key, apply_filters('widget_text', $option));
    }
  }
}