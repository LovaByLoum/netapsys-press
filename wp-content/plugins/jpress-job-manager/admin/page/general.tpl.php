<?php
$options = get_option( JM_OPTIONS );
?>
<style>
    .form-table{
        width: auto;
    }
    .form-table tr td,
    .form-table tr th{
        vertical-align: top;
        border-bottom : 1px #ddd solid ;
    }
</style>
<div class="wrap">
     <form method="post" action="">
         <table class="form-table">
             <tbody>
             <thead>
             <th scope="row">Types de publication actifs</th>
                 <th>Types</th>
                 <th>Taxonomies</th>
             </thead>
             <tr>
                 <th scope="row"></th>
                 <td>
                     <fieldset>
                         <label for="<?php echo JM_POSTTYPE_SOCIETE;?>">
                             <input name="types[]" type="checkbox" id="<?php echo JM_POSTTYPE_SOCIETE;?>" value="<?php echo JM_POSTTYPE_SOCIETE;?>" <?php if( jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ) :?>checked<?php endif;?>>
                             Société
                         </label>
                     </fieldset>
                 </td>
                 <td>
                     <fieldset>
                         <label for="<?php echo JM_TAXONOMIE_DOMAINE;?>">
                             <input name="tax[]" type="checkbox" id="<?php echo JM_TAXONOMIE_DOMAINE;?>" value="<?php echo JM_TAXONOMIE_DOMAINE;?>" <?php if( jpress_jm_is_in_options( JM_TAXONOMIE_DOMAINE, 'tax' ) ) :?>checked<?php endif;?>>
                             Domaine d'activités
                         </label>
                     </fieldset>
                 </td>
             </tr>

             <tr>
                 <th scope="row"></th>
                 <td>
                     <fieldset>
                         <label for="<?php echo JM_POSTTYPE_OFFRE;?>">
                             <input name="types[]" type="checkbox" id="<?php echo JM_POSTTYPE_OFFRE;?>" value="<?php echo JM_POSTTYPE_OFFRE;?>" <?php if( jpress_jm_is_in_options( JM_POSTTYPE_OFFRE, 'types' ) ) :?>checked<?php endif;?>>
                             Offre
                         </label>
                     </fieldset>
                 </td>
                 <td>
                     <fieldset>
                         <label for="<?php echo JM_TAXONOMIE_LOCALISATION;?>">
                             <input name="tax[]" type="checkbox" id="<?php echo JM_TAXONOMIE_LOCALISATION;?>" value="<?php echo JM_TAXONOMIE_LOCALISATION;?>" <?php if( jpress_jm_is_in_options( JM_TAXONOMIE_LOCALISATION, 'tax' ) ) :?>checked<?php endif;?>>
                             Localisation
                         </label>
                     </fieldset>
                     <fieldset>
                         <label for="<?php echo JM_TAXONOMIE_CATEGORIE;?>">
                             <input name="tax[]" type="checkbox" id="<?php echo JM_TAXONOMIE_CATEGORIE;?>" value="<?php echo JM_TAXONOMIE_CATEGORIE;?>" <?php if( jpress_jm_is_in_options( JM_TAXONOMIE_CATEGORIE, 'tax' ) ) :?>checked<?php endif;?>>
                             Catégorie d'offres
                         </label>
                     </fieldset>
                     <fieldset>
                         <label for="<?php echo JM_TAXONOMIE_TYPE_CONTRAT;?>">
                             <input name="tax[]" type="checkbox" id="<?php echo JM_TAXONOMIE_TYPE_CONTRAT;?>" value="<?php echo JM_TAXONOMIE_TYPE_CONTRAT;?>" <?php if( jpress_jm_is_in_options( JM_TAXONOMIE_TYPE_CONTRAT, 'tax' ) ) :?>checked<?php endif;?>>
                             Type de contrat
                         </label>
                     </fieldset>
                     <fieldset>
                         <label for="<?php echo JM_TAXONOMIE_DEPARTEMENT;?>">
                             <input name="tax[]" type="checkbox" id="<?php echo JM_TAXONOMIE_DEPARTEMENT;?>" value="<?php echo JM_TAXONOMIE_DEPARTEMENT;?>" <?php if( jpress_jm_is_in_options( JM_TAXONOMIE_DEPARTEMENT, 'tax' ) ) :?>checked<?php endif;?>>
                             Département
                         </label>
                     </fieldset>
                 </td>
             </tr>

             <tr>
                <th scope="row"></th>
                <td>
                    <fieldset>
                        <label for="<?php echo JM_POSTTYPE_CANDIDATURE;?>">
                            <input name="types[]" type="checkbox" id="<?php echo JM_POSTTYPE_CANDIDATURE;?>" value="<?php echo JM_POSTTYPE_CANDIDATURE;?>" <?php if( jpress_jm_is_in_options( JM_POSTTYPE_CANDIDATURE, 'types' ) ) :?>checked<?php endif;?>>
                            Candidature
                        </label>
                    </fieldset>
                </td>
                <td></td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="profil_rh">Ajouter un profil "Responsable RH"</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[profil_rh]" type="checkbox" id="profil_rh" value="1" <?php if( jpress_jm_is_in_options( 'profil_rh', 'settings' ) ) :?>checked<?php endif;?>>
                     <br><i>Ce profil ne gerera que les entités de Job Manager (société, offre, candidature). Vous pouvez personnaliser ses droits dans l'onglet "Droits et Utilisateurs".</i>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="rh_by_soc">Attacher le compte du responsable RH à une société</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[rh_by_soc]" type="checkbox" id="rh_by_soc" value="1" <?php if( jpress_jm_is_in_options( 'rh_by_soc', 'settings' ) ) :?>checked<?php endif;?>>
                     <br><i>L'utilisateur n'aura accès qu'aux offres et candidatures associés à la société définie dans son profil d'utilisateur et recevra une notification que pour les candidatures aux offres qui lui concernent.</i>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="blogname">Attacher le compte du responsable RH par</label>
                 </th>
                 <td colspan="2">
                     <select class="chosen-select" name="settings[rh_by_tax]">
                         <option value="">Séléctionner</option>
                         <?php
                         $array_tax = array(
                             JM_TAXONOMIE_DEPARTEMENT,
                             JM_TAXONOMIE_TYPE_CONTRAT,
                             JM_TAXONOMIE_CATEGORIE,
                             JM_TAXONOMIE_LOCALISATION
                         );
                         foreach ($array_tax as $tax) {
                             $the_taxonomie  = get_taxonomy( $tax );
                             echo '<option value="' . $tax . '" ' . ( jpress_jm_is_in_options( 'rh_by_tax', 'settings' ) == $tax ? 'selected' : '' ) . '>' . $the_taxonomie->labels->name . '</option>';
                         }
                         ?>
                     </select>
                     <br><i>L'utilisateur n'aura accès qu'aux offres et candidatures associés au terme défini dans son profil d'utilisateur et recevra une notification que pour les candidatures aux offres qui lui concernent.</i>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label>Envoyer un mail à</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[email_candidature]" type="text" value="<?php echo jpress_jm_is_in_options( 'email_candidature', 'settings' ); ?>" class="regular-text">
                     <br><i>Si rempli, cet adresse email recevra les candidatures. Séparer les adresses email par un virgule.</i>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="societe_notification">Envoyer un mail à la société rattaché à l'offre</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[societe_notification]" type="checkbox" id="societe_notification" value="1" <?php if( jpress_jm_is_in_options( 'societe_notification', 'settings' ) ) :?>checked<?php endif;?>>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="author_notification">Envoyer un mail à l'auteur de l'offre</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[author_notification]" type="checkbox" id="author_notification" value="1" <?php if( jpress_jm_is_in_options( 'author_notification', 'settings' ) ) :?>checked<?php endif;?>>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label for="author_notification">Envoyer un mail de confirmation à l'internaute</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[candidat_confirmation]" type="checkbox" id="candidat_confirmation" value="1" <?php if( jpress_jm_is_in_options( 'candidat_confirmation', 'settings' ) ) :?>checked<?php endif;?>>
                 </td>
             </tr>

             <tr>
                 <th scope="row">
                     <label>Intervale d'expiration des offres (en jour)</label>
                 </th>
                 <td colspan="2">
                     <input name="settings[expire_delay]" type="text" value="<?php echo jpress_jm_is_in_options( 'expire_delay', 'settings' ); ?>" class="regular-text">
                     <br><i>Si renseigné, la date d'expiration des offres nouvellement crées sera calculée automatiquement.</i>
                 </td>
             </tr>

             </tbody>
         </table>
        <p class="submit"><input type="submit" name="jm_submit_general" id="submit" class="button button-primary" value="Enregistrer"></p>
    </form>
</div>