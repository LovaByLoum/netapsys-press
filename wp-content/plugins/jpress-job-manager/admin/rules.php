<?php
/**
 * regle d'accès et droits en BO
 */

//ajout champs filtre taxonomique sur la fiche user
add_action("edit_user_profile", "jpress_jm_profile_personal_options");
add_action("show_user_profile", "jpress_jm_profile_personal_options");
function jpress_jm_profile_personal_options($profile){
    global $current_user;
    $options = get_option( JM_OPTIONS );
    $rh_by_tax = jpress_jm_is_in_options( 'rh_by_tax', 'settings' );
    $rh_by_soc = jpress_jm_is_in_options( 'rh_by_soc', 'settings' );
    if ( !empty($rh_by_tax) || !empty($rh_by_soc) ):?>
        <h3>Gestion des offres</h3>
        <table class="form-table">
            <tbody>

            <?php if ( !empty($rh_by_soc) ) :
                $value_selected = get_user_meta( $profile->ID, JM_META_USER_SOCIETE_FILTER_ID, true);?>
                <tr>
                    <th scope="row">
                        <label>Votre compte peut gérer les offres rattachés à la société : </label>
                    </th>
                    <td>
                        <?php if( in_array( $current_user->roles[0], array( 'administrator' ) ) ):
                            $societes = JM_Societe::getBy();
                            ?>
                            <select class="chosen-select" name="jpress_jm_soc_filter">
                                <?php foreach ( $societes as $soc):?>
                                    <option <?php if ( $soc->id == $value_selected ) :?>selected<?php endif;?> value="<?php echo $soc->id;?>"><?php echo $soc->titre;?></option>
                                <?php endforeach;?>
                            </select>
                        <?php else:?>
                            <select class="chosen-select">
                                <option> <?php
                                    $societe = JM_Societe::getById( $value_selected );
                                    echo $societe->titre;
                                    ?> </option>
                            </select>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif;?>

            <?php if ( !empty($rh_by_tax) ) :
                $value_selected = get_user_meta( $profile->ID, JM_META_USER_TAX_FILTER_ID );?>
                <tr>
                    <th scope="row">
                        <label>Votre compte peut gérer les offres rattachés aux termes (<?php echo $rh_by_tax; ?>) : </label>
                    </th>
                    <td>
                        <?php if( in_array( $current_user->roles[0], array( 'administrator' ) ) ):?>
                            <?php echo jpress_jm_custom_taxonomy_walker($rh_by_tax, 'checkbox', 0, 0, $value_selected, 'jpress_jm_tax_filter'); ?>
                            <?php else:?>
                                <?php
                                    $glue = '';
                                    foreach ( $value_selected as $tid): ?>
                                    <label>
                                        <?php
                                            $t = get_term( $tid, $rh_by_tax);
                                            echo $glue . $t->name;
                                            $glue = ', ';
                                        ?>
                                    </label>
                                <?php endforeach;?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif;?>

            </tbody>
        </table>
        <?php
    endif;
}

//ajout sauvegarde filtre taxonomique sur la fiche user
add_action( "profile_update", "jpress_jm_profile_update", 10, 2 );
function jpress_jm_profile_update( $user_id, $old_user_data){
    $profile = get_user_by('id',$user_id);
    if ( !in_array($profile->roles[0], array( JM_ROLE_RESPONSABLE_RH ) ) ) return;

    if(isset($_POST["jpress_jm_soc_filter"]) && intval($_POST["jpress_jm_soc_filter"]) > 0 ){
        update_user_meta( $user_id, JM_META_USER_SOCIETE_FILTER_ID, intval($_POST["jpress_jm_soc_filter"])  );
    }else{
        delete_user_meta($user_id, JM_META_USER_SOCIETE_FILTER_ID );
    }

    if(isset($_POST["jpress_jm_tax_filter"]) && !empty($_POST["jpress_jm_tax_filter"]) ){
        foreach ( $_POST["jpress_jm_tax_filter"] as $tid) {
            add_user_meta( $user_id, JM_META_USER_TAX_FILTER_ID, $tid ) ;
        }
    }else{
        delete_user_meta($user_id, JM_META_USER_TAX_FILTER_ID );
    }
}

//filtrer les entités
if(is_admin()){
    add_filter( 'parse_query', 'jpress_jm_post_type_query' );
}

//filtre sur la page de liste
function jpress_jm_post_type_query( $query )
{
    global $current_screen, $pagenow, $current_user ;
    if ( !in_array($current_user->roles[0], array( JM_ROLE_RESPONSABLE_RH ) ) ) return $query;

    if ( !$query->is_main_query()  ) return $query;

    $rh_by_tax = jpress_jm_is_in_options( 'rh_by_tax', 'settings' );
    $rh_by_soc = jpress_jm_is_in_options( 'rh_by_soc', 'settings' );

    if ( $pagenow == 'edit.php' ) {
        $pt = $current_screen->post_type;
        switch ($pt){
            case JM_POSTTYPE_SOCIETE :
                if ( $rh_by_soc ){
                    $the_societe =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE );
                    $query->query_vars['post__in']=array( $the_societe );
                }
                break;
            case JM_POSTTYPE_OFFRE :
                if ( $rh_by_soc && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
                    $the_societe =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE ) ;
                    $mq= array(
                        'key' => JM_META_SOCIETE_OFFRE_RELATION ,
                        'value' => $the_societe,
                        'compare' => '='
                    );
                    $query->query_vars['meta_query'][] = $mq;
                    $query->query_vars['meta_query']['relation'] = 'AND';
                }
                if ( $rh_by_tax ){
                    $the_term = apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_TAX_FILTER_ID ), $rh_by_tax );
                    $tq= array(
                        'taxonomy' => $rh_by_tax ,
                        'field' => 'id',
                        'terms' => $the_term,
                        'include_children' => true
                    );
                    $query->query_vars['tax_query'][] = $tq;
                    $query->query_vars['tax_query']['relation'] = 'AND';
                }

                break;
            case JM_POSTTYPE_CANDIDATURE :
                if ( $rh_by_tax ){
                    $the_term = apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_TAX_FILTER_ID ), $rh_by_tax );
                    $offre_rattache_au_tax = JM_Offre::getBy(array(
                        'taxonomy' => $rh_by_tax,
                        'id' => $the_term
                    ),
                    null, null, true);
                    if ( !empty($offre_rattache_au_tax) ){
                        $mq= array(
                            'key' => JM_META_OFFRE_CANDIDATURE_RELATION ,
                            'value' => $offre_rattache_au_tax,
                            'compare' => 'IN'
                        );
                        $mqe= array(
                            'key' => JM_META_OFFRE_CANDIDATURE_RELATION ,
                            'value' => 0,
                            'compare' => '='
                        );
                        $query->query_vars['meta_query'][] = $mq;
                        $query->query_vars['meta_query'][] = $mqe;
                        $query->query_vars['meta_query']['relation'] = 'OR';
                    }
                }
                if ( $rh_by_soc && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
                    $the_societe =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE );
                    $offre_rattache_au_soc = JM_Offre::getBy(
                        null,null, null, true,
                        array(
                            'key' => JM_META_SOCIETE_OFFRE_RELATION,
                            'value' => $the_societe
                        )
                    );
                    $mq= array(
                        'key' => JM_META_OFFRE_CANDIDATURE_RELATION ,
                        'value' => $offre_rattache_au_soc,
                        'compare' => 'IN'
                    );
                    $query->query_vars['meta_query'][] = $mq;
                    $query->query_vars['meta_query']['relation'] = 'AND';
                }


                break;
            default:
                break;
        }
    }

    return $query;
}

//filtre l'edition de contenu pour les RH
add_action( 'admin_init', 'jpress_jm_filter_content_rh' );
function jpress_jm_filter_content_rh(){
    global  $pagenow, $current_user ;

    if ( !in_array($current_user->roles[0], array( JM_ROLE_RESPONSABLE_RH ) ) ) return '';

    $rh_by_tax = jpress_jm_is_in_options( 'rh_by_tax', 'settings' );
    $rh_by_soc = jpress_jm_is_in_options( 'rh_by_soc', 'settings' );

    if ( $pagenow == 'post.php' && isset($_REQUEST['post'] ) ){
        $pst = get_post($_REQUEST['post']);
        switch ($pst->post_type){
            case JM_POSTTYPE_SOCIETE :
                if ( $rh_by_soc ){
                    $the_societe_id =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE );
                    $soc = JM_Societe::getById($the_societe_id);
                    if ( $the_societe_id != $pst->ID ){
                        wp_die( 'Alors, on triche ? Votre compte vous permet uniquement de modifier les offres rattachées à la société ' . $soc->titre );
                    }
                }
                break;
            case JM_POSTTYPE_OFFRE :
                if ( $rh_by_soc && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
                    $the_societe_id =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE );
                    $soc = JM_Societe::getById($the_societe_id);
                    $offre = JM_Offre::getById( $pst->ID );
                    if ( $the_societe_id != $offre->societe_associe ){
                        wp_die( 'Alors, on triche ? Votre compte vous permet uniquement de modifier les offres rattachées à la société ' . $soc->titre );
                    }
                }
                if ( $rh_by_tax ){
                    $the_term_ids = apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_TAX_FILTER_ID ), $rh_by_tax );
                    $all_family = array();
                    $glue = '';
                    $the_term_names = '';
                    foreach ( $the_term_ids as $the_term_id){
                        $the_term = get_term( $the_term_id, $rh_by_tax );
                        $all_family = array_merge($all_family, jpress_jm_get_all_family_tax($rh_by_tax, $the_term_id ));
                        $the_term_names .= $glue . $the_term->name;
                        $glue = ', ';
                    }

                    $terms = wp_get_post_terms($pst->ID, $rh_by_tax, array('fields' => 'ids'));
                    $bool_in = false;
                    foreach ( $terms as $t) {
                        if ( in_array( $t, $all_family ) ){
                            $bool_in = true;
                        }
                    }
                    if ( !$bool_in ){
                        wp_die( 'Alors, on triche ? Votre compte vous permet uniquement de modifier les offres rattachées au ' . $rh_by_tax . ' ' . $the_term_names );
                    }
                }
                break;
            case JM_POSTTYPE_CANDIDATURE:
                if ( $rh_by_soc && jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
                    $the_societe_id =  apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_SOCIETE_FILTER_ID, true), JM_POSTTYPE_SOCIETE );
                    $candidature = JM_Candidature::getById( $pst->ID );
                    $offre = JM_Offre::getById( $candidature->offre_associe );
                    $societe = JM_Societe::getById($offre->societe_associe);
                    if ( $offre->societe_associe != $the_societe_id ){
                        wp_die( 'Alors, on triche ? Votre compte vous permet uniquement de modifier les candidatures aux offres rattachées à la société ' . $societe->titre );
                    }
                }
                if ( $rh_by_tax ){
                    $the_term_ids = apply_filters( 'jpress_jm_post_id_filter', get_user_meta( $current_user->ID, JM_META_USER_TAX_FILTER_ID ), $rh_by_tax );
                    $all_family = array();
                    $glue = '';
                    $the_term_names = '';
                    foreach ( $the_term_ids as $the_term_id){
                        $the_term = get_term( $the_term_id, $rh_by_tax );
                        $all_family = array_merge($all_family, jpress_jm_get_all_family_tax($rh_by_tax, $the_term_id ));
                        $the_term_names .= $glue . $the_term->name;
                        $glue = ', ';
                    }

                    $candidature = JM_Candidature::getById( $pst->ID );
                    $terms = wp_get_post_terms($candidature->offre_associe, $rh_by_tax, array('fields' => 'ids'));
                    $bool_in = false;
                    foreach ( $terms as $t) {
                        if ( in_array( $t, $all_family ) ){
                            $bool_in = true;
                        }
                    }
                    if ( !$bool_in ){
                        wp_die( 'Alors, on triche ? Votre compte vous permet uniquement de modifier les candidatures aux offres rattachées au ' . $rh_by_tax . ' ' . $the_term_names );
                    }

                }
                break;
            default:
                break;
        }
    }
}

