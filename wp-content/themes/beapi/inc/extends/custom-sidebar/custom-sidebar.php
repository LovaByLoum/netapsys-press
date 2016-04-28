<?php
/**
 * Initialisation des custom sidebars
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */
function beapi_widgets_init() {

    register_sidebar( array(
        'name' => __( 'Main Sidebar', 'beapi' ),
        'id' => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    //register other sidebar
    //http://generatewp.com/sidebar/
}
add_action( 'widgets_init', 'beapi_widgets_init' );