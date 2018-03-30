<?php
/*
 * Definir ici les constantes applicatives
 *
 * Essayez de bien organiser la declaration des constantes :
 * en regroupant les mêmes types de constantes
 * en prefixant les mêmes types de constantes
 * et en mettant des commentaires
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

//post types
define( 'POST_TYPE_ARTICLE', 'article' );
define( 'POST_TYPE_ACTUALITE', 'actualite' );
define( 'POST_TYPE_PAGE', 'page' );

//taxonomies
define( 'TAXONOMOMY_CATEGORY', 'category' );
define( 'TAXONOMOMY_TAG', 'post_tag' );
define( 'TAXONOMY_TYPE_ACTUS', 'type_actualite' );

//champs personnalisés (meta value) : syntaxe FIELD_{type de post}_{nom du champ}
define ('FIELD_POST_POST_NOTE', 'post_note');
define ('FIELD_ACTUALITE_LIEU', 'actualite_lieu');
define ('FIELD_TYPE_ACTUS_ICONE', 'type_actus_icone');
define ('FIELD_USER_CIVILITE', 'user_civilite');
define ('FIELD_USER_DATE_NAISSANCE', 'user_date');
define ('FIELD_USER_ADRESSE', 'user_adresse');
define ('FIELD_USER_VILLE', 'user_ville');
define ('FIELD_USER_CP', 'user_cp');

//user role
define ('USER_PROFILE_MEMBRE', 'subscriber');
define ('USER_PROFILE_WEBMASTER', 'webmaster');
define ('USER_PROFILE_ADMIN', 'administrator');

//image size
define ('IMAGE_SIZE_ACTUS_VIGNETTE', 'actualite_vignette');
define ('IMAGE_SIZE_ACTUS_MEDIUM', 'actualite_medium');

//....