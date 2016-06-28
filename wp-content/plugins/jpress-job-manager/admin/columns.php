<?php
/**
 * gestion des colonnes
 */

//colonne societe
add_filter('manage_edit-' . JM_POSTTYPE_SOCIETE . '_columns', 'jpress_jm_manage_societe_columns',10);
function jpress_jm_manage_societe_columns($columns){
    global $jpress_jm_societe_fields;

    $columns['thumbnail'] = 'Logo' ;
    foreach ( $jpress_jm_societe_fields as $field) {
        if ( $field['admin_column'] == 0 || $field['enable'] == 0 ) continue;
        $columns[$field['metakey']] = $field['label'];
    }

    return  $columns;
}

//colonne offre
add_filter('manage_edit-' . JM_POSTTYPE_OFFRE . '_columns', 'jpress_jm_manage_offre_columns',10);
function jpress_jm_manage_offre_columns($columns){
    global $jpress_jm_offre_fields;

    foreach ( $jpress_jm_offre_fields as $field) {
        if ( $field['admin_column'] == 0 || $field['enable'] == 0 ) continue;
        $columns[$field['metakey']] = $field['label'];
    }

    if ( jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
        $columns['societe'] = 'Société rattachée';
    }

    $columns['nbr_candidature'] = 'Nombre de candidatures';

    return  $columns;
}

//colonne candidature
add_filter('manage_edit-' . JM_POSTTYPE_CANDIDATURE . '_columns', 'jpress_jm_manage_candidature_columns',10);
function jpress_jm_manage_candidature_columns($columns){
    global $jpress_jm_candidature_fields;

    foreach ( $jpress_jm_candidature_fields as $field) {
        if ( $field['admin_column'] == 0 || $field['enable'] == 0 ) continue;
        $columns[$field['metakey']] = $field['label'];
    }

    if ( jpress_jm_is_in_options( JM_POSTTYPE_OFFRE, 'types' ) ){
        $columns['offre'] = 'Offre rattachée';
    }
    return  $columns;
}

//societe column value
add_action( 'manage_' . JM_POSTTYPE_SOCIETE .'_posts_custom_column', 'jpress_jm_manage_societe_column_value', 10, 2 );
function jpress_jm_manage_societe_column_value($column_name, $post_id){
    global $jpress_jm_societe_fields;
    $object = JM_Societe::getById($post_id);

    switch ($column_name){
        case 'thumbnail' :
            if ( $object->logo ){
                echo '<img src="' . $object->logo . '" width="100%" style="max-width:200px;"/>';
            }
            break;
        default:
            $the_field = $jpress_jm_societe_fields[$column_name];
            jpress_jm_render_field_value($the_field, $object);
            break;
    }
}

//offre column value
add_action( 'manage_' . JM_POSTTYPE_OFFRE .'_posts_custom_column', 'jpress_jm_manage_offre_column_value', 10, 2 );
function jpress_jm_manage_offre_column_value($column_name, $post_id){
    global $jpress_jm_offre_fields;
    $object = JM_Offre::getById($post_id);

    switch ($column_name){
        case 'societe' :
            $soc = JM_Societe::getById($object->societe_associe);
            if ( $soc ){
                echo '<a href="post.php?post=' . $soc->id . '&action=edit">' . $soc->titre . '</a>';
            }
            break;
        case 'nbr_candidature':
            echo JM_Offre::get_nombre_candidature($post_id);
            break;
        default:
            $the_field = $jpress_jm_offre_fields[$column_name];
            jpress_jm_render_field_value($the_field, $object);
            break;
    }
}

//candidature column value
add_action( 'manage_' . JM_POSTTYPE_CANDIDATURE .'_posts_custom_column', 'jpress_jm_manage_candidature_column_value', 10, 2 );
function jpress_jm_manage_candidature_column_value($column_name, $post_id){
    global $jpress_jm_candidature_fields;
    $object = JM_Candidature::getById($post_id);
    switch ($column_name){
        case 'offre' :
            $offre = JM_Offre::getById($object->offre_associe);
            if ( $offre ){
                echo '<a href="post.php?post=' . $offre->id . '&action=edit">' . $offre->titre . '</a>';
            }
            break;
        default:
            $the_field = $jpress_jm_candidature_fields[$column_name];
            jpress_jm_render_field_value($the_field, $object);
            break;
    }
}

//gerer les colonnes users
add_filter('manage_users_columns', 'jpress_jm_manage_users_columns');
function jpress_jm_manage_users_columns( $columns ){
    $rh_by_tax = jpress_jm_is_in_options( 'rh_by_tax', 'settings' );
    if ( $rh_by_tax ){
        $the_taxonomie = get_taxonomy( $rh_by_tax );
        $columns[ $rh_by_tax ] = 'RH ' . $the_taxonomie->labels->name;
    }

    $rh_by_soc = jpress_jm_is_in_options( 'rh_by_soc', 'settings' );
    if ( $rh_by_soc ){
        $columns['societe'] = 'RH Société';
    }

    return $columns;
}

add_filter('manage_users_custom_column', 'jpress_jm_manage_users_custom_column', 10, 3);
function jpress_jm_manage_users_custom_column($html, $columnname, $uid){
    $rh_by_tax = jpress_jm_is_in_options( 'rh_by_tax', 'settings' );

    switch ( $columnname ){
        case 'societe' :
            $soc_id = get_user_meta( $uid, JM_META_USER_SOCIETE_FILTER_ID, true );
            $soc = JM_Societe::getById($soc_id);
            return '<a href="post.php?post=' . $soc->id . '&action=edit">' . $soc->titre . '</a>';
            break;
        case $rh_by_tax :
            if (!empty($rh_by_tax)){
                $terms_id = get_user_meta( $uid, JM_META_USER_TAX_FILTER_ID );
                $html = '';
                $glue = '';
                if ( is_array($terms_id) && sizeof($terms_id) > 0 ){
                    foreach ( $terms_id as $tid) {
                        $t = get_term($tid, $rh_by_tax);
                        $html .= $glue . '<a href="edit.php?' . $rh_by_tax . '=' . $t->slug . '&post_type=' . JM_POSTTYPE_OFFRE .  '">' . $t->name . '</a>';
                        $glue = ', ';
                    }
                }
                return $html;
            }
            break;
        default:
            break;
    }
}