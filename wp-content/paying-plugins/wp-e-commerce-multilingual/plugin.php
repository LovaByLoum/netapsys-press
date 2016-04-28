<?php
/*
Plugin Name: WP e-Commerce Multilingual
Plugin URI: http://wpml.org/
Description: WPEC integration with WPML
Version: 0.2
Author: OnTheGoSystems
Author URI: http://wpml.org/
*/  
  

/* DEBUG STUFF */

add_action('plugins_loaded', 'init_WP_e_Commerce_Multilingual', 15);  
function init_WP_e_Commerce_Multilingual(){
    if(!defined('ICL_SITEPRESS_VERSION') || !defined('WPSC_DIR_NAME')){
        add_action('admin_notices', 'wpecml_error_no_plugins');
    }else{
        require_once dirname(__FILE__) . '/wp-e-commerce-multilingual.class.php';
        $WP_e_Commerce_Multilingual = new WP_e_Commerce_Multilingual;        
    }
    
}  

function wpecml_error_no_plugins(){
    ?>
    <div class="error">
        <p><strong><?php printf(__('WP e-Commerce Multilingual only works when %s and %s are installed and active.', 'wp_e_commerce_multilingual'), 'WPML', 'WP e-Commerce'); ?></strong></p>
    </div>
    <?php
}
