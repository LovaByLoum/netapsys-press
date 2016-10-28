<?php
/**
Plugin Name: WP Project Init
Description: Generateur de themes vides en mettant en place les bestpractices WordPress et Uniformisation de developpement, Mettre en place les outils de sécurisation de site, d'optimisation de performance, de developpement et de test IC
Version: 1.0.0
Author: Johary Ranarimanana
*/
require_once('inc/constante.php');
require_once('admin/inc/admin.class.php');
require_once('inc/generate_themes.class.php');
require_once('inc/plugin_manager.class.php');
require_once('models/plugins/plugins.info.php');
require_once('models/plugins/plugins-custom.info.php');
require_once('models/plugins/plugins-premium.info.php');
require_once('inc/zipper.php');
require_once('inc/wp-cli-class.php');

class WP_Project_Init{
	
	function __construct(){
		//add hook callback
		add_action('admin_menu', array($this,'admin_menu'));

    add_action('admin_print_styles', array($this, 'admin_print_styles'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    add_action('admin_init', array($this, 'admin_init'));
		
	}

  //add main admin menu
	static function admin_menu(){
		add_menu_page('Project Init', 'Project Init', 'manage_options','project-init','WP_Project_Init::admin_page', plugin_dir_url(__FILE__). '/medias/images/cog_4.png');
		
	}

  //add css
  static function admin_print_styles(){
      wp_enqueue_style('project-init',plugin_dir_url(__FILE__).'admin/css/project-init.css');
  }

  //add js
  static function admin_enqueue_scripts(){
      wp_enqueue_script('project-init',plugin_dir_url(__FILE__).'admin/js/project-init.js');
  }

  //admin main page tpl
	static function admin_page(){
		include 'admin/main-page.php';
	}

  static function admin_init(){
    if ( isset($_REQUEST['action']) && 'export_theme' == $_REQUEST['action'] && isset($_REQUEST['theme']) && !empty($_REQUEST['action']) && isset($_REQUEST['nonce']) && !empty($_REQUEST['nonce']) ){
      $theme_slug = $_REQUEST['theme'];
      if ( wp_verify_nonce( $_REQUEST['nonce'], 'theme-' . $theme_slug ) ){
        $zip = new Zipper();
        $filename = $theme_slug . ".zip";
        $filepath = ABSPATH . $filename;
        if ( $zip->open($filepath, ZipArchive::OVERWRITE) !== true ) {
          wp_die("Impossible d'ouvrir le fichier <$filepath>.");
        }
        $theme_path = get_theme_root() . DIRECTORY_SEPARATOR . $theme_slug;
        if(file_exists($theme_path)){
          $zip->addDir( $theme_path, $theme_slug );
        }
        $zip->close();
        if( file_exists($filepath) ){
          // push to download the zip
          header( 'Content-Type: application/force-download' );
          header( 'Content-Transfer-Encoding: binary' );
          header('Content-Disposition: attachment; filename="'.$filename.'"');
          readfile($filepath);
          // remove zip file is exists in temp path
          unlink($filepath);
          die;
        }
      }
    }

    if ( isset($_REQUEST['action']) && 'wppi-install-custom-plugin' == $_REQUEST['action'] && isset($_REQUEST['plugin']) && !empty($_REQUEST['plugin'])  ){
      $plugin = $_REQUEST['plugin'];
      check_admin_referer('wppi-install-custom-plugin_' . $plugin);
      if ( defined('WPPI_PLUGIN_CUSTOM_BASE_URL') ){
        $url = WPPI_PLUGIN_CUSTOM_BASE_URL . $plugin . '.zip';
        $newfile = 'tmp_file.zip';
        if (!copy($url, $newfile)) {
          wp_die ("failed to copy $url...");
        }
        $zip = new ZipArchive;
        if ($zip->open($newfile) === TRUE) {
          $zip->extractTo(WP_PLUGIN_DIR  );
          $zip->close();

          //acivate
          $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
          activate_plugin( $plugin_path, '', false, true );
          wp_redirect( site_url("wp-admin/plugins.php") );
        } else {
          wp_die ("failed to open $newfile ...");
        }
      }


    }

    if ( isset($_REQUEST['action']) && 'wppi-install-premium-plugin' == $_REQUEST['action'] && isset($_REQUEST['plugin']) && !empty($_REQUEST['plugin'])  ){
      $plugin = $_REQUEST['plugin'];
      check_admin_referer('wppi-install-premium-plugin_' . $plugin);
      if ( defined('WPPI_PLUGIN_PREMIUM_BASE_URL') ){
        $url = WPPI_PLUGIN_PREMIUM_BASE_URL . $plugin . '.zip';
        $newfile = 'tmp_file.zip';
        if (!copy($url, $newfile)) {
          wp_die ("failed to copy $url...");
        }
        $zip = new ZipArchive;
        if ($zip->open($newfile) === TRUE) {
          $zip->extractTo(WP_PLUGIN_DIR  );
          $zip->close();

          //acivate
          $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
          activate_plugin( $plugin_path, '', false, true );
          wp_redirect( site_url("wp-admin/plugins.php") );
        } else {
          wp_die ("failed to open $newfile ...");
        }
      }


    }
  }
}
global $wp_project_init;
$wp_project_init = new WP_Project_Init();
?>