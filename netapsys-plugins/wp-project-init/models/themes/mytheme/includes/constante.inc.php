<?php
//le source du fichier contact
define('NOM_FICHIER_CONTACT' , 'contact');

//les taxonomies du sous-rubrique "nous rejooindre"
define('CDD_CDI', 'cddcdi');
define('ALTERNANCE', 'alternance');
define('STAGE', 'stage');
//id taxonomies post  offres type contrat
define('ID_CDD', 212);
define('ID_CDI', 213);
define('ID_ALTERNANCE', 215);
define('ID_STAGE', 214);
//id statut offres
define('ID_EMBAUCHE_EFFECTUER', 276);
define('ID_EN_COURS', 275);

//les base de donnes import

define('TABLE_DOC', 'jnew_ldc_documents');
define('TABLE_DOC_LANGUE', 'jnew_ldc_language_documents');
define('TABLE_TYPE_DOC', 'jnew_ldc_types_documents');
define('TABLE_CODE_LANGUE', 'jnew_languages');
define('TABLE_TAX_PAYS_OFFRE', 'jnew_jl_jobposting_pays');
define('TABLE_TAX_REGION_OFFRE', 'jnew_jl_jobposting_regions');
define('TABLE_TAX_TYPE_METIER_OFFRE', 'jnew_jl_jobposting_metiers');
define('TABLE_POST_OFFRE', 'jnew_jl_jobposting');
define('TABLE_POST_OFFRE_STAGE', 'jnew_jl_stageposting');

// nom du fichier a mette les document
define('NOM_FICHIER_DOC', 'documents');
//RUBRIQUE

//id informations reglementée en FR
define('ID_INFOS_REGLEMENTEE', 37);
define('ID_RUBRIQUE_MARQUE', 8);
define('ID_RUBRIQUE_PROCESS_RECRUTEMENT', 44);


//finance
define('ID_RUBRIQUE_FINANCE', 10);
define('ID_SRUB_CHIFFRE_CLE', 36);

//metier
define('ID_RUBRIQUE_METIERS', 11);
define('ID_RUBRIQUE_NOS_METIERS', 38);
define('ID_NOUSREJOINDRE', 42);
define('ID_OFFRE_SPONTANEE', 236);

//nos activités
define('ID_RUBRIQUE_NOS_ACTIVITES', 7);
define('ID_SRUB_NOS_IMPLANTATIONS', 223);
define('ID_SRUB_POLES_VOLAILLE', 228);
//nosgroupe
define('ID_RUB_LE_GROUPE', 6);
define('ID_SRUB_CULTURE_VALEUR', 17);

//nos engagement
define('ID_SOUS_RUBRIQUE_QUALITE', 22);
define('ID_SOUS_RUBRIQUE_QUALITE_MOBILE', 22);
define('ID_SOUS_RUBRIQUE_ENVIRONNEMENT', 24);
define('ID_RUBRIQUE_NOS_ENGAGEMENT', 9);

//medias
define('ID_SOUS_RUBRIQUES_ACTUS', 46);
//define('ID_SOUS_RUBRIQUES_ACTUS', 244);
define('ID_RUBRIQUE_MEDIAS',12);
define('ID_SOUS_RUBRIQUE_MEDIATHEQUE', 45);
define('SLUG_MEDIAS', 'medias');

//id du taxonomie type offres CDD pour la rubriques nos offres
define('ID_TAX_TYPE_CONTRAT_CDD', 212);
//id du taxonomie type offres CDI pour la rubriques nos offres
define('ID_TAX_TYPE_CONTRAT_CDI', 213);
//id du taxonomie type offres CDD.CDI pour la rubriques nos offres
define('ID_TAX_TYPE_CONTRAT_CDDCDI', 348);

//SLUG du taxonomie type offres CDD.CDI pour la rubriques nos offres
define('SLUG_CDDCDI', 'cddcdipos');

//slug taxonomie exercice infos reglementee
define('SOUS_TAX_EXERCICE', 'exercice');
//slug champ fichier infos reglementee
define('CHAMP_FICHIER', 'fichier_document');

//reglement du cycle pour les filtres sur l'exercice
define('DEBUT_CYCLE_MOIS',7);
define('FIN_CYCLE_MOIS', 6);

//tax rubrique
define('RUBRIQUE','rubrique');
//slug taxonomies rubrique document
define('TAX_DOCUMENT', 'type-document');
//slug référence
define('CHAMP_REFERENCE_OFFRE', 'reference_offre');
//slug durré offre
define('CHAMP_DUREE_OFFRE', 'duree_offre');
//slug type metier
define('TAX_TYPE_METIER', 'type_metier');
//type contrat
define('TAX_TYPE_CONTRAT_OFFRE', 'type_contrat');
//pays nos offre
define('TAX_PAYS_OFFRE', 'pays');
//region nos offres
define('TAX_REGION_OFFRE', 'region');
//type de poste
define('TAX_TYPE_POSTE_IMPLANTATION','type_activite');

//pages
define('PAGE_NOSMARQUES_ID' , icl_object_id(84,'page',true));

//formulaire
define('FORM_CANDIDATURE_DEPOT_ID', 2);
define('FORM_CANDIDATURE_SPONTANNEE_ID', 5);
define('FORM_ETRE_ALERTE', 3);

//ID_TERM_ASSEMBLE_GENERAL
define('ID_ASSEMBLE_GENERAL',33);

/*CONSTANTE PAGINATION*/
define('PAGINATION_OFFRE_COL',2);
define('PAGINATION_OFFRE_COL_MOBILE',1);
define('NBR_ELTS_MOBILE_OFFRE',5);
define('PAGINATION_MEDIA_COL',1);

// type_activite
define('AMONT',216);
define('VOLAILLE',217);
define('TRAITEUR',218);
define('INTERNATIONNAL',219);
//filtre des langue
define('FILTRE_LANGUE_CURRENT', 'skip_missing=1');
define('WPML_ACTIVES_LANGUES', 'fr');

//minified js et css
define('MINIFIED_CSS', true);
define('MINIFIED_JS', true);
define('MINIFIED_VERSION', '2.2');

//taxonomies type de doc AG
define('ID_TYPE_DOC_AG', 299);