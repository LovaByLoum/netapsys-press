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
     *  install plugins by profile
     *
     *  Example :
     *  wp init profile install netapsys
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

  /**
   * Plugin init command
   */
  class WP_Project_Init_Plugin_Command extends WP_CLI_Command{
    /**
     *  list all custom or premium plugins
     *
     *  Example :
     *  wp init plugin list_ --custom
     *  wp init plugin list_ --premium
     */
    public function list_( $args, $assoc_args ){
      global $wppi_plugins_custom, $wppi_plugins_premium;
      $all = !array_key_exists( 'custom', $assoc_args ) && !array_key_exists( 'premium', $assoc_args );
      if ( array_key_exists( 'custom', $assoc_args ) || $all ){
        echo "-------------- Custom plugins -------------------\n";
        foreach ( $wppi_plugins_custom as $section => $plugins){
          echo "$section :\n";
          foreach ( $plugins as $plugin ){
            echo "  $plugin\n";
          }
        }
      }
      if ( array_key_exists( 'premium', $assoc_args ) || $all ){
        echo "-------------- Premium plugins -------------------\n";
        foreach ( $wppi_plugins_premium as $section => $plugins){
          echo "$section :\n";
          foreach ( $plugins as $plugin ){
            echo "  $plugin\n";
          }
        }
      }

    }
    /**
     *  install plugins custom or  premium
     *
     *  Example :
     *  wp init plugin install jenkins-press --custom --activate
     *  wp init plugin install sitepress-multilingual-cms --premium --activate
     *
     */
    function install( $args, $assoc_args ){
      list($plugin) = $args;

      if ( empty($plugin) ){
        WP_CLI::error('Plugin slug is required');
      }

      if ( !isset($assoc_args['custom']) && !isset($assoc_args['premium']) ){
        WP_CLI::error('Please specify if it\'s a custom or premium plugin');
      }

      if ( defined('WPPI_PLUGIN_CUSTOM_BASE_URL') ){
        if ( array_key_exists('custom', $assoc_args) ){
          $url = WPPI_PLUGIN_CUSTOM_BASE_URL . $plugin . '.zip';
        } else {
          $url = WPPI_PLUGIN_PREMIUM_BASE_URL . $plugin . '.zip';
        }

        $newfile = 'tmp_file.zip';
        if (!copy($url, $newfile)) {
          WP_CLI::error("failed to copy $url...");
        }
        $zip = new ZipArchive;
        if ($zip->open($newfile) === TRUE) {
          $zip->extractTo(WP_PLUGIN_DIR  );
          $zip->close();

          //acivate
          if ( array_key_exists( 'activate', $assoc_args ) ){
            $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
            activate_plugin( $plugin_path, '', false, true );
          }
        } else {
          WP_CLI::error("failed to open $newfile ...");
        }
      }

      WP_CLI::success("Plugin installed successfully.");
    }
  }


  /**
   * Secure init command
   */
  class WP_Project_Init_Secure_Command extends WP_CLI_Command{
    /**
     *  install mu-plugin to renforce security of your wordpress site
     */
    public function install( $args, $assoc_args ){
      $postdata = array(
        'mu_captcha' => 1,
        'mu_lock' => 1,
        'mu_secure' => 1,
      );
      $plug_manager = new WP_Plugin_Manager($postdata);
      $plug_manager->copy_muplugins();
      WP_CLI::success('mu-plugins copied successfully.');
    }
  }

  WP_CLI::add_command( 'init', 'WP_Project_Init_Command' );
  WP_CLI::add_command( 'init theme', 'WP_Project_Init_Theme_Command' );
  WP_CLI::add_command( 'init profile', 'WP_Project_Init_Profile_Command' );
  WP_CLI::add_command( 'init plugin', 'WP_Project_Init_Plugin_Command' );
  WP_CLI::add_command( 'init secure', 'WP_Project_Init_Secure_Command' );
}