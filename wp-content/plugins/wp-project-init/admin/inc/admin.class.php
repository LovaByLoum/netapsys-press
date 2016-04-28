<?php
/**
 * classes de services admin
 */

class WP_Project_Init_Admin{
	static $_tabs = array(
	    'generate-themes' => 'Generateur de thème',
	    'plugin-install' => 'Plugins de base',
	    'plugin-custom-install' => 'Plugins Custom',
	    'plugin-premium-install' => 'Plugins Payants',
	    'plugin-secure' => 'Must Use Plugin et Sécurisation',
	);
	static $_required = array();
	static $_error = array();
	
	//render fields
	public static function render_fields($type, $name, $required = false, $info = '', $default=''){
		switch ($type){
			case 'text':?>
			<input class="regular-text <?php if(in_array($name,self::$_error)):?>error<?php endif;?>" type="text" value="<?php echo $default;?>" name="<?php echo $name;?>">
			<?php if(!empty($info)):?><br><em><?php echo $info;?></em><?php endif;?>
			<?php break;
			
			case 'textarea':?>
			<textarea class="regular-text <?php if(in_array($name,self::$_error)):?>error<?php endif;?>" name="<?php echo $name;?>"><?php echo $default;?></textarea>
			<?php if(!empty($info)):?><br><em><?php echo $info;?></em><?php endif;?>
			<?php break;	
			
			case 'file':?>
			<input class="regular-text <?php if(in_array($name,self::$_error)):?>error<?php endif;?>" type="file" name="<?php echo $name;?>">
			<?php if(!empty($info)):?><br><em><?php echo $info;?></em><?php endif;?>
			<?php break;	
			
			case 'checkbox':?>
			<input class="<?php if(in_array($name,self::$_error)):?>error<?php endif;?>" type="checkbox" value="1" <?php echo $default;?> name="<?php echo $name;?>" id="<?php echo $name;?>">
			<?php if(!empty($info)):?><label for="<?php echo $name;?>"><?php echo $info;?></label><?php endif;?>
			<?php break;	
			
			default:break;
		}
		
		if($required){
			array_push(self::$_required,$name);
		}
	}
	
	public static function hidden_fields(){
		$hiddenfields = implode(',',self::$_required);
		?>
		<input type="hidden" value="<?php echo $hiddenfields;?>" name="required_fields">
		<?php
	}
	
	//process submission
	public static function process_post(){
		$message = '';
		foreach (self::$_tabs as $tab => $lab) {
			if(isset($_POST) && isset($_POST[$tab])){
				$func = 'process_'.str_replace('-','_',$tab);
				return self::$func();
			}
		}		
	}
	
	//process submission generate theme form
	public static function process_generate_themes(){
		$postdata = $_POST;
		$msg = self::check_required_fields($postdata);
		if(!empty($msg)) return $msg;
		
		//files
		if(isset($_FILES)){
			$postdata['theme_screenshot'] = $_FILES['theme_screenshot'];
			$info = pathinfo($postdata['theme_screenshot']['name']);
			if($info['extension']!='jpg' && $info['extension']!='png') return 'Le format de theme_screenshot n\'est pas au format jpg ou png<br>';

			$postdata['theme_logo_admin'] = $_FILES['theme_logo_admin'];
			$info = pathinfo($postdata['theme_logo_admin']['name']);
			if($info['extension']!='jpg' && $info['extension']!='png') return 'Le format de theme_logo_admin n\'est pas au format jpg ou png<br>';
		}
		
		//valid
		if(empty($msg)){
			//process
			unset($postdata['required_fields']);
			unset($postdata['generate-themes']);
			$gen_themes = new WP_Generate_Themes($postdata);
			$msg = $gen_themes->generate();
		}
		
		return $msg;
	}
	
	//process submission theme option form
	public static function process_plugin_install(){
    $postdata = $_POST;
    $plug_manager = new WP_Plugin_Manager($postdata);
    $msg = $plug_manager->install_plugins();

		return $msg;
	}

  //process submission theme option form
  public static function process_plugin_secure(){
    $postdata = $_POST;
    $plug_manager = new WP_Plugin_Manager($postdata);
    $msg = $plug_manager->copy_muplugins();

    return $msg;
  }
	
	//check required fields
	public static function check_required_fields($postdata){
		$msg = '';
		if(isset($postdata['required_fields'])){
			$required_fields = explode(',',$postdata['required_fields']);
			foreach ($required_fields as $field) {
				if((!isset($postdata[$field]) || empty($postdata[$field])) && (!isset($_FILES[$field]) || empty($_FILES[$field]['name']))){
					$msg.= 'Le champs ' . $field . ' est requis<br>';
					array_push(self::$_error,$field);
				}
			}	
		}
		return $msg;		
	}

  public static function get_plugin_path( $plugin ){
    $plugins = get_plugins();
    $plugins = array_keys($plugins);

    foreach($plugins as $plugin_path){
      if(strpos($plugin_path, $plugin.'/') === 0) {
        return $plugin_path;
        break;
      }
    }
    return false;
  }
	
}
