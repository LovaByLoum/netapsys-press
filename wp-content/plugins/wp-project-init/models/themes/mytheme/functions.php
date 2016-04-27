<?php
/**
 * mytheme functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, mytheme_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'mytheme_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

require_once( get_template_directory() . '/inc/constante.inc.php' );
require_once( get_template_directory() . '/inc/default.config.php' );
require_once( get_template_directory() . '/inc/utils/functions.php' );
require_once( get_template_directory() . '/login.php' );

//classes de service
require_once_files_in( get_template_directory() . '/inc/classes/posttype' );
require_once_files_in( get_template_directory() . '/inc/classes/taxonomy' );
require_once_files_in( get_template_directory() . '/inc/classes/user' );

if (is_admin()){
  require_once( get_template_directory() . '/admin-functions.php' );

  /*** Theme Option ***/
  if ( is_dir( get_template_directory() . '/theme-options' ) ){
    require get_template_directory() . '/theme-options/theme-options.php';
  }
}

//lib
require_once( get_template_directory() . '/lib/cssmin.php' );
require_once( get_template_directory()  . '/lib/jsmin.php' );

global $mytheme_options;
$mytheme_options = get_option( 'mytheme_theme_options' );

/**
 * Tell WordPress to run mytheme_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'mytheme_setup' );

if ( ! function_exists( 'mytheme_setup' ) ):
function mytheme_setup() {

    require_once_files_in( get_template_directory() . '/inc/extends/custom-sidebar' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-fields/acf' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-metaboxes' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-rules' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-role' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-mce-tools' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-shortcodes' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-sidebar' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-types' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-taxonomies' );
    require_once_files_in( get_template_directory() . '/inc/extends/custom-widgets' );

	/* Make mytheme available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on mytheme, use a find and replace
	 * to change 'mytheme' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'mytheme', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	//add_editor_style();

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'mytheme' ) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	add_image_size( IMAGE_SIZE_ACTUS_VIGNETTE, 100, 100, true );
	add_image_size( IMAGE_SIZE_ACTUS_MEDIUM, 500, 300 );

}
endif; // mytheme_setup