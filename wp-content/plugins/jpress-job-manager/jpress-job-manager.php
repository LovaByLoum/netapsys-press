<?php
/*
Plugin Name: jPress Job Manager
Description: Utilitaire de gestion d'offres et de candidatures, avec gestion de droits
Depends : jpress-ajax-upload
Version: 1.0
Author: Johary
*/

require_once ('inc/constantes.inc.php');
require_once ('inc/default-config.inc.php');
require_once ('utils/functions.utils.php');
require_once ('inc/functions.inc.php');
require_once ('classes/societe.class.php');
require_once ('classes/offre.class.php');
require_once ('classes/candidature.class.php');
require_once ('inc/rewrite.inc.php');
require_once ('extends/extends.php');

//admin
require_once ('admin/admin.php');
require_once ('admin/metaboxes.php');
require_once ('admin/columns.php');
require_once ('admin/rules.php');

add_action('init', 'jpress_jm_init' );
function jpress_jm_init(){
    global $jpress_jm_candidature_fields, $jpress_jm_offre_fields, $jpress_jm_societe_fields;

    //initialisation des post types et taxonomies coeur
    jpress_jm_register_post_types_tax();

    $jpress_jm_candidature_fields = jpress_jm_add_array_key('metakey', apply_filters ( 'jpress_jm_champs_candidature', $jpress_jm_candidature_fields ));
    $jpress_jm_offre_fields = jpress_jm_add_array_key('metakey', apply_filters ( 'jpress_jm_champs_offre', $jpress_jm_offre_fields ));
    $jpress_jm_societe_fields = jpress_jm_add_array_key('metakey', apply_filters ( 'jpress_jm_champs_societe', $jpress_jm_societe_fields ));

    register_post_status( 'expired', array(
        'label'                     => __( 'Expiré'),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Expiré <span class="count">(%s)</span>', 'Expiré <span class="count">(%s)</span>' ),
    ) );

    jpress_jm_expired_offre();
}

register_activation_hook( __FILE__ , 'jpress_jm_on_activate' );
function jpress_jm_on_activate(){
    global $jpress_jm_capabilities;
    $options = get_option( JM_OPTIONS );
    if ( is_null($options) || $options == false ){
        //initialisation role
        $roles = get_editable_roles();
        $post_types = array(
            JM_POSTTYPE_SOCIETE,
            JM_POSTTYPE_OFFRE,
            JM_POSTTYPE_CANDIDATURE
        );
        foreach ( $roles as $name => $role ) {
            $role = get_role( $name );
            foreach ( $jpress_jm_capabilities as $cap) {
                $post_cap = sprintf( $cap , 'post' );
                $post_cap_with_s = sprintf( $cap , 'posts' );
                if ( $role->has_cap( $post_cap ) || $role->has_cap( $post_cap_with_s ) || $role->has_cap( 'manage_categories') ) {
                    foreach ( $post_types as $pt ) {
                        $post_type_cap = sprintf( $cap, $pt );
                        $role->add_cap( $post_type_cap );
                    }
                }
            }
        }
    }

}

//add post status


//tache cron pour la dépublication des offres expirées
add_filter('cron_schedules', 'jpress_jm_add_scheduled_interval');
function jpress_jm_add_scheduled_interval($schedules) {
    $schedules['jm-hourly'] = array('interval'=>60, 'display'=>'Once 1 day');
    return $schedules;
}

if (!wp_next_scheduled('jpress_jm_hourly_task')) {
    wp_schedule_event(time(), 'jm-hourly', 'jpress_jm_hourly_task');
}
add_action ( 'jpress_jm_hourly_task', 'jpress_jm_expired_offre' );
function jpress_jm_expired_offre(){
    $date = date('Y-m-d');
    $pids = get_posts(array(
        'post_type' => JM_POSTTYPE_OFFRE,
        'post_status' => 'publish',
        'suppress_filters' => true,
        'fields' => 'ids',
        'numberposts' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'expire',
                'value' => $date,
                'compare' => '<='
            ),
            array(
                'key' => 'expire',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'expire',
                'value' => '',
                'compare' => '!='
            ),
        )
    ));

    if (!empty($pids)){
        foreach ( $pids as $id) {
            wp_update_post(array(
                'ID' => $id,
                'post_status' => 'expired'
            ));
        }

        //flush cache
        //wp super cache
        if ( function_exists( 'prune_super_cache' ) ){
            global $cache_path;
            prune_super_cache ($cache_path, true);
        }

        //jpres zone cache
        if ( function_exists( 'jzc_purge' ) ){
            jzc_purge (null);
        }
    }
}


//add 'Expired' information for expired offre
add_filter( 'display_post_states', 'jpress_jm_display_expired_states', 10, 2 );
function jpress_jm_display_expired_states( $post_states, $post ) {
    if ( $post->post_status == 'expired' ) {
        $post_states['archived'] = "<span style='color:#D54E21;'>" . __( 'Expiré' ) . "</span>";
    }
    return $post_states;
}
