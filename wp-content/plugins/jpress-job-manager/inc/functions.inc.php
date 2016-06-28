<?php
/**
 * les fonctions core du plugin
 */

function jpress_jm_register_post_types_tax(){
    $options = get_option( JM_OPTIONS );
    if ( is_null($options) ){
        $options = array();
    }
    $menupos = 21;
    //::::::::::::::::::::::::::::societe::::::::::::::::::::::::::::::::::::::::::::::::::::::
    if ( jpress_jm_is_in_options( JM_POSTTYPE_SOCIETE, 'types' ) ){
        //post type societe
        $labels = jpress_jm_get_custom_post_type_labels('société', 'sociétés', 0);
        $data = array(
            'capability_type'      => JM_POSTTYPE_SOCIETE,
            'supports'             => array( 'title' , 'editor' , 'excerpt', 'thumbnail' ),
            'hierarchical'         => false,
            'exclude_from_search'  => false,
            'public'               => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => true,
            'menu_icon'            => JM_PLUGIN_URL . '/images/societe.png',
            'menu_position'        => $menupos,
            'labels'               => $labels,
            'query_var'            => true,
        );
        register_post_type( JM_POSTTYPE_SOCIETE, $data );

        //add taxonomy domaine hierarchique
        if ( jpress_jm_is_in_options( JM_TAXONOMIE_DOMAINE, 'tax' ) ){
            $labels_tax_doc = jpress_jm_get_custom_tax_labels('domaine d\'activités', 'domaines d\'activités', 1);
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels_tax_doc,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_term_' . JM_POSTTYPE_SOCIETE,
                    'edit_terms'   => 'manage_term_' . JM_POSTTYPE_SOCIETE,
                    'delete_terms' => 'manage_term_' . JM_POSTTYPE_SOCIETE,
                    'assign_terms' => 'manage_term_' . JM_POSTTYPE_SOCIETE,
                ),
                'rewrite'           => array( 'slug' => JM_TAXONOMIE_DOMAINE ),
            );
            register_taxonomy( JM_TAXONOMIE_DOMAINE, array( JM_POSTTYPE_SOCIETE ), $args );
        }
    }

    //::::::::::::::::::::::::::::offre::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $menupos++;
    if ( jpress_jm_is_in_options( JM_POSTTYPE_OFFRE, 'types' ) ){
        //post type offre
        $labels = jpress_jm_get_custom_post_type_labels('offre', 'offres', 0);
        $data = array(
            'capability_type'      => JM_POSTTYPE_OFFRE,
            'supports'             => array( 'title' , 'editor' /*, 'excerpt' */),
            'hierarchical'         => false,
            'exclude_from_search'  => false,
            'public'               => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => true,
            'menu_icon'            => JM_PLUGIN_URL . '/images/offre.png',
            'menu_position'        => $menupos,
            'labels'               => $labels,
            'query_var'            => true,
        );
        register_post_type( JM_POSTTYPE_OFFRE, $data );

        //add taxonomy type de contrat hierarchique
        if ( jpress_jm_is_in_options( JM_TAXONOMIE_TYPE_CONTRAT, 'tax' ) ){
            $labels_tax_doc = jpress_jm_get_custom_tax_labels('type de contrat', 'types de contrat', 1);
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels_tax_doc,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'edit_terms'   => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'delete_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'assign_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                ),
                'rewrite'           => array( 'slug' => JM_TAXONOMIE_TYPE_CONTRAT ),
            );
            register_taxonomy( JM_TAXONOMIE_TYPE_CONTRAT, array( JM_POSTTYPE_OFFRE ), $args );
        }

        //add taxonomy pays hierarchique
        if ( jpress_jm_is_in_options( JM_TAXONOMIE_LOCALISATION, 'tax' ) ){
            $labels_tax_doc = jpress_jm_get_custom_tax_labels('localisation', 'localisations', 1);
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels_tax_doc,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'edit_terms'   => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'delete_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'assign_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                ),
                'rewrite'           => array( 'slug' => JM_TAXONOMIE_LOCALISATION ),
            );
            register_taxonomy( JM_TAXONOMIE_LOCALISATION, array( JM_POSTTYPE_OFFRE ), $args );
        }

        //add taxonomy département hierarchique
        if ( jpress_jm_is_in_options( JM_TAXONOMIE_DEPARTEMENT, 'tax' ) ){
            $labels_tax_doc = jpress_jm_get_custom_tax_labels('département', 'départements', 1);
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels_tax_doc,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'edit_terms'   => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'delete_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'assign_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                ),
                'rewrite'           => array( 'slug' => JM_TAXONOMIE_DEPARTEMENT ),
            );
            register_taxonomy( JM_TAXONOMIE_DEPARTEMENT, array( JM_POSTTYPE_OFFRE ), $args );
        }

        //add taxonomy categorie hierarchique
        if ( jpress_jm_is_in_options( JM_TAXONOMIE_CATEGORIE, 'tax' ) ){
            $labels_tax_doc = jpress_jm_get_custom_tax_labels('catégorie', 'catégories', 0);
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels_tax_doc,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'edit_terms'   => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'delete_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                    'assign_terms' => 'manage_term_' . JM_POSTTYPE_OFFRE,
                ),
                'rewrite'           => array( 'slug' => JM_TAXONOMIE_CATEGORIE ),
            );
            register_taxonomy( JM_TAXONOMIE_CATEGORIE, array( JM_POSTTYPE_OFFRE ), $args );
        }
    }

    //::::::::::::::::::::::::::::candidature::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $menupos++;
    if ( jpress_jm_is_in_options( JM_POSTTYPE_CANDIDATURE, 'types' ) ){
        //post type candidature
        $labels = jpress_jm_get_custom_post_type_labels('candidature', 'candidatures', 1);
        $data = array(
            'capability_type'      => JM_POSTTYPE_CANDIDATURE,
            'supports'             => array( '' ),
            'hierarchical'         => false,
            'exclude_from_search'  => true,
            'public'               => false,
            'show_ui'              => true,
            'show_in_nav_menus'    => true,
            'menu_icon'            => JM_PLUGIN_URL . '/images/candidature.png',
            'menu_position'        => $menupos,
            'labels'               => $labels,
            'query_var'            => true,
        );
        register_post_type( JM_POSTTYPE_CANDIDATURE, $data );
    }

}

//dynamisation champ offre
add_filter( 'jpress_jm_champs_offre' , 'jpress_jm_dynamisation_champs_offre', 0);
function jpress_jm_dynamisation_champs_offre( $fields_array){
    foreach ( $fields_array as $key => $field) {
        if ( !jpress_jm_is_in_options( $field['metakey'], 'fields-offre' ) ){
            $fields_array[$key]['enable'] = 0;
        }
        if ( !jpress_jm_is_in_options( $field['metakey'], 'column-offre' ) ){
            $fields_array[$key]['admin_column'] = 0;
        }
    }
    return $fields_array;
}

//dynamisation champ candidature
add_filter( 'jpress_jm_champs_candidature' , 'jpress_jm_dynamisation_champs_candidature', 0);
function jpress_jm_dynamisation_champs_candidature( $fields_array){
    foreach ( $fields_array as $key => $field) {
        if ( !jpress_jm_is_in_options( $field['metakey'], 'fields-candidature' ) ){
            $fields_array[$key]['enable'] = 0;
        }
        if ( !jpress_jm_is_in_options( $field['metakey'], 'column-candidature' ) ){
            $fields_array[$key]['admin_column'] = 0;
        }
    }
    return $fields_array;
}

//dynamisation champ societe
add_filter( 'jpress_jm_champs_societe' , 'jpress_jm_dynamisation_champs_societe', 0);
function jpress_jm_dynamisation_champs_societe( $fields_array){
    foreach ( $fields_array as $key => $field) {
        if ( !jpress_jm_is_in_options( $field['metakey'], 'fields-societe' ) ){
            $fields_array[$key]['enable'] = 0;
        }
        if ( !jpress_jm_is_in_options( $field['metakey'], 'column-societe' ) ){
            $fields_array[$key]['admin_column'] = 0;
        }
    }
    return $fields_array;
}

