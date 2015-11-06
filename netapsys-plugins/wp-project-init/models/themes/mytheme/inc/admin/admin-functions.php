<?php
add_action('admin_head','ldc_custom_admin_head');
function ldc_custom_admin_head(){
	?>
	<style>
		.post-php.post-type-candidature #wpbody-content #wpcf-marketing,
		.post-php.post-type-candidature #wpbody-content #submitdiv,
		.post-php.post-type-candidature #wpbody-content #a-rubrique,
		.post-php.post-type-candidature #wpbody-content #a-type_activite,
		.post-php.post-type-candidature #wpbody-content #custom-post-type-onomies-offre,
		.post-php.post-type-candidature #wpbody-content #titlediv,
		.post-php.post-type-candidature #wpbody-content #postbox-container-1,
		.post-php.post-type-candidature #wpbody-content .acf_postbox ,
		.post-php.post-type-candidature-spontane #wpbody-content #wpcf-marketing,
		.post-php.post-type-candidature-spontane #wpbody-content #submitdiv,
		.post-php.post-type-candidature-spontane #wpbody-content #a-rubrique,
		.post-php.post-type-candidature-spontane #wpbody-content #a-type_activite,
		.post-php.post-type-candidature-spontane #wpbody-content #custom-post-type-onomies-offre,
		.post-php.post-type-candidature-spontane #wpbody-content #titlediv,
		.post-php.post-type-candidature-spontane #wpbody-content #postbox-container-1,
		.post-php.post-type-candidature-spontane #wpbody-content .acf_postbox ,
		.post-php.post-type-candidature #wpbody-content .wrap>h2,
		.post-php.post-type-candidature-spontane #wpbody-content .wrap>h2,
		.post-php #wpbody-content #icl_div_config,
		.edit-php.post-type-candidature #wpbody-content .bulkactions select option[value='edit'],
		.edit-php.post-type-candidature-spontane #wpbody-content .bulkactions select option[value='edit'],
		.edit-php.post-type-candidature #wpbody-content .wrap>h2>a,
		.edit-php.post-type-candidature-spontane #wpbody-content .wrap>h2>a,
		#adminmenu #menu-posts-candidature .wp-submenu li a[href='post-new.php?post_type=candidature'],
		#adminmenu #menu-posts-candidature-spontane .wp-submenu li a[href='post-new.php?post_type=candidature-spontane']{
			display:none;
		}
	</style>
	
	<?php
}

add_filter( 'post_row_actions',  'ldc_custom_post_row', 10, 2 );
function ldc_custom_post_row($actions, $id){
    global $post, $current_screen, $mode;
    $post_type_object = get_post_type_object( $post->post_type );

    if($post_type_object->name == "candidature"   || $post_type_object->name == "candidature-spontane"  ){
    	$actions['edit'] = str_replace('Modifier','Afficher',$actions['edit']);
        unset($actions['inline hide-if-no-js']);
        unset($actions['view']);
        unset($actions['clone']);
        unset($actions['edit_as_new_draft']);
        unset($actions['copy_to_template']);
    }
    return $actions;
}

add_action('add_meta_boxes','ldc_init_custom_metabox');
function ldc_init_custom_metabox(){
	add_meta_box('candidature-box','Informations candidature','ldc_candidature_box_callback','candidature', 'normal');	
	add_meta_box('candidature-spontannee-box','Informations candidature spontané','ldc_candidature_box_callback','candidature-spontane', 'normal');	
}
function ldc_candidature_box_callback(){
	$pid = $_REQUEST['post'];
	$pt = get_post_type($pid);
	$candidature = CCandidature::getById($pid);
	?>
	<table class="wp-list-table widefat fixed posts">
		<tbody>
			<tr>
				<td style="width:20%;"><strong>Nom : </strong></td>
				<td><?php echo $candidature->nom;?></td>
			</tr>
			<tr>
				<td><strong>Prénom : </strong></td>
				<td><?php echo $candidature->prenom;?></td>
			</tr>
			<tr>
				<td><strong>Adresse : </strong></td>
				<td><?php echo $candidature->numero .' '.$candidature->voie .' ' . $candidature->cp .' ' . $candidature->ville;?></td>
			</tr>
			<tr>
				<td><strong>Email : </strong></td>
				<td><?php echo $candidature->email;?></td>
			</tr>
			<tr>
				<td><strong>Lettre de motivation : </strong></td>
				<td><a target="_blank" href="<?php echo $candidature->lettre_de_motivation;?>">Voir la lettre de motivation</a></td>
			</tr>
			<tr>
				<td><strong>CV : </strong></td>
				<td><a target="_blank" href="<?php echo $candidature->cv;?>">Voir le CV</a></td>
			</tr>
			<tr>
				<td><strong>Connaissance de l'offre : </strong></td>
				<td><?php echo $candidature->connaissane;?></td>
			</tr>
			<tr>
				<td><strong>Région : </strong></td>
				<td><?php echo $candidature->region;?></td>
			</tr>
			<tr>
				<td><strong>Pays : </strong></td>
				<td><?php echo $candidature->pays;?></td>
			</tr>
			<tr>
				<td><strong>Offre : </strong></td>
				<td><?php 
					if($candidature->offre>0){
                        $o = CNos_offres::getById($candidature->offre);
						echo '<a target="_blank" href="post.php?post='.$candidature->offre.'&action=edit">'.$o->titre .' ('.$o->reference.')</a>';
					}?>
				</td>
			</tr>
			<tr>
				<td><strong>Nature poste: </strong></td>
				<td><?php echo $candidature->nature;?></td>
			</tr>
			<tr>
				<td><strong>Métier : </strong></td>
				<td><?php echo $candidature->metier;?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<a class="button button-primary button-large" href="edit.php?post_type=<?php echo $pt;?>&page=<?php 
		if($pt == 'candidature'){
			echo 'reply_candidate';
		}else{
			echo 'reply_candidate_spontanee';
		}
	?>&id=<?php echo $pid;?>">Répondre au candidat</a>
	<?php
}

add_filter('manage_edit-candidature_columns', 'ldcc_manage_candidature_columns',10);
add_filter('manage_edit-candidature-spontane_columns', 'ldcc_manage_candidature_columns',10);
function ldcc_manage_candidature_columns($columns){
  $columns["adresse"] = 'Adresse';
  $columns["email"] = 'Email';
  $columns["lm"] = 'Lettre de motivation';
  $columns["cv"] = 'CV';
  $columns["region"] = 'Région';
  $columns["pays"] = 'Pays';

  return  $columns;
}

add_action( 'manage_candidature_posts_custom_column', 'ldc_manage_candidature_column_value', 11, 2 );
add_action( 'manage_candidature-spontane_posts_custom_column', 'ldc_manage_candidature_column_value', 11, 2 );
function ldc_manage_candidature_column_value($column_name, $post_id){
  $candidature = CCandidature::getById($post_id);
  switch($column_name){
  	case 'adresse':
      echo $candidature->numero .' '.$candidature->voie .' ' . $candidature->cp .' ' . $candidature->ville;
      break;
    case 'lm':
      echo '<a target="_blank" href="' . $candidature->lettre_de_motivation. '">Voir</a>'; 	
      break;
    case 'cv':
      echo '<a target="_blank" href="' . $candidature->cv. '">Voir</a>'; 	
      break;
    default:
      echo 	$candidature->$column_name;
      break;
  }
}