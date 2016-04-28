<?php 
/*
Plugin Name: Shopp multilingual
Plugin URI: http://wpml.org/
Description: Add multilingual support for Shopp
Author: ICanLocalize
Author URI: http://wpml.org
Version: 0.9.0
*/

if(defined('SHOPP_MULTILINGUAL_VERSION')) return;

define('SHOPP_MULTILINGUAL_VERSION', '0.9.0');
define('SHOPP_MULTILINGUAL_PATH', dirname(__FILE__));

require SHOPP_MULTILINGUAL_PATH . '/inc/shopp_multilingual.class.php';

$shopp_multilingual = new Shopp_multilingual();
?>
