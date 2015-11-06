<?php
if (!is_admin())
  return;
/********************** Creation d'un champ personnalisé pour un post (club) **********************/
/**
 * Initialisation
 */
add_action('add_meta_boxes','init_metabox_club');
function init_metabox_club(){
  add_meta_box('cotisation_club', 'Cotisation par type de licence', 'field_cotisation', 'club', 'normal');
  add_meta_box('fields_moniteur', 'Informations "Moniteur"', 'fields_moniteur_club_callback', 'club', 'normal');
}

/**
 * Ajout des champs
 */
function field_cotisation($post){
  $type_licences = type_licence::get_all_type_licence();
  $cotisation = jcpt_get_value_column('club','cotisation_type_licence',$post->ID);
  $cotisation = maybe_unserialize($cotisation);
  if(testNonVide($type_licences)){
    foreach($type_licences as $type_licence){
      echo '<div class="cotisation-wrapper">';
      echo '<label for="cotisation_' . $type_licence->id . '_meta">' . $type_licence->name . ' : </label>';
      echo '<input id="cotisation_' . $type_licence->id . '_meta" type="text" name="cotisation_' . $type_licence->id . '" value="' . $cotisation[$type_licence->id] . '" /> &euro;';
      echo '</div>';
    }
  }else{
    echo 'Type de licence vide';
  }
}

add_action('admin_init', 'validation_cotisatoin_init');
function validation_cotisatoin_init(){
  wp_enqueue_script('jquery');
}

/**
 * Fonction qui valide le champ cotisation.
 */
add_action('edit_form_advanced', 'validation_cotisatoin');
function validation_cotisatoin(){
  global $post;
  if($post->post_type == 'club'){
    echo "<script type='text/javascript'>\n";
    echo "
      jQuery('#post').submit(function(){
        var bool = true;
        jQuery('#cotisation_club input[id^=\"cotisation_\"]').each(function(){
          var _valeur = jQuery(this).val();
          if((_valeur != '') && !jQuery.isNumeric(_valeur)){
            jQuery(this).css('border', '1px solid red');
            jQuery('#publish').removeClass('button-primary-disabled');
            bool = false;
          }
        });
        if(!bool){
          jQuery('.spinner').css('visibility', 'hidden');
          alert('Veuillez saisir un montant correct');
          return bool;
        }
      });";
    if(is_admin_club() || is_admin_ligue()){
      echo "jQuery('#acf-cotisation_ffcv_paye input[type=checkbox]').attr('disabled','disabled');
        jQuery('#acf-ecole_club input[type=checkbox]').attr('disabled','disabled');";
    }
    echo "</script>\n";
  }
}

/*champ moniteur*/
function fields_moniteur_club_callback($post){
  ?>
  <div class="field field-repeater" >
    <div class="repeater">
      <table class="widefat acf-input-table ">
        <thead>
        <tr>
          <th class="acf-th-nom" width="50%">
            <span>Moniteurs</span>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr class="row">
          <td>
          	<div class="helpbox postbox">
		        <span class="aacsprite aacsmicons aachelp tooltip">
		        	<img title="meta" rel="meta" src="<?php echo plugins_url('advanced-access-manager/view/css/images/nothing.gif');?>">
		        </span>
		        <div class="mbox">
		        	<?php
		              if(class_exists('WP_Autocomplete_Action')){
		                $autocomplete_input = new WP_Autocomplete_Action('moniteur-nom',350,'Rechercher un nom ou prénom d\'un licencié existant');
		                $autocomplete_input->display();
		              }
		            ?>
		            ou
		            <a href="<?php echo admin_url( 'post-new.php?post_type=licencie');?>" target="_blank" id="createlicencie" class="initialize-url lilsqbutton">Créér un nouveau licencié</a>
		        </div>
		        <div id="progressbar"></div>
		        <br class="clear">
		    </div>
			<table id="taxo_table" class="widefat">
                    <thead>
                    <tr>
                      <th>Civilité</th>
                      <th>Nom</th>
                      <th>Prénom</th>
                      <th>Email</th>
                      <th>Diplôme</th>
                      <th>Numéro de Licence</th>
                      <th>Période d'activité</th>
                      <th>Status juridique</th>
                      <th></th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                      <th>Civilité</th>
                      <th>Nom</th>
                      <th>Prénom</th>
                      <th>Email</th>
                      <th>Diplôme</th>
                      <th>Numéro de Licence</th>
                      <th>Période d'activité</th>
                      <th>Status juridique</th>
                      <th></th>
                    </tr>
                    </tfoot>

                    <tbody id="moniteur-list" class="taxbody ui-sortable">
                    	<?php 
                    	$club = club::getById($post->ID);
                    	if(is_array($club->moniteurs) && !empty($club->moniteurs)):
                    		foreach ($club->moniteurs as $id):
                    			$infos = licencie::getById($id);?>
									<tr>
										<td><?php echo (($infos->sexe=="m")?"Mr":"Mme/Mlle");?></td>
										<td><?php echo $infos->nom;?></td>
										<td><?php echo $infos->prenom;?></td>
										<td><?php echo $infos->email;?></td>
										<td><?php 
											$infos->diplome = get_term( $infos->diplome[0],'diplome')->name;
											echo $infos->diplome;?>
										</td>
										<td>
											<div class="ui-state-highlight ui-corner-all">
										 		<span><?php echo $infos->num_licence;?></span>
										    </div>
										</td>
                    <td>
                      du <?php echo convertDateEnToFr($infos->periode_activite['debut'],false); ?> au <?php echo convertDateEnToFr($infos->periode_activite['fin'],false); ?>
                    </td>
                    <td>
                      <?php echo $infos->status_juridique; ?>
                    </td>
										<td>
											<a href="javascript:;" class="remove-moniteur lilsqbutton">Supprimer</a>
											<input type="hidden" name="moniteur_id[]" value="<?php echo $infos->id; ?>">
										</td>
									</tr>
                    	<?php endforeach;
                    	endif;?>
                    </tbody>
                  </table>            
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php

}

/**
 * Fonction qui met a jour les valeurs des champs personnalisés
 */
add_action('save_post','save_metabox');
function save_metabox($post_id){
  $type_licences = type_licence::get_all_type_licence();
  $cotisation = array();
  $post_type = get_post_field('post_type',$post_id);

  if($post_type == 'club'){
    if(isset($_POST) && $_POST['action'] != 'inline-save'){
      if(is_admin_club() || is_admin_ligue()){
        $cotisation_ffcv = licencie::get_field_key_acf_by_name_field('cotisation_ffcv_paye');
        $ecole = licencie::get_field_key_acf_by_name_field('ecole_club');
        $labelise = licencie::get_field_key_acf_by_name_field('club_labelise_club');

        // cotisation_ffcv_paye
        if(isset($_POST['fields'][$cotisation_ffcv])){
          $_POST['fields'][$cotisation_ffcv] = get_field('cotisation_ffcv_paye',$post_id);
        }

        // club_labelise_club
        if(isset($_POST['fields'][$labelise])){
          $_POST['fields'][$labelise] = get_field('club_labelise_club',$post_id);
        }

        // ecole_club
        if(isset($_POST['fields'][$ecole])){
          $_POST['fields'][$ecole] = get_field('ecole_club',$post_id);
        }
      }
      //cotisation	
      foreach($type_licences as $type_licence){
        if(isset($_POST['cotisation_' . $type_licence->id]))
          $cotisation[$type_licence->id] = $_POST['cotisation_' . $type_licence->id];
      }
      update_post_meta($post_id, club::COTISATION, $cotisation);
      
      //moniteurs
      if(isset($_POST['moniteur_id'])){
      	 update_post_meta($post_id, club::MONITEURS, $_POST['moniteur_id']);
      }
    }
  }
}

//autocomplete action
add_filter('autocomplete_action_callback_moniteur_nom','autocomplete_action_callback_moniteur');
function autocomplete_action_callback_moniteur($term){
  $results = licencie::get_id_name($term);

  return $results;
}

add_action('admin_head','autocomplete_select_monieur');
function autocomplete_select_monieur(){
  $html = "\n
          <script type=\"text/javascript\">
            jQuery(document).ready(function(){
              jQuery(\".remove-moniteur\").live('click',function(){
                jQuery(this).parents('tr:first').remove();
              });
            });
          function autocomplete_select_callback_moniteur_nom(event,ui){
            var data = ui.item;
            jQuery(\"#\"+event.target.id).addClass('ui-autocomplete-loading');
            jQuery.ajax({
                type : 'post',
                url: autocompleteaction.ajaxurl,
                dataType: 'html',
                data: {
                  action : 'get_infos_moniteur',
                  id : data.id
                },
                success: function( data ) {
                  jQuery(\"#moniteur-list\").append(data);
                  jQuery(\"#\"+event.target.id).removeClass('ui-autocomplete-loading').val('');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR, textStatus, errorThrown);
                }
            });
          }
          </script>
          ";
  echo $html;
}

add_action( 'wp_ajax_get_infos_moniteur', 'get_infos_moniteur');
function get_infos_moniteur(){
  $id = $_POST['id'];

  $infos = licencie::getById($id);
  $infos->diplome = get_term( $infos->diplome[0],'diplome')->name;
  
  ?>
  <tr>
    <td><?php echo (($infos->sexe=="m")?"Mr":"Mme/Mlle");?></td>
    <td><?php echo $infos->nom;?></td>
    <td><?php echo $infos->prenom;?></td>
    <td><?php echo $infos->email;?></td>
    <td><?php echo $infos->diplome;?></td>
    <td>
    	<div class="ui-state-highlight ui-corner-all">
     		<span><?php echo $infos->num_licence;?></span>
	    </div>
    </td>
    <td>
       du <?php echo convertDateEnToFr($infos->periode_activite['debut'],false); ?> au <?php echo convertDateEnToFr($infos->periode_activite['fin'],false); ?>
    </td>
    <td>
      <?php echo $infos->status_juridique; ?>
    </td>
    <td>
    	<a href="javascript:;" class="remove-moniteur lilsqbutton">Supprimer</a>
    	<input type="hidden" name="moniteur_id[]" value="<?php echo $infos->id; ?>">
    </td>
  </tr>
  <?php
  die();
}
