<?php
/**
 * gestion admin
 */

add_action( 'admin_enqueue_scripts', 'jpress_jm_admin_enqueue_scripts' );
function jpress_jm_admin_enqueue_scripts(){
    wp_enqueue_style('jquery-date-picker', JM_PLUGIN_URL . '/css/jquery-ui-datepicker.css');
    wp_enqueue_style('jquery-choosen', JM_PLUGIN_URL . '/css/bootstrap.css');
    wp_enqueue_style('jquery-fancybox', JM_PLUGIN_URL . '/css/jquery.fancybox.css');
    wp_enqueue_style('jpress_jm_admin', JM_PLUGIN_URL . '/css/admin.css');
    wp_enqueue_script('jquery-choosen', JM_PLUGIN_URL . '/js/chosen.jquery.js');
    wp_enqueue_script('jquery-date-picker', JM_PLUGIN_URL . '/js/jquery.ui.datepicker.js');
    wp_enqueue_script('jquery-date-picker-fr', JM_PLUGIN_URL . '/js/jquery.ui.datepicker-fr.js');
    wp_enqueue_script('ep-datepicker-call', JM_PLUGIN_URL . '/js/datepicker-call.js');
    wp_enqueue_script('jquery-fancybox', JM_PLUGIN_URL . '/js/jquery.fancybox.pack.js');
    wp_enqueue_script('jpress_jm_admin', JM_PLUGIN_URL . '/js/admin.js');
}

add_action( 'admin_menu', 'jpress_jm_admin_menu' );
function jpress_jm_admin_menu(){

    add_options_page(
        'jPress Job Manager',
        'jPress Job Manager',
        'activate_plugins',
        'jpress-job-manager',
        'jpress_jm_admin_page'
    );
}
function jpress_jm_admin_page(){
    include 'page/admin.tpl.php';
}

add_action( 'admin_menu', 'jpress_jm_admin_init', 1 );
function jpress_jm_admin_init(){
    //submit admin form data
    jpress_jm_submit_admin_form();
}

function jpress_jm_submit_admin_form(){
    if ( isset( $_POST['jm_submit_general'] ) ){
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        unset($_POST['jm_submit_general']);
        $options['types'] = $_POST['types'];
        $options['tax'] = $_POST['tax'];
        $options['settings'] = is_array($_POST['settings']) ? $_POST['settings'] : array(1);
        update_option( JM_OPTIONS, $options );

        //profil rh
        if ( isset($_POST['settings']) && $_POST['settings']['profil_rh'] ){
            add_role( JM_ROLE_RESPONSABLE_RH, 'Responsable RH');
            $rh_role = get_role(JM_ROLE_RESPONSABLE_RH);
            $minimum_caps = array(
                'read' ,
                'upload_files' ,
                'level_0' ,
                'level_1' ,
                'level_2' ,
            );
            foreach ( $minimum_caps as $cap) {
                $rh_role->add_cap( $cap );
            }
        }else{
            remove_role( JM_ROLE_RESPONSABLE_RH );
        }
    }elseif ( isset( $_POST['jm_submit_societe'] ) ){
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        unset($_POST['jm_submit_societe']);
        $options['fields-societe'] = $_POST['fields-societe'];
        $options['column-societe'] = $_POST['column-societe'];
        update_option( JM_OPTIONS, $options );
    }elseif ( isset( $_POST['jm_submit_offre'] ) ){
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        unset($_POST['jm_submit_offre']);
        if (isset($_POST['fields-offre'])){
            $options['fields-offre'] = $_POST['fields-offre'];
        }else{
            $options['fields-offre'] = array();
        }
        if (isset($_POST['column-offre'])){
            $options['column-offre'] = $_POST['column-offre'];
        }else{
            $options['column-offre'] = array();
        }
        update_option( JM_OPTIONS, $options );
    }elseif ( isset( $_POST['jm_submit_candidature'] ) ){
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        unset($_POST['jm_submit_candidature']);
        $options['fields-candidature'] = $_POST['fields-candidature'];
        $options['column-candidature'] = $_POST['column-candidature'];
        update_option( JM_OPTIONS, $options );
    }elseif ( isset( $_POST['jm_submit_capabilities'] ) ){
        global $jpress_jm_capabilities;
        unset($_POST['jm_submit_capabilities']);
        $post_data = empty( $_POST ) ? array() : $_POST;

        $post_types = array(
            JM_POSTTYPE_SOCIETE,
            JM_POSTTYPE_OFFRE,
            JM_POSTTYPE_CANDIDATURE
        );

        //add / remove cap
        $roles = get_editable_roles();
        $all_capabilities = array();
        $all_jpress_jm_capabilities = array();
        foreach ( $post_types as $pt) {
            $capabilities = array_map( 'jpress_jm_array_map_capabilities', $jpress_jm_capabilities, array_fill( 0, sizeof( $jpress_jm_capabilities ), $pt) );
            $all_jpress_jm_capabilities = array_merge( $all_jpress_jm_capabilities, $capabilities );
        }
        foreach (  $roles as $name => $role ) {
            $role_object = get_role($name);
            foreach ( $all_jpress_jm_capabilities as $cap ) {
                if ( !isset( $post_data[$name] ) ){
                    $post_data[$name] = array();
                }
                if ( in_array( $cap, $post_data[$name] ) ){
                    $role_object->add_cap($cap);
                }else{
                    $role_object->remove_cap($cap);
                }
            }
        }
    }elseif ( isset( $_POST['jm_submit_add_list_page'] ) ){
        unset($_POST['jm_submit_add_list_page']);
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        $options['template-list'] = $_POST['template-list'];
        update_option( JM_OPTIONS, $options );

        $gabarit = jpress_jm_is_in_options( JM_META_LIST_OFFRE_GABARIT, 'template-list');

        $current_theme = jpress_jm_get_current_theme();
        if (!empty( $current_theme )){
            $theme_path = get_theme_root() . DIRECTORY_SEPARATOR  . $current_theme;
            $template_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_LISTE_OFFRE ;
            $template_path = JM_PLUGIN_ROOT . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'liste-offre-' . $gabarit . '.tpl.php';
            if (!copy($template_path, $template_theme_path)) {
                wp_die ("La copie du fichier a échoué...");
            }else{
                $pageoffre = jpress_jm_get_page_by_template( JM_TEMPLATE_LISTE_OFFRE );
                if ( is_null($pageoffre) || empty($pageoffre) ){
                    $page_id = wp_insert_post(
                        array(
                            'post_title' => 'Offres',
                            'post_content' => 'Retrouvez tous les offres sur cette page.',
                            'post_status' => 'publish',
                            'post_type' => 'page'
                        )
                    );
                    update_post_meta( $page_id , '_wp_page_template', JM_TEMPLATE_LISTE_OFFRE );
                }
            }
        }else{
            wp_die('Theme invalid');
        }
    }elseif ( isset( $_POST['jm_submit_add_postuler_page'] ) ){
        unset($_POST['jm_submit_add_postuler_page']);
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        $options['template-post'] = $_POST['template-post'];
        update_option( JM_OPTIONS, $options );

        $gabarit = jpress_jm_is_in_options( JM_META_POSTULER_OFFRE_GABARIT, 'template-post');

        $current_theme = jpress_jm_get_current_theme();
        if (!empty( $current_theme )){
            $theme_path = get_theme_root() . DIRECTORY_SEPARATOR  . $current_theme;
            $template_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_POSTULER_OFFRE ;
            $template_path = JM_PLUGIN_ROOT . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'postuler-offre-standard.tpl.php';
            if (!copy($template_path, $template_theme_path)) {
                wp_die ("La copie du fichier a échoué...");
            }else{
                $pagepostuler = jpress_jm_get_page_by_template( JM_TEMPLATE_POSTULER_OFFRE );
                if ( is_null($pagepostuler) || empty($pagepostuler) ){
                    $page_id = wp_insert_post(
                        array(
                            'post_title' => 'Postuler',
                            'post_content' => '',
                            'post_status' => 'publish',
                            'post_type' => 'page'
                        )
                    );
                    update_post_meta( $page_id , '_wp_page_template', JM_TEMPLATE_POSTULER_OFFRE );
                }
            }
        }else{
            wp_die('Theme invalid');
        }
    }elseif ( isset( $_POST['jm_submit_add_detail_page'] ) ){
        unset($_POST['jm_submit_add_detail_page']);
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        $options['template-detail'] = $_POST['template-detail'];
        update_option( JM_OPTIONS, $options );

        $gabarit = jpress_jm_is_in_options( JM_META_DETAIL_OFFRE_GABARIT, 'template-detail');

        $current_theme = jpress_jm_get_current_theme();
        if (!empty( $current_theme )){
            $theme_path = get_theme_root() . DIRECTORY_SEPARATOR  . $current_theme;
            $template_theme_path = $theme_path . DIRECTORY_SEPARATOR . JM_TEMPLATE_DETAIL_OFFRE ;
            $template_path = JM_PLUGIN_ROOT . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'detail-offre-' . $gabarit . '.tpl.php';
            if (!copy($template_path, $template_theme_path)) {
                wp_die ("La copie du fichier a échoué...");
            }else{
                //do nothing
            }
        }else{
            wp_die('Theme invalid');
        }


    }elseif( isset( $_POST['jm_submit_save_template_mail'] ) ){
        unset($_POST['jm_submit_save_template_mail']);
        $options = get_option( JM_OPTIONS );
        if ( is_null($options) ){
            $options = array();
        }
        $options['template-mail'] = $_POST['template-mail'];
        update_option( JM_OPTIONS, $options );
    }

    //clear cache
    wp_cache_flush();
    //if wp-access-manager active
    if ( class_exists( 'mvb_Model_Cache' ) ){
        mvb_Model_Cache::clearCache();
    }

}

//action de ligne
add_action ( 'post_row_actions', 'jpress_jm_post_row_actions' , 10, 2 );
function jpress_jm_post_row_actions ( $actions, $post ){
    if ( $post->post_type == JM_POSTTYPE_CANDIDATURE ){
        $actions['visualiser'] = '<a href="' . JM_PLUGIN_URL . '/admin/page/candidature.popup.php?cid=' . $post->ID . '" class="fancybox fancybox.iframe">Visualiser</a>';
    }
    return $actions;
}

add_action ( 'admin_head', 'jpress_jm_admin_head' );
function jpress_jm_admin_head() {
    ?>
    <style>
        <?php
        $color = array(
            '#fd0',
            '#6FFF73',
            'rgb(255,132,135)',
            '#6fe',
            '#1E8',
            '#2BC',
        );
        $type_contrat = get_terms( JM_TAXONOMIE_TYPE_CONTRAT, array( 'hide_empty' => false ) );
        foreach ( $type_contrat as $key => $t):?>
            #the-list .taxonomy-type-contrat a[href*="<?php echo JM_TAXONOMIE_TYPE_CONTRAT . "=" .  $t->slug ;?>"]{
                background-color: <?php echo $color[$key];?> ;
            }
        <?php endforeach;?>


    </style>
    <?php
}
