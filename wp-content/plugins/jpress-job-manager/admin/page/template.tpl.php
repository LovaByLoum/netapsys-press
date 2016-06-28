<?php
$options = get_option( JM_OPTIONS );
$page_list_created = true;
$current_theme = jpress_jm_get_current_theme();
$theme_path = get_theme_root() . DIRECTORY_SEPARATOR  . $current_theme;
$template_liste_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_LISTE_OFFRE ;
$template_detail_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_DETAIL_OFFRE ;
$template_postuler_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_POSTULER_OFFRE ;
$pageoffre = jpress_jm_get_page_by_template( JM_TEMPLATE_LISTE_OFFRE );
$pagepostuler = jpress_jm_get_page_by_template( JM_TEMPLATE_POSTULER_OFFRE );

?>
<div class="wrap">
    <h3>Liste des offres</h3>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Choisissez un gabarit pour la page de liste</th>
                    <td>
                        <?php
                        $selected = jpress_jm_is_in_options( JM_META_LIST_OFFRE_GABARIT, 'template-list');
                        $list_type = array(
                            'no-pagination'     => 'Sans pagination',
                            'pagination'        => 'Avec pagination (wp_page_navi)',
                            'pagination-ajax'   => 'Avec pagination Ajax (wp-pagination-loading)',
                            'infinite-loading'  => 'Chargement d\'offre supplémentaire à l\'infinie (wp-infinite-loading)',
                        )
                        ?>
                        <select name="template-list[<?php echo JM_META_LIST_OFFRE_GABARIT;?>]" class="chosen-select" style="width:400px">
                            <?php foreach ( $list_type as $k => $v):?>
                                <option value="<?php echo $k;?>" <?php if ( $selected == $k ):?>selected<?php endif;?>><?php echo $v;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Ajouter des filtres</th>
                    <td>
                        <?php
                        $array_tax = array(
                            JM_TAXONOMIE_DEPARTEMENT,
                            JM_TAXONOMIE_TYPE_CONTRAT,
                            JM_TAXONOMIE_CATEGORIE,
                            JM_TAXONOMIE_LOCALISATION
                        );
                        foreach ($array_tax as $tax) {
                            if ( !jpress_jm_is_in_options( $tax, 'tax' ) ) continue;
                            $the_taxonomie  = get_taxonomy( $tax );
                            echo '<input id="tax-filter-' . $tax . '" name="template-list[' . JM_META_LIST_OFFRE_FILTRE . '][]" type="checkbox" value="' . $tax . '" ' . ( @in_array( $tax, jpress_jm_is_in_options( JM_META_LIST_OFFRE_FILTRE, 'template-list' ) ) ? 'checked' : '' ) . '><label for="tax-filter-' . $tax . '">' . $the_taxonomie->labels->name . '</label><br>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Ajouter des tries</th>
                    <td>
                        <?php
                        global $jpress_jm_offre_fields;
                        foreach ( $jpress_jm_offre_fields as $field ) {
                            if ( $field['enable'] == false ) continue;
                            echo '<input id="meta-sort-' . $field['metakey'] . '" name="template-list[' . JM_META_LIST_OFFRE_SORT . '][]" type="checkbox" value="' . $field['metakey'] . '" ' . ( @in_array( $field['metakey'], jpress_jm_is_in_options( JM_META_LIST_OFFRE_SORT, 'template-list' ) ) ? 'checked' : '' ) . '><label for="meta-sort-' . $field['metakey'] . '">' . $field['label'] . '</label><br>';
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Nombre d'item par page</th>
                    <td>
                        <input type="number" name="template-list[<?php echo JM_META_LIST_ITEM_PER_PAGE;?>]" value="<?php echo jpress_jm_is_in_options( JM_META_LIST_ITEM_PER_PAGE, 'template-list' );?>" class="regular-text">
                    </td>
                </tr>

            </tbody>
        </table>
        <?php if ( is_file( $template_liste_theme_path ) && $pageoffre->ID > 0 ):?>
            <label style="color:blue">
                La page a été crée : <a target="_blank" href="<?php echo get_permalink($pageoffre->ID);?>"><?php echo $pageoffre->post_title;?></a>
                <br>Le template a déjà été crée
            </label>
            <p class="submit">
                <input type="submit" name="jm_submit_add_list_page" class="button button-secondary" value="Mettre à jour" onclick="return confirm('Attention ! Cet action écrasera le gabarit liste des offres dans votre thème.');">
                <br><i style="color:red;">Attention ! Cet action écrasera le gabarit liste des offres dans votre thème.</i>
            </p>
        <?php else:?>
            <p class="submit">
                <input type="submit" name="jm_submit_add_list_page" class="button button-secondary" value="Ajouter une page de liste">
                <br><i>Cet action va copier un gabarit de page dans vos fichiers de thème et créer une page "Offres" qui listera les offres.</i>
            </p>
        <?php endif;?>
    </form>
    <hr/>
    <h3>Detail d'une offre</h3>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">Choisissez un gabarit pour la page de détail</th>
                <td>
                    <?php
                    $selected = jpress_jm_is_in_options( JM_META_DETAIL_OFFRE_GABARIT, 'template-detail');
                    $list_type = array(
                        'left-sidebar'     => 'Avec un sidebar gauche',
                        'right-sidebar'     => 'Avec un sidebar droite',
                        'top-header'        => 'Avec un header',
                    )
                    ?>
                    <select name="template-detail[<?php echo JM_META_DETAIL_OFFRE_GABARIT;?>]" class="chosen-select" style="width:400px">
                        <?php foreach ( $list_type as $k => $v):?>
                            <option value="<?php echo $k;?>" <?php if ( $selected == $k ):?>selected<?php endif;?>><?php echo $v;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <?php if ( is_file( $template_detail_theme_path ) ):?>
            <label style="color:blue">
                Le template a déjà été crée
            </label>
            <p class="submit">
                <input type="submit" name="jm_submit_add_detail_page" class="button button-secondary" value="Mettre à jour" onclick="return confirm('Attention ! Cet action écrasera le template détail offre dans votre thème.');">
                <br><i style="color:red;">Attention ! Cet action écrasera le template détail offre dans votre thème.</i>
            </p>
        <?php else:?>
            <p class="submit">
                <input type="submit" name="jm_submit_add_detail_page" class="button button-secondary" value="Ajouter une page de détail">
                <br><i>Cet action va copier un fichier template dans votre thème pour afficher les détails d'une offre.</i>
            </p>
        <?php endif;?>
    </form>
    <hr/>
    <h3>Postuler à une offre</h3>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">Ajouter un captcha</th>
                <td>
                    <input type="checkbox" name="template-post[<?php echo JM_META_POSTULER_OFFRE_CAPTCHA;?>]" value="1" <?php if ( jpress_jm_is_in_options( JM_META_POSTULER_OFFRE_CAPTCHA, 'template-post' ) ): ?>checked<?php endif;?>>
                </td>
            </tr>

            </tbody>
        </table>
        <?php if ( is_file( $template_postuler_theme_path ) && $pagepostuler->ID > 0 ):?>
            <label style="color:blue">
                La page a été crée : <a target="_blank" href="<?php echo get_permalink($pagepostuler->ID);?>"><?php echo $pagepostuler->post_title;?></a>
                <br>Le template a déjà été crée
            </label>
            <p class="submit">
                <input type="submit" name="jm_submit_add_postuler_page" class="button button-secondary" value="Mettre à jour" onclick="return confirm('Attention ! Cet action écrasera le gabarit postuler une offre dans votre thème.');">
                <br><i style="color:red;">Attention ! Cet action écrasera le gabarit postuler une offre dans votre thème.</i>
            </p>
        <?php else:?>
            <p class="submit">
                <input type="submit" name="jm_submit_add_postuler_page" class="button button-secondary" value="Ajouter une page postuler">
                <br><i>Cet action va copier un gabarit de page dans vos fichiers de thème et créer une page "Postuler" pour pouvoir postuler à une offre.</i>
            </p>
        <?php endif;?>
    </form>
    <hr/>

    <form method="post" action="">
        <h3>Template mail de confirmation</h3>
        <em>Mail envoyé à l'internaute</em>
        <table>
            <tbody>
                <tr>
                    <td>Sujet</td>
                    <td>
                        <input type="text" name="template-mail[confirm-subject]" value="<?php
                            $value = jpress_jm_is_in_options( 'confirm-subject', 'template-mail' );
                            if ( !empty($value) && $value!== true ){
                                echo stripslashes($value);
                            }else{
                                echo __('Votre candidature a bien été enregistré', 'jpress-job-manager' );
                            }
                        ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td>
                        <textarea style="width:100%;height:300px;" name="template-mail[confirm-message]" class="regular-text"><?php
                            $value = jpress_jm_is_in_options( 'confirm-message', 'template-mail' );
                            if ( !empty($value) && $value!== true ){
                                echo stripslashes($value);
                            }else{
                                echo stripslashes(__("Bonjour\n\nVotre candidature a bien été enregistrée.\n\nNous nous efforcerons de vous donner une réponse dans un bref délai.\n\nCordialement,\nL'Equipe RH", 'jpress-job-manager' ));
                            }
                        ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h3>Template mail candidature à une offre</h3>
        <em>Mail envoyé à l'admin et RH</em>
        <table>
            <tbody>
                <tr>
                    <td>Sujet</td>
                    <td>
                        <input type="text" name="template-mail[admin-subject]" value="<?php
                        $value = jpress_jm_is_in_options( 'admin-subject', 'template-mail' );
                        if ( !empty($value) && $value!== true ){
                            echo stripslashes($value);
                        }else{
                            echo __('Nouvelle candidature sur l\'offre : {offre_titre}', 'jpress-job-manager');
                        }
                        ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td>
                        <textarea style="width:100%;height:300px;" name="template-mail[admin-message]" class="regular-text"><?php
                            $value = jpress_jm_is_in_options( 'admin-message', 'template-mail' );
                            if ( !empty($value) && $value!== true ){
                                echo stripslashes($value);
                            }else{
                                echo __("Bonjour,\n\nUne nouvelle candidature vient d'être enregistrée sur l'offre : {offre_titre}\n\nVoici les informations du candidat :\n\n{resume}\n\nVous pouvez consulter la candidature sur le site en cliquant <a href='{lien_candidature}'>ICI</a>\n\nCordialement", "jpress-job-manager" );
                            }
                        ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h3>Template mail candidature spontanée</h3>
        <em>Mail envoyé à l'admin et RH</em>
        <table>
            <tbody>
                <tr>
                    <td>Sujet</td>
                    <td>
                        <input type="text" name="template-mail[admin-subject-spontanee]" value="<?php
                        $value = jpress_jm_is_in_options( 'admin-subject-spontanee', 'template-mail' );
                        if ( !empty($value) && $value!== true ){
                            echo stripslashes($value);
                        }else{
                            echo __('Nouvelle candidature spontanée', 'jpress-job-manager' );
                        }
                        ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td>
                        <textarea style="width:100%;height:300px;" name="template-mail[admin-message-spontanee]" class="regular-text"><?php
                            $value = jpress_jm_is_in_options( 'admin-message-spontanee', 'template-mail' );
                            if ( !empty($value) && $value!== true ){
                                echo stripslashes($value);
                            }else{
                                echo __("Bonjour,\n\nUne nouvelle candidature spontanée vient d'être enregistrée.\n\nVoici les informations du candidat :\n\n{resume}\n\nVous pouvez consulter la candidature sur le site en cliquant <a href='{lien_candidature}'>ICI</a>\n\nCordialement", "jpress-job-manager" );
                            }
                            ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>Vous pouvez utiliser des tags HTML.<br>
        Vous pouvez également utiliser les tags suivants :<br>
            {resume}<br>
            {offre_titre}<br>
            {lien_candidature}<br>
            <?php
            global $jpress_jm_candidature_fields;
            foreach ( $jpress_jm_candidature_fields as $field) {
                if ($field['enable'] == false) continue;
                echo '{' . $field['metakey'] . '}<br>';
            }
            ?>
        </p>
        <?php if ( !isset($options['template-mail']) ):?>
            <p style="color:red;">Vous devez enregistrer le contenu des mails ci-dessus pour une première fois.</p>
        <?php endif;?>
        <p class="submit">
            <input type="submit" name="jm_submit_save_template_mail" class="button button-secondary" value="Enregistrer">
        </p>
    </form>
</div>