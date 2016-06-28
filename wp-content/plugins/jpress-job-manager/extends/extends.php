<?php
/**
 * fichier pour pouvoir etendre le fonctionnement du plugin sans toucher les fichiers du plugins
 *
 * mettre ici ou dans votre theme les surcharges pour ce plugin
 * se referer aux différents hook disponibles
 */

//surcharge champ offre
add_filter( 'jpress_jm_champs_offre' , 'jpress_jm_extends_champs_offre');
function jpress_jm_extends_champs_offre( $fields_array){
    //do stuff and return $fields_array
    return $fields_array;
}

//surcharge champ candidature
add_filter( 'jpress_jm_champs_candidature' , 'jpress_jm_extends_champs_candidature');
function jpress_jm_extends_champs_candidature( $fields_array){
    //do stuff and return $fields_array

    return $fields_array;
}

//surcharge champ societe
add_filter( 'jpress_jm_champs_societe' , 'jpress_jm_extends_champs_societe');
function jpress_jm_extends_champs_societe( $fields_array){
    //do stuff and return $fields_array

    return $fields_array;
}

