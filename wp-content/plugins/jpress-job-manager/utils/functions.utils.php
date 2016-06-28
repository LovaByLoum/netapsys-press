<?php
/**
 * fonctions utilitaires pour jPress Job Manager
 */

//obtenir les labels pour la création de post type en français accordé avec le genre et nombre
function jpress_jm_get_custom_post_type_labels($ptsingle, $ptplural, $masculin){
    $labels = array(
        "name"				=> ucfirst($ptsingle),
        "singular_name"		=> ucfirst($ptplural),
        "add_new"			=> "Ajouter" ,
        "add_new_item"		=> "Ajouter" . ($masculin ? " un nouveau " : " une nouvelle " ) . $ptsingle,
        "edit_item"			=> "Modifier " . $ptsingle,
        "new_item"			=> ($masculin ? "Nouveau " : "Nouvelle " ) . $ptsingle,
        "view_item"			=> "Voir " . $ptsingle,
        "search_items"		=> "Rechercher des "  . $ptplural ,
        "not_found"			=> ($masculin ? "Aucun " : "Aucune " ) . $ptsingle .  ($masculin ? " trouvé" : " trouvée" ),
        "not_found_in_trash"=> ($masculin ? "Aucun " : "Aucune " ) . $ptsingle .  ($masculin ? " trouvé " : " trouvée " ) . "dans la corbeille",
        "parent_item_colon"	=> ucfirst($ptsingle) . ($masculin ? " parent" : " parente" ),
        "all_items"			=> ($masculin ? "Tous les " : "Toutes les " ) . $ptplural,
        "menu_name"         => ucfirst($ptplural),
        "parent_item_colon" => "",
    );
    return $labels;
}

//obtenir les labels pour la création de taxonomie en français accordé avec le genre et nombre
function jpress_jm_get_custom_tax_labels($taxsingle, $taxplural, $masculin){
    $labels = array(
        'name'                       => ucfirst($taxsingle),
        'singular_name'              => ucfirst($taxsingle),
        'search_items'               => 'Rechercher des '. $taxplural,
        'popular_items'              => ucfirst($taxplural) . ' les plus populaires',
        'all_items'                  => ($masculin ? "Tous les " : "Toutes les " ) . $taxplural,
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Modifier',
        'update_item'                => 'Mettre à jour',
        'add_new_item'               => 'Ajouter ' . ($masculin ? "un " : "une " ) . $taxsingle,
        'new_item_name'              => 'Nouveau nom',
        'separate_items_with_commas' => 'Séparez les ' . $taxplural . ' par des virgules',
        'add_or_remove_items'        => 'Ajouter ou supprimer ' . ($masculin ? "un " : "une " ) . $taxsingle,
        'choose_from_most_used'      => 'Choisir parmi les ' . $taxplural . ' les plus '. ($masculin ? "utilisés" : "utilisées" ),
        'not_found'                  => ($masculin ? "Aucun " : "Aucune " ) . $taxsingle,
        'menu_name'                  => ucfirst($taxplural),
    );
    return $labels;
}

//troncage de texte
function jpress_jm_limite_text($string, $char_limit = NULL, $plus = '...') {
    if( $string && $char_limit ) {
        if( strlen( $string) > $char_limit ) {
            $words = substr($string, 0, $char_limit);
            $words = explode(' ', $words);
            array_pop($words);
            return implode(' ', $words) . $plus;
        } else {
            return $string;
        }
    } else {
        return $string;
    }
}

//render fields
function jpress_jm_render_field( $field, $object ){
    switch ( $field['type'] ){
        case 'text' :
            ?>
            <input name="jm_meta[<?php echo $field['metakey'];?>]" type="text" id="<?php echo $field['metakey'];?>" value="<?php echo $object->$field['attribut'];?>" class="regular-text">
            <?php
            break;
        case 'mail' :
            ?>
            <input name="jm_meta[<?php echo $field['metakey'];?>]" type="text" id="<?php echo $field['metakey'];?>" value="<?php echo $object->$field['attribut'];?>" class="regular-text mail">
            <?php
            break;
        case 'checkbox' :
        case 'radio' :
            ?>
            <input name="jm_meta[<?php echo $field['metakey'];?>]" type="<?php echo $field['type'];?>" id="<?php echo $field['metakey'];?>" value="1" <?php if( $object->$field['attribut'] ):?>checked<?php endif;?>   >
            <?php
            break;
        case 'select':
            ?>
            <select name="jm_meta[<?php echo $field['metakey'];?>]" data-placeholder="Séléctionner <?php echo $field['label'];?>" class="chosen-select" tabindex="2">
                <option value=""></option>
                <?php foreach ( $field['options'] as $key => $val):?>
                    <option value="<?php echo $key;?>" <?php if ( $key ==  $object->$field['attribut'] ): ?>selected<?php endif;?>><?php echo $val;?></option>
                <?php endforeach; ?>
            </select>
            <?php
            break;
        case 'date' :
            ?>
            <input name="jm_meta[<?php echo $field['metakey'];?>]" type="text" id="<?php echo $field['metakey'];?>" value="<?php echo $object->$field['attribut'];?>" class="regular-text jpress-datepicker" placeholder="DD/MM/YYYY" readonly>
            <?php
            break;
        case 'wysiwyg' :
            wp_editor(
                $object->$field['attribut'],
                $field['metakey'],
                array('textarea_name' => "jm_meta[" . $field['metakey'] . "]")
            );
            break;
        case 'file' :
            global $wpau;
            if ( $wpau ){
                $wpau->display(array(
                    'id'=> $field['metakey'],
                    'name'=> 'jm_meta[' . $field['metakey'] . ']',
                    'aid' => $object->$field['attribut'],
                    'label' => 'Télécharger ' . $field['label'],
                    'size' => 'thumbnail'
                ));
            }else{
                echo 'please activate the wp-ajax-upload plugin.';
            }
            ?>
            <?php
            break;
    }

}

//render fields value
function jpress_jm_render_field_value( $field, $object, $echo = true ){
    switch ( $field['type'] ){
        case 'text' :
            if ( $echo ){
                echo $object->$field['attribut'];
            }else{
                return $object->$field['attribut'];
            }
            break;
        case 'mail' :
            if ( $echo ){
                echo '<a href="mailto:' . $object->$field['attribut'] .'">' . $object->$field['attribut'] . '</a>';
            }else{
                return '<a href="mailto:' . $object->$field['attribut'] .'">' . $object->$field['attribut'] . '</a>';
            }
            break;
        case 'checkbox' :
        case 'radio' :
            if( $object->$field['attribut'] ){
                if ( $echo ){
                    echo 'Oui';
                }else{
                    return 'Oui';
                }
            }else{
                if ( $echo ){
                    echo 'Non';
                }else{
                    return 'Non';
                }
            }
            break;
        case 'select' :
            if ( $echo ){
                echo $field['options'][$object->$field['attribut']];
            }else{
                return $field['options'][$object->$field['attribut']];
            }
            break;
        case 'date' :
            if ( $echo ){
                echo $object->$field['attribut'];
            }else{
                return $object->$field['attribut'];
            }
            break;
        case 'wysiwyg' :
           if ( $echo ){
                echo  jpress_jm_limite_text( strip_shortcodes( strip_tags( $object->$field['attribut'] ) ) , 200 ) ;
           }else{
               return  strip_tags( $object->$field['attribut'] )  ;
           }
            break;
        case 'file' :
            if( empty( $object->$field['attribut'] ) ) break;
            if ( is_numeric( $object->$field['attribut'] ) ) $object->$field['attribut'] = array( $object->$field['attribut'] );
            foreach ( $object->$field['attribut'] as $aid ){
                $attchmnt = get_post($aid);
                if ( $attchmnt ){
                    list ( $mime ) = explode( '/', $attchmnt->post_mime_type );
                    if ( $mime == 'image' ){
                        list( $src ) = wp_get_attachment_image_src( $attchmnt->ID, 'thumbnail' );
                        if ( $echo ){
                            echo '<a target="_blank" href="' . wp_get_attachment_url( $attchmnt->ID ) .'"><img width="100%" style="max-width:200px;" src="' . $src .'"></a>';
                        }else{
                            return '<a target="_blank" href="' . wp_get_attachment_url( $attchmnt->ID ) .'"><img width="100%" style="max-width:200px;" src="' . $src .'"></a>';
                        }
                    }else{
                        $filename = basename(get_post_meta( $attchmnt->ID, '_wp_attached_file', true ));
                        if ( $echo ){
                            echo '<a target="_blank" href="' . wp_get_attachment_url( $attchmnt->ID ) .'">' . $filename . '</a>';
                        }else{
                            return '<a target="_blank" href="' . wp_get_attachment_url( $attchmnt->ID ) .'">' . $filename . '</a>';
                        }
                    }
                }
            }

            break;
    }

}


//recherche dans les options du plugin
function jpress_jm_is_in_options( $value, $option_key ){
    $options = get_option( JM_OPTIONS );
    if ( is_null($options) ){
        $options = array();
    }
    if ( isset($options[$option_key][$value] ) ){
        return $options[$option_key][$value];
    }elseif ( isset($options[$option_key]) && in_array($value, $options[$option_key]) ){
        return true;
    }elseif( !isset($options[$option_key]) ){
        //default value
        return true;
    }else{
        return false;
    }
}

//ajouter un clé parmis les valeurs d'un tableau
function jpress_jm_add_array_key( $keynew, $array ){
    foreach ( $array as $key => $subarray) {
        $newkey = $subarray[$keynew];
        $array[$newkey] = $subarray;
        unset( $array[$key] );
    }
    return $array;
}

//ajouter un clé parmis les valeurs d'un tableau objet
function jpress_jm_get_object_key( $keynew, $array ){
    $newarray = array();
    foreach ( $array as $key => $subarray) {
        $newkey = $subarray->$keynew;
        $newarray[] = $newkey;
    }
    return $newarray;
}

//map an array of sprintfed string
function jpress_jm_array_map_capabilities( $item, $post_type ){
    return sprintf($item, $post_type);
}

//create an walker of taxonomy in option fields
function jpress_jm_custom_taxonomy_walker($taxonomy, $display = 'option', $parent = 0, $depth = 0 , $value_selected = null, $name = '')
{
    if (!is_array($value_selected)){
        $value_selected = (array)$value_selected;
    }

    $terms = get_terms($taxonomy, array('parent' => $parent, 'hide_empty' => false));
    if(count($terms) > 0)
    {
        $out = ( $display != 'option' && $display != 'checkbox' ?  '<ul>' : '');
        foreach ($terms as $term)
        {
            if (  $display == 'option' ){
                $out .='<option value="' . $term->term_id . '"  ' . ( in_array( $term->term_id, $value_selected ) ? 'selected' : '' )  . '>' . str_repeat("--", $depth ) . ' ' . $term->name  . "</option>";
                $out .= jpress_jm_custom_taxonomy_walker($taxonomy, $display, $term->term_id, $depth+1, $value_selected, $name);
            }elseif (  $display == 'checkbox' ){
                $out .= str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth ) . '<input name="'. $name . '[]" type="checkbox" id="cb-tax-' . $taxonomy . $term->term_id . '" value="' . $term->term_id . '"  ' . ( in_array( $term->term_id, $value_selected ) ? 'checked' : '' )  . '><label for="cb-tax-' . $taxonomy . $term->term_id . '">' . $term->name  . "</label><br>";
                $out .= jpress_jm_custom_taxonomy_walker($taxonomy, $display, $term->term_id, $depth+1, $value_selected, $name);
            }else{
                $out .='<li>' . $term->name . jpress_jm_custom_taxonomy_walker($taxonomy, $display, $term->term_id, $depth+1, $value_selected, $name) . '</li>';
            }
        }
        $out .= ( $display != 'option' && $display != 'checkbox' ? '</ul>' : '' );
        return $out;
    }
    return;
}

//get taxonomy childre and parent
function jpress_jm_get_all_family_tax( $taxonomy, $parent = 0 ) {
    $terms = get_terms( $taxonomy, array( 'parent' => $parent, 'fields' => 'ids' ) );
    $children = array($parent);
    foreach ( $terms as $term_id ){
        $childsofchild = jpress_jm_get_all_family_tax( $taxonomy, $term_id );
        foreach ( $childsofchild as $c) {
            $children[] = $c;
        }
    }
    return $children;
}

//get current theme
function jpress_jm_get_current_theme(){
    $current_theme = '';
    if ( $theme = get_option( 'current_theme' ) ){
        $current_theme =  $theme;
    }else{
        $current_theme =  wp_get_theme()->get('Name');
    }
    return $current_theme;
}

//get_ page par template
function jpress_jm_get_page_by_template( $tpl ){
    list( $page ) = get_posts(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $tpl,
        'post_type' => 'page',
        'post_status' => 'publish',
        'suppress_filters' => false,
    ));
    return $page;
}

//concatenation d'une propriété d'un tableau d'objet
function jpress_jm_implode_array_of_object( $array , $attribut, $separator = ', ' ){
    $str = '';
    $glue = '';
    if ( !empty($array) ){
        foreach ( $array as $obj) {
            $str.=  $glue .  $obj->$attribut ;
            $glue = $separator;
        }
    }

    return $str;
}

//get usr offre id
function jpress_jm_get_current_user_posts(  $pt ){
    global $current_user, $wpdb;
    return $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_author = " . $current_user->ID . " AND post_status = 'publish' AND post_type =  '" . $pt . "'" );
}

//insertion candidature
function jpress_jm_post_candidature( $args ){
    global $jpress_jm_candidature_fields;
    $default_args = array();
    foreach ( $jpress_jm_candidature_fields as $field) {
         $default_args[ $field['metakey'] ] = '';
    }
    $args = wp_parse_args($args, $default_args);

    $idcandidature = wp_insert_post(array(
            'post_title' => $args['nom'] . ' ' . $args['prenom'],
            'post_name' => sanitize_title( $args['nom'] . ' ' . $args['prenom']),
            'post_status' => 'publish',
            'post_type' => JM_POSTTYPE_CANDIDATURE,
    ));

    //wpml compatibility
    if ( function_exists( 'icl_object_id' ) ){
        $_POST['icl_post_language'] = $language_code = ICL_LANGUAGE_CODE;
        include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
        wpml_update_translatable_content( 'post_' . JM_POSTTYPE_CANDIDATURE, $idcandidature, $language_code );
    }

    if ($idcandidature){
        foreach ( $jpress_jm_candidature_fields as $field) {
            if ( isset($args[$field['metakey']]) ){
                add_post_meta( $idcandidature, $field['metakey'], $args[$field['metakey']]);
            }
        }
    }
    //association à une offre
    if ( isset($args[JM_META_OFFRE_CANDIDATURE_RELATION]) ){
        add_post_meta( $idcandidature, JM_META_OFFRE_CANDIDATURE_RELATION, $args[JM_META_OFFRE_CANDIDATURE_RELATION]);
    }

    return $idcandidature;

}

function jpress_jm_post_candidature_mail( $candidature_id, $candidat_mail, $tplmailcandidat = null,  $tplmailadmin = null ){
    add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

    //mail for candidat
    if ( jpress_jm_is_in_options('candidat_confirmation', 'settings') ){
        $emailto = $candidat_mail;
        $headers[] = 'From : ' . get_bloginfo('name').' <noreply@' . $_SERVER['HTTP_HOST'] . '>';
        $email_subject =  '[' . get_bloginfo('name') . '] ' . jpress_jm_is_in_options( 'confirm-subject', 'template-mail' );
        $tplmailcandidat = is_null($tplmailcandidat) ? jpress_jm_is_in_options( 'confirm-message', 'template-mail' ) : $tplmailcandidat;

        $email_subject = stripslashes($email_subject);
        $tplmailcandidat = stripslashes(nl2br($tplmailcandidat));
        wp_mail($emailto, $email_subject, $tplmailcandidat, $headers);
    }

    //envoi admin
    $email = array();
    $candidature = JM_Candidature::getById($candidature_id);
    $offre = JM_Offre::getById($candidature->offre_associe);
    if ( jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
        $societe = JM_Societe::getById($offre->societe_associe);
    }

    //get email admin
    //$email[] = get_option('admin_email');

    //get global mail
    if ( jpress_jm_is_in_options('email_candidature', 'settings') ){
        $with_comma_mail = jpress_jm_is_in_options('email_candidature', 'settings');
        $email = array_merge($email, explode(',', $with_comma_mail) );
    }

    //mail societe
    if ( jpress_jm_is_in_options('societe_notification', 'settings') && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
        $email[] = $societe->email;
    }

    //mail auteur
    if ( jpress_jm_is_in_options('author_notification', 'settings') ){
        $auteur = get_user_by('id', $offre->auteur);
        $email[] = $auteur->user_email;
    }

    //profil RH
    if ( jpress_jm_is_in_options('profil_rh', 'settings') ){
        $args = array(
            'role' => JM_ROLE_RESPONSABLE_RH
        );

        //societe
        if ( jpress_jm_is_in_options('rh_by_soc', 'settings') && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
            $args['meta_query'][] = array(
                'key' => JM_META_USER_SOCIETE_FILTER_ID ,
                'value' => $societe->id,
                'compare' => '='
            );
        }

        //tax
        if ( $tax = jpress_jm_is_in_options('rh_by_tax', 'settings') ){
            if ( !empty($offre->$tax) ){
                $term_ids = jpress_jm_get_object_key('term_id',$offre->$tax);
                $args['meta_query'][] = array(
                    'key' => JM_META_USER_TAX_FILTER_ID ,
                    'value' => jpress_jm_get_all_trad( $term_ids, $tax ),
                    'compare' => 'IN'
                );
            }
        }

        $users_rh = get_users($args);
        foreach ( $users_rh as $u) {
            $email[] = $u->user_email;
        }

    }

    //filter and unicity
    $email = array_unique( $email );
    $email = implode(',', $email);
    $emailto = $email;

    $resume = '<ul>';
    global $jpress_jm_candidature_fields;
    foreach ( $jpress_jm_candidature_fields as $field) {
        if ($field['enable'] == false) continue;
        $resume .= '<li><strong>' .  $field['label'] . '&nbsp;:&nbsp;</strong>&nbsp;' . jpress_jm_render_field_value($field, $candidature,false) . '</li>' ;
    }
    $resume .= '</ul>' ;

    $tags = array(
        '{resume}',
        '{offre_titre}',
        '{lien_candidature}',
    );
    foreach ( $jpress_jm_candidature_fields as $field) {
        if ($field['enable'] == false) continue;
        $tags[] = '{' . $field['metakey']. '}';
    }

    $tagsreplace = array(
        $resume,
        $offre->titre,
        site_url() . '/wp-admin/post.php?post=' . $candidature->id . '&action=edit',
    );
    foreach ( $jpress_jm_candidature_fields as $field) {
        if ($field['enable'] == false) continue;
        $tagsreplace[] = jpress_jm_render_field_value($field, $candidature,false);
    }

    if ( isset($offre->id) && $offre->id> 0 ){
        $email_subject =  '[' . get_bloginfo('name') . '] ' . str_replace($tags, $tagsreplace, jpress_jm_is_in_options( 'admin-subject', 'template-mail' ))  ;
        $tplmailadmin = is_null($tplmailadmin) ? str_replace($tags, $tagsreplace, jpress_jm_is_in_options( 'admin-message', 'template-mail' )) : $tplmailadmin ;
    }else{
        $email_subject =  '[' . get_bloginfo('name') . '] ' . str_replace($tags, $tagsreplace, jpress_jm_is_in_options( 'admin-subject-spontanee', 'template-mail' ))  ;
        $tplmailadmin = is_null($tplmailadmin) ? str_replace($tags, $tagsreplace, jpress_jm_is_in_options( 'admin-message-spontanee', 'template-mail' )) : $tplmailadmin ;
    }
    $email_subject = stripslashes($email_subject);
    $tplmailadmin = stripslashes(nl2br($tplmailadmin));

    wp_mail($emailto, $email_subject, $tplmailadmin, $headers);

}

//get page by name
function jpress_jm_get_post_id_by( $post_part_column, $value, $post_type ){
    global $wpdb;
    return $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts WHERE post_{$post_part_column} = '" . $value . "' AND post_type = '" . $post_type . "' " );
}

//filtre id
add_filter( 'jpress_jm_post_id_filter', 'jpress_jm_get_translate_id', 10, 2 );
function jpress_jm_get_translate_id( $id, $pt ){
    //WPML compatibility
    if ( function_exists('icl_object_id') ) {
        if (is_array($id)){
            foreach ( $id as $k => $i) {
                $id[$k] = icl_object_id( $i, $pt, true, ICL_LANGUAGE_CODE );
            }
        }else{
            $id = icl_object_id( $id, $pt, true, ICL_LANGUAGE_CODE );
        }

    }

    return $id;
}

//get all translated id
function jpress_jm_get_all_trad( $id, $pt ){
    //WPML compatbilité
    if ( function_exists('icl_object_id') ) {
        if ( is_array( $id )){
            $array_ids = array();
            foreach ( $id as $i) {
                $array_ids = array_merge( $array_ids, jpress_jm_get_all_trad($i, $pt) );
            }
        }else{
            global $sitepress;
            $alllanguages = $sitepress->get_active_languages();
            $array_ids = array();
            foreach ( $alllanguages as $lang) {
                $array_ids[] = icl_object_id( $id, $pt, true,  $lang['code']);
            }
            $array_ids = array_filter($array_ids);
        }
    }else{
        if ( is_array( $id )){
            $array_ids = $id;
        }else{
            $array_ids = array($id);
        }
    }
    return $array_ids;
}