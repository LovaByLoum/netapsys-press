<?php
/**
 * clas service generate themes
 */

class WP_Plugin_Manager{
	public $data;
	
	public function __construct($data){
			$this->data = (object)$data;

	}

  public function copy_muplugins(){
		$msg = '';
		//mk mu plugin dir
    if(!file_exists( ABSPATH . 'wp-content/mu-plugins' )){
			$b = @mkdir( ABSPATH . 'wp-content/mu-plugins' );
			if(!$b) return 'Can\'t create mu-plugins folder.';
		}
		
		//copy models
		$msg = $this->copy_plugin_dir(WPI_MUPLUGINS_MODEL_PATH, ABSPATH . 'wp-content/mu-plugins');
		
		return $msg;
	}

  public function install_plugins(){
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; //for plugins_api..
    include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

    if (isset($this->data->plugin) && !empty($this->data->plugin)){
      $plugins_to_activate = array_keys($this->data->plugin);
      if ( isset( $this->data->type_profil ) ){
        $filename = '';
        if ( $this->data->type_profil == 1 ){
          if (isset($this->data->profile_name) && !empty($this->data->profile_name)){
            $filename = sanitize_title($this->data->profile_name) . '.profile';
          }
        }else if( $this->data->type_profil == 2 ){
          if (isset($this->data->profile_file) && !empty($this->data->profile_file)){
            $filename = $this->data->profile_file;
          }
        }
        if ( !empty($filename) && !empty($plugins_to_activate)){
          $content = implode("\n", $plugins_to_activate) . "\n";
          self::write_file( WPI_PROFILES_PATH . $filename,  $content, 'w'  );
        }
      }

      //activate
      if ( !empty($plugins_to_activate) ){
        foreach ( $plugins_to_activate as $plugin ){
          $plugin = trim($plugin);
          if ( !is_dir( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin) ){
            //install
            $api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => array('sections' => false) ) );
            $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
            $upgrader->install($api->download_link);

            //acivate
            $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
            activate_plugin( $plugin_path, '', false, true );
          }else{
            //acivate
            $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
            if ( !is_plugin_active($plugin_path) ){
              activate_plugin( $plugin_path, '', false, true );
            }
          }
        }
      }

    }else{
      return 'Rien à activer';
    }

  }

  public function copy_plugin_dir($path,$dest){
		$msg = '';
		$nodes = glob($path . '/*');
	    foreach ($nodes as $node) {
	        if(is_dir($node)){
            $copy = $this->copy_or_not($node);
            if ( !$copy ) continue;

	        	$pathinfo = pathinfo($node);
	        	$filename = $pathinfo['basename'];
	        	$fullpath = $dest.DIRECTORY_SEPARATOR.$filename;
	        	if(!file_exists($fullpath)){
              $b = mkdir($fullpath);
              if(!$b) $msg.= 'Can\'t create folder ' . $fullpath . '<br>';
            }
	          $msg .= $this->copy_plugin_dir($node,$fullpath);
	        }elseif (is_file($node))  {
	        	$pathinfo = pathinfo($node);
	        	$filename = $pathinfo['basename'];

	        	$fullpath = $dest.DIRECTORY_SEPARATOR.$filename;

            $copy = $this->copy_or_not($fullpath,$filename);
            if($copy){
              $b = copy($node,$fullpath);
              if(!$b){
                $msg.= 'Can\'t copy file ' . $fullpath . '<br>';
              }else{
                $msg.= 'Fichier ' . $fullpath . ' copié avec succés.<br>';
              }
            }
	        }
	    }
	    return $msg;
	}

  //check if we copy files( template)
  public function copy_or_not($fullpath,$filename = ''){
    //wp-secure.php
    if($filename == 'wp-secure.php' && !isset($this->data->mu_secure)){
        return false;
    }

    //loginlockdown.php
    if($filename == 'loginlockdown.php' && !isset($this->data->mu_lock)){
        return false;
    }

    //captcha.php
    if($filename == 'captcha.php' && !isset($this->data->mu_captcha)){
      return false;
    }

    //captcha
    if ( is_dir($fullpath) && preg_match('!(captcha_source)$!', $fullpath) &&  !isset($this->data->mu_captcha)){
      return false;
    }

    return true;
  }

  public static function get_profile( $file ){
    $result = file_get_contents( WPI_PROFILES_PATH . $file );
    $array = explode("\n", $result);
    $array = array_filter($array);
    return $array;
  }

  public static function write_file($filename, $somecontent, $openmode = "a"){
    if (!$handle = @fopen($filename, $openmode)) {
      return false;
    }
    if (@fwrite($handle, $somecontent) === FALSE) {
      return false;
    }
    @fclose($handle);
    return true;
  }
}