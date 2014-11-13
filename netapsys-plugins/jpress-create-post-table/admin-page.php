<?php
global $jcpt_options,$wpdb;
$posts_types = get_post_types();
wp_enqueue_script( 'accordion' );

if(isset($_POST["jcptsubmit"])){ 
  unset($_POST["jcptsubmit"]);
  $recreate = $_POST["recreate"];
  unset($_POST["recreate"]);
  update_option('jcpt_options',$_POST);
  
  $jcpt_options = get_option('jcpt_options');
  foreach ( $posts_types as $pt){
  	 if(in_array($pt,$jcpt_options['enable'])){
  	 	$columns = $jcpt_options['column'][$pt];
  	 	$types = $jcpt_options['type'][$pt];
  	 	$values = $jcpt_options['value'][$pt];
  	 	$indexes = $jcpt_options['index'][$pt];
  	 	$tablename = $wpdb->prefix.$pt.'s';
  	 	
  	 	//drop table if checkbox enabled => disabled
  	 	if(is_array($recreate) && in_array($pt,$recreate) && $wpdb->get_var("show tables like '$tablename'") == $tablename){
  	 		$sql = "DROP TABLE  `". $tablename."`";
  	 		$wpdb->query($sql);
  	 	}
  	 	
  	 	//create table
  	 	if($wpdb->get_var("show tables like '$tablename'") != $tablename) {
		    $sql = "CREATE TABLE `".$tablename."` (
				  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
				  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `post_content` longtext NOT NULL,
				  `post_title` text NOT NULL,
				  `post_excerpt` text NOT NULL,
				  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
				  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
				  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
				  `post_password` varchar(20) NOT NULL DEFAULT '',
				  `post_name` varchar(200) NOT NULL DEFAULT '',
				  `to_ping` text NOT NULL,
				  `pinged` text NOT NULL,
				  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `post_content_filtered` longtext NOT NULL,
				  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
				  `guid` varchar(255) NOT NULL DEFAULT '',
				  `menu_order` int(11) NOT NULL DEFAULT '0',
				  `post_type` varchar(20) NOT NULL DEFAULT '".$pt."',
				  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
				  `comment_count` bigint(20) NOT NULL DEFAULT '0',";
		    		
		    		//add custom column
		    	   foreach ($columns as $key=>$field) {
		    			$field = sanitize_title($field);
		    			$field = str_replace('-','_',$field);
		    			$sql.="`". $field ."` " . $types[$key]." NULL,";
		    	   }
				  
		    	   //add index to custom column
		    	  $indexsql =  '';
				  foreach ($columns as $key=>$field) {
				  		$field = sanitize_title($field);
		    			$field = str_replace('-','_',$field);
				  		if($indexes[$key]==1){
				  			$indexsql.=" KEY `".$field."` (`".$field."`),";			
				  		}
				  
		    	  }
		    $sql.="PRIMARY KEY (`ID`),
		    	  " . $indexsql ."	
				  KEY `post_name` (`post_name`),
				  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
				  KEY `post_parent` (`post_parent`),
				  KEY `post_author` (`post_author`)
				) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		    
			$wpdb->query($sql);
		}
  	 	
  	 }else{
  	 	//drop table if exist
  	 	//no need to drop table
  	 }
  }
}

if(!$jcpt_options){
	$jcpt_options = get_option('jcpt_options');	
}

?>
<style>
.jcpt-button-add,
.jcpt-button-remove,
.jcpt-button-edit,
.jcpt-button-delete {
	background: url(<?php echo plugin_dir_url(__FILE__);?>/images/sprite.png) -16px -116px no-repeat #fff;
	display: inline-block;
    height: 18px;
    width: 18px;
    border-radius: 9px;
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
    margin-top: 3px;
}
.jcpt-button-remove {
	background-position: -66px -116px;
}
</style>
<script>
	jQuery(document).ready(function(){
		jQuery(".jcpt-button-add").live('click',function(){
			jQuery(this).parents("table").find("tr:last").clone().appendTo(jQuery(this).parents("table"));
			jQuery(this).parents("table").find("tr:last").find('input[type="text"]').val("");
			jQuery(this).parents("table").find("tr:last").find('select option').removeAttr("selected");
			jQuery(this).parents("table").find("tr:last").find('input[type="checkbox"]').removeAttr("checked");
		});
		jQuery(".jcpt-button-remove").live('click',function(){
			if(jQuery(this).parents("table").find("tr").length>1){
				jQuery(this).parents("tr").remove();
			}
		});
		
	})
</script>
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Création de tables personnalisés pour les contenus</h2>
    <br><br>
    <form method="post" action="" id="jcpt-cpt">
      <h3>Activer l'API</h3>
      <input type="checkbox" name="doturn" value="1" <?php if(isset($jcpt_options['doturn'])){echo 'checked';}?>>
      <div id="side-sortables" class="accordion-container">
        <ul class="outer-border">
        <?php
        $taxonomies = get_taxonomies();
        foreach ( $posts_types as $pt):
          $posttype = get_post_type_object($pt);
          ?>
            <li class="control-section accordion-section top">
              <h3 class="accordion-section-title hndle" tabindex="0"><?php echo $posttype->labels->name ;?></h3>
              <div class="accordion-section-content " style="display: none;">
                <div class="inside">
                  <label><input type="checkbox" name="enable[]" value="<?php echo $pt;?>" <?php if(isset($jcpt_options['enable']) && in_array($pt,$jcpt_options['enable'])){echo 'checked';}?>>  Créer une table custom pour ce type de post</label><br>
                  <em>Si coché, les requettes SQL se feront sur cette nouvelle table et non vers wp_posts</em>
                  <br>
                  <label><input type="checkbox" name="recreate[]" value="<?php echo $pt;?>">  Recréer la table</label><br>
                  <em>Si coché, la table esistante sera supprimée et recréée ; les données seront perdues.</em>
                  <br>
                  <h3 class="title">Liste des colonnes par defaut</h3>
                  <table class="wp-list-table widefat fixed posts">
	                  <thead>
	                  	<tr>
	                  		<td><label><strong>FIELD</strong></label></td>
	                  		<td><label><strong>TYPE</strong></label></td>
	                  		<td><label><strong>INDEX</strong></label></td>
	                  	</tr>
					 </thead>
					 <tbody>
					 	<tr><td><label>ID</label></td><td><label>BIGINT(20)</label></td><td><label>PRIMARY,UNIQUE</label></td></tr> 
						<tr><td><label>post_author</label></td><td><label>BIGINT(20)</label></td><td><label>YES</label></td></tr> 
						<tr><td><label>post_date</label></td><td><label>DATETIME</label></td><td><label>YES</label></td></tr>   
						<tr><td><label>post_date_gmt</label></td><td><label>DATETIME</label></td><td><label></label></td></tr>     
						<tr><td><label>post_content</label></td><td><label>LONGTEXT</label></td><td><label></label></td></tr>      
						<tr><td><label>post_title</label></td><td><label>TEXT</label></td><td><label></label></td></tr>       
						<tr><td><label>post_excerpt</label></td><td><label>TEXT</label></td><td><label></label></td></tr>         
						<tr><td><label>post_status</label></td><td><label>VARCHAR(20)</label></td><td><label>YES</label></td></tr>   
						<tr><td><label>comment_status</label></td><td><label>VARCHAR(20)</label></td><td><label></label></td></tr>   
						<tr><td><label>ping_status</label></td><td><label>VARCHAR(20)</label></td><td><label></label></td></tr>   
						<tr><td><label>post_password</label></td><td><label>VARCHAR(20)</label></td><td><label></label></td></tr>   
						<tr><td><label>post_name</label></td><td><label>VARCHAR(200)</label></td><td><label>YES</label></td></tr>   
						<tr><td><label>to_ping</label></td><td><label>TEXT</label></td><td><label></label></td></tr>           
						<tr><td><label>pinged</label></td><td><label>TEXT</label></td><td><label></label></td></tr>           
						<tr><td><label>post_modified</label></td><td><label>DATETIME</label></td><td><label></label></td></tr>      
						<tr><td><label>post_modified_gmt</label></td><td><label>DATETIME</label></td><td><label></label></td></tr>    
						<tr><td><label>post_content_filtered</label></td><td><label>LONGTEXT</label></td><td><label></label></td></tr>      
						<tr><td><label>post_parent</label></td><td><label>BIGINT(20)</label></td><td><label>YES</label></td></tr>    
						<tr><td><label>guid</label></td><td><label>VARCHAR(255)</label></td><td><label></label></td></tr>   
						<tr><td><label>menu_order</label></td><td><label>INT(11)</label></td><td><label></label></td></tr>        
						<tr><td><label>post_type</label></td><td><label>VARCHAR(20)</label></td><td><label>YES</label></td></tr>   
						<tr><td><label>post_mime_type</label></td><td><label>VARCHAR(100)</label></td><td><label></label></td></tr>   
						<tr><td><label>comment_count</label></td><td><label>BIGINT(20)</label></td><td><label></label></td></tr>    
					 </tbody>
				</table>
				<br>
				<h3 class="title">Nouvelles colonnes personnalisées</h3>
                <table class="wp-list-table widefat fixed posts">
                   	<thead>
	                  	<tr>
	                  		<td style="width: 25em;"><label><strong>FIELD</strong></label></td>
	                  		<td><label><strong>TYPE</strong></label></td>
	                  		<td><label><strong>REPRISE</strong></label></td>
	                  		<td style="width: 50px;"><label><strong>INDEXER</strong></label></td>
	                  		<td><label></label></td>
	                  	</tr>
					 </thead>
                     <tbody>
                  	  <?php 
                  	  $typeoptions = array(
                  	  	'BIGINT',
                  	  	'DATE',
                  	  	'DATETIME',
                  	  	'INT(11)',
                  	  	'LONGTEXT',
                  	  	'TEXT',
                  	  	'VARCHAR(20)',
                  	  	'VARCHAR(200)',
                  	  	'VARCHAR(255)'
                  	  );
                  	  $metakeys = $wpdb->get_col("SELECT DISTINCT meta_key FROM ffcv_postmeta
                            INNER JOIN {$wpdb->prefix}posts ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id  )
                            WHERE {$wpdb->prefix}posts.post_type = '".$pt."'
                            ORDER BY meta_key ASC");

                  	  if(isset($jcpt_options["column"][$pt])):
	                  	  foreach ($jcpt_options["column"][$pt] as $key => $libelle):?>
		                  <tr>
		                  	<td>
		                  		<input name="column[<?php echo $pt ;?>][]" type="text" value="<?php echo $libelle;?>" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<select name="type[<?php echo $pt ;?>][]">
		                  			<?php foreach ($typeoptions as $option):?>
		                  				<option value="<?php echo $option;?>" <?php if($jcpt_options["type"][$pt][$key] == $option):?>selected<?php endif;?>><?php echo $option;?></option>
		                  			<?php endforeach;?>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<select name="value[<?php echo $pt ;?>][]">
		                  			<option value="">Aucun<option>
                            <?php if(count($metakeys) > 0) : ?>
		                  			<?php foreach ($metakeys as $option):?>
		                  				<option value="<?php echo $option;?>" <?php if($jcpt_options["value"][$pt][$key] == $option):?>selected<?php endif;?>><?php echo $option;?></option>
		                  			<?php endforeach;?>
                            <?php elseif(count($jcpt_options["value"][$pt]) > 0) : ?>
                              <?php foreach ($jcpt_options["value"][$pt] as $option):?>
                                <option value="<?php echo $option;?>" <?php if($jcpt_options["value"][$pt][$key] == $option):?>selected<?php endif;?>><?php echo $option;?></option>
                              <?php endforeach;?>
                            <?php endif; ?>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<select name="index[<?php echo $pt ;?>][]">
		                  			<option value="0">Non</option>
		                  			<option value="1" <?php if($jcpt_options["index"][$pt][$key] == '1'):?>selected<?php endif;?>>Oui</option>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<a href="javascript:;" class="jcpt-button-remove" title="supprimer"></a>
		                  		<a href="javascript:;" class="jcpt-button-add" title="ajouter"></a>
		                  	</td>
		                  </tr>
		                  <?php endforeach;
		              else:?>
		              	  <tr>
		                  	<td>
		                  		<input name="column[<?php echo $pt ;?>][]" type="text" value="" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<select name="type[<?php echo $pt ;?>][]">
		                  			<?php foreach ($typeoptions as $option):?>
		                  				<option value="<?php echo $option;?>"><?php echo $option;?></option>
		                  			<?php endforeach;?>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<select name="value[<?php echo $pt ;?>][]">
		                  			<option value="">Aucun<option>
		                  			<?php foreach ($metakeys as $option):?>
		                  				<option value="<?php echo $option;?>"><?php echo $option;?></option>
		                  			<?php endforeach;?>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<select name="index[<?php echo $pt ;?>][]">
		                  			<option value="0">Non</option>
		                  			<option value="1">Oui</option>
		                  		</select>
		                  	</td>
		                  	<td>
		                  		<a href="javascript:;" class="jcpt-button-remove" title="supprimer"></a>
		                  		<a href="javascript:;" class="jcpt-button-add" title="ajouter"></a>
		                  	</td>
		                  </tr>
	                  <?php endif;?>	
	                  </tbody>
                  </table>
                  <h3 class="title">Reprise de données</h3>
                  <label><input type="checkbox" name="cronjob[]" value="<?php echo $pt;?>" <?php if(isset($jcpt_options['cronjob']) && in_array($pt,$jcpt_options['cronjob'])){echo 'checked';}?>>  Créer une action CRON pour reprendre les données.</label><br>
                  <em>Les données seront importées par lot de 100.</em>
                </div>
              </div>
            </li>
        <?php endforeach;?>
        </ul>
      </div>
      <br>
      <input type="submit" class="button-primary" name="jcptsubmit" value="Save">
    </form>
</div>