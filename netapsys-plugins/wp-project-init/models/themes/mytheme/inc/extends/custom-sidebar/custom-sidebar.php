<?php
/**
 * Register our sidebars and widgetized areas.
 *
 * @since mytheme 1.0
 */
function mytheme_widgets_init() {

    register_sidebar( array(
        'name' => __( 'Main Sidebar', 'mytheme' ),
        'id' => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    //register other sidebar
    //http://generatewp.com/sidebar/
}
add_action( 'widgets_init', 'mytheme_widgets_init' );