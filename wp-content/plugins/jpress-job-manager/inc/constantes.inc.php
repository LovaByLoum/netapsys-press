<?php
/**
 * declaration des constantes
 */

//modifier le slug des post type et taxonomies ici, si en conflit avec d'autres
define ( 'JM_POSTTYPE_SOCIETE', 'societe' );
define ( 'JM_POSTTYPE_OFFRE', 'offre' );
define ( 'JM_POSTTYPE_CANDIDATURE', 'candidature' );

define ( 'JM_TAXONOMIE_TYPE_CONTRAT', 'type-contrat' );
define ( 'JM_TAXONOMIE_LOCALISATION', 'localisation' );
define ( 'JM_TAXONOMIE_DEPARTEMENT', 'departement' );
define ( 'JM_TAXONOMIE_CATEGORIE', 'categorie-offre' );
define ( 'JM_TAXONOMIE_DOMAINE', 'domaine-activite' );

//role
define ( 'JM_ROLE_RESPONSABLE_RH' , 'responsable_rh');

//meta name
define ('JM_META_OFFRE_CANDIDATURE_RELATION', 'offre-rattache' );
define ('JM_META_SOCIETE_OFFRE_RELATION', 'societe-rattache' );
define ('JM_META_USER_TAX_FILTER_ID', 'jpress_jm_user_tax_filter' );
define ('JM_META_USER_SOCIETE_FILTER_ID', 'jpress_jm_soc_filter' );

define ('JM_META_LIST_OFFRE_GABARIT', 'list-gabarit-type' );
define ('JM_META_LIST_OFFRE_FILTRE', 'list-filter-tax' );
define ('JM_META_LIST_OFFRE_SORT', 'list-sort-meta' );
define ('JM_META_LIST_ITEM_PER_PAGE', 'item-per-page' );

define ('JM_META_DETAIL_OFFRE_GABARIT', 'detail-gabarit-type' );

define ('JM_META_POSTULER_OFFRE_GABARIT', 'post-gabarit-type' );
define ('JM_META_POSTULER_OFFRE_CAPTCHA', 'post-captcha-validation' );

define ('JM_REWRITE_SLUG_POSTULER', __('offre','jpress-job-manager') );
define ('JM_REWRITE_QUERYVAR_OFFREPOSTULER', 'offrepostuler' );


//constante utils
define ( 'JM_PLUGIN_URL' , plugins_url(basename(dirname(dirname(__FILE__)))) );
define ( 'JM_PLUGIN_ROOT' , dirname(dirname(__FILE__))  );
define ( 'JM_TEMPLATE_LISTE_OFFRE' , 'template-liste-offre.tpl.php' );
define ( 'JM_TEMPLATE_DETAIL_OFFRE' , 'single-' . JM_POSTTYPE_OFFRE . '.php');
define ( 'JM_TEMPLATE_POSTULER_OFFRE' , 'template-postuler-offre.tpl.php');

//admin
define ('JM_OPTIONS', 'jpress_jm_options' );
define ('JM_DROITS', 'jpress_jm_capabilities' );


