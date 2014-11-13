<?php
$posts_types = get_post_types();
wp_enqueue_script( 'accordion' );

if(isset($_POST["jiesubmit"])){
  unset($_POST["jiesubmit"]);
  update_option('jie_options',$_POST);
}

$jie_options = get_option('jie_options');

?>
<style>
.jie-button-add,
.jie-button-remove,
.jie-button-edit,
.jie-button-delete {
	background: url(<?php echo plugin_dir_url(__FILE__);?>/images/sprite.png) -16px -116px no-repeat #fff;
	display: inline-block;
    height: 18px;
    width: 18px;
    border-radius: 9px;
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
    margin-top: 3px;
}
.jie-button-remove {
	background-position: -66px -116px;
}
</style>
<script>
	jQuery(document).ready(function(){
		jQuery(".jie-button-add").live('click',function(){
			jQuery(this).parents("table").find("tr:last").clone().appendTo(jQuery(this).parents("table"));
			jQuery(this).parents("table").find("tr:last").find('input').val("");
		});
		jQuery(".jie-button-remove").live('click',function(){
			if(jQuery(this).parents("table").find("tr").length>1){
				jQuery(this).parents("tr").remove();
			}
		});
		
	})
</script>
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Importer Excel Administration</h2>
    <br><br>
    <form method="post" action="" id="jie-cpt">
      <div id="side-sortables" class="accordion-container">
        <ul class="outer-border">
        <?php
        global $wpdb;
        $taxonomies = get_taxonomies();
        foreach ( $posts_types as $pt):
          $posttype = get_post_type_object($pt);
          ?>
            <li class="control-section accordion-section top">
              <h3 class="accordion-section-title hndle" tabindex="0"><?php echo $posttype->labels->name ;?></h3>
              <div class="accordion-section-content " style="display: none;">
                <div class="inside">
                  <label><input type="checkbox" name="enable[]" value="<?php echo $pt;?>" <?php if(isset($jie_options['enable']) && in_array($pt,$jie_options['enable'])){echo 'checked';}?>>  Activer l'import par excel</label><br><br>
                  <h3 class="title">Liste des colonnes</h3>
                  <table>
                  	  <?php 
                  	  if(isset($jie_options["libelle"][$pt])):
	                  	  foreach ($jie_options["libelle"][$pt] as $key => $libelle):?>
		                  <tr>
		                  	<td>
		                  		<label>Libellé  </label><input name="libelle[<?php echo $pt ;?>][]" type="text" value="<?php echo $libelle;?>" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<label>Slug  </label><input name="slug[<?php echo $pt ;?>][]" type="text" value="<?php echo $jie_options["slug"][$pt][$key];?>" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<a href="javascript:;" class="jie-button-remove"></a>
		                  		<a href="javascript:;" class="jie-button-add"></a>
		                  	</td>
		                  </tr>
		                  <?php endforeach;
		              else:?>
		              	  <tr>
		                  	<td>
		                  		<label>Libellé  </label><input name="libelle[<?php echo $pt ;?>][]" type="text" value="" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<label>Slug  </label><input name="slug[<?php echo $pt ;?>][]" type="text" value="" class="regular-text code">
		                  	</td>
		                  	<td>
		                  		<a href="javascript:;" class="jie-button-remove"></a>
		                  		<a href="javascript:;" class="jie-button-add"></a>
		                  	</td>
		                  </tr>
	                  <?php endif;?>	
                  </table>
                </div>
              </div>
            </li>
        <?php endforeach;?>
        </ul>
      </div>
      <br>
      <input type="submit" class="button-primary" name="jiesubmit" value="Save">
    </form>
</div>