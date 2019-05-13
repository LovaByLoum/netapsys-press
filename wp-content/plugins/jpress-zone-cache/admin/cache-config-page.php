<?php 
/**
 * page d'administration
 */
    global $jpress_zone_cache;
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
	
	//add zone cache
    $msgzonecacheform = '';
	if (isset($_POST["add-cache"])){
        if ( !empty($_POST['cache-name']) && !empty($_POST['cache-file']) ){
            jPressZoneCacheService::set_cache_config(
                $_POST['cache-name'],
                intval($_POST['cache-role']),
                intval($_POST['cache-langue']),
                $_POST['cache-file'],
                intval($_POST['cache-prechargement'])
            );
        } else {
            $msgzonecacheform = __('Le nom du cache de zone et le fichier de cache sont requis','jzc');
        }

	}
	
	//delete zone cache
    $msgzonecachelist = '';
	if ( isset($_POST["delete-cache"]) ){
        if ( !empty($_POST['cache-ids']) ){
            jPressZoneCacheService::delete_cache_config($_POST['cache-ids']);
        } else {
            $msgzonecachelist = __('Veuillez séléctionner au moins un élément à supprimer','jzc');
        }
    }

    //delete cache bdd
    $msgcacheslist = '';
	if ( isset($_POST["delete-caches"]) ){
        if ( !empty($_POST['cache-ids']) ){
            jPressZoneCacheService::delete_caches($_POST['cache-ids']);
        } else {
            $msgcacheslist = __('Veuillez séléctionner au moins un élément à supprimer','jzc');
        }
    }
	
	//generate cache bdd
	if ( isset($_POST["generate-cache"]) ){
        if ( !empty($_POST['cache-ids']) ){
            jPressZoneCacheService::generate_caches($_POST['cache-ids']);
        } else {
            $msgcacheslist = __('Veuillez séléctionner au moins un élément à précharger','jzc');
        }
    }

    //generate all cache
    if ( isset($_POST["generate-all-cache"]) ){
        jPressZoneCacheService::flush_and_preload_cache();
    }

	//purge cache bdd
	if ( isset($_POST["purge-caches"]) ){
        jPressZoneCacheService::purge_all();
	}

    //save preload config
    if ( isset($_POST['save-preload-config']) ){
        $events = isset($_POST['preload-event']) ? $_POST['preload-event'] : array();
        update_option('jzc-preload-event', $events);
    }

?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#filtre_limit").val("<?php echo $limit;?>");
		
	});
</script>
<div id="wpbody">
	<div id="wpbody-content">
		<div class="wrap">
            <h1><?php _e('Zone cache', 'jzc');?></h1>
		</div>

		<div id="col-container">
			<div id="col-left" style="float:left;">
				<div class="col-wrap">
                    <h3><?php _e('Paramétrage de cache', 'jzc');?></h3>
					<div class="form-wrap">
						<h2><?php _e('Ajouter une zone cache', 'jzc');?></h2>
						<form class="validate" action="" method="post" >
                            <?php if ( !empty($msgzonecacheform) ) : ?>
                                <p style="color: red;"><?php echo $msgzonecacheform;?></p>
                            <?php endif;?>
							<div class="form-field form-required">
								<label for="cache-name"><?php _e('Nom de la zone cache', 'jzc');?></label>
								<input type="text" required="true" size="40" value="" id="cache-name" name="cache-name">
								<p><?php _e('L\'identifiant de la zone à mettre en cache (Pas d\'espace)', 'jzc');?></p>
							</div>
							<div class="form-field">
								<label for="cache-role"><?php _e('Cache différent par rôle', 'jzc');?></label>
								<input type="checkbox" value="1" id="cache-role" name="cache-role" style="width:auto;">
								<p><?php _e('Si coché, permet d\'avoir une mise en cache différente selon le rôle de l\'utilisateur qui visite', 'jzc');?></p>
							</div>
							<div class="form-field">
								<label for="cache-langue"><?php _e('Cache différent par langue', 'jzc');?></label>
								<input type="checkbox" value="1" id="cache-langue" name="cache-langue" style="width:auto;">
								<p><?php _e('Si coché, permet d\'avoir une mise en cache différente selon la langue de la page visitée', 'jzc');?></p>
							</div>
							<div class="form-field">
								<label for="cache-file"><?php _e('Template de la zone', 'jzc');?></label>
								<?php
								$dirname = $jpress_zone_cache->dir_caches;
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
								<select class="postform" id="cache-file" required="true" name="cache-file">
									<?php echo $fileoption;?>
								</select>
								<p><?php echo sprintf(__('Les caches de zone seront genérés à partir du fichier template de la zone se trouvant dans votre theme dans le dossier %s. Si ce champ est vide, il vous faudra d\'abord ajouter votre fichier template dans ce dossier en mettant le contenu dynamique de la zone à mettre en cache.', 'jzc'), $jpress_zone_cache->dir_caches);?></p>
							</div>
                            <div class="form-field">
                                <label for="cache-prechargement"><?php _e('Précharger le cache', 'jzc');?></label>
                                <input type="checkbox" value="1" id="cache-prechargement" name="cache-prechargement" style="width:auto;">
                                <p><?php _e('Si coché, le cache de cette zone sera préchargé automatiquement dès que le cache est vidé.', 'jzc');?></p>
                            </div>
							<p class="submit"><input type="submit" value="<?php _e('Ajouter une zone cache', 'jzc');?>" class="button button-primary" id="submit" name="add-cache"></p>
						</form>
					</div>
				</div>

                <div class="col-wrap">
                    <form method="post" action="" id="">
                        <h2><?php _e('Liste des zones cache', 'jzc');?></h2>

                        <?php if ( !empty($msgzonecachelist) ) : ?>
                            <p style="color: red;"><?php echo $msgzonecachelist;?></p>
                        <?php endif;?>

                        <table class="wp-list-table widefat fixed tags">
                            <thead>
                            <tr valign="top">
                                <td style="width:20px;"><label for="primary" style="font-weight:bold;"></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Nom', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Rôle', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Langue', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Fichier template', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Prechargement auto', 'jzc');?></label></td>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr valign="top">
                                <td style="width:20px;"><label for="primary" style="font-weight:bold;"></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Nom', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Rôle', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Langue', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Fichier template', 'jzc');?></label></td>
                                <td><label for="primary" style="font-weight:bold;"><?php _e('Prechargement auto', 'jzc');?></label></td>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            $list = jPressZoneCacheService::get_list_cache_config();
                            if (!empty($list["data"])):
                                foreach ($list["data"] as $line):?>
                                    <tr valign="top">
                                        <td style="width:20px;"><input type="checkbox" name="cache-ids[]" value="<?php echo $line->id; ?>"></td>
                                        <td><?php echo $line->name; ?></td>
                                        <td><?php echo $line->role; ?></td>
                                        <td width="30%"><?php echo $line->langue; ?></a></td>
                                        <td><?php echo $line->zone_file; ?></td>
                                        <td><?php echo $line->preload; ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            else:
                                ?>
                                <tr valign="top">
                                    <td colspan="6"><h5 style="text-align: center;"><?php _e('Aucun résultat', 'jzc');?></h5></td>
                                </tr>
                            <?php
                            endif;
                            ?>
                            </tbody>
                        </table>
                        <br>
                        <p class="submit">
                            <input type="submit" value="<?php _e('Supprimer la séléction', 'jzc');?>" class="button action" id="delete-cache" name="delete-cache">
                            &nbsp;&nbsp;
                            <input type="submit" name="generate-cache" id="generate-cache" class="button" value="<?php _e('Précharger la séléction', 'jzc');?>">
                        </p>
                    </form>
                </div>

			</div>
			
			<div id="col-right">
				<div class="col-wrap">
                    <h3><?php _e('Liste des caches de zone générés en base de données', 'jzc');?></h3>
					<form method="post" action="" id="posts-filter">

                        <?php if ( !empty($msgcacheslist) ) : ?>
                            <p style="color: red;"><?php echo $msgcacheslist;?></p>
                        <?php endif;?>

						<?php 
						$offset = $limit*($paged -1) ;
						$list = jPressZoneCacheService::get_list_caches($offset,$limit);
						?>
						<div class="tablenav top">
							<div class="alignleft actions">
								<select name="filtre_limit" >
									<option selected="selected" value="-1"><?php _e('Affichage', 'jzc');?></option>
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
								<input type="submit" value="<?php _e('Appliquer', 'jzc');?>" class="button action" name="">
							</div>
							<div class="tablenav-pages">
								<div class="tablenav-pages">
									<span class="displaying-num"><?php echo $list["total"];?> <?php _e('élément', 'jzc');?><?php if ($list["total"]>0):?>s<?php endif;?></span>
									<span class="pagination-links">
										<a href="?page=envoie-mail-alerte" title="<?php _e('Aller à la première page', 'jzc');?>" class="first-page">«</a>
										<a <?php if(!$noprecpage):?>href="?page=envoie-mail-alerte&amp;paged=<?php echo $precpage;?>"<?php endif;?> title="<?php _e('Aller à la page précèdente', 'jzc');?>" class="prev-page <?php if($noprecpage):?>disabled<?php endif;?>">‹</a>
										<span class="paging-input">
											<?php echo $paged;?> sur <span class="total-pages"><?php echo $nbrpage;?></span>
										</span>
										<a <?php if(!$nonextpage):?>href="?page=envoie-mail-alerte&amp;paged=<?php echo $nextpage;?>"<?php endif;?> title="<?php _e('Aller à la page suivante', 'jzc');?>" class="next-page <?php if($nonextpage):?>disabled<?php endif;?>">›</a>
										<a href="?page=envoie-mail-alerte&amp;paged=<?php echo $nbrpage;?>" title="<?php _e('Aller à la dernière page', 'jzc');?>" class="last-page">»</a>
									</span>
								</div>
							</div>
							<br class="clear">
						</div>
						<table class="wp-list-table widefat fixed tags">
							<thead>
								<tr valign="top">
										<td style="width:20px;"><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Nom', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Rôle', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Langue', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Date', 'jzc');?></label></td>
								</tr>
							</thead>
							<tfoot>
								<tr valign="top">
										<td style="width:20px;"><label for="primary" style="font-weight:bold;"></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Nom', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Rôle', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Langue', 'jzc');?></label></td>
										<td><label for="primary" style="font-weight:bold;"><?php _e('Date', 'jzc');?></label></td>
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
									$cache = jPressZoneCacheService::get_cache_by('id',$line->cache_id);
								?>
									<tr valign="top">
											<td style="width:20px;"><input type="checkbox" name="cache-ids[]" value="<?php echo $line->id; ?>"></td>
											<td><?php echo $cache->name; ?></td>
											<td><?php echo $line->role; ?></td>
											<td width="30%"><?php echo $line->langue; ?></a></td>
											<td><?php echo $line->date; ?></a></td>
									</tr>
								<?php 
								endforeach;
							else:
								?>
								<tr valign="top">
									<td colspan="5"><h5 style="text-align: center;"><?php _e('Aucun résultat', 'jzc');?></h5></td>
								</tr>
								<?php
							endif;
							?>	
						</tbody>
					</table>
                    <br>
					<p class="submit">
                        <input type="submit" name="delete-caches" id="delete-caches" class="button action" value="<?php _e('Supprimer la séléction', 'jzc');?>">
                        &nbsp;&nbsp;
                        <input type="submit" name="purge-caches" id="purge-caches" class="button button-primary action" value="<?php _e('Vider tout les caches', 'jzc');?>"></p>
					</form>
				</div>

                <div class="col-wrap">
                    <h3><?php _e('Préchargement de cache', 'jzc');?></h3>
                    <form  method="post" action="" id="">
                        <p><?php _e( 'Vous pouvez précharger manuellement le cache à l\'aide des liens individuels sur la liste des zones cache à gauche ou à partir du bouton ci-dessous pour tout précharger.' , 'jzc');?></p>
                        <p><?php _e( 'Assurez-vous que tous les codes permettant de précharger correctement la zone cache sont présents dans les fichiers template zone cache mais pas à l\'exterieur, sinon le cache ne sera pas correctement généré ou provoquera une erreur.','jzc');?></p>
                        <p class="submit">
                            <input type="submit" name="generate-all-cache" id="submit" class="button button-primary" value="<?php _e('Tout précharger maintenant', 'jzc');?>">
                        </p>

                        <?php $jzc_preload_event = get_option('jzc-preload-event', array());?>
                        <p><?php _e( 'Les zones cache avec la valeur preload à 1 peuvent être préchargées automatiquement et momentanément lors des évènements ci-dessous :', 'jzc');?></p>
                        <div class="form-field">
                            <label><input type="checkbox" value="save_post" <?php if ( in_array('save_post', $jzc_preload_event) ) : ?>checked="checked"<?php endif;?> name="preload-event[]" style="width:auto;">&nbsp;<?php _e('A la publication/modification des posts', 'jzc');?></label>
                        </div>
                        <div class="form-field">
                            <label><input type="checkbox" value="edit_term"  <?php if ( in_array('edit_term', $jzc_preload_event) ) : ?>checked="checked"<?php endif;?> name="preload-event[]" style="width:auto;">&nbsp;<?php _e('A la publication/modification des termes de taxonomie', 'jzc');?></label>
                        </div>
                        <br>
                        <p class="submit">
                            <input type="submit" name="save-preload-config" id="submit" class="button" value="<?php _e('Sauvegarder la configuration', 'jzc');?>">
                        </p>

                    </form>
                </div>

			</div>
			<div class="clear"></div>

	</div><!-- wpbody-content -->
	<div class="clear"></div>
</div>
</div>