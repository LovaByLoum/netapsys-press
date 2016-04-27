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


}