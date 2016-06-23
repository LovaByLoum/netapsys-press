<?php
/**
 * Plugin Name: ACF Fields Repeater Collapser Admin
 * Plugin URI: https://github.com/mrwweb/ACF-Repeater-Collapser
 * Description: Extension of plugin "Advanced Custom Fields Repeater Collapser 1.1.0".
 * Author:      Johary Ranarimanana
 */

/* Load the javascript and CSS files on the ACF admin pages */
add_action( 'admin_enqueue_scripts', 'carambole_repeater_collapser_assets' );
function carambole_repeater_collapser_assets() {
	wp_enqueue_script(
		'carambole_repeater_collapser_admin_js',
    plugin_dir_url( __FILE__ ) . 'js/acf-repeater-collapser-admin.js',
		array( 'jquery' )
	);
	wp_enqueue_style(
		'carambole_repeater_collapser_admin_css',
    plugin_dir_url( __FILE__ ) . 'css/acf-repeater-collapser-admin.css'
	);
}