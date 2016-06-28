<?php
/**
 * gestion des metaboxes
 */

//metabox offre
add_action('add_meta_boxes','jpress_jm_init_metabox_offre');
function jpress_jm_init_metabox_offre(){
    global $jpress_jm_offre_fields;
    $active_metabox_info = false;
    foreach (  $jpress_jm_offre_fields as $field){
        if ($field['enable'] == 1) $active_metabox_info = true;
    }
    if ($active_metabox_info){
        add_meta_box('offre_fields', 'Informations offre', 'jpress_jm_offre_fields', JM_POSTTYPE_OFFRE, 'normal');
    }

    if ( jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
        add_meta_box('offre_relations', 'Relations', 'jpress_jm_offre_relations', JM_POSTTYPE_OFFRE, 'normal');
    }
}
function jpress_jm_offre_fields($post){
    global $jpress_jm_offre_fields;
    $offre = JM_Offre::getById($post->ID);
    //mp($offre);
    ?>
    <table class="form-table">
        <tbody>
            <?php foreach (  $jpress_jm_offre_fields as $field):
                if ($field['enable'] == 0) continue;
                ?>
            <tr>
                <th scope="row">
                    <label for="<?php echo $field['metakey'];?>"><?php echo $field['label'];?></label>
                </th>
                <td>
                    <?php jpress_jm_render_field( $field, $offre ) ;?>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php
}

function jpress_jm_offre_relations($post){
    $offre = JM_Offre::getById($post->ID);
    $societes = JM_Societe::getBy();
    ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>Société rattaché</label>
                </th>
                <td>
                    <select name="<?php echo JM_META_SOCIETE_OFFRE_RELATION;?>" data-placeholder="Séléctionner une société" class="chosen-select" tabindex="2">
                        <option value=""></option>
                        <?php foreach ( $societes as $soc):?>
                            <option value="<?php echo $soc->id;?>" <?php if ( $offre->societe_associe ==  $soc->id ): ?>selected<?php endif;?>><?php echo $soc->titre;?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}

//metabox candidature
add_action('add_meta_boxes','jpress_jm_init_metabox_candidature');
function jpress_jm_init_metabox_candidature(){
    add_meta_box('candidature_fields', 'Informations candidature', 'jpress_jm_candidature_fields', JM_POSTTYPE_CANDIDATURE, 'normal');
    if ( jpress_jm_is_in_options( JM_POSTTYPE_OFFRE, 'types' ) ){
        add_meta_box('candidature_relations', 'Relations', 'jpress_jm_candidature_relations', JM_POSTTYPE_CANDIDATURE, 'normal');
    }
}
function jpress_jm_candidature_fields( $post ){
    global $jpress_jm_candidature_fields, $is_popup_candidature;
    $candidature = JM_Candidature::getById($post->ID);
    ?>
    <table class="form-table">
        <tbody>
        <?php foreach (  $jpress_jm_candidature_fields as $field):
            if ($field['enable'] == 0) continue;
            ?>
            <tr>
                <th scope="row">
                    <label for="<?php echo $field['metakey'];?>"><?php echo $field['label'];?></label>
                </th>
                <td>
                    <?php
                    if ( $is_popup_candidature ){
                        jpress_jm_render_field_value( $field, $candidature ) ;
                    }else{
                        jpress_jm_render_field( $field, $candidature ) ;
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php
}
function jpress_jm_candidature_relations($post){
    global $is_popup_candidature;

    $candidature = JM_Candidature::getById($post->ID);
    $offres = JM_Offre::getBy();
    ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>Offre rattachée</label>
            </th>
            <td>
                <?php if ($is_popup_candidature) :?>
                    <label><?php
                        $offr = JM_Offre::getById( $candidature->offre_associe );
                        echo '<a href="post.php?post=' . $offr->id . '&action=edit">' . $offr->titre . '</a>';
                        ?>
                    </label>
                <?php else : ?>
                    <select name="<?php echo JM_META_OFFRE_CANDIDATURE_RELATION;?>" data-placeholder="Séléctionner une offre" class="chosen-select" tabindex="2">
                        <option value=""></option>
                        <?php foreach ( $offres as $offre):?>
                            <option value="<?php echo $offre->id;?>" <?php if ( $candidature->offre_associe ==  $offre->id ): ?>selected<?php endif;?>><?php echo $offre->titre;?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif;?>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}

//metabox societe
add_action('add_meta_boxes','jpress_jm_init_metabox_societe');
function jpress_jm_init_metabox_societe(){
    add_meta_box('societe_fields', 'Informations société', 'jpress_jm_societe_fields', JM_POSTTYPE_SOCIETE, 'normal');

}
function jpress_jm_societe_fields($post){
    global $jpress_jm_societe_fields;
    $societe = JM_Societe::getById($post->ID);
    ?>
    <table class="form-table">
        <tbody>
        <?php foreach (  $jpress_jm_societe_fields as $field):
            if ($field['enable'] == 0) continue;?>
            <tr>
                <th scope="row">
                    <label for="<?php echo $field['metakey'];?>"><?php echo $field['label'];?></label>
                </th>
                <td>
                    <?php jpress_jm_render_field( $field, $societe ) ;?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php
}


//sauvegarde post
if (is_admin()){
    add_action('save_post', 'jpress_jm_save_metabox', 10, 2);
}
function jpress_jm_save_metabox($post_id, $post){
    //common
    if ( $post->post_type == JM_POSTTYPE_OFFRE  || $post->post_type == JM_POSTTYPE_CANDIDATURE || $post->post_type == JM_POSTTYPE_SOCIETE ){
        if ( isset ($_POST['jm_meta'] ) ){
            foreach (  $_POST['jm_meta'] as $jmeta => $value ) {
                if ( isset($_POST['jm_meta'][$jmeta]) ){
                    update_post_meta( $post_id, $jmeta, $value );
                }
            }
        }
    }

    if ( $post->post_type == JM_POSTTYPE_OFFRE ){
        update_post_meta( $post_id, JM_META_SOCIETE_OFFRE_RELATION, $_POST[JM_META_SOCIETE_OFFRE_RELATION] );

        //expire auto
        $expire_delay = jpress_jm_is_in_options( 'expire_delay', 'settings' );
        $current_expire_value = get_post_meta($post_id, 'expire', true );
        if (!empty($expire_delay) && intval($expire_delay)>0 && ( is_null($current_expire_value) || empty($current_expire_value) ) ){
            $expire_day  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+intval($expire_delay), date("Y")));
            update_post_meta( $post_id, 'expire', $expire_day );
        }
    }

    if ( $post->post_type == JM_POSTTYPE_CANDIDATURE ){
        //save relation
        update_post_meta( $post_id, JM_META_OFFRE_CANDIDATURE_RELATION, $_POST[JM_META_OFFRE_CANDIDATURE_RELATION] );

        //rename titre : nom + prenom
        remove_action('save_post', 'jpress_jm_save_metabox', 10, 2);
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $_POST['jm_meta']['nom'] . ' ' . $_POST['jm_meta']['prenom'],
            'post_name' => sanitize_title($_POST['jm_meta']['nom'] . ' ' . $_POST['jm_meta']['prenom']),
        ));
    }
}