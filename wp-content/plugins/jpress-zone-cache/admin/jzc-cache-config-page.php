<?php 
/**
 * page d'administration
 */
	@session_start();

	if (isset($_POST["filtre_limit"])){
		$limit = $_POST["filtre_limit"];
		$_SESSION["filtre_limit"] = $limit;
	}else{
		if(isset($_SESSION["filtre_limit"])){
			$limit = $_SESSION["filtre_limit"];
		}else{
			$limit = 10;
		}
	}
	
	if (isset($_REQUEST["paged"])){
		$paged = $_REQUEST["paged"];
	}else{
		$paged = 1;
	}
	
	//add
	if (isset($_POST["add-cache"])){
		jzc_set_cache(
			$_POST['cache-name'],
			intval($_POST['cache-role']),
			intval($_POST['cache-langue']),
			$_POST['cache-file']
		);
	}
	
	//delete
	if (isset($_POST["delete-cache"]) && !empty($_POST['cache-ids'])){
		jzc_delete_cache($_POST['cache-ids']);
	}
	
	if (isset($_POST["delete-caches"]) && !empty($_POST['cache-ids'])){
		jzc_delete_caches($_POST['cache-ids']);
	}
	
	//generate
	if (isset($_POST["generate-cache"]) && !empty($_POST['cache-ids'])){ 
		jzc_generate_caches($_POST['cache-ids']);
	}

?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#filtre_limit").val("<?php echo $limit;?>");
		
	});
</script>
<div id="wpbody">
	<div id="wpbody-content">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2 class="nav-tab-wrapper">
			<a href="#list-env-tab" title="Liste des caches" class="nav-tab nav-tab-active" id="list-env">Liste des caches</a>
		</h2>
		<div id="col-container">
			<div id="col-left" style="float:left;">
				<div class="col-wrap">
					<div class="form-wrap">
						<h3>Ajouter un nouveau cache</h3>
						<form class="validate" action="" method="post" >
							<div class="form-field form-required">
								<label for="cache-name">Nom</label>
								<input type="text" aria-required="true" size="40" value="" id="cache-name" name="cache-name">
								<p>L'identifiant de la zone à mettre en cache</p>
							</div>
							<div class="form-field">
								<label for="cache-role">Rôle</label>
								<input type="checkbox" value="1" id="cache-role" name="cache-role" style="width:auto;">
								<p>Permettre d'avoir une mise en cache différent selon le rôle</p>
							</div>
							<div class="form-field">
								<label for="cache-langue">Langue</label>
								<input type="checkbox" value="1" id="cache-langue" name="cache-langue" style="width:auto;">
								<p>Permettre d'avoir une mise en cache différent selon la langue</p>
							</div>
							<div class="form-field">
								<label for="cache-file">Fichier de cache</label>
								<?php
								global $jzc_dir_caches;
								//$dirname = WP_PLUGIN_DIR . '/jpress-zone-cache/zone_file/';
								$dirname = $jzc_dir_caches;
								$dir = opendir($dirname); 
								$fileoption = "";
								while($file = readdir($dir)) {
									if($file != '.' && $file != '..' && !is_dir($dirname.$file))
									{
										$fileoption .= '<option value="'.$file.'">'.$file.'</option>';
									}
								}
								closedir($dir);
								?>
								<select class="postform" id="cache-file" name="cache-file">
									<?php echo $fileoption;?>
								</select>
								<p>Les caches seront genérés à partir du fichier zone de cache.</p>
							</div>
							<p class="submit"><input type="submit" value="Ajouter un nouveau cache" class="button button-primary" id="submit" name="add-cache"></p>
						</form>
					</div>
				</div>
			</div>
			
			<div id="col-right">
				<div class="col-wrap">
					<form method="post" action="" id="posts-filter">
						<?php 
						$offset = $limit*($paged -1) ;
						$list = jzc_get_list_caches($offset,$limit);
						?>
						<div class="tablenav top">
							<div class="alignleft actions">
								<select name="filtre_limit" >
									<option selected="selected" value="-1">Affichage</option>
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
								<input type="submit" value="Appliquer" class="button action" name="">
							</div>
							<div class="tablenav-pages">
								<div class="tablenav-pages">
									<span class="displaying-num"><?php echo $list["total"];?> élément<?php if ($list["total"]>0):?>s<?php endif;?></span>
									<span class="pagination-links">
										<a href="?page=envoie-mail-alerte" title="Aller à la première page" class="first-page">«</a>
										<a <?php if(!$noprecpage):?>href="?page=envoie-mail-alerte&amp;paged=<?php echo $precpage;?>"<?php endif;?> title="Aller à la page précèdente" class="prev-page <?php if($noprecpage):?>disabled<?php endif;?>">‹</a>
										<span class="paging-input">
											<?php echo $paged;?> sur <span class="total-pages"><?php echo $nbrpage;?></span>
										</span>
										<a <?php if(!$nonextpage):?>href="?page=envoie-mail-alerte&amp;paged=<?php echo $nextpage;?>"<?php endif;?> title="Aller à la page suivante" class="next-page <?php if($nonextpage):?>disabled<?php endif;?>">›</a>
										<a href="?page=envoie-mail-alerte&amp;paged=<?php echo $nbrpage;?>" title="Aller à la dernière page" class="last-page">»</a>
									</span>
								</div>
							</div>
							<br class="clear">
						</div>
						<table class="wp-list-table widefat fixed tags">
							<thead>
								<tr valign="top">
										<td><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;">Nom</label></td>
										<td><label for="primary" style="font-weight:bold;">Rôle</label></td>
										<td><label for="primary" style="font-weight:bold;">Langue</label></td>
								</tr>
							</thead>
							<tfoot>
								<tr valign="top">
										<td><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;">Nom</label></td>
										<td><label for="primary" style="font-weight:bold;">Rôle</label></td>
										<td><label for="primary" style="font-weight:bold;">Langue</label></td>
								</tr>
							</tfoot>
							<tbody>
							<?php 
							
							$nbrpage = ceil($list["total"]/$limit);
							$precpage = $paged-1;
							$nextpage = $paged+1;
							$noprecpage = ($precpage==0)?true:false;
							$nonextpage = ($nextpage>$nbrpage)?true:false;
							if (!empty($list["data"])):
								foreach ($list["data"] as $line):
									$cache = jzc_get_cache_by('id',$line->cache_id);
								?>
									<tr valign="top">
											<td><input type="checkbox" name="cache-ids[]" value="<?php echo $line->id; ?>"></td>
											<td><?php echo $cache->name; ?></td>
											<td><?php echo $line->role; ?></td>
											<td width="30%"><?php echo $line->langue; ?></a></td>
									</tr>			
								<?php 
								endforeach;
							else:
								?>
								<tr valign="top">
									<td><h5>Aucun résultat</h5></td>
								</tr>
								<?php
							endif;
							?>	
						</tbody>
					</table>
					<p class="submit"><input type="submit" name="delete-caches" id="delete-caches" class="button action" value="Supprimer"></p>
					</form>
				</div>
			</div>
			<div class="clear"></div>
			
			<div id="col-left">
				<div class="col-wrap">
					<form method="post" action="" id="">
						<table class="wp-list-table widefat fixed tags">
							<thead>
								<tr valign="top">
										<td><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;">Nom</label></td>
										<td><label for="primary" style="font-weight:bold;">Rôle</label></td>
										<td><label for="primary" style="font-weight:bold;">Langue</label></td>
										<td><label for="primary" style="font-weight:bold;">Zone file</label></td>
								</tr>
							</thead>
							<tfoot>
								<tr valign="top">
										<td><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;">Nom</label></td>
										<td><label for="primary" style="font-weight:bold;">Rôle</label></td>
										<td><label for="primary" style="font-weight:bold;">Langue</label></td>
										<td><label for="primary" style="font-weight:bold;">Zone file</label></td>
								</tr>
							</tfoot>
							<tbody>
							<?php 
							$list = jzc_get_list_cache();
							if (!empty($list["data"])):
								foreach ($list["data"] as $line):?>
									<tr valign="top">
											<td><input type="checkbox" name="cache-ids[]" value="<?php echo $line->id; ?>"></td>
											<td><?php echo $line->name; ?></td>
											<td><?php echo $line->role; ?></td>
											<td width="30%"><?php echo $line->langue; ?></a></td>
											<td><?php echo $line->zone_file; ?></td>
									</tr>			
								<?php 
								endforeach;
							else:
								?>
								<tr valign="top">
									<td><h5>Aucun résultat</h5></td>
								</tr>
								<?php
							endif;
							?>	
						</tbody>
					</table>
					<p class="submit"><input type="submit" name="generate-cache" id="submit" class="button button-primary" value="Genérér"> &nbsp;&nbsp;&nbsp;<input type="submit" value="Supprimer" class="button action" id="delete-cache" name="delete-cache"></p>
					</form>
				</div>
			</div>
			
	</div><!-- wpbody-content -->
	<div class="clear"></div>
</div>
</div>