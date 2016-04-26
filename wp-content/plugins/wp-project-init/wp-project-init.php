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

class WP_Project_Init{
	
	function __construct(){
		//add hook callback
		add_action('admin_menu', array($this,'admin_menu'));

        add_action('admin_print_styles', array($this, 'admin_print_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		
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
}
global $wp_project_init;
$wp_project_init = new WP_Project_Init();
?>