<?php
/*
* Plugin Name: Wordpress secure
* Description: Met en place toutes les régles de sécurité pour WordPress
* Version: 1.0
* Author: Johary
*/

class Wordpress_Secure
{
	function __construct(){
		//Désactiver la remise à zéro du mot de passe de WordPress
		//add_filter( 'show_password_fields', array( $this, 'wps_disable_password_fields' ) );
		//add_filter( 'allow_password_reset', array( $this, 'wps_disable_password_fields' ) );
		//add_filter( 'gettext', array( $this, 'wps_remove_lost_password' ) );
		
		//Désactiver la remise à zéro du mot de passe de WordPress
		remove_action('wp_head', 'wp_generator');
		
		//Désactiver l'autocompletion de mots de passe
	    add_action('login_enqueue_scripts', array( $this, 'wps_kill_autocompletion'));

	}
	
	function wps_disable_password_fields(){
		if ( is_admin() ) {
			$userdata = wp_get_current_user();
			$user = new WP_User($userdata->ID);
			if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator' )
				return true;
		}
		return false;
	}
	
	function wps_remove_lost_password($text){
		return str_replace( array('Lost your password?', 'Lost your password'), '', trim($text, '?') );
	}
	
	function wps_kill_autocompletion(){
        echo '<script type="text/javascript">window.onload=function(){document.getElementById("user_pass").setAttribute("autocomplete","off")};</script>';

    }
}
$wp_secure = new Wordpress_Secure();

?>