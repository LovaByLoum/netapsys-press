<?php
/**
 * clas service generate themes
 */

class WP_Generate_Themes{
	public $data;
	
	public function __construct($data){
			$this->data = (object)$data;
			
			//secure
			$this->data->theme_slug = str_replace('-','_',sanitize_title($this->data->theme_slug));
			
			$this->model_name = 'mytheme';
	}
	
	public function generate(){
		$msg = '';
		//mk theme dir
		$fullpath = get_theme_root() . DIRECTORY_SEPARATOR . $this->data->theme_slug;
		if(!file_exists($fullpath)){
			$b = mkdir($fullpath);
			if(!$b) return 'Can\'t create theme folder.';
		}
		
		//copy models
		$msg = $this->copy_theme_dir(WPI_THEME_MODEL_PATH. $this->model_name, $fullpath);
		
		//copy screenshot
		move_uploaded_file($this->data->theme_screenshot['tmp_name'],$fullpath.DIRECTORY_SEPARATOR.'screenshot.png');
		
		//logo admin
		move_uploaded_file($this->data->theme_logo_admin['tmp_name'],$fullpath.DIRECTORY_SEPARATOR.'images/logo_admin.png');

    $nonce_export = wp_create_nonce('theme-' . $this->data->theme_slug);
		$msg='Votre thème a été generé : <a target="__blank" href="' . site_url('wp-admin/themes.php').'">Voir</a> ou <a href="' . site_url('wp-admin/') . '?action=export_theme&theme=' . $this->data->theme_slug . '&nonce=' . $nonce_export . '">Exporter</a>';
		return $msg;
	}

  public function copy_theme_dir($path,$dest){
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
	          $msg .= $this->copy_theme_dir($node,$fullpath);
	        }elseif (is_file($node))  {
	        	$pathinfo = pathinfo($node);
	        	$filename = $pathinfo['basename'];

                //rename some files
                if($filename == $this->model_name.'.pot') $filename = $this->data->theme_slug.'.pot';
                if($filename == $this->model_name.'.css') $filename = $this->data->theme_slug.'.css';

	        	$fullpath = $dest.DIRECTORY_SEPARATOR.$filename;

                $copy = $this->copy_or_not($fullpath,$filename);
                if($copy){
                    $b = copy($node,$fullpath);
                    if(!$b) $msg.= 'Can\'t copy file ' . $fullpath . '<br>';

                    //process file
	                $msg.=$this->process_file($fullpath);
                }
	        }
	    }
	    return $msg;
	}

    //check if we copy files( template)
    public function copy_or_not($fullpath,$filename = ''){
      //archive.php
      if($filename == 'archive.php' && !isset($this->data->theme_tpl_archive)){
          return false;
      }

      //author.php
      if($filename == 'author.php' && !isset($this->data->theme_tpl_author)){
          return false;
      }

      //attachment.php
      if($filename == 'attachment.php' && !isset($this->data->theme_tpl_attachment)){
          return false;
      }

      //category.php
      if($filename == 'category.php' && !isset($this->data->theme_tpl_category)){
          return false;
      }

      //comments.php
      if($filename == 'comments.php' && !isset($this->data->theme_tpl_comment)){
          return false;
      }

      //taxonomy.php
      if($filename == 'taxonomy.php' && !isset($this->data->theme_tpl_taxonomy)){
          return false;
      }

      //date.php
      if($filename == 'date.php' && !isset($this->data->theme_tpl_date)){
          return false;
      }

      //search.php
      if(($filename == 'search.php' || $filename == 'searchform.php') && !isset($this->data->theme_tpl_search)){
          return false;
      }

      //tag.php
      if($filename == 'tag.php' && !isset($this->data->theme_tpl_tag)){
          return false;
      }

      //theme options
      if ( is_dir($fullpath) && preg_match('!(theme-options)$!', $fullpath) &&  !isset($this->data->theme_options)){
        return false;
      }

        return true;
    }

    public function copy_dir($path,$dest){
        $msg = '';
        $nodes = glob($path . '/*');
        foreach ($nodes as $node) {
            if(is_dir($node)){
                $pathinfo = pathinfo($node);
                $filename = $pathinfo['basename'];
                $fullpath = $dest.DIRECTORY_SEPARATOR.$filename;
                if(!file_exists($fullpath)){
                    $b = mkdir($fullpath);
                    if(!$b) $msg.= 'Can\'t create folder ' . $fullpath . '<br>';
                }
                $msg .= $this->copy_dir($node,$fullpath);
            }elseif (is_file($node))  {
                $pathinfo = pathinfo($node);
                $filename = $pathinfo['basename'];
                $fullpath = $dest.DIRECTORY_SEPARATOR.$filename;
                $b = copy($node,$fullpath);
                if(!$b) $msg.= 'Can\'t copy file ' . $fullpath . '<br>';
            }
        }
        return $msg;
    }
	
	
	public function process_file($file){
		//replace theme slug
		$filecontent = file_get_contents($file);
		$filecontent = str_replace($this->model_name,$this->data->theme_slug,$filecontent);

        //author file
        $filecontent = str_replace('__WPI__THEME__AUTHOR__',$this->data->theme_author,$filecontent);

        //theme version
        $filecontent = str_replace('__WPI__THEME__VERSION__',$this->data->theme_version,$filecontent);
		
		//theme info
		$pathinfo = pathinfo($file);
	    $filename = $pathinfo['basename'];
	    if($filename == 'style.css'){
	    	$filecontent = str_replace('__WPI__THEME__NAME__',$this->data->theme_name,$filecontent);
	    	$filecontent = str_replace('__WPI__THEME__DESC__',$this->data->theme_desc,$filecontent);
	    	$filecontent = str_replace('__WPI__THEME__AUTHOR_SITE__',$this->data->theme_author_site,$filecontent);
	    	$filecontent = str_replace('__WPI__THEME__TAGS__',$this->data->theme_tags,$filecontent);
	    }

		if (!$handle = @fopen($file, 'w')) {
	        return '<span style="color:red;">failed : can\'t open file.</span>';
	    }
	    if (@fwrite($handle, $filecontent) === FALSE) {
	        return '<span style="color:red;">failed : can\'t write file.</span>';
	    }
	    @fclose($handle);
	}
	
}