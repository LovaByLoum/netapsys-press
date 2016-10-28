<?php
/**
 * wp cli command
 */

if ( defined('WP_CLI') && WP_CLI ){
  /**
   * Project init command
   */
  class WP_Project_Init_Command extends WP_CLI_Command{

  }
  /**
   * Theme init command
   */
  class WP_Project_Init_Theme_Command extends WP_CLI_Command{
    /**
     * generate advanced empty theme with tools and utils
     *
     * Example :
     * wp init theme generate mytheme --name="My theme" --desc="Special Theme for my client" --tags="White, sidebar, responsive" --author="Johary" --author_site="https://netapsys.fr" --version="1.0" --prefix="mt" --options
     *
     */
    function generate( $args, $assoc_args ){
      list($theme_slug) = $args;

      if ( empty($theme_slug) ){
        WP_CLI::error('Theme slug is required');
      }

      $postdata = array(
        'theme_slug' => $theme_slug
      );

      foreach ( $assoc_args as $key => $value ){
        $postdata['theme_' . $key] = $value;
        if ( $key=='options' ) $value = 1;
      }

      $gen_themes = new WP_Generate_Themes($postdata);
      $msg = $gen_themes->generate();
      WP_CLI::success( 'Your theme as been generated : /wp-content/themes/' . $theme_slug );
    }
  }
  /**
   * Profile init command
   */
  class WP_Project_Init_Profile_Command extends WP_CLI_Command{
    /**
     *  list all profiles
     */
    public function list_( $args, $assoc_args ){
      $profiles = glob (WPI_PROFILES_PATH . '/*.profile');
      foreach ( $profiles as $file ){
        $pi = pathinfo($file);
        echo $pi['filename']."\n";
      }
    }
    /**
     *  install plugins by profile, make wp secure
     */
    function install( $args, $assoc_args ){
      list($profile) = $args;

      if ( empty($profile) ){
        WP_CLI::error('Profile name is required');
      }

      $plugins_to_active = WP_Plugin_Manager::get_profile($profile.'.profile');
      foreach ( $plugins_to_active as $plug ){
        exec('wp plugin install ' . $plug . ' --activate');
      }
      /*$postdata = array(
        'plugin' => $plugins_to_active
      );
      $plug_manager = new WP_Plugin_Manager($postdata);
      $msg = $plug_manager->install_plugins();*/

      WP_CLI::success("Profile installed successfully.");
    }
  }
  WP_CLI::add_command( 'init', 'WP_Project_Init_Command' );
  WP_CLI::add_command( 'init theme', 'WP_Project_Init_Theme_Command' );
  WP_CLI::add_command( 'init profile', 'WP_Project_Init_Profile_Command' );
}