<?php

/*
Plugin Name: apply-script
Description: Launch any php script of your own with WP API fully loaded
Version: 1.0
Author: Johary
*/

class apply_script{
	
	public static function hooks(){
		add_action('admin_menu',array('apply_script','admin_menu'));
	}
	
	public static function admin_menu(){
								 
		$page = add_menu_page('Lancer un script', 
					  'Lancer un script', 
					  'activate_plugins', 
					  'apply_script_menu_page', 
					   array('apply_script','menu_page') 
					  );

	}
	
	public static function menu_page(){
		
		if( !current_user_can('activate_plugins') ){
			return;
		}
		
		$dirname = dirname(__FILE__). '/scripts/';
		$dir = opendir($dirname); 
		$fileoption = "";
		while($file = readdir($dir)) {
			if($file != '.' && $file != '..' && !is_dir($dirname.$file))
			{
				$fileoption .= '<option value="'.$dirname.$file.'">'.$file.'</option>';
			}
		}
		closedir($dir);
		?>
		
		<div class="wrap">
		
			<h2>Lancer un script</h2>
			
			<form id="scriptform" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" >
				<select name="script_to_apply">
					<?php echo $fileoption;?>
				</select>
				<?php wp_nonce_field('apply_script_file'); ?>
				<input type="submit" />
			</form>
			
			<?php 
				if( !empty($_POST['script_to_apply']) ){
					
					check_admin_referer('apply_script_file');
					
					$script_full_path = $_POST['script_to_apply'];
					
					?>
					<h3 style="margin-bottom:5px">Exécution du script :</h3>
					<div id="script_result" style="padding:8px; border:1px solid #bbb; background-color:#fff">
						<?php 
							if( file_exists($script_full_path) ){
								require_once($script_full_path);
							}else{
								echo "<br/>Erreur : Fichier [$script_full_path] non trouvé...";
							}
						?>
					</div>
					<?php 
				}
			?>
			
		</div>
		
		<?php 
		
	}
	

}

apply_script::hooks();